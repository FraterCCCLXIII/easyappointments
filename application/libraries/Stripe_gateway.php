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
        $currency = setting('stripe_currency', 'USD');
        
        $session_data = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => $service['name'],
                        'description' => $service['description'] ?? '',
                    ],
                    'unit_amount' => (int)($service['price'] * 100), // Stripe expects cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => site_url('booking/payment_success/' . $appointment['hash']),
            'cancel_url' => site_url('booking/payment_cancel/' . $appointment['hash']),
            'client_reference_id' => $appointment['id'],
            'customer_email' => $customer['email'],
            'metadata' => [
                'appointment_id' => $appointment['id'],
                'appointment_hash' => $appointment['hash'],
            ],
        ];

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
}
