<?php
/**
 * Local variables.
 *
 * @var array $grouped_timezones
 */
?>

<div id="wizard-frame-2" class="wizard-frame" style="display:none;">
    <div class="frame-container">

        <h2 class="frame-title"><?= lang('appointment_date_and_time') ?></h2>

        <div class="row frame-content">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="date-picker-container mb-3">
                    <div class="date-picker">
                        <div class="months-container" id="months-container"></div>
                        <div class="dates-container" id="dates-container"></div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 bg-body rounded-3 px-4 py-2 border date-time-summary">
                    <p id="selected-date" class="mb-0 fw-semibold text-dark"></p>
                    <div id="select-time" class="d-flex align-items-center">
                        <?php component('timezone_dropdown', [
                            'attributes' => 'id="select-timezone" class="form-select form-select-sm border-0 bg-transparent shadow-none" style="width: auto;" value="UTC"',
                            'grouped_timezones' => $grouped_timezones,
                        ]); ?>
                    </div>
                </div>

                <div class="d-none">
                    <div id="select-date"></div>
                </div>

                <?php slot('after_select_date'); ?>

                <div id="available-hours-container">
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
