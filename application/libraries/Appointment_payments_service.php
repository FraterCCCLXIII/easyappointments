<?php defined('BASEPATH') or exit('No direct script access allowed');

class Appointment_payments_service
{
    private CI_Controller $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('appointments_model');
        $this->CI->load->model('appointment_payments_model');
        $this->CI->load->model('services_model');
        $this->CI->load->model('customers_model');
        $this->CI->load->library('stripe_gateway');
    }

    public function calculate_amounts(array $service): array
    {
        $total = round((float) ($service['price'] ?? 0), 2);
        $down_payment_type = strtolower((string) ($service['down_payment_type'] ?? 'none'));
        $down_payment_value = (float) ($service['down_payment_value'] ?? 0);

        $deposit = match ($down_payment_type) {
            'fixed' => $down_payment_value,
            'percent' => round(($total * $down_payment_value) / 100, 2),
            default => $total,
        };

        if ($total <= 0) {
            $deposit = 0;
        }

        $deposit = max(0, min($deposit, $total));
        $remaining = round(max(0, $total - $deposit), 2);

        return [
            'total_amount' => $total,
            'deposit_amount' => $deposit,
            'remaining_amount' => $remaining,
        ];
    }

    public function initialize_appointment_payment_state(array &$appointment, array $service): void
    {
        $amounts = $this->calculate_amounts($service);

        $appointment['total_amount'] = $amounts['total_amount'];
        $appointment['deposit_amount'] = $amounts['deposit_amount'];
        $appointment['remaining_amount'] = $amounts['remaining_amount'];
        $appointment['payment_amount'] = $amounts['deposit_amount'];
        $appointment['payment_status'] = $amounts['deposit_amount'] > 0 ? 'pending' : 'not-paid';
        $appointment['payment_stage'] = $amounts['deposit_amount'] > 0 ? 'deposit_pending' : 'not_paid';
        $appointment['billing_status'] = $amounts['deposit_amount'] > 0 ? 'payment_link_sent' : 'unpaid';
        $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
    }

    public function mark_checkout_completed(
        array $appointment,
        object $session,
        string $event_id = '',
        ?string $flow = null,
    ): array {
        $flow_type = $flow ?: (string) ($session->metadata->payment_flow ?? 'deposit');
        $intent_id = (string) ($session->payment_intent ?? '');
        $payment_method_id = (string) ($session->payment_method ?? '');
        if ($payment_method_id === '' && !empty($session->payment_intent) && is_object($session->payment_intent)) {
            $intent_id = (string) ($session->payment_intent->id ?? $intent_id);
            $payment_method_id = (string) ($session->payment_intent->payment_method ?? '');
        }
        $remaining_before_capture = (float) ($appointment['remaining_amount'] ?? 0);
        $deposit_before_capture = (float) ($appointment['deposit_amount'] ?? 0);

        if ($flow_type === 'final' || $flow_type === 'final_retry') {
            $appointment['payment_status'] = 'paid';
            $appointment['payment_stage'] = 'fully_paid';
            $appointment['stripe_final_payment_intent_id'] = $intent_id !== '' ? $intent_id : null;
            $appointment['remaining_amount'] = 0;
            $appointment['payment_amount'] = (float) ($appointment['total_amount'] ?? $appointment['payment_amount'] ?? 0);
        } else {
            $appointment['payment_status'] = 'paid';
            $appointment['payment_stage'] =
                (float) ($appointment['remaining_amount'] ?? 0) > 0 ? 'deposit_paid' : 'fully_paid';
            $appointment['stripe_deposit_payment_intent_id'] = $intent_id !== '' ? $intent_id : null;
            $appointment['payment_amount'] = (float) ($appointment['deposit_amount'] ?? $appointment['payment_amount'] ?? 0);
        }

        if ($intent_id !== '') {
            $appointment['stripe_payment_intent_id'] = $intent_id;
            $appointment['billing_reference'] = $intent_id;
        }

        if ($payment_method_id !== '') {
            $appointment['stripe_payment_method_id'] = $payment_method_id;
        }

        $appointment['billing_status'] = 'paid';
        $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
        $appointment['final_charge_error_code'] = null;
        $appointment['final_charge_error_message'] = null;

        $this->CI->appointments_model->save($appointment);
        $appointment = $this->CI->appointments_model->find((int) $appointment['id']);

        if ($intent_id !== '' || $event_id !== '') {
            $this->record_payment([
                'id_appointments' => (int) $appointment['id'],
                'kind' => $flow_type === 'final' || $flow_type === 'final_retry' ? 'final' : 'deposit',
                'amount' =>
                    $flow_type === 'final' || $flow_type === 'final_retry'
                        ? $remaining_before_capture
                        : $deposit_before_capture,
                'currency' => setting('stripe_currency', 'USD'),
                'status' => 'succeeded',
                'stripe_payment_intent_id' => $intent_id !== '' ? $intent_id : null,
                'stripe_event_id' => $event_id !== '' ? $event_id : null,
            ]);
        }

        return $appointment;
    }

    public function maybe_charge_remaining_on_completed(array $before, array $after): void
    {
        $before_status = strtolower((string) ($before['status'] ?? ''));
        $after_status = strtolower((string) ($after['status'] ?? ''));

        if ($before_status === 'completed' || $after_status !== 'completed') {
            return;
        }

        $remaining_amount = (float) ($after['remaining_amount'] ?? 0);
        if ($remaining_amount <= 0) {
            return;
        }

        $payment_stage = (string) ($after['payment_stage'] ?? '');
        if (!in_array($payment_stage, ['deposit_paid', 'final_charge_failed'], true)) {
            return;
        }

        $this->attempt_final_charge((int) $after['id'], 'status_completed');
    }

    public function attempt_final_charge(int $appointment_id, string $trigger_source = 'manual'): array
    {
        $appointment = $this->CI->appointments_model->find($appointment_id);
        $remaining_amount = (float) ($appointment['remaining_amount'] ?? 0);

        if ($remaining_amount <= 0) {
            return $appointment;
        }

        $customer = $this->CI->customers_model->find((int) $appointment['id_users_customer']);
        $payment_method_id = (string) ($appointment['stripe_payment_method_id'] ?? '');

        $appointment['payment_stage'] = 'final_charge_pending';
        $appointment['final_charge_attempts'] = (int) ($appointment['final_charge_attempts'] ?? 0) + 1;
        $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
        $this->CI->appointments_model->save($appointment);

        if (empty($customer['stripe_customer_id']) || $payment_method_id === '') {
            $appointment['payment_stage'] = 'final_charge_failed';
            $appointment['final_charge_error_code'] = 'missing_payment_method';
            $appointment['final_charge_error_message'] = 'No reusable payment method found for final charge.';
            $appointment['billing_status'] = 'unpaid';
            $this->CI->appointments_model->save($appointment);
            return $this->CI->appointments_model->find($appointment_id);
        }

        try {
            $intent = $this->CI->stripe_gateway->create_off_session_payment_intent(
                $customer['stripe_customer_id'],
                $payment_method_id,
                (int) round($remaining_amount * 100),
                setting('stripe_currency', 'USD'),
                [
                    'appointment_id' => (string) $appointment_id,
                    'payment_flow' => 'final',
                    'trigger_source' => $trigger_source,
                ],
                'final-' . $appointment_id . '-' . ((int) $appointment['final_charge_attempts']),
            );

            $appointment['stripe_final_payment_intent_id'] = $intent->id ?? null;
            $appointment['stripe_payment_intent_id'] = $intent->id ?? null;
            $appointment['payment_status'] = 'paid';
            $appointment['payment_stage'] = 'fully_paid';
            $appointment['remaining_amount'] = 0;
            $appointment['payment_amount'] = (float) ($appointment['total_amount'] ?? $appointment['payment_amount'] ?? 0);
            $appointment['billing_status'] = 'paid';
            $appointment['billing_reference'] = $intent->id ?? null;
            $appointment['final_charge_error_code'] = null;
            $appointment['final_charge_error_message'] = null;
            $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
            $this->CI->appointments_model->save($appointment);

            $this->record_payment([
                'id_appointments' => $appointment_id,
                'kind' => 'final',
                'amount' => $remaining_amount,
                'currency' => setting('stripe_currency', 'USD'),
                'status' => 'succeeded',
                'stripe_payment_intent_id' => $intent->id ?? null,
            ]);
        } catch (Throwable $e) {
            $appointment['payment_stage'] = 'final_charge_failed';
            $appointment['billing_status'] = 'unpaid';
            $appointment['final_charge_error_code'] = method_exists($e, 'getStripeCode') ? $e->getStripeCode() : 'charge_failed';
            $appointment['final_charge_error_message'] = $e->getMessage();
            $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
            $this->CI->appointments_model->save($appointment);

            $this->record_payment([
                'id_appointments' => $appointment_id,
                'kind' => 'final',
                'amount' => $remaining_amount,
                'currency' => setting('stripe_currency', 'USD'),
                'status' => 'failed',
                'error_code' => $appointment['final_charge_error_code'],
                'error_message' => $appointment['final_charge_error_message'],
            ]);
        }

        return $this->CI->appointments_model->find($appointment_id);
    }

    public function prepare_remaining_payment_link(int $appointment_id): array
    {
        $appointment = $this->CI->appointments_model->find($appointment_id);
        $customer = $this->CI->customers_model->find((int) $appointment['id_users_customer']);
        $service = $this->CI->services_model->find((int) $appointment['id_services']);

        if (empty($customer['email'])) {
            throw new RuntimeException('The appointment customer does not have an email address.');
        }

        $remaining_amount = round((float) ($appointment['remaining_amount'] ?? 0), 2);
        if ($remaining_amount <= 0) {
            $total_amount = round((float) ($appointment['total_amount'] ?? 0), 2);
            if ($total_amount <= 0) {
                $total_amount = round((float) ($service['price'] ?? 0), 2);
                $appointment['total_amount'] = $total_amount;
            }

            if ($total_amount > 0) {
                $remaining_amount = $total_amount;
                $appointment['remaining_amount'] = $remaining_amount;
                $appointment['deposit_amount'] = round((float) ($appointment['deposit_amount'] ?? 0), 2);
                $appointment['payment_stage'] = $appointment['payment_stage'] ?? 'final_charge_pending';
            }
        }

        if ($remaining_amount <= 0) {
            throw new RuntimeException('There is no outstanding balance for this appointment.');
        }

        $appointment['payment_stage'] = 'final_charge_pending';
        $appointment['payment_status'] = 'pending';
        $appointment['billing_status'] = 'payment_link_sent';
        $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
        $this->CI->appointments_model->save($appointment);
        $appointment = $this->CI->appointments_model->find($appointment_id);

        $session = $this->CI->stripe_gateway->create_amount_checkout_session(
            $appointment,
            $service,
            $customer,
            $remaining_amount,
            'Final balance for ' . ($service['name'] ?? 'appointment'),
            [
                'payment_flow' => 'final_retry',
                'appointment_id' => (string) $appointment_id,
                'appointment_hash' => (string) $appointment['hash'],
            ],
        );

        return [
            'payment_link' => $session->url,
            'appointment' => $appointment,
            'customer' => $customer,
            'service' => $service,
        ];
    }

    public function record_payment(array $payment): void
    {
        $event_id = (string) ($payment['stripe_event_id'] ?? '');
        if ($event_id !== '' && $this->CI->appointment_payments_model->find_by_event_id($event_id)) {
            return;
        }

        $payload = [
            'id_appointments' => (int) $payment['id_appointments'],
            'kind' => (string) ($payment['kind'] ?? 'deposit'),
            'amount' => (float) ($payment['amount'] ?? 0),
            'currency' => (string) ($payment['currency'] ?? setting('stripe_currency', 'USD')),
            'status' => (string) ($payment['status'] ?? 'pending'),
            'stripe_payment_intent_id' => $payment['stripe_payment_intent_id'] ?? null,
            'stripe_event_id' => $payment['stripe_event_id'] ?? null,
            'error_code' => $payment['error_code'] ?? null,
            'error_message' => $payment['error_message'] ?? null,
        ];

        $this->CI->appointment_payments_model->save($payload);
    }
}
