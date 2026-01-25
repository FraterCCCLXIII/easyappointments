<?php
/**
 * Local variables.
 *
 * @var bool $manage_mode
 * @var string $display_terms_and_conditions
 * @var string $display_privacy_policy
 */
?>

<div id="wizard-frame-4" class="wizard-frame" style="display:none;">
    <div class="frame-container">
        <h2 class="frame-title"><?= lang('appointment_confirmation') ?></h2>

        <div class="row frame-content">
            <div class="col-12 col-md-8 offset-md-2">
                <div id="appointment-details" class="text-center text-md-start mb-4">
                    <!-- JS -->
                </div>

                <div id="customer-details" class="text-center text-md-start">
                    <!-- JS -->
                </div>
            </div>
        </div>

        <?php slot('after_details'); ?>

        <?php if (setting('require_captcha')): ?>
            <div class="row frame-content">
                <div class="col-12 col-md-8 offset-md-2">
                    <label class="captcha-title" for="captcha-text">
                        CAPTCHA
                        <button class="btn btn-link text-dark text-decoration-none py-0">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </label>
                    <img class="captcha-image" src="<?= site_url('captcha') ?>" alt="CAPTCHA">
                    <input id="captcha-text" class="captcha-text form-control" type="text" value=""/>
                    <span id="captcha-hint" class="help-block" style="opacity:0">&nbsp;</span>
                </div>
            </div>
        <?php endif; ?>

        <?php slot('after_captcha'); ?>
    </div>

    <div class="row frame-content fs-6">
        <div class="col-12 col-md-8 offset-md-2">
            <?php if ($display_terms_and_conditions): ?>
                <div class="form-check mb-3">
                    <input type="checkbox" class="required form-check-input" id="accept-to-terms-and-conditions">
                    <label class="form-check-label" for="accept-to-terms-and-conditions">
                        <?= strtr(lang('read_and_agree_to_terms_and_conditions'), [
                            '{$link}' => '<a href="#" data-bs-toggle="modal" data-bs-target="#terms-and-conditions-modal">',
                            '{/$link}' => '</a>',
                        ]) ?>
                    </label>
                </div>
            <?php endif; ?>

            <?php if ($display_privacy_policy): ?>
                <div class="form-check mb-3">
                    <input type="checkbox" class="required form-check-input" id="accept-to-privacy-policy">
                    <label class="form-check-label" for="accept-to-privacy-policy">
                        <?= strtr(lang('read_and_agree_to_privacy_policy'), [
                            '{$link}' => '<a href="#" data-bs-toggle="modal" data-bs-target="#privacy-policy-modal">',
                            '{/$link}' => '</a>',
                        ]) ?>
                    </label>
                </div>
            <?php endif; ?>

            <?php slot('after_select_policies'); ?>
        </div>
    </div>

    <div class="command-buttons">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <form id="book-appointment-form" class="w-100" method="post">
                    <button id="book-appointment-submit" type="button" class="btn btn-primary w-100 py-3">
                        <i class="fas fa-check-square me-2"></i>
                        <?= $manage_mode ? lang('update') : lang('confirm') ?>
                    </button>
                    <input type="hidden" name="csrfToken"/>
                    <input type="hidden" name="post_data"/>
                </form>
            </div>
        </div>
    </div>
</div>
