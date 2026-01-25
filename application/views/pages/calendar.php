<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="calendar-page">
    <div class="row g-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm" id="calendar-toolbar">
        <div id="calendar-filter" class="col-12 col-md-4 col-xl-3">
            <div class="calendar-filter-items">
                <select id="select-filter-item"
                        class="form-select w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-slate-300 focus:outline-none"
                        data-tippy-content="<?= lang('select_filter_item_hint') ?>"
                        aria-label="Filter">
                    <!-- JS -->
                </select>
            </div>
        </div>

        <div id="calendar-actions" class="col-12 col-md-8 col-xl-9 d-flex flex-wrap items-center justify-content-md-end gap-3">
            <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
                <?php if (vars('calendar_view') === CALENDAR_VIEW_DEFAULT): ?>
                    <button
                        id="enable-sync"
                        class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900"
                        data-tippy-content="<?= lang('enable_appointment_sync_hint') ?>"
                        hidden>
                        <i class="fas fa-rotate mr-2" style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        <?= lang('enable_sync') ?>
                    </button>

                    <div class="btn-group" id="sync-button-group" hidden>
                        <button type="button"
                                class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900"
                                id="trigger-sync"
                                data-tippy-content="<?= lang('trigger_sync_hint') ?>">
                            <i class="fas fa-rotate mr-2" style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            <?= lang('synchronize') ?>
                        </button>
                        <button type="button"
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-700 transition hover:border-slate-300 hover:text-slate-900 dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">
                                Toggle Dropdown
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                            <li>
                                <a class="dropdown-item block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                   href="#"
                                   id="disable-sync">
                                    <?= lang('disable_sync') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (can('add', PRIV_APPOINTMENTS)): ?>
                    <div class="dropdown d-sm-inline-block">
                        <button class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-label="<?= lang('add') ?>">
                            <i class="fas fa-plus-square" style="width: 16px; height: 16px; font-size: 16px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        </button>
                        <ul class="dropdown-menu mt-2 w-64 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                            <li>
                                <a class="dropdown-item block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                   href="#"
                                   id="insert-appointment">
                                    <?= lang('appointment') ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                   href="#"
                                   id="insert-unavailability">
                                    <?= lang('unavailability') ?>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                   href="#"
                                   id="insert-working-plan-exception" <?= session('role_slug') !== DB_SLUG_ADMIN
                                       ? 'hidden'
                                       : '' ?>>
                                    <?= lang('working_plan_exception') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="dropdown d-sm-inline-block">
                    <button class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:text-slate-900"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-label="More options">
                        <i class="fas fa-ellipsis-vertical" style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                        <li>
                            <button id="reload-appointments"
                                    type="button"
                                    class="dropdown-item block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                    data-tippy-content="<?= lang('reload_appointments_hint') ?>">
                                Reload
                            </button>
                        </li>
                        <?php if (vars('calendar_view') === CALENDAR_VIEW_DEFAULT): ?>
                            <li>
                                <a class="dropdown-item block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                   href="<?= site_url('calendar?view=table') ?>">
                                    <?= lang('table') ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (vars('calendar_view') === CALENDAR_VIEW_TABLE): ?>
                            <li>
                                <a class="dropdown-item block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                                   href="<?= site_url('calendar?view=default') ?>">
                                    <?= lang('default') ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <?php slot('after_calendar_actions'); ?>
        </div>
    </div>

    <div id="calendar">
        <!-- Dynamically Generated Content -->
    </div>
</div>

<!-- Page Components -->

<?php component('appointments_modal', [
    'available_services' => vars('available_services'),
    'appointment_status_options' => vars('appointment_status_options'),
    'timezones' => vars('timezones'),
    'require_first_name' => vars('require_first_name'),
    'require_last_name' => vars('require_last_name'),
    'require_email' => vars('require_email'),
    'require_phone_number' => vars('require_phone_number'),
    'require_address' => vars('require_address'),
    'require_city' => vars('require_city'),
    'require_zip_code' => vars('require_zip_code'),
    'require_notes' => vars('require_notes'),
]); ?>

<?php component('unavailabilities_modal', [
    'timezones' => vars('timezones'),
    'timezone' => vars('timezone'),
]); ?>

<?php component('working_plan_exceptions_modal'); ?>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/vendor/fullcalendar/index.global.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/fullcalendar-moment/index.global.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_default_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_table_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_event_popover.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/calendar_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<?php if (vars('calendar_view') === CALENDAR_VIEW_DEFAULT): ?>
    <script src="<?= asset_url('assets/js/utils/calendar_sync.js') ?>"></script>
    <script src="<?= asset_url('assets/js/http/google_http_client.js') ?>"></script>
    <script src="<?= asset_url('assets/js/http/caldav_http_client.js') ?>"></script>
<?php endif; ?>
<script src="<?= asset_url('assets/js/pages/calendar.js') ?>"></script>

<?php end_section('scripts'); ?>

