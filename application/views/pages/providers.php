<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="providers-page">
    <div class="row" id="providers">
        <div id="filter-providers" class="filter-records column col-12 col-md-5 backend-sticky-panel">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" aria-label="keyword">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="mb-4">
                <button id="add-provider" class="btn btn-primary w-100">
                    <i class="fas fa-plus-square me-2"></i>
                    <?= lang('add') ?>
                </button>
            </div>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('providers') ?>
            </h4>

            <?php slot('after_page_title'); ?>

            <div class="results">
                <!-- JS -->
            </div>
        </div>

        <div class="record-details column col-12 col-md-7">
            <div class="mb-4 d-flex w-100 justify-content-end">
                <div class="add-edit-delete-group btn-group">
                    <button id="edit-provider" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-provider" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group" style="display:none;">
                    <button id="save-provider" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-provider" class="btn btn-secondary">
                        <?= lang('cancel') ?>
                    </button>
                </div>

                <?php slot('after_page_actions'); ?>
            </div>

            <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4">
                <div class="text-lg font-semibold text-slate-900" id="provider-summary-name">
                    —
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-700">
                    <span class="inline-flex items-center gap-2" id="provider-summary-email">
                        <i class="fas fa-envelope text-slate-400"
                           style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        <span class="summary-text">—</span>
                    </span>
                    <span class="inline-flex items-center gap-2" id="provider-summary-phone">
                        <i class="fas fa-phone text-slate-400"
                           style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        <span class="summary-text">—</span>
                    </span>
                    <span class="inline-flex items-center gap-2" id="provider-summary-location">
                        <i class="fas fa-location-dot text-slate-400"
                           style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        <span class="summary-text">—</span>
                    </span>
                </div>
            </div>

            <div class="flex w-full">
                <ul class="booking-tab-line-list" id="provider-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab-line active" id="provider-details-tab" data-bs-toggle="pill"
                                data-bs-target="#details" type="button" role="tab" aria-selected="true">
                            <?= lang('details') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab-line" id="provider-working-plan-tab" data-bs-toggle="pill"
                                data-bs-target="#working-plan" type="button" role="tab" aria-selected="false"
                                tabindex="-1">
                            <?= lang('working_plan') ?>
                        </button>
                    </li>
                </ul>
            </div>

            <?php
