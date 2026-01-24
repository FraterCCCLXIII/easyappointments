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
 * Customer authentication controller.
 *
 * @package Controllers
 */
class Customer_auth extends EA_Controller
{
    protected int $max_failed_attempts = 5;
    protected int $lockout_minutes = 15;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('customers_model');
        $this->load->model('customer_auth_model');
        $this->load->model('settings_model');
    }

    /**
     * Render the customer login/registration page.
     */
    public function login(): void
    {
        if (customer_logged_in()) {
            redirect('booking');
            return;
        }

        $theme = request('theme', setting('theme', 'default'));

        if (empty($theme) || !file_exists(__DIR__ . '/../../assets/css/themes/' . $theme . '.min.css')) {
            $theme = 'default';
        }

        html_vars([
            'page_title' => 'Customer Login',
            'theme' => $theme,
            'company_name' => setting('company_name'),
            'company_logo' => setting('company_logo'),
            'company_color' => setting('company_color'),
            'display_booking_header' => false,
            'auth_error' => session('customer_auth_error'),
            'auth_mode' => session('customer_auth_mode'),
        ]);

        session(['customer_auth_error' => null, 'customer_auth_mode' => null]);

        $this->load->view('pages/customer_auth');
    }

    /**
     * Authenticate a customer.
     */
    public function authenticate(): void
    {
        try {
            rate_limit($this->input->ip_address(), 30, 120);

            $email = trim((string) request('email'));
            $password = (string) request('password');

            if (empty($email) || empty($password)) {
                throw new InvalidArgumentException('Email and password are required.');
            }

            $auth = $this->customer_auth_model->find_by_email($email);

            if (empty($auth) || $auth['status'] !== 'active') {
                throw new InvalidArgumentException('Invalid credentials provided.');
            }

            if (!empty($auth['locked_until']) && strtotime($auth['locked_until']) > time()) {
                throw new RuntimeException('Account is temporarily locked. Please try again later.');
            }

            if (!password_verify($password, $auth['password_hash'])) {
                $failed_attempts = $auth['failed_attempts'] + 1;
                $update = [
                    'id' => $auth['id'],
                    'failed_attempts' => $failed_attempts,
                ];

                if ($failed_attempts >= $this->max_failed_attempts) {
                    $update['locked_until'] = date('Y-m-d H:i:s', strtotime('+' . $this->lockout_minutes . ' minutes'));
                }

                $this->customer_auth_model->save($update);

                throw new InvalidArgumentException('Invalid credentials provided.');
            }

            $this->customer_auth_model->save([
                'id' => $auth['id'],
                'failed_attempts' => 0,
                'locked_until' => null,
                'last_login_at' => date('Y-m-d H:i:s'),
            ]);

            $this->session->sess_regenerate();

            session([
                'customer_id' => $auth['customer_id'],
                'customer_email' => $auth['email'],
            ]);

            $return_url = session('customer_return_url') ?: site_url('booking');
            session(['customer_return_url' => null]);

            redirect($return_url);
        } catch (Throwable $e) {
            session([
                'customer_auth_error' => $e->getMessage(),
                'customer_auth_mode' => 'login',
            ]);

            redirect('customer/login');
        }
    }

    /**
     * Register a new customer account.
     */
    public function register(): void
    {
        try {
            rate_limit($this->input->ip_address(), 20, 120);

            $email = trim((string) request('email'));
            $password = (string) request('password');
            $password_confirm = (string) request('password_confirm');

            if (empty($email) || empty($password) || empty($password_confirm)) {
                throw new InvalidArgumentException('Email and password are required.');
            }

            if ($password !== $password_confirm) {
                throw new InvalidArgumentException('Passwords do not match.');
            }

            if (strlen($password) < MIN_PASSWORD_LENGTH || strlen($password) > MAX_PASSWORD_LENGTH) {
                throw new InvalidArgumentException('Password length is invalid.');
            }

            $customer_data = [
                'email' => $email,
            ];

            if ($this->customers_model->exists($customer_data)) {
                $customer_id = $this->customers_model->find_record_id($customer_data);
            } else {
                $customer_id = $this->customers_model->create_shell($customer_data);
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $this->customer_auth_model->save([
                'customer_id' => $customer_id,
                'email' => $email,
                'password_hash' => $password_hash,
                'status' => 'active',
                'password_updated_at' => date('Y-m-d H:i:s'),
            ]);

            $this->session->sess_regenerate();

            session([
                'customer_id' => $customer_id,
                'customer_email' => $email,
            ]);

            redirect('customer/account?complete=1');
        } catch (Throwable $e) {
            session([
                'customer_auth_error' => $e->getMessage(),
                'customer_auth_mode' => 'register',
            ]);

            redirect('customer/login');
        }
    }

    /**
     * Log out a customer.
     */
    public function logout(): void
    {
        $this->session->unset_userdata(['customer_id', 'customer_email']);
        redirect('customer/login');
    }
}
