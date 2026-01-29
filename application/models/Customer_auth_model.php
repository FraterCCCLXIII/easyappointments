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
 * Customer authentication model.
 *
 * Handles credentials for customer accounts (separate from staff).
 *
 * @package Models
 */
class Customer_auth_model extends EA_Model
{
    /**
     * @var array
     */
    protected array $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'email_verified' => 'integer',
        'failed_attempts' => 'integer',
    ];

    /**
     * Save (insert or update) a customer auth record.
     *
     * @param array $record
     *
     * @return int
     */
    public function save(array $record): int
    {
        if (!empty($record['id'])) {
            $existing = $this->db->get_where('customer_auth', ['id' => $record['id']])->row_array();

            if (empty($existing)) {
                throw new InvalidArgumentException('Customer auth record was not found.');
            }

            $record = array_merge($existing, $record);
        }

        $this->validate($record);

        if (empty($record['id'])) {
            return $this->insert($record);
        }

        return $this->update($record);
    }

    /**
     * Find customer auth record by email.
     *
     * @param string $email
     *
     * @return array|null
     */
    public function find_by_email(string $email): ?array
    {
        $record = $this->db->get_where('customer_auth', ['email' => $email])->row_array();

        if (empty($record)) {
            return null;
        }

        $this->cast($record);

        return $record;
    }

    /**
     * Find customer auth record by customer ID.
     *
     * @param int $customer_id
     *
     * @return array|null
     */
    public function find_by_customer_id(int $customer_id): ?array
    {
        $record = $this->db->get_where('customer_auth', ['customer_id' => $customer_id])->row_array();

        if (empty($record)) {
            return null;
        }

        $this->cast($record);

        return $record;
    }

    /**
     * Validate the customer auth data.
     *
     * @param array $record
     */
    public function validate(array $record): void
    {
        if (empty($record['customer_id'])) {
            throw new InvalidArgumentException('Customer ID is required.');
        }

        if (empty($record['email'])) {
            throw new InvalidArgumentException('Email is required.');
        }

        if (!filter_var($record['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address provided: ' . $record['email']);
        }

        if (empty($record['id']) && !array_key_exists('password_hash', $record)) {
            throw new InvalidArgumentException('Password hash is required.');
        }

        $record_id = $record['id'] ?? null;

        $email_count = $this->db
            ->where('email', $record['email'])
            ->where('id !=', $record_id)
            ->count_all_results('customer_auth');

        if ($email_count > 0) {
            throw new InvalidArgumentException('The provided email address is already in use.');
        }

        $customer_count = $this->db
            ->where('customer_id', $record['customer_id'])
            ->where('id !=', $record_id)
            ->count_all_results('customer_auth');

        if ($customer_count > 0) {
            throw new InvalidArgumentException('Customer auth record already exists.');
        }
    }

    /**
     * Insert a record.
     */
    protected function insert(array $record): int
    {
        $timestamp = date('Y-m-d H:i:s');
        $record['create_datetime'] = $record['create_datetime'] ?? $timestamp;
        $record['update_datetime'] = $record['update_datetime'] ?? $timestamp;

        $this->db->insert('customer_auth', $record);

        return (int) $this->db->insert_id();
    }

    /**
     * Update a record.
     */
    protected function update(array $record): int
    {
        $record_id = $record['id'];
        unset($record['id']);

        $record['update_datetime'] = date('Y-m-d H:i:s');

        $this->db->where('id', $record_id)->update('customer_auth', $record);

        return (int) $record_id;
    }
}
