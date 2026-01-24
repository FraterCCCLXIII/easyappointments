<?php
/**
 * Local variables.
 *
 * @var array $grouped_timezones
 */
?>

<div id="wizard-frame-2" class="wizard-frame" style="display:none;">
    <div class="frame-container">
        <div class="wizard-back-button-wrapper">
            <button type="button" id="button-back-2" class="btn btn-outline-secondary wizard-back-button button-back"
                    data-step_index="2" aria-label="<?= lang('back') ?>">
                <i class="fas fa-arrow-left"></i>
            </button>
        </div>

        <h2 class="frame-title"><?= lang('appointment_date_and_time') ?></h2>

        <div class="row frame-content">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="date-picker-container mb-3">
                    <div class="date-picker">
                        <div class="months-container" id="months-container"></div>
                        <div class="dates-container" id="dates-container"></div>
                    </div>
                </div>

                <p id="selected-date" class="text-center mt-3"></p>

                <div class="d-none">
                    <div id="select-date"></div>
                </div>

                <?php slot('after_select_date'); ?>

                <div id="select-time">
                    <div class="mb-3">
                        <label for="select-timezone" class="form-label">
                            <?= lang('timezone') ?>
                        </label>
                        <?php component('timezone_dropdown', [
                            'attributes' => 'id="select-timezone" class="form-select" value="UTC"',
                            'grouped_timezones' => $grouped_timezones,
                        ]); ?>
                    </div>

                    <?php slot('after_select_timezone'); ?>


                    <div id="available-hours"></div>

                    <?php slot('after_available_hours'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="command-buttons">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <button type="button" id="button-next-2" class="btn button-next btn-dark text-center w-100 py-3"
                        data-step_index="2">
                    <?= lang('next') ?>
                    <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>
