<?php
/**
 * Local variables.
 *
 * @var array $available_services
 */
?>

<div id="wizard-frame-1" class="wizard-frame booking-section">
    <div class="frame-container">
        <h2 class="frame-title booking-frame-title"><?= lang('service_and_provider') ?></h2>

        <div class="frame-content mt-6">
            <div class="booking-frame-content">
                <div class="mb-6">
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

                    <div id="service-card-list-label" class="mb-2 text-sm font-medium text-slate-700">
                        <strong>Select a Service</strong>
                    </div>
                    <div id="service-card-list"
                         class="grid gap-2"
                         role="radiogroup"
                         aria-labelledby="service-card-list-label">
                        <?php foreach ($grouped_services as $key => $group) { ?>
                            <?php if ($has_category && $key !== 'uncategorized' && count($group) > 0) { ?>
                                <div class="mt-3 text-sm font-medium text-slate-500">
                                    <?= e($group[0]['service_category_name']) ?>
                                </div>
                            <?php } ?>
                            <?php foreach ($group as $service) { ?>
                                <button type="button"
                                        class="booking-card service-card"
                                        data-service-id="<?= e($service['id']) ?>"
                                        role="radio"
                                        aria-checked="false">
                                    <div class="font-medium">
                                        <?= e($service['name']) ?>
                                    </div>
                                    <?php if (!empty($service['duration'])) { ?>
                                        <div class="booking-card-subtitle service-card-duration">
                                            <?= e($service['duration']) ?> <?= lang('minutes') ?>
                                        </div>
                                    <?php } ?>
                                </button>
                            <?php } ?>
                        <?php } ?>
                    </div>

                    <select id="select-service" class="sr-only" aria-hidden="true" tabindex="-1">
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

                <div id="provider-card-container" class="mb-6 hidden" aria-hidden="true">
                    <div id="provider-card-list-label" class="mb-2 text-sm font-medium text-slate-700">
                        <strong><?= lang('select_provider') ?></strong>
                    </div>
                    <div id="provider-card-list"
                         class="grid gap-2"
                         role="radiogroup"
                         aria-labelledby="provider-card-list-label"
                         aria-describedby="provider-card-list-label">
                        <!-- JS -->
                    </div>
                    <div id="service-area-zip-required"
                         class="mt-2 hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700"
                         aria-live="polite">
                        <div class="fw-medium mb-1"><?= lang('service_area_zip_required_title') ?></div>
                        <div class="mb-2"><?= lang('service_area_zip_required_body') ?></div>
                        <button type="button" id="service-area-add-zip" class="btn btn-outline-secondary btn-sm">
                            <?= lang('service_area_zip_required_action') ?>
                        </button>
                    </div>

                    <div id="service-area-no-providers"
                         class="mt-2 hidden rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"
                         aria-live="polite">
                        <div class="fw-medium mb-1"><?= lang('service_area_no_providers_title') ?></div>
                        <div class="mb-2"><?= lang('service_area_no_providers_body') ?></div>
                        <?php if (vars('customer_logged_in')): ?>
                            <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('customer/account') ?>">
                                <?= lang('service_area_no_providers_action') ?>
                            </a>
                        <?php else: ?>
                            <button type="button" id="service-area-update-address" class="btn btn-outline-secondary btn-sm">
                                <?= lang('service_area_no_providers_action') ?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <select id="select-provider" class="sr-only" aria-hidden="true" tabindex="-1">
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

    <div class="command-buttons mt-6">
        <div class="booking-frame-content">
            <button type="button" id="button-next-1" class="button-next booking-button"
                    data-step_index="1">
                <?= lang('next') ?>
                <i class="fas fa-chevron-right ml-2"></i>
            </button>
        </div>
    </div>
</div>
