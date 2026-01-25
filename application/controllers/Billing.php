<?php defined('BASEPATH') or exit('No direct script access allowed');

class Billing extends EA_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!can('view', PRIV_SYSTEM_SETTINGS)) {
            redirect('login');
        }

        $this->load->model('appointments_model');
        $this->load->model('customers_model');
    }

    public function index()
    {
        $user_id = (int)session('user_id');
        $user_display_name = $this->accounts->get_user_display_name($user_id);

        $this->db->select('appointments.*, users.first_name, users.last_name, services.name as service_name');
        $this->db->from('appointments');
        $this->db->join('users', 'users.id = appointments.id_users_customer');
        $this->db->join('services', 'services.id = appointments.id_services');
        $this->db->where('payment_status !=', 'not-paid');
        $this->db->order_by('appointments.book_datetime', 'DESC');
        $query = $this->db->get();
        $transactions = $query->result_array();

        html_vars([
            'page_title' => 'Billing',
            'active_menu' => 'billing',
            'transactions' => $transactions,
            'user_display_name' => $user_display_name,
            'role_slug' => session('role_slug'),
        ]);

        $this->load->view('pages/billing', [
            'active_menu' => 'billing',
            'user_display_name' => $user_display_name,
            'transactions' => $transactions,
        ]);
    }
}
