/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     Easy!Appointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Forms settings list page.
 */
App.Pages.FormsSettingsList = (function () {
    const $formsList = $('#forms-list');
    const $formsSearch = $('#forms-search');

    let forms = [];

    function renderFormsList(list) {
        $formsList.empty();

        if (!list.length) {
            $formsList.append(
                $('<div/>', { class: 'text-sm text-slate-500', text: 'No forms yet.' }),
            );
            return;
        }

        list.forEach((form) => {
            const $item = $('<div/>', {
                class: 'd-flex align-items-center justify-content-between gap-2',
                html: [
                    $('<span/>', { class: 'text-slate-800', text: form.name }),
                    $('<a/>', {
                        class: 'btn btn-outline-secondary btn-sm',
                        href: App.Utils.Url.siteUrl(`forms_settings/view/${form.id}`),
                        text: 'View',
                    }),
                ],
            });

            $formsList.append($('<div/>', { class: 'border rounded-md px-3 py-2', html: $item }));
        });
    }

    function onSearch() {
        const term = $formsSearch.val().toLowerCase();
        const filtered = forms.filter((form) => form.name.toLowerCase().includes(term));
        renderFormsList(filtered);
    }

    function loadForms() {
        App.Http.Forms.list().done((response) => {
            forms = Array.isArray(response) ? response : [];
            renderFormsList(forms);
        });
    }

    function initialize() {
        loadForms();
        $formsSearch.on('input', onSearch);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
