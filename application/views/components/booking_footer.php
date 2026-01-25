<?php
/**
 * Local variables.
 *
 * @var bool $display_login_button
 */
?>

<div id="frame-footer" class="mt-4 text-center">
    <div class="d-flex justify-content-center gap-3">
        <?php if (vars('display_terms_and_conditions') === '1'): ?>
            <a href="#" class="text-secondary text-decoration-none small" data-bs-toggle="modal"
               data-bs-target="#terms-and-conditions-modal">
                <?= lang('terms_and_conditions') ?>
            </a>
        <?php endif; ?>

        <?php if (vars('display_privacy_policy') === '1'): ?>
            <a href="#" class="text-secondary text-decoration-none small" data-bs-toggle="modal"
               data-bs-target="#privacy-policy-modal">
                <?= lang('privacy_policy') ?>
            </a>
        <?php endif; ?>
    </div>
</div>
