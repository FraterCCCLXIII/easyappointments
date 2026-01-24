<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div class="wizard-frame">
    <div class="frame-container">
        <h2 class="frame-title">My Bookings</h2>

        <div class="mb-4 text-end">
            <a href="<?= site_url('booking') ?>" class="btn btn-dark">
                New Booking
            </a>
        </div>

        <?php if (empty(vars('appointments'))): ?>
            <div class="alert alert-secondary">
                You have no appointments yet.
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
                    <?php foreach (vars('appointments') as $row): ?>
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
</div>
<?php end_section('content'); ?>
