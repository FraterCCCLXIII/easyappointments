<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Customer account controller.
 *
 * @package Controllers
 */
class Customer_account extends EA_Controller
{
    protected array $allowed_customer_fields = [
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'timezone',
        'language',
        'custom_field_1',
        'custom_field_2',
        'custom_field_3',
        'custom_field_4',
        'custom_field_5',
        'notes',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->load->model('customers_model');
        $this->load->model('customer_auth_model');
        $this->load->model('appointments_model');
        $this->load->library('timezones');
        $this->load->library('stripe_gateway');
    }

    public function index(): void
    {
        $customer = $this->require_customer();

        $theme = request('theme', setting('theme', 'default'));

        if (empty($theme) || !file_exists(__DIR__ . '/../../assets/css/themes/' . $theme . '.min.css')) {
            $theme = 'default';
        }

        $flash = session('customer_flash');
        session(['customer_flash' => null]);

        $appointments = $this->appointments_model->get([
            'id_users_customer' => $customer['id'],
            'payment_status !=' => 'not-paid',
        ]);

        html_vars([
            'page_title' => 'My Account',
            'theme' => $theme,
            'company_name' => setting('company_name'),
            'company_logo' => setting('company_logo'),
            'company_color' => setting('company_color'),
            'display_booking_header' => false,
            'customer' => $customer,
            'profile_incomplete' => !$this->is_profile_complete($customer),
            'flash' => $flash,
            'complete_profile' => request('complete') === '1',
            'grouped_timezones' => $this->timezones->to_grouped_array(),
            'appointments' => $appointments,
            'stripe_enabled' => $this->stripe_gateway->is_enabled(),
        ]);

        $this->load->view('pages/customer_account');
    }

    public function stripe_portal(): void
    {
        try {
            $customer = $this->require_customer();

            if (empty($customer['stripe_customer_id'])) {
                throw new RuntimeException('No Stripe customer ID found.');
            }

            $session = $this->stripe_gateway->create_portal_session($customer['stripe_customer_id']);
            redirect($session->url);
        } catch (Throwable $e) {
            session([
                'customer_flash' => [
                    'type' => 'danger',
                    'message' => 'Could not open billing portal: ' . $e->getMessage(),
                ],
            ]);
            redirect('customer/account');
        }
    }

    public function update_profile(): void
    {
        try {
            $customer = $this->require_customer();
            $input = request('customer');

            if (!is_array($input)) {
                throw new InvalidArgumentException('Invalid customer data.');
            }

            $updated = array_merge($customer, $input);
            $updated['id'] = $customer['id'];

            $this->customers_model->only($updated, array_merge($this->allowed_customer_fields, ['id', 'email']));

            $this->customers_model->save($updated);

            session([
                'customer_flash' => [
                    'type' => 'success',
                    'message' => 'Account details updated.',
                ],
            ]);

            redirect('customer/account');
        } catch (Throwable $e) {
            session([
                'customer_flash' => [
                    'type' => 'danger',
                    'message' => $e->getMessage(),
                ],
            ]);

            redirect('customer/account');
        }
    }

    public function update_email(): void
    {
        try {
            $customer = $this->require_customer();
            $auth = $this->customer_auth_model->find_by_customer_id($customer['id']);

            if (empty($auth)) {
                throw new RuntimeException('Customer account was not found.');
            }

            $new_email = trim((string) request('email'));
            $password = (string) request('password');

            if (empty($new_email) || empty($password)) {
                throw new InvalidArgumentException('Email and password are required.');
            }

            if (!password_verify($password, $auth['password_hash'])) {
                throw new InvalidArgumentException('Current password is invalid.');
            }

            $customer['email'] = $new_email;
            $this->customers_model->save($customer);

            $this->customer_auth_model->save([
                'id' => $auth['id'],
                'email' => $new_email,
            ]);

            session([
                'customer_email' => $new_email,
                'customer_flash' => [
                    'type' => 'success',
                    'message' => 'Email updated.',
                ],
            ]);

            redirect('customer/account');
        } catch (Throwable $e) {
            session([
                'customer_flash' => [
                    'type' => 'danger',
                    'message' => $e->getMessage(),
                ],
            ]);

            redirect('customer/account');
        }
    }

    public function update_password(): void
    {
        try {
            $customer = $this->require_customer();
            $auth = $this->customer_auth_model->find_by_customer_id($customer['id']);

            if (empty($auth)) {
                throw new RuntimeException('Customer account was not found.');
            }

            $current_password = (string) request('current_password');
            $new_password = (string) request('new_password');
            $confirm_password = (string) request('confirm_password');

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                throw new InvalidArgumentException('All password fields are required.');
            }

            if (!password_verify($current_password, $auth['password_hash'])) {
                throw new InvalidArgumentException('Current password is invalid.');
            }

            if ($new_password !== $confirm_password) {
                throw new InvalidArgumentException('Passwords do not match.');
            }

            if (strlen($new_password) < MIN_PASSWORD_LENGTH || strlen($new_password) > MAX_PASSWORD_LENGTH) {
                throw new InvalidArgumentException('Password length is invalid.');
            }

            $this->customer_auth_model->save([
                'id' => $auth['id'],
                'password_hash' => password_hash($new_password, PASSWORD_DEFAULT),
                'password_updated_at' => date('Y-m-d H:i:s'),
                'failed_attempts' => 0,
                'locked_until' => null,
            ]);

            session([
                'customer_flash' => [
                    'type' => 'success',
                    'message' => 'Password updated.',
                ],
            ]);

            redirect('customer/account');
        } catch (Throwable $e) {
            session([
                'customer_flash' => [
                    'type' => 'danger',
                    'message' => $e->getMessage(),
                ],
            ]);

            redirect('customer/account');
        }
    }

    protected function require_customer(): array
    {
        if (!customer_logged_in()) {
            session(['customer_return_url' => current_url()]);
            redirect('customer/login');
            exit;
        }

        $customer = $this->customers_model->find(customer_id());

        if (empty($customer)) {
            $this->session->unset_userdata(['customer_id', 'customer_email']);
            redirect('customer/login');
            exit;
        }

        return $customer;
    }

    protected function is_profile_complete(array $customer): bool
    {
        if (empty($customer['first_name'])) {
            return false;
        }

        if (empty($customer['last_name'])) {
            return false;
        }

        if (empty($customer['address'])) {
            return false;
        }

        return true;
    }
}
