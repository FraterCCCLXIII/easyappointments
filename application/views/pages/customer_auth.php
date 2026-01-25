<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div class="wizard-frame">
    <div class="frame-container">
        <h2 id="customer-auth-title" class="frame-title booking-frame-title">Create an Account</h2>

        <?php if (vars('auth_error')): ?>
            <div class="alert alert-danger mb-4">
                <?= e(vars('auth_error')) ?>
            </div>
        <?php endif; ?>

        <div class="frame-content">
            <div id="customer-auth-register">
                <div class="text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="#" id="customer-auth-show-login" class="booking-link">
                        Login
                    </a>
                </div>
                <form method="post" action="<?= site_url('customer/register') ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                    <div class="mt-6 space-y-4">
                        <div>
                            <label for="customer-register-email" class="form-label">Email</label>
                            <input type="email" id="customer-register-email" name="email" class="booking-input" required>
                        </div>
                        <div>
                            <label for="customer-register-password" class="form-label">Password</label>
                            <input type="password" id="customer-register-password" name="password" class="booking-input" required>
                        </div>
                        <div>
                            <label for="customer-register-password-confirm" class="form-label">Confirm Password</label>
                            <input type="password" id="customer-register-password-confirm" name="password_confirm" class="booking-input" required>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">
                        You will complete your profile after creating your account.
                    </p>
                    <button type="submit" class="booking-button mt-4">
                        Create Account
                    </button>
                </form>
            </div>

            <div id="customer-auth-login" class="hidden">
                <div class="text-center text-sm text-slate-500">
                    Need to create an account?
                    <a href="#" id="customer-auth-show-register" class="booking-link">
                        Create Account
                    </a>
                </div>
                <form method="post" action="<?= site_url('customer/authenticate') ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(vars('csrf_token')) ?>">
                    <div class="mt-6 space-y-4">
                        <div>
                            <label for="customer-login-email" class="form-label">Email</label>
                            <input type="email" id="customer-login-email" name="email" class="booking-input" required>
                        </div>
                        <div>
                            <label for="customer-login-password" class="form-label">Password</label>
                            <input type="password" id="customer-login-password" name="password" class="booking-input" required>
                        </div>
                    </div>
                    <button type="submit" class="booking-button mt-4">
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

        const titleEl = document.getElementById('customer-auth-title');
        const togglePanels = (showLoginPanel) => {
            if (showLoginPanel) {
                registerPanel.classList.add('hidden');
                loginPanel.classList.remove('hidden');
                titleEl.textContent = 'Login';
            } else {
                loginPanel.classList.add('hidden');
                registerPanel.classList.remove('hidden');
                titleEl.textContent = 'Create an Account';
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
