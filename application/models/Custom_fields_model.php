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
 * Custom fields model.
 *
 * Handles dynamic customer custom fields.
 *
 * @package Models
 */
class Custom_fields_model extends EA_Model
{
    protected array $casts = [
        'id' => 'integer',
        'is_required' => 'boolean',
        'is_displayed' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function find_all(bool $active_only = true): array
    {
        $query = $this->db->order_by('sort_order')->get('custom_fields');
        $fields = $query->result_array();

        if ($active_only) {
            $fields = array_values(array_filter($fields, function ($field) {
                return (int) ($field['is_active'] ?? 0) === 1;
            }));
        }

        foreach ($fields as &$field) {
            $this->cast($field);
        }

        return $fields;
    }

    public function find_displayed(): array
    {
        $fields = $this->find_all(true);
        $fields = array_values(array_filter($fields, function ($field) {
            return (int) ($field['is_displayed'] ?? 0) === 1;
        }));

        return $fields;
    }

    public function sync_fields(array $fields): void
    {
        $existing = $this->db->get_where('custom_fields', [])->result_array();
        $existing_by_id = [];

        foreach ($existing as $field) {
            $existing_by_id[$field['id']] = $field;
        }

        $incoming_ids = [];

        foreach ($fields as $field) {
            $data = [
                'label' => $field['label'] ?? '',
                'is_required' => (int) ($field['is_required'] ?? 0),
                'is_displayed' => (int) ($field['is_displayed'] ?? 0),
                'is_active' => 1,
                'sort_order' => (int) ($field['sort_order'] ?? 0),
                'update_datetime' => date('Y-m-d H:i:s'),
            ];

            if (!empty($field['id'])) {
                $incoming_ids[] = (int) $field['id'];
                $this->db->update('custom_fields', $data, ['id' => (int) $field['id']]);
            } else {
                $data['create_datetime'] = date('Y-m-d H:i:s');
                $this->db->insert('custom_fields', $data);
                $incoming_ids[] = (int) $this->db->insert_id();
            }
        }

        foreach ($existing_by_id as $existing_id => $existing_field) {
            if (!in_array((int) $existing_id, $incoming_ids, true)) {
                $this->db->update('custom_fields', ['is_active' => 0], ['id' => (int) $existing_id]);
            }
        }
    }
}
