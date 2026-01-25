<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="customers-page">
    <div class="row" id="customers">
        <div id="filter-customers" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group mb-3">
                    <input type="text" class="key form-control" aria-label="keyword">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <?php if (
                can('add', PRIV_CUSTOMERS) &&
                (!setting('limit_customer_access') || vars('role_slug') === DB_SLUG_ADMIN)
            ): ?>
                <div class="mb-4">
                    <button id="add-customer" class="btn btn-primary w-100">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                </div>
            <?php endif; ?>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('customers') ?>
            </h4>

            <?php slot('after_page_title'); ?>

            <div class="results">
                <!-- JS -->
            </div>
        </div>

        <div class="record-details col-12 col-md-7">
            <div class="mb-4 d-flex w-100 justify-content-end">
                <div id="add-edit-delete-group" class="btn-group">
                    <?php if (can('edit', PRIV_CUSTOMERS)): ?>
                        <button id="edit-customer" class="btn btn-outline-secondary" disabled="disabled">
                            <i class="fas fa-edit me-2"></i>
                            <?= lang('edit') ?>
                        </button>
                    <?php endif; ?>

                    <?php if (can('delete', PRIV_CUSTOMERS)): ?>
                        <button id="delete-customer" class="btn btn-outline-secondary" disabled="disabled">
                            <i class="fas fa-trash-alt me-2"></i>
                            <?= lang('delete') ?>
                        </button>
                    <?php endif; ?>
                </div>

                <div id="save-cancel-group" style="display:none;">
                    <button id="save-customer" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-customer" class="btn btn-secondary">
                        <?= lang('cancel') ?>
                    </button>
                </div>

                <?php slot('after_page_actions'); ?>
            </div>

            <input id="customer-id" type="hidden">

            <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <?= lang('details') ?>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-slate-700">
                    <span class="rounded-full bg-slate-100 px-3 py-1" id="customer-summary-first-name">
                        <?= lang('first_name') ?>: —
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1" id="customer-summary-last-name">
                        <?= lang('last_name') ?>: —
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1" id="customer-summary-email">
                        <?= lang('email') ?>: —
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1" id="customer-summary-phone">
                        <?= lang('phone_number') ?>: —
                    </span>
                    <span class="rounded-full bg-slate-100 px-3 py-1" id="customer-summary-location">
                        <?= lang('location') ?>: —
                    </span>
                </div>
            </div>

            <div class="flex justify-center">
                <ul class="booking-tab-list" id="customer-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab active" id="customer-appointments-tab" data-bs-toggle="pill"
                                data-bs-target="#customer-appointments-panel" type="button" role="tab"
                                aria-selected="true">
                            <?= lang('appointments') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab" id="customer-billing-tab" data-bs-toggle="pill"
                                data-bs-target="#customer-billing-panel" type="button" role="tab"
                                aria-selected="false" tabindex="-1">
                            <?= lang('billing_history') ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="booking-tab" id="customer-account-tab" data-bs-toggle="pill"
                                data-bs-target="#customer-account-panel" type="button" role="tab"
                                aria-selected="false" tabindex="-1">
                            <?= lang('account') ?>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content mt-6" id="customer-tabs-content">
                <div class="tab-pane fade show active" id="customer-appointments-panel" role="tabpanel"
                     aria-labelledby="customer-appointments-tab">
                    <div id="customer-appointments" class="card bg-white border"></div>
                </div>
                <div class="tab-pane fade" id="customer-billing-panel" role="tabpanel"
                     aria-labelledby="customer-billing-tab">
                    <div id="customer-billing" class="card bg-white border">
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3"><?= lang('date') ?></th>
                                        <th><?= lang('amount') ?></th>
                                        <th class="pe-3"><?= lang('status') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="billing-history-body">
                                    <!-- JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="customer-account-panel" role="tabpanel"
                     aria-labelledby="customer-account-tab">
                    <h4 class="text-black-50 mb-3 fw-light">
                        <?= lang('details') ?>
                    </h4>

                    <div id="form-message" class="alert" style="display:none;"></div>

                    <div class="row">
                        <div class="col-12 col-md-6" style="margin-left: 0;">
                            <div class="mb-3">
                                <label for="first-name" class="form-label">
                                    <?= lang('first_name') ?>
                                    <?php if (vars('require_first_name')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="first-name"
                                       class="<?= vars('require_first_name') ? 'required' : '' ?> form-control"
                                       maxlength="100" disabled/>
                            </div>

                            <div class="mb-3">
                                <label for="last-name" class="form-label">
                                    <?= lang('last_name') ?>
                                    <?php if (vars('require_last_name')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="last-name"
                                       class="<?= vars('require_last_name') ? 'required' : '' ?> form-control"
                                       maxlength="120" disabled/>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <?= lang('email') ?>
                                    <?php if (vars('require_email')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="email"
                                       class="<?= vars('require_email') ? 'required' : '' ?> form-control"
                                       maxlength="120" disabled/>
                            </div>

                            <div class="mb-3">
                                <label for="phone-number" class="form-label">
                                    <?= lang('phone_number') ?>
                                    <?php if (vars('require_phone_number')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="phone-number" maxlength="60"
                                       class="<?= vars('require_phone_number') ? 'required' : '' ?> form-control"
                                       disabled/>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <?= lang('address') ?>
                                    <?php if (vars('require_address')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="address"
                                       class="<?= vars('require_address') ? 'required' : '' ?> form-control"
                                       maxlength="120" disabled/>
                            </div>

                            <div class="mb-3">
                                <label for="city" class="form-label">
                                    <?= lang('city') ?>
                                    <?php if (vars('require_city')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="city"
                                       class="<?= vars('require_city') ? 'required' : '' ?> form-control"
                                       maxlength="120" disabled/>
                            </div>

                            <div class="mb-3">
                                <label for="zip-code" class="form-label">
                                    <?= lang('zip_code') ?>
                                    <?php if (vars('require_zip_code')): ?>
                                        <span class="text-danger" hidden>*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="text" id="zip-code"
                                       class="<?= vars('require_zip_code') ? 'required' : '' ?> form-control"
                                       maxlength="120" disabled/>
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

                            <?php component('custom_fields', [
                                'disabled' => true,
                            ]); ?>

                            <div class="mb-3">
                                <label class="form-label" for="notes">
                                    <?= lang('notes') ?>
                                </label>
                                <textarea id="notes" rows="4" class="form-control" disabled></textarea>
                            </div>

                            <?php slot('after_primary_fields'); ?>
                        </div>
                    </div>

                    <?php slot('after_secondary_fields'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/customers.js') ?>"></script>

<?php end_section('scripts'); ?>
