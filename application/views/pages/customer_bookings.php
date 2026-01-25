<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div class="wizard-frame">
    <div class="frame-container">
        <h2 class="frame-title">My Bookings</h2>

        <div class="row">
            <div class="col-12 mx-auto text-end mb-4">
                <a href="<?= site_url('booking') ?>" class="btn btn-dark">
                    New Booking
                </a>
            </div>
        </div>

        <ul class="nav nav-pills mb-5 justify-content-center" id="bookings-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="current-tab" data-bs-toggle="pill" data-bs-target="#current" type="button" role="tab">Current</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-tab" data-bs-toggle="pill" data-bs-target="#past" type="button" role="tab">Past</button>
            </li>
        </ul>

        <div class="tab-content" id="bookings-tabs-content">
            <?php
            $current_appointments = [];
            $past_appointments = [];
            $now = time();

            foreach (vars('appointments') as $row) {
                if (strtotime($row['appointment']['start_datetime']) >= $now) {
                    $current_appointments[] = $row;
                } else {
                    $past_appointments[] = $row;
                }
            }
            ?>

            <div class="tab-pane fade show active" id="current" role="tabpanel">
                <?php if (empty($current_appointments)): ?>
                    <div class="alert alert-secondary text-center py-4">
                        You have no upcoming appointments.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>Service</th>
                                <th>Provider</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($current_appointments as $row): ?>
                                <?php
                                $appointment = $row['appointment'];
                                $service = $row['service'];
                                $provider = $row['provider'];
                                ?>
                                <tr>
                                    <td><?= e($service['name'] ?? '-') ?></td>
                                    <td><?= e(($provider['first_name'] ?? '') . ' ' . ($provider['last_name'] ?? '')) ?></td>
                                    <td><?= e(format_date_time($appointment['start_datetime'])) ?></td>
                                    <td><?= e($appointment['status'] ?? '-') ?></td>
                                    <td class="text-end">
                                        <a href="<?= site_url('booking/reschedule/' . $appointment['hash']) ?>"
                                           class="btn btn-outline-dark btn-sm">
                                            Reschedule
                                        </a>
                                        <a href="<?= site_url('booking/reschedule/' . $appointment['hash']) ?>#cancel-appointment-frame"
                                           class="btn btn-outline-danger btn-sm">
                                            Cancel
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="past" role="tabpanel">
                <?php if (empty($past_appointments)): ?>
                    <div class="alert alert-secondary text-center py-4">
                        You have no past appointments.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                            <tr>
                                <th>Service</th>
                                <th>Provider</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($past_appointments as $row): ?>
                                <?php
                                $appointment = $row['appointment'];
                                $service = $row['service'];
                                $provider = $row['provider'];
                                ?>
                                <tr>
                                    <td><?= e($service['name'] ?? '-') ?></td>
                                    <td><?= e(($provider['first_name'] ?? '') . ' ' . ($provider['last_name'] ?? '')) ?></td>
                                    <td><?= e(format_date_time($appointment['start_datetime'])) ?></td>
                                    <td><?= e($appointment['status'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php end_section('content'); ?>
