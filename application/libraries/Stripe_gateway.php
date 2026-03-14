<?php defined('BASEPATH') or exit('No direct script access allowed');

use Stripe\StripeClient;
use Stripe\Checkout\Session;

class Stripe_gateway
{
    /**
     * @var CI_Controller
     */
    protected $CI;

    /**
     * @var StripeClient
     */
    protected $stripe;

    /**
     * Stripe_gateway constructor.
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        
        $secret_key = setting('stripe_secret_key');
        
        if ($secret_key) {
            $this->stripe = new StripeClient($secret_key);
        }
    }

    /**
     * Check if Stripe is enabled and configured.
     * 
     * @return bool
     */
    public function is_enabled(): bool
    {
        return (bool)setting('stripe_enabled') && !empty(setting('stripe_secret_key'));
    }

    /**
     * Create a Stripe Checkout Session for an appointment.
     * 
     * @param array $appointment Appointment data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @return Session
     */
    public function create_checkout_session(array $appointment, array $service, array $customer): Session
    {
        return $this->create_amount_checkout_session(
            $appointment,
            $service,
            $customer,
            (float) ($service['price'] ?? 0),
            (string) ($service['name'] ?? 'Service'),
            [
                'payment_flow' => 'deposit',
            ],
        );
    }

    /**
     * Create a Stripe Checkout Session for a custom amount.
     *
     * @param array $appointment Appointment data.
     * @param array $service Service data.
     * @param array $customer Customer data.
     * @param float $amount Amount in major currency unit.
     * @param string $line_item_name Line item display name.
     * @param array $extra_metadata Additional metadata.
     *
     * @return Session
     */
    public function create_amount_checkout_session(
        array $appointment,
        array $service,
        array $customer,
        float $amount,
        string $line_item_name,
        array $extra_metadata = [],
    ): Session {
        $currency = setting('stripe_currency', 'USD');

        $product_data = [
            'name' => $line_item_name,
        ];

        $service_description = trim((string)($service['description'] ?? ''));

        if ($service_description !== '') {
            $product_data['description'] = $service_description;
        }

        $amount_cents = (int) round($amount * 100);
        if ($amount_cents <= 0) {
            throw new InvalidArgumentException('Stripe checkout amount must be greater than zero.');
        }

        $session_data = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => $product_data,
                    'unit_amount' => $amount_cents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'payment_intent_data' => [
                'setup_future_usage' => 'off_session',
            ],
            'success_url' => site_url('booking/payment_success/' . $appointment['hash']) .
                '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => site_url('booking/payment_cancel/' . $appointment['hash']),
            'client_reference_id' => $appointment['id'],
            'customer_email' => $customer['email'],
            'metadata' => [
                'appointment_id' => $appointment['id'],
                'appointment_hash' => $appointment['hash'],
                'amount_cents' => $amount_cents,
            ],
        ];

        foreach ($extra_metadata as $key => $value) {
            $session_data['metadata'][(string) $key] = (string) $value;
        }

        // If customer has a stripe_customer_id, use it
        if (!empty($customer['stripe_customer_id'])) {
            $session_data['customer'] = $customer['stripe_customer_id'];
        }

        return $this->stripe->checkout->sessions->create($session_data);
    }

    /**
     * Verify a Stripe Webhook signature.
     * 
     * @param string $payload
     * @param string $sig_header
     * @return \Stripe\Event
     */
    public function construct_webhook_event(string $payload, string $sig_header): \Stripe\Event
    {
        $webhook_secret = setting('stripe_webhook_secret');
        return \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
    }

    /**
     * Create a Stripe Customer Portal session.
     * 
     * @param string $stripe_customer_id
     * @return \Stripe\BillingPortal\Session
     */
    public function create_portal_session(string $stripe_customer_id): \Stripe\BillingPortal\Session
    {
        return $this->stripe->billingPortal->sessions->create([
            'customer' => $stripe_customer_id,
            'return_url' => site_url('customer/account'),
        ]);
    }

    /**
     * Retrieve a Stripe Checkout session.
     *
     * @param string $session_id
     * @return Session
     */
    public function retrieve_checkout_session(string $session_id): Session
    {
        return $this->stripe->checkout->sessions->retrieve($session_id, [
            'expand' => ['payment_intent'],
        ]);
    }

    /**
     * Create a Stripe refund for a payment intent.
     *
     * @param string $payment_intent_id
     * @param int $amount_cents
     * @param string|null $reason
     *
     * @return \Stripe\Refund
     */
    public function create_refund(string $payment_intent_id, int $amount_cents, ?string $reason = null): \Stripe\Refund
    {
        $payload = [
            'payment_intent' => $payment_intent_id,
            'amount' => $amount_cents,
        ];

        if (!empty($reason)) {
            $payload['metadata'] = [
                'reason_note' => $reason,
            ];
        }

        return $this->stripe->refunds->create($payload);
    }

    /**
     * Create an off-session payment intent against a stored payment method.
     */
    public function create_off_session_payment_intent(
        string $stripe_customer_id,
        string $payment_method_id,
        int $amount_cents,
        string $currency,
        array $metadata = [],
        ?string $idempotency_key = null,
    ): \Stripe\PaymentIntent {
        $payload = [
            'amount' => $amount_cents,
            'currency' => strtolower($currency),
            'customer' => $stripe_customer_id,
            'payment_method' => $payment_method_id,
            'off_session' => true,
            'confirm' => true,
            'metadata' => $metadata,
        ];

        $options = [];
        if (!empty($idempotency_key)) {
            $options['idempotency_key'] = $idempotency_key;
        }

        return $this->stripe->paymentIntents->create($payload, $options);
    }
}
