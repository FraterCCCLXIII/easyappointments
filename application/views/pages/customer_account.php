<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div class="wizard-frame">
    <div class="frame-container">
        <h2 class="frame-title">My Account</h2>

        <?php if (vars('complete_profile')): ?>
            <div class="alert alert-warning mb-4">
                Please complete your profile before booking.
            </div>
        <?php endif; ?>

        <?php if (vars('profile_incomplete')): ?>
            <div class="alert alert-warning mb-4">
                Your profile is missing required information.
            </div>
        <?php endif; ?>

        <?php if (vars('flash')): ?>
            <div class="alert alert-<?= e(vars('flash')['type']) ?> mb-4">
                <?= e(vars('flash')['message']) ?>
            </div>
        <?php endif; ?>

        <ul class="nav nav-pills mb-5 justify-content-center" id="account-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">Security</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="billing-tab" data-bs-toggle="pill" data-bs-target="#billing" type="button" role="tab">Billing</button>
            </li>
        </ul>

        <div class="tab-content" id="account-tabs-content">
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="row frame-content">
                    <div class="col-12 col-lg-8 mx-auto">
                        <h5 class="mb-3">Profile Details</h5>
                        <form method="post" action="<?= site_url('customer/account/update') ?>">
                            <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer-first-name" class="form-label">First Name</label>
                                    <input type="text" id="customer-first-name" name="customer[first_name]" class="form-control"
                                           value="<?= e(vars('customer')['first_name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer-last-name" class="form-label">Last Name</label>
                                    <input type="text" id="customer-last-name" name="customer[last_name]" class="form-control"
                                           value="<?= e(vars('customer')['last_name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="customer-phone" class="form-label">Phone</label>
                                <input type="text" id="customer-phone" name="customer[phone_number]" class="form-control"
                                       value="<?= e(vars('customer')['phone_number'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="customer-address" class="form-label">Address</label>
                                <input type="text" id="customer-address" name="customer[address]" class="form-control"
                                       value="<?= e(vars('customer')['address'] ?? '') ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="customer-city" class="form-label">City</label>
                                    <input type="text" id="customer-city" name="customer[city]" class="form-control"
                                           value="<?= e(vars('customer')['city'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="customer-state" class="form-label">State</label>
                                    <input type="text" id="customer-state" name="customer[state]" class="form-control"
                                           value="<?= e(vars('customer')['state'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="customer-zip" class="form-label">Zip</label>
                                    <input type="text" id="customer-zip" name="customer[zip_code]" class="form-control"
                                           value="<?= e(vars('customer')['zip_code'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="customer-timezone" class="form-label">Timezone</label>
                                <select id="customer-timezone" name="customer[timezone]" class="form-select">
                                    <?php foreach (vars('grouped_timezones') as $group => $timezones): ?>
                                        <optgroup label="<?= e($group) ?>">
                                            <?php foreach ($timezones as $timezone): ?>
                                                <option value="<?= e($timezone) ?>"
                                                    <?= vars('customer')['timezone'] === $timezone ? 'selected' : '' ?>>
                                                    <?= e($timezone) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-3">
                                Save Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="row frame-content">
                    <div class="col-12 col-lg-8 mx-auto">
                        <h5 class="mb-3">Update Email</h5>
                        <form method="post" action="<?= site_url('customer/account/email') ?>" class="mb-5">
                            <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                            <div class="mb-3">
                                <label for="customer-email" class="form-label">Email</label>
                                <input type="email" id="customer-email" name="email" class="form-control"
                                       value="<?= e(vars('customer')['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer-email-password" class="form-label">Current Password</label>
                                <input type="password" id="customer-email-password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-outline-dark w-100 py-3">
                                Update Email
                            </button>
                        </form>

                        <h5 class="mb-3">Update Password</h5>
                        <form method="post" action="<?= site_url('customer/account/password') ?>">
                            <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                            <div class="mb-3">
                                <label for="customer-current-password" class="form-label">Current Password</label>
                                <input type="password" id="customer-current-password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer-new-password" class="form-label">New Password</label>
                                <input type="password" id="customer-new-password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label for="customer-confirm-password" class="form-label">Confirm New Password</label>
                                <input type="password" id="customer-confirm-password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-outline-dark w-100 py-3">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="billing" role="tabpanel">
                <div class="row frame-content">
                    <div class="col-12 col-lg-8 mx-auto">
                        <?php if (vars('stripe_enabled') && !empty(vars('customer')['stripe_customer_id'])): ?>
                            <div class="card mb-5 border">
                                <div class="card-body d-flex justify-content-between align-items-center py-4">
                                    <div>
                                        <h5 class="mb-1">Payment Methods</h5>
                                        <p class="text-muted mb-0 small">Manage your saved cards and billing info via Stripe.</p>
                                    </div>
                                    <a href="<?= site_url('customer/account/stripe_portal') ?>" class="btn btn-dark px-4">
                                        Open Stripe Portal
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <h5 class="mb-3">Billing History</h5>
                        <div class="card bg-white border">
                            <div class="card-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Date</th>
                                            <th>Amount</th>
                                            <th class="pe-4">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty(vars('appointments'))): ?>
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">No billing history found.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach (vars('appointments') as $appointment): ?>
                                                <tr>
                                                    <td class="ps-4"><?= date('Y-m-d H:i', strtotime($appointment['book_datetime'])) ?></td>
                                                    <td><?= number_format($appointment['payment_amount'], 2) ?></td>
                                                    <td class="pe-4">
                                                        <span class="badge bg-<?= $appointment['payment_status'] === 'paid' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($appointment['payment_status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php end_section('content'); ?>
