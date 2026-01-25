<?php
/**
 * Local variables.
 *
 * @var string $active_menu
 * @var string $company_logo
 */
?>

<?php
$nav_link_base = 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition';
$nav_link_inactive = $nav_link_base . ' text-slate-600 hover:bg-slate-100 hover:text-slate-900';
$nav_link_active = $nav_link_base . ' bg-slate-100 text-slate-900';
$dropdown_item_class = 'block rounded-lg px-3 py-2 text-sm normal-case text-slate-600 hover:bg-slate-100 hover:text-slate-900';
$dropdown_menu_class = 'dropdown-menu dropdown-menu-end absolute right-0 top-full z-50 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg';
?>

<nav id="header" class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="mx-auto w-full px-4 py-3 backend-header-grid">
        <a id="header-logo" href="<?= site_url('dashboard') ?>" class="flex items-center text-slate-900">
            <svg role="img" aria-label="<?= e(setting('company_name')) ?>" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 176.86 49.47" style="height: 32px;">
                <defs>
                    <style>
                        .cls-1 { fill: #024225; }
                    </style>
                </defs>
                <path class="cls-1"
                      d="M13.54,35.5l-5.51-.19c-3.63-.1-3.63-1.3-3.63-1.75,0-1.25,1.03-2.23,3.08-2.91.96.35,2.02.54,3.08.54,4.87,0,8.98-3.89,8.98-8.5,0-1.77-.59-3.49-1.67-4.94l3.9.07h.74v-3.95l-.63-.32-5.62,2.55c-1.59-1.27-3.61-1.97-5.74-1.97-4.84,0-8.93,3.91-8.93,8.55,0,2.7,1.51,5.33,3.87,6.91-2.78.94-4.39,2.7-4.39,4.87,0,1.16.45,2.59,2.37,3.45-1.84.99-3.42,2.89-3.42,5.26,0,3.82,4.16,6.29,10.6,6.29,7.17,0,11.8-2.98,11.8-7.59,0-3.88-3.23-6.21-8.86-6.39ZM10.56,29.22c-3.57,0-5.2-3.39-5.2-6.54s1.35-6.59,5.15-6.59,5.25,3.4,5.25,6.59c0,3.02-1.36,6.54-5.2,6.54ZM5.8,38.52c.48.09,1.03.13,1.63.13h0l5.31.1c6.51.2,6.51,3.06,6.51,4,0,3.03-2.85,4.77-7.83,4.77-2.38,0-7.93-.48-7.93-4.96,0-2.05,1.18-3.65,2.31-4.03Z"/>
                <path class="cls-1"
                      d="M33.74,14.14c-6.8,0-12.33,5.68-12.33,12.66s5.53,12.62,12.33,12.62,12.33-5.66,12.33-12.62-5.53-12.66-12.33-12.66ZM33.74,16.09c6.1,0,8.26,5.77,8.26,10.71,0,7.86-4.27,10.66-8.26,10.66s-8.26-2.8-8.26-10.66c0-4.94,2.16-10.71,8.26-10.71Z"/>
                <path class="cls-1"
                      d="M59.69,14.14c-6.8,0-12.33,5.68-12.33,12.66s5.53,12.62,12.33,12.62,12.33-5.66,12.33-12.62-5.53-12.66-12.33-12.66ZM59.69,16.09c6.1,0,8.26,5.77,8.26,10.71,0,7.86-4.27,10.66-8.26,10.66s-8.26-2.8-8.26-10.66c0-4.94,2.16-10.71,8.26-10.71Z"/>
                <path class="cls-1"
                      d="M95.15,33.03V0l-5.6,2.5v.78l.29.16c1.26.69,1.82,1.45,1.82,2.49v9.17c-1.43-.62-3.17-.96-5.01-.96-8.2,0-13.1,6.85-13.1,13.48,0,7.17,3.99,11.8,10.17,11.8,4.28,0,6.79-2.05,8.17-3.85v3.92l5.36-1.81v-.86l-.35-.14c-1.24-.49-1.76-.69-1.76-3.66ZM77.43,26.75c0-6.18,3.6-10.66,8.55-10.66s5.68,3.86,5.68,6.16v10.66c-1.56,2.4-3.94,3.78-6.54,3.78-4.67,0-7.69-3.9-7.69-9.94Z"/>
                <path class="cls-1"
                      d="M119.14,32.4v-10.73c0-4.79-2.64-7.54-7.25-7.54-3.08,0-5.98,1.24-8.26,3.52v-4.16l-5.41,3.12v.71l.25.16c1.25.79,1.85,1.82,1.85,3.13v11.83c0,2.47-.14,3.83-.98,5.34l-.07.12v.79h5.6v-.8l-.08-.13c-.93-1.5-.97-2.87-.97-5.36v-12.47c1.71-1.78,4.32-2.83,7.02-2.83,3.24,0,4.82,1.67,4.82,5.1v10.25c0,2.37-.15,3.85-.98,5.34l-.07.12v.79h5.6v-.8l-.08-.13c-.93-1.51-.97-2.8-.97-5.36Z"/>
                <path class="cls-1"
                      d="M140.3,32.46c-1.61,2.7-4.89,3.66-7.43,3.66-4.99,0-7.98-3.75-7.98-10.04,0-.58.03-1.14.08-1.66h16.4l.04-.51c.19-2.62-.69-5.18-2.4-7.02-1.66-1.78-4.01-2.77-6.63-2.77-6.4,0-11.23,5.59-11.23,13s4.36,12.28,10.84,12.28c4.56,0,8.21-2.46,9.52-6.41l.16-.49-1.15-.46-.24.4ZM125.25,22.6c.9-4.14,3.42-6.5,7-6.5,3.34,0,4.99,2.04,5.05,6.24l-12.05.26Z"/>
                <path class="cls-1"
                      d="M152.13,24.52l-.3-.11c-3.47-1.34-5.43-2.21-5.43-4.64,0-2.19,1.84-3.67,4.58-3.67,1.82,0,3.76.49,5.6,4.06l.2.39,1.27-.32-.57-5.07-.32-.12c-1.52-.56-3.52-.9-5.36-.9-4.09,0-8.45,1.72-8.45,6.53s4.16,6.21,7.2,7.36c3.33,1.26,5.68,2.36,5.68,5.09s-2.06,4.34-5.25,4.34-5.09-1.55-6.58-5.17l-.18-.44-1.3.33.56,5.66.28.13c1.29.62,3.37,1.44,6.56,1.44,5.62,0,8.98-2.66,8.98-7.11,0-5.03-4.13-6.63-7.15-7.79Z"/>
                <path class="cls-1"
                      d="M169.71,24.52l-.3-.11c-3.47-1.34-5.43-2.21-5.43-4.64,0-2.19,1.84-3.67,4.58-3.67,1.82,0,3.76.49,5.6,4.06l.2 .39,1.27-.32-.57-5.07-.32-.12c-1.52-.56-3.52-.9-5.36-.9-4.09,0-8.45,1.72-8.45,6.53s4.16,6.21,7.2,7.36c3.33,1.26,5.68,2.36,5.68,5.09s-2.06,4.34-5.25,4.34-5.09-1.55-6.58-5.17l-.18-.44-1.3.33.56,5.66.28.13c1.29.62,3.37,1.44,6.56,1.44,5.62,0,8.98-2.66,8.98-7.11,0-5.03-4.13-6.63-7.15-7.79Z"/>
            </svg>
        </a>

        <div id="header-menu" class="flex justify-center header-menu">
            <ul class="flex items-center gap-1">
                    <?php $hidden = can('view', PRIV_APPOINTMENTS) ? '' : 'hidden'; ?>
                    <?php $active = $active_menu == PRIV_APPOINTMENTS; ?>
                    <li class="<?= $hidden ?>">
                        <a href="<?= site_url(
                            'calendar' . (vars('calendar_view') === CALENDAR_VIEW_TABLE ? '?view=table' : ''),
                        ) ?>"
                           class="<?= $active ? $nav_link_active : $nav_link_inactive ?>"
                           data-tippy-content="<?= lang('manage_appointment_record_hint') ?>">
                            <i class="fas fa-calendar-alt mr-2"
                               style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            <?= lang('calendar') ?>
                        </a>
                    </li>

                    <?php $hidden = can('view', PRIV_CUSTOMERS) ? '' : 'hidden'; ?>
                    <?php $active = $active_menu == PRIV_CUSTOMERS; ?>
                    <li class="<?= $hidden ?>">
                        <a href="<?= site_url('customers') ?>" class="<?= $active ? $nav_link_active : $nav_link_inactive ?>"
                           data-tippy-content="<?= lang('manage_customers_hint') ?>">
                            <i class="fas fa-user-friends mr-2"
                               style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            <?= lang('customers') ?>
                        </a>
                    </li>

                    <?php $hidden = can('view', PRIV_SYSTEM_SETTINGS) ? '' : 'hidden'; ?>
                    <?php $active = $active_menu == 'billing'; ?>
                    <li class="<?= $hidden ?>">
                        <a href="<?= site_url('billing') ?>" class="<?= $active ? $nav_link_active : $nav_link_inactive ?>"
                           data-tippy-content="View all billing transactions">
                            <i class="fas fa-file-invoice-dollar mr-2"
                               style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            Billing
                        </a>
                    </li>

                    <?php $hidden = can('view', PRIV_SERVICES) ? '' : 'hidden'; ?>
                    <?php $active = $active_menu == PRIV_SERVICES; ?>
                    <li class="dropdown relative <?= $hidden ?>">
                        <button class="<?= $active ? $nav_link_active : $nav_link_inactive ?> gap-2"
                                type="button"
                                data-bs-toggle="dropdown"
                           data-tippy-content="<?= lang('manage_services_hint') ?>">
                            <i class="fas fa-business-time"
                               style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            <?= lang('services') ?>
                            <i class="fas fa-chevron-down"
                               style="width: 10px; height: 10px; font-size: 10px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        </button>
                        <div class="<?= $dropdown_menu_class ?>">
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('services') ?>">
                                <?= lang('services') ?>
                            </a>
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('service_categories') ?>">
                                <?= lang('categories') ?>
                            </a>
                        </div>
                    </li>

                    <?php $hidden = can('view', PRIV_USERS) ? '' : 'hidden'; ?>
                    <?php $active = $active_menu == PRIV_USERS; ?>
                    <li class="dropdown relative <?= $hidden ?>">
                        <button class="<?= $active ? $nav_link_active : $nav_link_inactive ?> gap-2"
                                type="button"
                                data-bs-toggle="dropdown"
                           data-tippy-content="<?= lang('manage_users_hint') ?>">
                            <i class="fas fa-users"
                               style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            <?= lang('users') ?>
                            <i class="fas fa-chevron-down"
                               style="width: 10px; height: 10px; font-size: 10px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        </button>
                        <div class="<?= $dropdown_menu_class ?>">
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('providers') ?>">
                                <?= lang('providers') ?>
                            </a>
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('secretaries') ?>">
                                <?= lang('secretaries') ?>
                            </a>
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('admins') ?>">
                                <?= lang('admins') ?>
                            </a>
                        </div>
                    </li>
            </ul>
        </div>

        <div class="flex justify-end header-account">
            <ul class="flex items-center gap-1">
                    <?php slot('before_user_nav_item'); ?>

                    <?php $hidden = can('view', PRIV_SYSTEM_SETTINGS) || can('view', PRIV_USER_SETTINGS) ? '' : 'hidden'; ?>
                    <?php $active = $active_menu == PRIV_SYSTEM_SETTINGS; ?>
                    <li class="dropdown relative <?= $hidden ?>">
                        <button class="<?= $active ? $nav_link_active : $nav_link_inactive ?> gap-2"
                                type="button"
                                data-bs-toggle="dropdown"
                           data-tippy-content="<?= lang('settings_hint') ?>">
                            <i class="fas fa-user"
                               style="width: 14px; height: 14px; font-size: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                            <?= e(vars('user_display_name')) ?>
                            <i class="fas fa-chevron-down"
                               style="width: 10px; height: 10px; font-size: 10px; display: inline-flex; align-items: center; justify-content: center;"></i>
                        </button>
                        <div class="<?= $dropdown_menu_class ?>">
                            <?php if (can('view', PRIV_SYSTEM_SETTINGS)): ?>
                                <a class="<?= $dropdown_item_class ?>" href="<?= site_url('general_settings') ?>">
                                    <?= lang('settings') ?>
                                </a>
                            <?php endif; ?>

                            <?php slot('after_settings_dropdown_item'); ?>

                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('account') ?>">
                                <?= lang('account') ?>
                            </a>
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('about') ?>">
                                <?= lang('about') ?>
                            </a>
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('appointments') ?>">
                                <?= lang('go_to_booking_page') ?>
                            </a>
                            <div class="px-3 py-2">
                                <small class="mb-2 block text-xs text-slate-500">Language</small>
                                <button id="select-language" type="button"
                                        class="inline-flex w-full items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:border-slate-300 hover:text-slate-900">
                                    <?= ucfirst(config('language')) ?>
                                    <i class="fas fa-chevron-down"
                                       style="width: 10px; height: 10px; font-size: 10px; display: inline-flex; align-items: center; justify-content: center;"></i>
                                </button>
                            </div>
                            <div class="my-2 border-t border-slate-200"></div>
                            <a class="<?= $dropdown_item_class ?>" href="<?= site_url('logout') ?>">
                                <?= lang('log_out') ?>
                            </a>
                        </div>
                    </li>
            </ul>
        </div>
    </div>
</nav>

<div id="notification" style="display: none;"></div>

<div id="loading" style="display: none;">
    <div class="any-element animation is-loading">
        &nbsp;
    </div>
</div>
