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
 * Forms user view page.
 */
App.Pages.FormsUserView = (function () {
    const $title = $('#forms-user-title');
    const $content = $('#forms-user-content');
    const $fields = $('#forms-user-fields');
    const $status = $('#forms-user-status');
    const $message = $('#forms-user-message');
    const $reset = $('#forms-user-reset');

    let currentForm = null;

    function showMessage(text, type = 'danger') {
        $message.removeClass('d-none').removeClass('alert-danger alert-success');
        $message.addClass(`alert-${type}`).text(text);
    }

    function resetMessage() {
        $message.addClass('d-none').removeClass('alert-danger alert-success').text('');
    }

    function renderTextBlock(field) {
        return $('<div/>', {
            class: 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-slate-50/60 p-3 text-sm text-slate-600',
            html: field.label || '',
        });
    }

    function renderField(field, value = '') {
        if ((field.field_type || 'input') === 'text') {
            return renderTextBlock(field);
        }
        const $wrapper = $('<div/>', { class: 'form-field' });
        const $label = $('<label/>', { class: 'form-label', text: field.label });
        const $input = $('<input/>', {
            type: 'text',
            class: 'form-control',
            value,
            disabled: true,
        });
        $wrapper.append($label, $input);
        return $wrapper;
    }

    function renderForm(form) {
        $title.text(form.name);
        $content.empty().hide();
        $fields.empty();

        (form.fields || []).forEach((field) => {
            $fields.append(renderField(field, field.value || ''));
        });

        if (form.status === 'complete') {
            $status.text('This form has been completed.');
        } else {
            $status.text('This form has not been completed yet.');
        }

        const canReset = Number(vars('can_reset_form')) === 1;
        $reset.toggleClass('d-none', !(canReset && form.status === 'complete'));
    }

    function loadForm() {
        resetMessage();
        const formId = Number(vars('form_id'));
        const userId = Number(vars('user_id'));
        const userType = vars('user_type');

        if (!formId || !userId || !userType) {
            showMessage('Invalid form.');
            return;
        }

        App.Http.Forms.findForRecord(formId, userId, userType)
            .done((form) => {
                currentForm = form;
                renderForm(form);
            })
            .fail((xhr) => {
                showMessage(xhr.responseJSON?.message || 'Failed to load form.');
            });
    }

    function onReset() {
        if (!currentForm) {
            return;
        }

        if (!window.confirm('Reset this form submission?')) {
            return;
        }

        App.Http.Forms.resetSubmission(currentForm.id, Number(vars('user_id')), vars('user_type'))
            .done(() => {
                showMessage('Form reset.', 'success');
                loadForm();
            })
            .fail((xhr) => {
                showMessage(xhr.responseJSON?.message || 'Failed to reset form.');
            });
    }

    function initialize() {
        $reset.on('click', onReset);
        loadForm();
    }

    $(document).ready(initialize);

    return {};
})();
