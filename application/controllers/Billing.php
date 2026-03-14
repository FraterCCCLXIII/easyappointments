<?php defined('BASEPATH') or exit('No direct script access allowed');

class Billing extends EA_Controller
{
    private const BILLING_STATUS_OPTIONS = [
        'unpaid' => 'Unpaid',
        'payment_link_sent' => 'Payment Link Sent',
        'paid' => 'Paid',
        'paid_by_phone' => 'Paid by Phone',
        'partially_refunded' => 'Partially Refunded',
        'refunded' => 'Refunded',
        'voided' => 'Voided',
    ];

    private const BILLING_STATUS_TRANSITIONS = [
        'unpaid' => ['payment_link_sent', 'paid', 'paid_by_phone', 'voided'],
        'payment_link_sent' => ['unpaid', 'paid', 'paid_by_phone', 'voided'],
        'paid' => ['partially_refunded', 'refunded', 'voided'],
        'paid_by_phone' => ['partially_refunded', 'refunded', 'voided'],
        'partially_refunded' => ['refunded'],
        'refunded' => [],
        'voided' => [],
    ];

    public function __construct()
    {
        parent::__construct();

        if (!can('view', PRIV_SYSTEM_SETTINGS)) {
            redirect('login');
        }

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
        $this->load->model('services_model');

        $this->load->library('stripe_gateway');
        $this->load->library('email_messages');
    }

    public function index()
    {
        $user_id = (int)session('user_id');
        $user_display_name = $this->accounts->get_user_display_name($user_id);

        $this->db->select('appointments.*, users.first_name, users.last_name, services.name as service_name');
        $this->db->from('appointments');
        $this->db->join('users', 'users.id = appointments.id_users_customer');
        $this->db->join('services', 'services.id = appointments.id_services');
        $this->db->order_by('appointments.book_datetime', 'DESC');
        $query = $this->db->get();
        $transactions = $query->result_array();

        html_vars([
            'page_title' => 'Billing',
            'active_menu' => 'billing',
            'transactions' => $transactions,
            'billing_status_options' => self::BILLING_STATUS_OPTIONS,
            'user_display_name' => $user_display_name,
            'role_slug' => session('role_slug'),
        ]);

        script_vars([
            'billing_status_options' => self::BILLING_STATUS_OPTIONS,
        ]);

        $this->load->view('pages/billing', [
            'active_menu' => 'billing',
            'user_display_name' => $user_display_name,
            'transactions' => $transactions,
            'billing_status_options' => self::BILLING_STATUS_OPTIONS,
        ]);
    }

