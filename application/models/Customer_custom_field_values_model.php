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
 * Customer custom field values model.
 *
 * @package Models
 */
class Customer_custom_field_values_model extends EA_Model
{
    public function find_for_user(int $user_id): array
    {
        $rows = $this->db
            ->get_where('customer_custom_field_values', ['id_users' => $user_id])
            ->result_array();

        $values = [];
        foreach ($rows as $row) {
            $values[(int) $row['id_custom_fields']] = $row['value'];
        }

        return $values;
    }

    public function save_for_user(int $user_id, array $values): void
    {
        foreach ($values as $field_id => $value) {
            $field_id = (int) $field_id;
            $value = is_string($value) ? trim($value) : '';

            if ($value === '') {
                $this->db->delete('customer_custom_field_values', [
                    'id_users' => $user_id,
                    'id_custom_fields' => $field_id,
                ]);
                continue;
            }

            $existing = $this->db
                ->get_where('customer_custom_field_values', [
                    'id_users' => $user_id,
                    'id_custom_fields' => $field_id,
                ])
                ->row_array();

            $data = [
                'id_users' => $user_id,
                'id_custom_fields' => $field_id,
                'value' => $value,
                'update_datetime' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $this->db->update(
                    'customer_custom_field_values',
                    $data,
                    ['id' => (int) $existing['id']]
                );
            } else {
                $this->db->insert('customer_custom_field_values', $data);
            }
        }
    }
}
