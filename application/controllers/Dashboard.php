<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends EA_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('customers_model');
    }

    public function index(): void
    {
        if (!customer_logged_in()) {
            session(['customer_return_url' => current_url()]);
            redirect('customer/login');
            return;
        }

        $customer = $this->customers_model->find(customer_id());

        html_vars([
            'page_title' => 'Dashboard',
            'company_name' => setting('company_name'),
            'company_logo' => setting('company_logo'),
            'company_color' => setting('company_color'),
            'customer' => $customer,
            'theme' => setting('theme', 'default'),
            'display_booking_header' => false,
        ]);

        $this->load->view('pages/dashboard');
    }
}