    public function update_status(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            $appointment_id = (int) request('appointment_id');
            $target_status = trim((string) request('billing_status', ''));
            $billing_reference = trim((string) request('billing_reference', ''));
            $billing_notes = trim((string) request('billing_notes', ''));

            if (!$appointment_id || $target_status === '') {
                abort(400, 'Bad Request');
            }

            $allowed_statuses = array_keys(self::BILLING_STATUS_TRANSITIONS);
            if (!in_array($target_status, $allowed_statuses, true)) {
                throw new InvalidArgumentException('Invalid billing status provided.');
            }

            $appointment = $this->appointments_model->find($appointment_id);
            $current_status = $appointment['billing_status'] ?? 'unpaid';

            if ($current_status !== $target_status) {
                $allowed_transitions = self::BILLING_STATUS_TRANSITIONS[$current_status] ?? [];
                if (!in_array($target_status, $allowed_transitions, true)) {
                    throw new InvalidArgumentException('The requested billing status transition is not allowed.');
                }
            }

            $appointment['billing_status'] = $target_status;
            $appointment['billing_reference'] = $billing_reference === '' ? null : $billing_reference;
            $appointment['billing_notes'] = $billing_notes === '' ? null : $billing_notes;
            $appointment['billing_updated_at'] = date('Y-m-d H:i:s');

            if (in_array($target_status, ['paid', 'paid_by_phone'], true)) {
                $appointment['payment_status'] = 'paid';
            } elseif ($target_status === 'partially_refunded') {
                $appointment['payment_status'] = 'partially-refunded';
            } elseif ($target_status === 'refunded') {
                $appointment['payment_status'] = 'refunded';
            } elseif (in_array($target_status, ['unpaid', 'payment_link_sent'], true)) {
                $appointment['payment_status'] = 'not-paid';
            }

            $this->appointments_model->save($appointment);

            json_response([
                'success' => true,
                'billing_status' => $appointment['billing_status'],
                'payment_status' => $appointment['payment_status'] ?? null,
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function refund(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            $appointment_id = (int) request('appointment_id');
            $refund_mode = trim((string) request('refund_mode', 'amount'));
            $refund_value = (float) request('refund_value', 0);
            $refund_reason = trim((string) request('refund_reason', ''));

            if (!$appointment_id || $refund_value <= 0) {
                abort(400, 'Bad Request');
            }

            if (!in_array($refund_mode, ['amount', 'percent'], true)) {
                throw new InvalidArgumentException('Invalid refund mode provided.');
            }

            if (!$this->stripe_gateway->is_enabled()) {
                throw new RuntimeException('Stripe payments are not enabled.');
            }

            $appointment = $this->appointments_model->find($appointment_id);

            if (!in_array($appointment['billing_status'] ?? '', ['paid', 'paid_by_phone', 'partially_refunded'], true)) {
                throw new RuntimeException('Only paid appointments can be refunded.');
            }

            if (empty($appointment['stripe_payment_intent_id'])) {
                throw new RuntimeException('No Stripe payment intent is available for this appointment.');
            }

            $max_amount_cents = (int) round((float) ($appointment['payment_amount'] ?? 0) * 100);
            if ($max_amount_cents <= 0) {
                throw new RuntimeException('The appointment does not have a refundable payment amount.');
            }

            if ($refund_mode === 'percent') {
                if ($refund_value > 100) {
                    throw new InvalidArgumentException('Refund percent cannot exceed 100.');
                }
                $refund_amount_cents = (int) round(($max_amount_cents * $refund_value) / 100);
            } else {
                $refund_amount_cents = (int) round($refund_value * 100);
            }

            if ($refund_amount_cents <= 0 || $refund_amount_cents > $max_amount_cents) {
                throw new InvalidArgumentException('Refund amount is out of allowed range.');
            }

            $refund = $this->stripe_gateway->create_refund(
                (string) $appointment['stripe_payment_intent_id'],
                $refund_amount_cents,
                $refund_reason !== '' ? $refund_reason : null,
            );

            $is_full_refund = $refund_amount_cents === $max_amount_cents;
            $appointment['billing_status'] = $is_full_refund ? 'refunded' : 'partially_refunded';
            $appointment['payment_status'] = $is_full_refund ? 'refunded' : 'partially-refunded';
            $appointment['billing_reference'] = $refund->id ?? $appointment['billing_reference'];
            $appointment['billing_notes'] = $this->append_refund_note(
                (string) ($appointment['billing_notes'] ?? ''),
                $refund_amount_cents,
                $max_amount_cents,
                $refund_reason,
                (string) ($refund->id ?? ''),
            );
            $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
            $this->appointments_model->save($appointment);

            json_response([
                'success' => true,
                'billing_status' => $appointment['billing_status'],
                'payment_status' => $appointment['payment_status'],
                'refund_reference' => $appointment['billing_reference'],
                'refund_amount' => number_format($refund_amount_cents / 100, 2, '.', ''),
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function create_payment_link(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            $appointment_id = (int) request('appointment_id');
            $payload = $this->prepare_payment_link($appointment_id);
            json_response($payload);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function send_payment_link_email(): void
    {
        try {
            if (cannot('edit', PRIV_SYSTEM_SETTINGS)) {
                abort(403, 'Forbidden');
            }

            $appointment_id = (int) request('appointment_id');
            $payload = $this->prepare_payment_link($appointment_id);

            $settings = [
                'company_name' => setting('company_name'),
                'company_link' => setting('company_link'),
                'company_email' => setting('company_email'),
                'company_logo_email_png' => setting('company_logo_email_png'),
                'company_color' => setting('company_color'),
                'date_format' => setting('date_format'),
                'time_format' => setting('time_format'),
            ];

            $this->email_messages->send_payment_link(
                $payload['appointment'],
                $payload['service'],
                $payload['customer'],
                $settings,
                $payload['customer']['email'],
                $payload['payment_link'],
            );

            json_response([
                'success' => true,
                'payment_link' => $payload['payment_link'],
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    private function prepare_payment_link(int $appointment_id): array
    {
        if (!$appointment_id) {
            abort(400, 'Bad Request');
        }

        if (!$this->stripe_gateway->is_enabled()) {
            throw new RuntimeException('Stripe payments are not enabled.');
        }

        $appointment = $this->appointments_model->find($appointment_id);
        $customer = $this->customers_model->find((int) $appointment['id_users_customer']);
        $service = $this->services_model->find((int) $appointment['id_services']);

        if (empty($customer['email'])) {
            throw new RuntimeException('The appointment customer does not have an email address.');
        }

        if (empty($service['price']) || (float) $service['price'] <= 0) {
            throw new RuntimeException('The selected service has no payable amount.');
        }

        $appointment['payment_amount'] = (float) $service['price'];
        $appointment['payment_status'] = 'pending';
        $appointment['billing_status'] = 'payment_link_sent';
        $appointment['billing_updated_at'] = date('Y-m-d H:i:s');
        $this->appointments_model->save($appointment);
        $appointment = $this->appointments_model->find($appointment_id);

        $session = $this->stripe_gateway->create_checkout_session($appointment, $service, $customer);

        return [
            'payment_link' => $session->url,
            'appointment' => $appointment,
            'customer' => $customer,
            'service' => $service,
        ];
    }

    private function append_refund_note(
        string $existing_notes,
        int $refund_amount_cents,
        int $max_amount_cents,
        string $refund_reason,
        string $refund_reference,
    ): string {
        $notes = trim($existing_notes);
        $percent = round(($refund_amount_cents / $max_amount_cents) * 100, 2);
        $parts = [
            sprintf(
                '[%s] Refund processed: %s (%s%%)',
                date('Y-m-d H:i:s'),
                number_format($refund_amount_cents / 100, 2, '.', ''),
                rtrim(rtrim((string) $percent, '0'), '.'),
            ),
        ];

        if ($refund_reference !== '') {
            $parts[] = 'ref=' . $refund_reference;
        }

        if ($refund_reason !== '') {
            $parts[] = 'reason=' . $refund_reason;
        }

        $line = implode(' | ', $parts);

        return $notes === '' ? $line : $notes . PHP_EOL . $line;
    }
}
