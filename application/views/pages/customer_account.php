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

        <div class="row frame-content">
            <div class="col-12 col-lg-6">
                <h5 class="mb-3">Profile</h5>
                <form method="post" action="<?= site_url('customer/account/update') ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                    <div class="mb-3">
                        <label for="customer-first-name" class="form-label">First Name</label>
                        <input type="text" id="customer-first-name" name="customer[first_name]" class="form-control"
                               value="<?= e(vars('customer')['first_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="customer-last-name" class="form-label">Last Name</label>
                        <input type="text" id="customer-last-name" name="customer[last_name]" class="form-control"
                               value="<?= e(vars('customer')['last_name'] ?? '') ?>">
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
                    <div class="mb-3">
                        <label for="customer-city" class="form-label">City</label>
                        <input type="text" id="customer-city" name="customer[city]" class="form-control"
                               value="<?= e(vars('customer')['city'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="customer-state" class="form-label">State</label>
                        <input type="text" id="customer-state" name="customer[state]" class="form-control"
                               value="<?= e(vars('customer')['state'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="customer-zip" class="form-label">Zip</label>
                        <input type="text" id="customer-zip" name="customer[zip_code]" class="form-control"
                               value="<?= e(vars('customer')['zip_code'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
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
            <div class="col-12 col-lg-6 mt-5 mt-lg-0">
                <h5 class="mb-3">Account Settings</h5>
                <form method="post" action="<?= site_url('customer/account/email') ?>" class="mb-4">
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
</div>
<?php end_section('content'); ?>
