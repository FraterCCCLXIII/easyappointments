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
 * Customer form view page.
 */
App.Pages.CustomerFormView = (function () {
    const $title = $('#customer-form-title');
    const $content = $('#customer-form-content');
    const $fields = $('#customer-form-fields');
    const $submit = $('#customer-form-submit');
    const $status = $('#customer-form-status');
    const $message = $('#customer-form-message');

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

    function renderFieldInput(field, value = '', readOnly = false) {
        const $wrapper = $('<div/>', { class: 'form-field' });
        const $label = $('<label/>', {
            class: 'form-label',
            text: field.label,
        });

        const $input = $('<input/>', {
            type: 'text',
            class: 'form-control form-response-input',
            value: value,
            disabled: readOnly,
            'data-field-id': field.id ?? field.field_id,
        });

        if (field.is_required && !readOnly) {
            $input.addClass('required');
        }

        $wrapper.append($label, $input);
        return $wrapper;
    }

    function renderField(field, value = '', readOnly = false) {
        if ((field.field_type || 'input') === 'text') {
            return renderTextBlock(field);
        }
        return renderFieldInput(field, value, readOnly);
    }

    function renderIncomplete(form) {
        $fields.empty();
        (form.fields || []).forEach((field) => {
            $fields.append(renderField(field, '', false));
        });
        $submit.prop('disabled', false).show();
        $status.text('Complete the required fields and submit.');
    }

    function renderComplete(form) {
        $fields.empty();
        (form.fields || []).forEach((field) => {
            $fields.append(renderField(field, field.value || '', true));
        });
        $submit.prop('disabled', true).hide();
        $status.text('This form has been completed and is read-only.');
    }

    function collectResponses() {
        const responses = [];
        let hasErrors = false;

        $fields.find('.form-response-input').each((_, input) => {
            const $input = $(input);
            const value = $input.val().trim();
            const fieldId = Number($input.data('field-id'));
            const required = $input.hasClass('required');

            $input.removeClass('is-invalid');

            if (required && !value) {
                $input.addClass('is-invalid');
                hasErrors = true;
            }

            responses.push({
                field_id: fieldId,
                value,
            });
        });

        if (hasErrors) {
            showMessage('Please complete all required fields.');
            return null;
        }

        return responses;
    }

    function onSubmit() {
        resetMessage();

        const responses = collectResponses();
        if (!responses) {
            return;
        }

        App.Http.CustomerForms.submit(currentForm.id, responses)
            .done(() => {
                showMessage('Form submitted successfully.', 'success');
                loadForm();
            })
            .fail((xhr) => {
                showMessage(xhr.responseJSON?.message || 'Failed to submit form.');
            });
    }

    function loadForm() {
        resetMessage();
        const formId = Number(vars('form_id'));
        if (!formId) {
            showMessage('Invalid form.');
            return;
        }

        App.Http.CustomerForms.find(formId)
            .done((form) => {
                currentForm = form;
                $title.text(form.name);
                $content.empty().hide();

                if (form.status === 'complete') {
                    renderComplete(form);
                } else {
                    renderIncomplete(form);
                }
            })
            .fail((xhr) => {
                showMessage(xhr.responseJSON?.message || 'Failed to load form.');
            });
    }

    function initialize() {
        $submit.on('click', onSubmit);
        loadForm();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
