/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend layout.
 *
 * This module implements the backend layout functionality.
 */
window.App.Layouts.Backend = (function () {
    const $selectLanguage = $('#select-language');
    const $notification = $('#notification');
    const $loading = $('#loading');
    const $footer = $('#footer');

    const DB_SLUG_ADMIN = 'admin';
    const DB_SLUG_PROVIDER = 'provider';
    const DB_SLUG_SECRETARY = 'secretary';
    const DB_SLUG_CUSTOMER = 'customer';

    const PRIV_VIEW = 1;
    const PRIV_ADD = 2;
    const PRIV_EDIT = 4;
    const PRIV_DELETE = 8;

    const PRIV_APPOINTMENTS = 'appointments';
    const PRIV_CUSTOMERS = 'customers';
    const PRIV_SERVICES = 'services';
    const PRIV_USERS = 'users';
    const PRIV_SYSTEM_SETTINGS = 'system_settings';
    const PRIV_USER_SETTINGS = 'user_settings';

    /**
     * Display backend notifications to user.
     *
     * Using this method you can display notifications to the use with custom messages. If the 'actions' array is
     * provided then an action link will be displayed too.
     *
     * @param {String} message Notification message
     * @param {Array} [actions] An array with custom actions that will be available to the user. Every array item is an
     * object that contains the 'label' and 'function' key values.
     */
    function displayNotification(message, actions = []) {
        if (!message) {
            return;
        }

        const $toast = $(`
            <div class="toast fixed bottom-0 end-0 m-4 flex w-full max-w-sm items-center gap-2 rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-slate-900/90 p-2 shadow-lg show backend-notification" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-body w-100 text-white">
                    ${message}
                </div>
                <button type="button" class="btn-close me-2 text-white/70 hover:text-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `).appendTo('body');

        actions.forEach(function (action) {
            $('<button/>', {
                class: 'btn btn-light btn-sm ms-2',
                text: action.label,
                on: {
                    click: action.function,
                },
            }).prependTo($toast);
        });

        const toast = new bootstrap.Toast($toast[0]);
        toast.show();

        setTimeout(() => {
            toast.dispose();
            $toast.remove();
        }, 5000);
    }

    /**
     * Initialize the module.
     */
    let sidebarTooltipInstances = [];

    function destroyTooltips(instances) {
        if (!instances || !instances.length) {
            return;
        }
        instances.forEach((instance) => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });
        instances.length = 0;
    }

    function initGlobalTooltips() {
        if (!window.tippy) {
            return;
        }

        const selector = '[data-tippy-content]:not(.backend-sidebar [data-tippy-content])';
        window.tippy(selector);
    }

    function syncSidebarTooltips() {
        if (!window.tippy) {
            return;
        }

        destroyTooltips(sidebarTooltipInstances);

        const isCollapsed = document.body.classList.contains('backend-sidebar-collapsed');
        if (!isCollapsed) {
            return;
        }

        const sidebarItems = document.querySelectorAll('.backend-sidebar [data-tippy-content]');
        if (!sidebarItems.length) {
            return;
        }

        sidebarTooltipInstances = window.tippy(sidebarItems, {
            placement: 'right',
            allowHTML: false,
        });
    }

    function enableResponsiveTables() {
        const tables = document.querySelectorAll('.backend-page table.table');

        tables.forEach((table) => {
            if (table.closest('.table-responsive')) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';

            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    }

    function initialize() {
        $(document).ajaxStart(() => {
            $loading.show();
        });

        $(document).ajaxStop(() => {
            $loading.hide();
        });

        initGlobalTooltips();
        syncSidebarTooltips();
        enableResponsiveTables();

        App.Utils.Lang.enableLanguageSelection($selectLanguage);

    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        DB_SLUG_ADMIN,
        DB_SLUG_PROVIDER,
        DB_SLUG_SECRETARY,
        DB_SLUG_CUSTOMER,
        PRIV_VIEW,
        PRIV_ADD,
        PRIV_EDIT,
        PRIV_DELETE,
        PRIV_APPOINTMENTS,
        PRIV_CUSTOMERS,
        PRIV_SERVICES,
        PRIV_USERS,
        PRIV_SYSTEM_SETTINGS,
        PRIV_USER_SETTINGS,
        displayNotification,
        syncSidebarTooltips,
    };
})();
