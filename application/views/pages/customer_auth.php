<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div class="wizard-frame">
    <div class="frame-container">
        <h2 class="frame-title">Customer Account</h2>

        <?php if (vars('auth_error')): ?>
            <div class="alert alert-danger mb-4">
                <?= e(vars('auth_error')) ?>
            </div>
        <?php endif; ?>

        <div class="frame-content">
            <div id="customer-auth-register">
                <div class="mb-3">
                    <a href="#" id="customer-auth-show-login" class="text-decoration-none">
                        Already have an account? Login
                    </a>
                </div>
                <h5 class="mb-3">Create Account</h5>
                <form method="post" action="<?= site_url('customer/register') ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                    <div class="mb-3">
                        <label for="customer-register-email" class="form-label">Email</label>
                        <input type="email" id="customer-register-email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer-register-password" class="form-label">Password</label>
                        <input type="password" id="customer-register-password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label for="customer-register-password-confirm" class="form-label">Confirm Password</label>
                        <input type="password" id="customer-register-password-confirm" name="password_confirm" class="form-control" required>
                    </div>
                    <p class="text-muted small mb-3">
                        You will complete your profile after creating your account.
                    </p>
                    <button type="submit" class="btn btn-outline-dark w-100 py-3">
                        Create Account
                    </button>
                </form>
            </div>

            <div id="customer-auth-login" class="d-none">
                <div class="mb-3">
                    <a href="#" id="customer-auth-show-register" class="text-decoration-none">
                        Need to create an account? Create Account
                    </a>
                </div>
                <h5 class="mb-3">Sign In</h5>
                <form method="post" action="<?= site_url('customer/authenticate') ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                    <div class="mb-3">
                        <label for="customer-login-email" class="form-label">Email</label>
                        <input type="email" id="customer-login-email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label for="customer-login-password" class="form-label">Password</label>
                        <input type="password" id="customer-login-password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-3">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php end_section('content'); ?>

<?php section('scripts'); ?>
<script>
    (function () {
        const registerPanel = document.getElementById('customer-auth-register');
        const loginPanel = document.getElementById('customer-auth-login');
        const showLogin = document.getElementById('customer-auth-show-login');
        const showRegister = document.getElementById('customer-auth-show-register');

        const togglePanels = (showLoginPanel) => {
            if (showLoginPanel) {
                registerPanel.classList.add('d-none');
                loginPanel.classList.remove('d-none');
            } else {
                loginPanel.classList.add('d-none');
                registerPanel.classList.remove('d-none');
            }
        };

        showLogin.addEventListener('click', (event) => {
            event.preventDefault();
            togglePanels(true);
        });

        showRegister.addEventListener('click', (event) => {
            event.preventDefault();
            togglePanels(false);
        });
    })();
</script>
<?php end_section('scripts'); ?>
