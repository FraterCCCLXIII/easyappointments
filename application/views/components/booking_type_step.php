<?php
/**
 * Local variables.
 *
 * @var array $available_services
 */
?>

<div id="wizard-frame-1" class="wizard-frame" style="visibility: hidden;">
    <div class="frame-container">
        <h2 class="frame-title mt-md-5"><?= lang('service_and_provider') ?></h2>

        <div class="row frame-content">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="mb-3">
                    <?php
                    // Group services by category, only if there is at least one service with a parent category.
                    $has_category = false;
                    foreach ($available_services as $service) {
                        if (!empty($service['service_category_id'])) {
                            $has_category = true;
                            break;
                        }
                    }

                    if ($has_category) {
                        $grouped_services = [];

                        foreach ($available_services as $service) {
                            if (!empty($service['service_category_id'])) {
                                if (!isset($grouped_services[$service['service_category_name']])) {
                                    $grouped_services[$service['service_category_name']] = [];
                                }

                                $grouped_services[$service['service_category_name']][] = $service;
                            }
                        }

                        // We need the uncategorized services at the end of the list, so we will use another
                        // iteration only for the uncategorized services.
                        $grouped_services['uncategorized'] = [];
                        foreach ($available_services as $service) {
                            if ($service['service_category_id'] == null) {
                                $grouped_services['uncategorized'][] = $service;
                            }
                        }
                    } else {
                        $grouped_services = ['uncategorized' => $available_services];
                    }
                    ?>

                    <div id="service-card-list-label" class="form-label mb-2">
                        <strong>Select a Service</strong>
                    </div>
                    <div id="service-card-list"
                         class="d-grid gap-2"
                         role="radiogroup"
                         aria-labelledby="service-card-list-label">
                        <?php foreach ($grouped_services as $key => $group) { ?>
                            <?php if ($has_category && $key !== 'uncategorized' && count($group) > 0) { ?>
                                <div class="text-muted fw-semibold mt-2">
                                    <?= e($group[0]['service_category_name']) ?>
                                </div>
                            <?php } ?>
                            <?php foreach ($group as $service) { ?>
                                <button type="button"
                                        class="btn btn-outline-dark text-start w-100 py-3 service-card"
                                        data-service-id="<?= e($service['id']) ?>"
                                        role="radio"
                                        aria-checked="false">
                                    <div class="fw-semibold">
                                        <?= e($service['name']) ?>
                                    </div>
                                    <?php if (!empty($service['duration'])) { ?>
                                        <div class="text-muted small service-card-duration">
                                            <?= e($service['duration']) ?> <?= lang('minutes') ?>
                                        </div>
                                    <?php } ?>
                                </button>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <select id="select-service" class="form-select visually-hidden" aria-hidden="true" tabindex="-1">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                        <?php if ($has_category) { ?>
                            <?php foreach ($grouped_services as $key => $group) { ?>
                                <?php
                                $group_label = $key !== 'uncategorized' ? $group[0]['service_category_name'] : 'Uncategorized';
                                if (count($group) > 0) { ?>
                                    <optgroup label="<?= e($group_label) ?>">
                                        <?php foreach ($group as $service) { ?>
                                            <option value="<?= e($service['id']) ?>">
                                                <?= e($service['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?php foreach ($available_services as $service) { ?>
                                <option value="<?= e($service['id']) ?>"><?= e($service['name']) ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>

                <?php slot('after_select_service'); ?>

                <div class="mb-3" hidden>
                    <div id="provider-card-list-label" class="form-label mb-2">
                        <strong><?= lang('provider') ?></strong>
                    </div>
                    <div class="text-muted small mb-2" id="provider-card-list-help">
                        <?= lang('please_select') ?>
                    </div>

                    <div id="provider-card-list"
                         class="d-grid gap-2"
                         role="radiogroup"
                         aria-labelledby="provider-card-list-label"
                         aria-describedby="provider-card-list-help">
                        <!-- JS -->
                    </div>

                    <select id="select-provider" class="form-select visually-hidden" aria-hidden="true" tabindex="-1">
                        <option value="">
                            <?= lang('please_select') ?>
                        </option>
                    </select>
                </div>

                <?php slot('after_select_provider'); ?>

                <div id="service-description" class="small">
                    <!-- JS -->
                </div>

                <?php slot('after_service_description'); ?>

            </div>
        </div>
    </div>

    <div class="command-buttons">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <button type="button" id="button-next-1" class="btn button-next btn-dark text-center w-100 py-3"
                        data-step_index="1">
                    <?= lang('next') ?>
                    <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>