// This form message is outside the details view, so that it can be
// visible when the user has working plan view active.
?>

            <div class="form-message alert" style="display:none;"></div>

            <div class="tab-content mt-6 w-full">
                <div class="details-view tab-pane fade show active clearfix" id="details">
                    <input type="hidden" id="id" class="record-id">

                    <div class="row">
                        <div class="details col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="first-name">
                                    <?= lang('first_name') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input id="first-name" class="form-control required" maxlength="256" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="last-name">
                                    <?= lang('last_name') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input id="last-name" class="form-control required" maxlength="512" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">
                                    <?= lang('email') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input id="email" class="form-control required" max="512" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="phone-number">
                                    <?= lang('phone_number') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input id="phone-number" class="form-control required" max="128" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="mobile-number">
                                    <?= lang('mobile_number') ?>

                                </label>
                                <input id="mobile-number" class="form-control" maxlength="128" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="address">
                                    <?= lang('address') ?>
                                </label>
                                <input id="address" class="form-control" maxlength="256" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="city">
                                    <?= lang('city') ?>

                                </label>
                                <input id="city" class="form-control" maxlength="256" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="state">
                                    <?= lang('state') ?>
                                </label>
                                <input id="state" class="form-control" maxlength="256" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="zip-code">
                                    <?= lang('zip_code') ?>

                                </label>
                                <input id="zip-code" class="form-control" maxlength="64" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="notes">
                                    <?= lang('notes') ?>
                                </label>
                                <textarea id="notes" class="form-control" rows="3" disabled></textarea>
                            </div>

                            <?php slot('after_primary_fields'); ?>
                        </div>
                        <div class="settings col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="username">
                                    <?= lang('username') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input id="username" class="form-control required" maxlength="256" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">
                                    <?= lang('password') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input type="password" id="password" class="form-control required"
                                       maxlength="512" autocomplete="new-password" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password-confirm">
                                    <?= lang('retype_password') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <input type="password" id="password-confirm"
                                       class="form-control required" maxlength="512"
                                       autocomplete="new-password" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="calendar-view">
                                    <?= lang('calendar') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <select id="calendar-view" class="form-select required" disabled>
                                    <option value="default"><?= lang('default') ?></option>
                                    <option value="table"><?= lang('table') ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="language">
                                    <?= lang('language') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <select id="language" class="form-select required" disabled>
                                    <?php foreach (vars('available_languages') as $available_language): ?>
                                        <option value="<?= $available_language ?>">
                                            <?= ucfirst($available_language) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="timezone">
                                    <?= lang('timezone') ?>
                                    <span class="text-danger" hidden>*</span>
                                </label>
                                <?php component('timezone_dropdown', [
                                    'attributes' => 'id="timezone" class="form-control required" disabled',
                                    'grouped_timezones' => vars('grouped_timezones'),
                                ]); ?>
                            </div>

                            <?php if (setting('ldap_is_active')): ?>
                                <div class="mb-3">
                                    <label for="ldap-dn" class="form-label">
                                        <?= lang('ldap_dn') ?>
                                    </label>
                                    <input type="text" id="ldap-dn" class="form-control" maxlength="100" disabled/>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="form-label mb-3">
                                    <?= lang('options') ?>
                                </label>
                            </div>

                            <div class="border rounded mb-3 p-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is-private">
                                    <label class="form-check-label" for="is-private">
                                        <?= lang('hide_from_public') ?>
                                    </label>
                                </div>

                                <div class="form-text text-muted mb-3">
                                    <small>
                                        <?= lang('private_hint') ?>
                                    </small>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notifications" disabled>
                                    <label class="form-check-label" for="notifications">
                                        <?= lang('receive_notifications') ?>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="form-label mb-3">
                                    <?= lang('services') ?>
                                </label>
                            </div>

                            <div id="provider-services" class="card card-body bg-white border">
                                <!-- JS -->
                            </div>

                            <?php slot('after_secondary_fields'); ?>
                        </div>
                    </div>
                </div>

                <div class="working-plan-view tab-pane fade clearfix" id="working-plan">
                    <h4 class="text-black-50 mb-3 fw-light">
                        <?= lang('working_plan') ?>
                    </h4>

                    <button id="reset-working-plan" class="btn btn-primary"
                            data-tippy-content="<?= lang('reset_working_plan') ?>">
                        <i class="fas fa-undo-alt me-2"></i>
                        <?= lang('reset_plan') ?></button>
                    <table class="working-plan table table-striped mt-2">
                        <thead>
                        <tr>
                            <th><?= lang('day') ?></th>
                            <th><?= lang('start') ?></th>
                            <th><?= lang('end') ?></th>
                        </tr>
                        </thead>
                        <tbody><!-- Dynamic Content --></tbody>
                    </table>

                    <?php slot('after_working_plan'); ?>

                    <br>

                    <h4 class="text-black-50 mb-3 fw-light">
                        <?= lang('breaks') ?>
                    </h4>

                    <p>
                        <?= lang('add_breaks_during_each_day') ?>
                    </p>

                    <div>
                        <button type="button" class="add-break btn btn-primary">
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('add_break') ?>
                        </button>
                    </div>

                    <br>

                    <table class="breaks table table-striped">
                        <thead>
                        <tr>
                            <th><?= lang('day') ?></th>
                            <th><?= lang('start') ?></th>
                            <th><?= lang('end') ?></th>
                            <th><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody><!-- Dynamic Content --></tbody>
                    </table>

                    <?php slot('after_breaks'); ?>

                    <br>

                    <h4 class="text-black-50 mb-3 fw-light">
                        <?= lang('working_plan_exceptions') ?>
                    </h4>

                    <p>
                        <?= lang('add_working_plan_exceptions_during_each_day') ?>
                    </p>

                    <div>
                        <button type="button" class="add-working-plan-exception btn btn-primary me-2">
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('add_working_plan_exception') ?>
                        </button>
                    </div>

                    <br>

                    <table class="working-plan-exceptions table table-striped">
                        <thead>
                        <tr>
                            <th><?= lang('day') ?></th>
                            <th><?= lang('start') ?></th>
                            <th><?= lang('end') ?></th>
                            <th><?= lang('actions') ?></th>
                        </tr>
                        </thead>
                        <tbody><!-- Dynamic Content --></tbody>
                    </table>

                    <?php component('working_plan_exceptions_modal'); ?>

                    <?php slot('after_working_plan_exceptions'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/account_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/providers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/providers.js') ?>"></script>

<?php end_section('scripts'); ?>



