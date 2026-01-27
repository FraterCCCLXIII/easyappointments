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
 * Form submissions model.
 *
 * Handles all the database operations of the form submissions resource.
 *
 * @package Models
 */
class Form_submissions_model extends EA_Model
{
    protected array $casts = [
        'id' => 'integer',
        'id_forms' => 'integer',
        'id_users' => 'integer',
    ];

    public function create(int $form_id, int $user_id, ?string $fields_snapshot = null): int
    {
        $data = [
            'id_forms' => $form_id,
            'id_users' => $user_id,
            'submitted_at' => date('Y-m-d H:i:s'),
        ];

        if ($fields_snapshot) {
            $data['fields_snapshot'] = $fields_snapshot;
        }

        if (!$this->db->insert('form_submissions', $data)) {
            throw new RuntimeException('Could not create form submission.');
        }

        return $this->db->insert_id();
    }

    public function find_for_user(int $form_id, int $user_id): ?array
    {
        $submission = $this->db
            ->get_where('form_submissions', [
                'id_forms' => $form_id,
                'id_users' => $user_id,
            ])
            ->row_array();

        if (!$submission) {
            return null;
        }

        $this->cast($submission);

        return $submission;
    }

    public function delete_for_form(int $form_id): void
    {
        $this->db->delete('form_submissions', ['id_forms' => $form_id]);
    }
}
