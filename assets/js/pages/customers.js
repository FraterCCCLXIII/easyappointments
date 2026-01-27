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
 * Customers page.
 *
 * This module implements the functionality of the customers page.
 */
App.Pages.Customers = (function () {
    const $customers = $('#customers');
    const $filterCustomers = $('#filter-customers');
    const $id = $('#customer-record-id');
    const $firstName = $('#first-name');
    const $lastName = $('#last-name');
    const $email = $('#email');
    const $phoneNumber = $('#phone-number');
    const $address = $('#address');
    const $city = $('#city');
    const $zipCode = $('#zip-code');
    const $timezone = $('#timezone');
    const $language = $('#language');
    const $ldapDn = $('#ldap-dn');
    const $customFieldInputs = $('.custom-field-input');
    const $notes = $('#notes');
    const $formMessage = $('#form-message');
    const $customerAppointments = $('#customer-appointments');
    const $customerAppointmentsList = $('#customer-appointments-list');
    const $customerAppointmentDetails = $('#customer-appointment-details');
    const $customerAppointmentBack = $('#customer-appointment-back');
    const $customerAppointmentEditLink = $('#customer-appointment-edit-link');
    const $customerAppointmentId = $('#customer-appointment-id');
    const $customerAppointmentHash = $('#customer-appointment-hash');
    const $customerAppointmentService = $('#customer-appointment-service');
    const $customerAppointmentDate = $('#customer-appointment-date');
    const $customerAppointmentTime = $('#customer-appointment-time');
    const $customerAppointmentDuration = $('#customer-appointment-duration');
    const $customerAppointmentStatus = $('#customer-appointment-status');
    const $customerAppointmentProvider = $('#customer-appointment-provider');
    const $customerAppointmentCustomer = $('#customer-appointment-customer');
    const $customerAppointmentAddress = $('#customer-appointment-address');
    const $customerAppointmentPaymentStatus = $('#customer-appointment-payment-status');
    const $customerAppointmentPaymentAmount = $('#customer-appointment-payment-amount');
    const $customerAppointmentNotes = $('#customer-appointment-notes');
    const $customerAppointmentSaveNotes = $('#customer-appointment-save-notes');
    const $customerAppointmentNotesList = $('#customer-appointment-notes-list');
    const $billingHistoryBody = $('#billing-history-body');
    const $customerNoteText = $('#customer-note-text');
    const $customerNotesList = $('#customer-notes-list');
    const $customerVisitNotesList = $('#customer-visit-notes-list');
    const $customerFilesPanel = $('#customer-files-panel .user-files-panel');
    const $customerFormsPanel = $('#customer-forms-panel .user-forms-panel');
    const $addCustomerNote = $('#add-customer-note');
    const $summaryName = $('#customer-summary-name');
    const $summaryId = $('#customer-summary-id');
    const $summaryEmail = $('#customer-summary-email');
    const $summaryPhone = $('#customer-summary-phone');
    const $summaryLocation = $('#customer-summary-location');

    const moment = window.moment;

    let filterResults = {};
    let filterLimit = 20;
    let pendingSlug = null;
    let userFilesManager;
    let userFormsManager;

    function getCustomFieldValues($container = $customers) {
        const values = {};
        $container.find('.custom-field-input').each((index, input) => {
            const $input = $(input);
            const fieldId = $input.data('custom-field-id');
            if (!fieldId) {
                return;
            }
            values[fieldId] = $input.val();
        });
        return values;
    }

    function applyCustomFieldValues(values, $container = $customers) {
        if (!values) {
            return;
        }
        $container.find('.custom-field-input').each((index, input) => {
            const $input = $(input);
            const fieldId = $input.data('custom-field-id');
            if (!fieldId) {
                return;
            }
            if (Object.prototype.hasOwnProperty.call(values, fieldId)) {
                $input.val(values[fieldId]);
            }
        });
    }

    function setRecordDetailsVisible(visible) {
        const $recordDetails = $customers.find('.record-details');

        if (visible) {
            $recordDetails.show();
        } else {
            $recordDetails.hide();
        }
    }

    /**
     * Add the page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Filter Customers Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $customers.on('submit', '#filter-customers form', (event) => {
            event.preventDefault();
            const key = $filterCustomers.find('.key').val();
            $filterCustomers.find('.selected').removeClass('selected');
            filterLimit = 20;
            App.Pages.Customers.resetForm();
            App.Pages.Customers.filter(key);
        });

        /**
         * Event: Filter Entry "Click"
         *
         * Display the customer data of the selected row.
         *
         * @param {jQuery.Event} event
         */
        $customers.on('click', '.customer-row', (event) => {
            if ($filterCustomers.find('.filter').prop('disabled')) {
                return; // Do nothing when user edits a customer record.
            }

            const customerId = $(event.currentTarget).attr('data-id');
            const customer = filterResults.find((filterResult) => Number(filterResult.id) === Number(customerId));

            App.Pages.Customers.display(customer);
            $('#filter-customers .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-customer, #delete-customer').prop('disabled', false);
            setRecordDetailsVisible(true);
            updateProfileUrl(customer?.slug);
        });

        /**
         * Event: Add Customer Button "Click"
         */
        $customers.on('click', '#add-customer', () => {
            App.Pages.Customers.resetForm();
            setRecordDetailsVisible(true);
            $customers.find('#add-edit-delete-group').hide();
            $customers.find('#save-cancel-group').show();
            $customers.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $customers.find('.record-details .form-label span').prop('hidden', false);
            $filterCustomers.find('button').prop('disabled', true);
            $filterCustomers.find('.results').css('color', '#AAA');
            document.getElementById('customer-account-tab')?.click();
        });

        /**
         * Event: Edit Customer Button "Click"
         */
        $customers.on('click', '#edit-customer', () => {
            $customers.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $customers.find('.record-details .form-label span').prop('hidden', false);
            $customers.find('#add-edit-delete-group').hide();
            $customers.find('#save-cancel-group').show();
            $filterCustomers.find('button').prop('disabled', true);
            $filterCustomers.find('.results').css('color', '#AAA');
            document.getElementById('customer-account-tab')?.click();
        });

        /**
         * Event: Cancel Customer Add/Edit Operation Button "Click"
         */
        $customers.on('click', '#cancel-customer', () => {
            const id = $id.val();

            App.Pages.Customers.resetForm();

            if (id) {
                select(id, true);
            }
        });

        /**
         * Event: Save Add/Edit Customer Operation "Click"
         */
        $customers.on('click', '#save-customer', () => {
            const customer = {
                first_name: $firstName.val(),
                last_name: $lastName.val(),
                email: $email.val(),
                phone_number: $phoneNumber.val(),
                address: $address.val(),
                city: $city.val(),
                zip_code: $zipCode.val(),
                notes: $notes.val(),
                timezone: $timezone.val(),
                language: $language.val() || 'english',
                ldap_dn: $ldapDn.val(),
            };

            const custom_fields = getCustomFieldValues($customers);

            if ($id.val()) {
                customer.id = $id.val();
            }

            if (!App.Pages.Customers.validate()) {
                return;
            }

            App.Pages.Customers.save(customer, custom_fields);
        });

        /**
         * Event: Delete Customer Button "Click"
         */
        $customers.on('click', '#delete-customer', () => {
            const customerId = $id.val();
            const buttons = [
                {
                    text: lang('cancel'),
                    click: (event, messageModal) => {
                        messageModal.hide();
                    },
                },
                {
                    text: lang('delete'),
                    click: (event, messageModal) => {
                        App.Pages.Customers.remove(customerId);
                        messageModal.hide();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete_customer'), lang('delete_record_prompt'), buttons);
        });

        $customers.on('input', '#customer-note-text', (event) => {
            if ($(event.currentTarget).val().trim() !== '') {
                $(event.currentTarget).removeClass('is-invalid');
            }
        });

        $customers.on('click', '#add-customer-note', () => {
            const customerId = Number($id.val());
            const noteText = $customerNoteText.val().trim();

            if (!customerId) {
                return;
            }

            if (!noteText) {
                $customerNoteText.addClass('is-invalid');
                return;
            }

            App.Http.Customers.storeNote({
                id_users_customer: customerId,
                note: noteText,
            }).then(() => {
                $customerNoteText.val('');
                loadCustomerNotes(customerId);
            });
        });

        $customers.on('click', '.customer-note-edit', (event) => {
            const $noteCard = $(event.currentTarget).closest('[data-note-id]');
            const note = $noteCard.data('note');

            if (!note) {
                return;
            }

            enterNoteEditMode($noteCard, note);
        });

        $customers.on('click', '.customer-note-save', (event) => {
            const $noteCard = $(event.currentTarget).closest('[data-note-id]');
            const note = $noteCard.data('note');
            const $textarea = $noteCard.find('.customer-note-input');

            if (!note || !$textarea.length) {
                return;
            }

            const updatedText = $textarea.val().trim();

            if (!updatedText) {
                $textarea.addClass('is-invalid');
                return;
            }

            App.Http.Customers.updateNote({
                id: note.id,
                note: updatedText,
            }).then(() => {
                loadCustomerNotes(Number($id.val()));
            });
        });

        $customers.on('click', '.customer-note-cancel', (event) => {
            const $noteCard = $(event.currentTarget).closest('[data-note-id]');
            const note = $noteCard.data('note');

            if (!note) {
                return;
            }

            renderNoteCard($noteCard, note);
        });

        $customers.on('click', '.customer-note-delete', (event) => {
            const $noteCard = $(event.currentTarget).closest('[data-note-id]');
            const note = $noteCard.data('note');

            if (!note) {
                return;
            }

            const buttons = [
                {
                    text: lang('cancel'),
                    click: (event, messageModal) => {
                        messageModal.hide();
                    },
                },
                {
                    text: lang('delete'),
                    click: (event, messageModal) => {
                        App.Http.Customers.deleteNote(note.id).then(() => {
                            loadCustomerNotes(Number($id.val()));
                        });
                        messageModal.hide();
                    },
                },
            ];

            App.Utils.Message.show(lang('delete'), lang('delete_record_prompt'), buttons);
        });

        $customers.on('click', '.customer-appointment-edit', (event) => {
            event.preventDefault();

            const $link = $(event.currentTarget);
            const appointmentId = $link.closest('tr').data('id');
            const appointmentsById = $customerAppointments.data('appointmentsById');

            if (!appointmentsById || !appointmentsById.has(appointmentId)) {
                window.location.href = $link.attr('href');
                return;
            }

            if (!$('#appointments-modal').length) {
                window.location.href = $link.attr('href');
                return;
            }

            try {
                openAppointmentModal(appointmentsById.get(appointmentId));
            } catch (error) {
                window.location.href = $link.attr('href');
            }
        });

        $customers.on('click', '.customer-appointment-row', (event) => {
            if ($(event.target).closest('.customer-appointment-edit').length) {
                return;
            }

            const appointmentId = $(event.currentTarget).data('id');
            showAppointmentDetails(appointmentId);
        });

        $customers.on('click', '#customer-appointment-back', () => {
            showAppointmentList();
        });

        $customers.on('click', '.customer-appointment-status', (event) => {
            const status = $(event.currentTarget).data('status');
            const appointmentId = $customerAppointmentDetails.data('appointmentId');

            if (!appointmentId || !status) {
                return;
            }

        $customerAppointmentNotes.removeClass('is-invalid');
            updateAppointmentFields(appointmentId, { status: status });
        });

        $customers.on('click', '#customer-appointment-save-notes', () => {
            const appointmentId = $customerAppointmentDetails.data('appointmentId');

            if (!appointmentId) {
                return;
            }

            const noteText = $customerAppointmentNotes.val().trim();

            if (!noteText) {
                $customerAppointmentNotes.addClass('is-invalid');
                return;
            }

            $customerAppointmentNotes.removeClass('is-invalid');
            App.Http.Appointments.storeNote({
                id_appointments: appointmentId,
                note: noteText,
            }).then(() => {
                $customerAppointmentNotes.val('').removeClass('is-invalid');
                loadAppointmentNotes(appointmentId);
            });
        });

        $customers.on('hidden.bs.modal', '#appointments-modal', () => {
            const currentId = $id.val();

            if (!currentId) {
                return;
            }

            App.Pages.Customers.filter($filterCustomers.find('.key').val(), currentId, true);
        });

        $customers.on(
            'input',
            '#first-name, #last-name, #email, #phone-number, #address, #city, #zip-code',
            () => updateCustomerSummaryFromInputs(),
        );
    }

    /**
     * Save a customer record to the database (via ajax post).
     *
     * @param {Object} customer Contains the customer data.
     */
    function save(customer, custom_fields) {
        App.Http.Customers.save(customer, custom_fields).then((response) => {
            App.Layouts.Backend.displayNotification(lang('customer_saved'));
            App.Pages.Customers.resetForm();
            $('#filter-customers .key').val('');
            App.Pages.Customers.filter('', response.id, true);
        });
    }

    /**
     * Delete a customer record from database.
     *
     * @param {Number} id Record id to be deleted.
     */
    function remove(id) {
        App.Http.Customers.destroy(id).then(() => {
            App.Layouts.Backend.displayNotification(lang('customer_deleted'));
            App.Pages.Customers.resetForm();
            App.Pages.Customers.filter($('#filter-customers .key').val());
        });
    }

    function updateCustomerSummaryFromInputs() {
        const customerId = $id.val();
        const firstName = $firstName.val();
        const lastName = $lastName.val();
        const email = $email.val();
        const phoneNumber = $phoneNumber.val();
        const address = $address.val();
        const city = $city.val();
        const zipCode = $zipCode.val();

        const locationParts = [address, city, zipCode].filter(Boolean);
        const locationText = locationParts.length ? locationParts.join(', ') : '—';

        const nameParts = [firstName, lastName].filter(Boolean);
        $summaryName.text(nameParts.length ? nameParts.join(' ') : '—');
        $summaryId.text(`ID: ${customerId || '—'}`);

        $summaryEmail.find('.summary-text').text(email || '—');
        $summaryPhone.find('.summary-text').text(phoneNumber || '—');
        $summaryLocation.find('.summary-text').text(locationText);
    }

    function openAppointmentModal(appointment) {
        const $appointmentsModal = $('#appointments-modal');

        if (!appointment || !$appointmentsModal.length) {
            return;
        }

        const customerInfo = appointment.customer || $customerAppointments.data('customerInfo') || {};

        App.Components.AppointmentsModal.resetModal();

        $appointmentsModal.find('.modal-header h3').text(lang('edit_appointment_title'));
        $appointmentsModal.find('#appointment-id').val(appointment.id);
        $appointmentsModal.find('#select-service').val(appointment.id_services).trigger('change');
        $appointmentsModal.find('#select-provider').val(appointment.id_users_provider);

        const startMoment = moment(appointment.start_datetime);
        App.Utils.UI.setDateTimePickerValue($appointmentsModal.find('#start-datetime'), startMoment.toDate());

        const endMoment = moment(appointment.end_datetime);
        App.Utils.UI.setDateTimePickerValue($appointmentsModal.find('#end-datetime'), endMoment.toDate());

        $appointmentsModal.find('#customer-id').val(appointment.id_users_customer);
        $appointmentsModal.find('#first-name').val(customerInfo.first_name || '');
        $appointmentsModal.find('#last-name').val(customerInfo.last_name || '');
        $appointmentsModal.find('#email').val(customerInfo.email || '');
        $appointmentsModal.find('#phone-number').val(customerInfo.phone_number || '');
        $appointmentsModal.find('#address').val(customerInfo.address || '');
        $appointmentsModal.find('#city').val(customerInfo.city || '');
        $appointmentsModal.find('#zip-code').val(customerInfo.zip_code || '');
        $appointmentsModal.find('#language').val(customerInfo.language || vars('default_language'));
        $appointmentsModal.find('#timezone').val(customerInfo.timezone || vars('default_timezone'));
        $appointmentsModal.find('#appointment-location').val(appointment.location || '');
        $appointmentsModal.find('#appointment-status').val(appointment.status || '');
        $appointmentsModal.find('#appointment-notes').val(appointment.notes || '');
        $appointmentsModal.find('#customer-notes').val(customerInfo.notes || '');
        applyCustomFieldValues(customerInfo.custom_field_values || {}, $appointmentsModal);

        App.Components.ColorSelection.setColor($appointmentsModal.find('#appointment-color'), appointment.color);

        $appointmentsModal.modal('show');
    }

    /**
     * Validate customer data before save (insert or update).
     */
    function validate() {
        $formMessage.removeClass('alert-danger').hide();
        $('.is-invalid').removeClass('is-invalid');

        try {
            // Validate required fields.
            let missingRequired = false;

            $('.required').each((index, requiredField) => {
                if ($(requiredField).val() === '') {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            // Validate email address.
            const email = $email.val();

            if (email && !App.Utils.Validation.email(email)) {
                $email.addClass('is-invalid');
                throw new Error(lang('invalid_email'));
            }

            // Validate phone number.
            const phoneNumber = $phoneNumber.val();

            if (phoneNumber && !App.Utils.Validation.phone(phoneNumber)) {
                $phoneNumber.addClass('is-invalid');
                throw new Error(lang('invalid_phone'));
            }

            return true;
        } catch (error) {
            $formMessage.addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Bring the customer form back to its initial state.
     */
    function resetForm() {
        $customers.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $customers.find('.record-details .form-label span').prop('hidden', true);
        $customers.find('.record-details #timezone').val(vars('default_timezone'));
        $customers.find('.record-details #language').val(vars('default_language'));

        $customerAppointmentsList.empty();
        $billingHistoryBody.empty();
        $customerNoteText.val('');
        $customerNoteText.removeClass('is-invalid');
        $customerNoteText.prop('disabled', true);
        $addCustomerNote.prop('disabled', true);
        $customerNotesList.empty();
        $customerVisitNotesList.empty();
        showAppointmentList();

        updateCustomerSummaryFromInputs();

        $customers.find('#edit-customer, #delete-customer').prop('disabled', true);
        $customers.find('#add-edit-delete-group').show();
        $customers.find('#save-cancel-group').hide();

        $customers.find('.record-details .is-invalid').removeClass('is-invalid');
        $customers.find('.record-details #form-message').hide();

        $filterCustomers.find('button').prop('disabled', false);
        $filterCustomers.find('.selected').removeClass('selected');
        $filterCustomers.find('.results').css('color', '');
        setRecordDetailsVisible(false);

        if (userFilesManager) {
            userFilesManager.reset();
        }
        if (userFormsManager) {
            userFormsManager.reset();
        }
    }

    /**
     * Display a customer record into the form.
     *
     * @param {Object} customer Contains the customer record data.
     */
    function display(customer) {
        $id.val(customer.id);
        $firstName.val(customer.first_name);
        $lastName.val(customer.last_name);
        $email.val(customer.email);
        $phoneNumber.val(customer.phone_number);
        $address.val(customer.address);
        $city.val(customer.city);
        $zipCode.val(customer.zip_code);
        $notes.val(customer.notes);
        $timezone.val(customer.timezone);
        $language.val(customer.language || 'english');
        $ldapDn.val(customer.ldap_dn);
        applyCustomFieldValues(customer.custom_field_values || {});

        updateCustomerSummaryFromInputs();

        $customerAppointmentsList.empty();
        $billingHistoryBody.empty();
        $customerNoteText.val('');
        $customerNoteText.removeClass('is-invalid');
        $customerNoteText.prop('disabled', false);
        $addCustomerNote.prop('disabled', false);
        $customerNotesList.empty();
        $customerVisitNotesList.empty();
        showAppointmentList();

        $customerAppointments.data('customerInfo', {
            first_name: customer.first_name,
            last_name: customer.last_name,
            email: customer.email,
            phone_number: customer.phone_number,
            address: customer.address,
            city: customer.city,
            zip_code: customer.zip_code,
            language: customer.language,
            timezone: customer.timezone,
            notes: customer.notes,
            custom_field_values: customer.custom_field_values || {},
        });

        if (userFilesManager) {
            userFilesManager.refresh();
        }
        if (userFormsManager) {
            userFormsManager.refresh();
        }

        const $appointmentsWrapper = $('<div/>', {
            'class': 'overflow-hidden rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white',
        });
        const $appointmentsTable = $('<table/>', {
            'class': 'w-full text-left text-sm',
        });
        const $appointmentsHead = $('<thead/>', {
            'class': 'bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500',
        });
        const $appointmentsBody = $('<tbody/>', {
            'class': 'divide-y divide-[var(--bs-border-color,#e2e8f0)]',
        });
        const $appointmentsHeadRow = $('<tr/>');

        [
            lang('service'),
            lang('provider'),
            'Date & Time',
            lang('status'),
            '',
        ].forEach((title) => {
            $('<th/>', {
                'class': 'px-4 py-3' + (title ? '' : ' text-right'),
                'text': title,
            }).appendTo($appointmentsHeadRow);
        });

        $appointmentsHead.append($appointmentsHeadRow);
        $appointmentsTable.append($appointmentsHead, $appointmentsBody);
        $appointmentsWrapper.append($appointmentsTable);

        let visibleAppointments = 0;
        const appointmentsById = new Map();

        customer.appointments.forEach((appointment) => {
            if (
                vars('role_slug') === App.Layouts.Backend.DB_SLUG_PROVIDER &&
                parseInt(appointment.id_users_provider) !== vars('user_id')
            ) {
                return;
            }

            if (
                vars('role_slug') === App.Layouts.Backend.DB_SLUG_SECRETARY &&
                vars('secretary_providers').indexOf(appointment.id_users_provider) === -1
            ) {
                return;
            }

            const start = App.Utils.Date.format(
                moment(appointment.start_datetime).toDate(),
                vars('date_format'),
                vars('time_format'),
                true,
            );

            visibleAppointments += 1;
            appointmentsById.set(Number(appointment.id), appointment);

            const providerName = `${appointment.provider.first_name} ${appointment.provider.last_name}`.trim();
            const statusLabel = appointment.status || 'Booked';
            const statusClass = 'bg-emerald-50 text-emerald-700';

            $('<tr/>', {
                'class': 'customer-appointment-row cursor-pointer bg-white border-b border-[var(--bs-border-color,#e2e8f0)] last:border-b-0',
                'data-id': appointment.id,
                'html': [
                    $('<td/>', {
                        'class': 'px-4 py-3 font-semibold text-slate-900',
                        'text': appointment.service.name || '-',
                    }),
                    $('<td/>', {
                        'class': 'px-4 py-3 text-slate-700',
                        'text': providerName || '-',
                    }),
                    $('<td/>', {
                        'class': 'px-4 py-3 text-slate-700',
                        'text': start,
                    }),
                    $('<td/>', {
                        'class': 'px-4 py-3',
                        'html': $('<span/>', {
                            'class': `inline-flex rounded-full px-3 py-1 text-xs font-semibold ${statusClass}`,
                            'text': statusLabel,
                        }),
                    }),
                    $('<td/>', {
                        'class': 'px-4 py-3 text-right',
                        'html': $('<a/>', {
                            'href': App.Utils.Url.siteUrl(`calendar/reschedule/${appointment.hash}`),
                            'class':
                                'customer-appointment-edit inline-flex items-center rounded-xl border border-[var(--bs-border-color,#e2e8f0)] px-3 py-2 text-sm font-semibold text-slate-700 hover:border-[var(--bs-border-color,#e2e8f0)] hover:text-slate-900',
                            'text': lang('edit'),
                        }),
                    }),
                ],
            }).appendTo($appointmentsBody);

            // Add to billing history if payment info exists
            if (appointment.payment_status && appointment.payment_status !== 'not-paid') {
                const paymentStatus = appointment.payment_status.toLowerCase();
                const paymentBadgeClass =
                    paymentStatus === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700';
                $('<tr/>', {
                    'class': 'bg-white border-b border-[var(--bs-border-color,#e2e8f0)] last:border-b-0',
                    'html': [
                        $('<td/>', {
                            'class': 'px-4 py-3 text-slate-700',
                            'text': moment(appointment.book_datetime).format('YYYY-MM-DD HH:mm'),
                        }),
                        $('<td/>', {
                            'class': 'px-4 py-3 text-slate-700',
                            'text': Number(appointment.payment_amount).toFixed(2),
                        }),
                        $('<td/>', {
                            'class': 'px-4 py-3',
                            'html': $('<span/>', {
                                'class': `inline-flex rounded-full px-3 py-1 text-xs font-semibold ${paymentBadgeClass}`,
                                'text': paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1),
                            }),
                        }),
                    ],
                }).appendTo($billingHistoryBody);
            }
        });

        if (!visibleAppointments) {
            $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-slate-50 px-4 py-6 text-center text-sm text-slate-500',
                'text': lang('no_records_found'),
            }).appendTo($customerAppointmentsList);
        } else {
            $appointmentsWrapper.appendTo($customerAppointmentsList);
        }

        $customerAppointments.data('appointmentsById', appointmentsById);

        if ($billingHistoryBody.is(':empty')) {
            $billingHistoryBody.append(
                '<tr><td colspan="3" class="px-4 py-3 text-center text-slate-500">No billing history found.</td></tr>',
            );
        }

        loadCustomerNotes(customer.id);
        loadCustomerVisitNotes(customer.id);
    }

    function loadCustomerNotes(customerId) {
        $customerNotesList.empty();

        if (!customerId) {
            return;
        }

        if (typeof App.Http.Customers.notes !== 'function') {
            return;
        }

        App.Http.Customers.notes(customerId)
            .then((notes) => {
                renderNotesList(notes);
            })
            .fail(() => {
                renderNotesList([]);
            });
    }

    function loadCustomerVisitNotes(customerId) {
        $customerVisitNotesList.empty();

        if (!customerId) {
            return;
        }

        fetchCustomerVisitNotes(customerId)
            .then((notes) => {
                renderCustomerVisitNotes(notes);
            })
            .fail(() => {
                renderCustomerVisitNotes([]);
            });
    }

    function fetchCustomerVisitNotes(customerId) {
        if (typeof App.Http.Customers.visitNotes === 'function') {
            return App.Http.Customers.visitNotes(customerId);
        }

        return App.Utils.Http.request('POST', 'customers/visit_notes', {
            csrf_token: vars('csrf_token'),
            customer_id: customerId,
        });
    }

    function renderCustomerVisitNotes(notes) {
        $customerVisitNotesList.empty();

        if (!notes || !notes.length) {
            $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-slate-50 px-4 py-6 text-center text-sm text-slate-500',
                'text': lang('no_records_found'),
            }).appendTo($customerVisitNotesList);
            return;
        }

        notes.forEach((note) => {
            const authorName = `${note.author_first_name || ''} ${note.author_last_name || ''}`.trim();
            const authorLabel = authorName || note.author_email || '-';
            const appointmentDate = note.appointment_start_datetime
                ? App.Utils.Date.format(
                    moment(note.appointment_start_datetime).toDate(),
                    vars('date_format'),
                    vars('time_format'),
                    true,
                )
                : '—';
            const appointmentLabel = `#${note.id_appointments} • ${note.service_name || '—'} • ${appointmentDate}`;
            const createdAt = formatNoteDate(note.create_datetime);

            const $noteCard = $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-4',
            });
            const $meta = $('<div/>', {
                'class': 'text-sm text-slate-500',
                'text': `${authorLabel} • ${createdAt}`,
            });
            const $appointmentMeta = $('<div/>', {
                'class': 'mt-1 text-xs uppercase tracking-wide text-slate-400',
                'text': appointmentLabel,
            });
            const $body = $('<div/>', {
                'class': 'mt-3 text-slate-700',
                'text': note.note,
            });

            $noteCard.append($meta, $appointmentMeta, $body).appendTo($customerVisitNotesList);
        });
    }

    function showAppointmentList() {
        $customerAppointmentDetails.addClass('d-none');
        $customerAppointmentsList.removeClass('d-none');
        $customerAppointmentDetails.removeData('appointmentId');
        $customerAppointmentNotes.prop('disabled', true);
        $customerAppointmentSaveNotes.prop('disabled', true);
    }

    function showAppointmentDetails(appointmentId) {
        const appointmentsById = $customerAppointments.data('appointmentsById');

        if (!appointmentsById || !appointmentsById.has(appointmentId)) {
            return;
        }

        const appointment = appointmentsById.get(appointmentId);
        const customerInfo = $customerAppointments.data('customerInfo') || {};
        const startMoment = moment(appointment.start_datetime);
        const endMoment = moment(appointment.end_datetime);
        const durationMinutes = endMoment.diff(startMoment, 'minutes');

        $customerAppointmentDetails.data('appointmentId', appointmentId);
        $customerAppointmentEditLink.attr(
            'href',
            App.Utils.Url.siteUrl(`calendar/reschedule/${appointment.hash}`),
        );
        $customerAppointmentId.text(appointment.id || '—');
        $customerAppointmentHash.text(appointment.hash || '—');
        $customerAppointmentService.text(appointment.service?.name || '—');
        $customerAppointmentDate.text(
            App.Utils.Date.format(startMoment.toDate(), vars('date_format'), vars('time_format')),
        );
        $customerAppointmentTime.text(formatAppointmentTime(startMoment));
        $customerAppointmentDuration.text(durationMinutes ? `${durationMinutes} min` : '—');
        $customerAppointmentStatus.empty().append(renderStatusBadge(appointment.status || 'Booked'));
        $customerAppointmentProvider.text(
            `${appointment.provider?.first_name || ''} ${appointment.provider?.last_name || ''}`.trim() || '—',
        );
        $customerAppointmentCustomer.text(
            `${customerInfo.first_name || ''} ${customerInfo.last_name || ''}`.trim() || '—',
        );
        $customerAppointmentAddress.text(buildAddress(customerInfo));
        $customerAppointmentPaymentStatus.text(appointment.payment_status || '—');
        $customerAppointmentPaymentAmount.text(
            appointment.payment_amount ? Number(appointment.payment_amount).toFixed(2) : '—',
        );
        $customerAppointmentNotes.val('').prop('disabled', false);
        $customerAppointmentSaveNotes.prop('disabled', false);
        $customerAppointmentNotesList.empty();

        loadAppointmentNotes(appointmentId);

        $customerAppointmentsList.addClass('d-none');
        $customerAppointmentDetails.removeClass('d-none');
    }

    function updateAppointmentFields(appointmentId, fields) {
        const appointmentsById = $customerAppointments.data('appointmentsById');

        if (!appointmentsById || !appointmentsById.has(appointmentId)) {
            return;
        }

        const appointment = appointmentsById.get(appointmentId);
        const payload = {
            id: appointmentId,
            start_datetime: appointment.start_datetime,
            end_datetime: appointment.end_datetime,
            id_services: appointment.id_services,
            id_users_provider: appointment.id_users_provider,
            id_users_customer: appointment.id_users_customer,
            is_unavailability: appointment.is_unavailability ?? false,
            location: appointment.location ?? '',
            color: appointment.color ?? '',
            status: appointment.status ?? '',
            notes: appointment.notes ?? '',
            ...fields,
        };

        $.post(App.Utils.Url.siteUrl('appointments/update'), {
            csrf_token: vars('csrf_token'),
            appointment: JSON.stringify(payload),
        })
            .then(() => {
                Object.assign(appointment, fields);
                appointmentsById.set(appointmentId, appointment);
                if (fields.status) {
                    const $row = $customerAppointmentsList.find(`tr[data-id="${appointmentId}"]`);
                    $row.find('td').eq(3).find('span').text(fields.status);
                }
                showAppointmentDetails(appointmentId);
            })
            .fail(() => {
                App.Layouts.Backend.displayNotification(lang('unexpected_issues'));
            });
    }

    function loadAppointmentNotes(appointmentId) {
        $customerAppointmentNotesList.empty();

        if (!appointmentId || typeof App.Http.Appointments?.notes !== 'function') {
            return;
        }

        App.Http.Appointments.notes(appointmentId)
            .then((notes) => {
                renderAppointmentNotes(notes);
            })
            .fail(() => {
                renderAppointmentNotes([]);
            });
    }

    function renderAppointmentNotes(notes) {
        $customerAppointmentNotesList.empty();

        if (!notes || !notes.length) {
            $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-slate-50 px-4 py-6 text-center text-sm text-slate-500',
                'text': lang('no_records_found'),
            }).appendTo($customerAppointmentNotesList);
            return;
        }

        notes.forEach((note) => {
            const authorName = `${note.author_first_name || ''} ${note.author_last_name || ''}`.trim();
            const authorLabel = authorName || note.author_email || '-';
            const createdAt = formatNoteDate(note.create_datetime);

            const $noteCard = $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-4',
            });
            const $header = $('<div/>', {
                'class': 'text-sm text-slate-500',
                'text': `${authorLabel} • ${createdAt}`,
            });
            const $body = $('<div/>', {
                'class': 'mt-3 text-slate-700',
                'text': note.note,
            });

            $noteCard.append($header, $body).appendTo($customerAppointmentNotesList);
        });
    }

    function buildAddress(customerInfo) {
        const parts = [customerInfo.address, customerInfo.city, customerInfo.zip_code].filter(Boolean);

        return parts.length ? parts.join(', ') : '—';
    }

    function formatAppointmentTime(startMoment) {
        const timeFormat = vars('time_format') === 'military' ? 'HH:mm' : 'h:mm a';
        return startMoment.format(timeFormat);
    }

    function renderStatusBadge(status) {
        return $('<span/>', {
            'class': 'inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700',
            'text': status,
        });
    }

    function renderNotesList(notes) {
        $customerNotesList.empty();

        if (!notes || !notes.length) {
            $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-slate-50 px-4 py-6 text-center text-sm text-slate-500',
                'text': lang('no_records_found'),
            }).appendTo($customerNotesList);
            return;
        }

        notes.forEach((note) => {
            const $noteCard = $('<div/>', {
                'class': 'rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-4',
                'data-note-id': note.id,
            });

            renderNoteCard($noteCard, note);
            $noteCard.appendTo($customerNotesList);
        });
    }

    function renderNoteCard($noteCard, note) {
        const authorName = `${note.author_first_name || ''} ${note.author_last_name || ''}`.trim();
        const authorLabel = authorName || note.author_email || '-';
        const createdAt = formatNoteDate(note.create_datetime);
        const isAuthor = Number(note.id_users_author) === Number(vars('user_id'));

        $noteCard
            .data('note', note)
            .removeData('editing')
            .empty();

        const $header = $('<div/>', {
            'class': 'd-flex align-items-start justify-content-between gap-3',
        });
        const $meta = $('<div/>', {
            'class': 'text-sm text-slate-500',
            'text': `${authorLabel} • ${createdAt}`,
        });
        const $actions = $('<div/>', {
            'class': 'd-flex gap-2',
        });

        if (isAuthor) {
            $('<button/>', {
                'type': 'button',
                'class': 'btn btn-outline-secondary btn-sm customer-note-edit',
                'text': lang('edit'),
            }).appendTo($actions);
        }

        $('<button/>', {
            'type': 'button',
            'class': 'btn btn-outline-secondary btn-sm customer-note-delete',
            'text': lang('delete'),
        }).appendTo($actions);

        $header.append($meta, $actions);

        const $body = $('<div/>', {
            'class': 'mt-3 text-slate-700 customer-note-body',
            'text': note.note,
        });

        $noteCard.append($header, $body);
    }

    function enterNoteEditMode($noteCard, note) {
        const $textarea = $('<textarea/>', {
            'class': 'form-control customer-note-input',
            'rows': 4,
            'text': note.note,
        });

        const $actions = $('<div/>', {
            'class': 'mt-3 d-flex justify-content-end gap-2',
        });

        $('<button/>', {
            'type': 'button',
            'class': 'btn btn-secondary customer-note-cancel',
            'text': lang('cancel'),
        }).appendTo($actions);

        $('<button/>', {
            'type': 'button',
            'class': 'btn btn-primary customer-note-save',
            'text': lang('save'),
        }).appendTo($actions);

        $noteCard.empty().append($textarea, $actions);
    }

    function formatNoteDate(value) {
        if (!value) {
            return '-';
        }

        return App.Utils.Date.format(
            moment(value).toDate(),
            vars('date_format'),
            vars('time_format'),
            true,
        );
    }

    /**
     * Filter customer records.
     *
     * @param {String} keyword This keyword string is used to filter the customer records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} show Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Customers.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $filterCustomers.find('.results').empty();

            response.forEach((customer) => {
                $('#filter-customers .results').append(App.Pages.Customers.getFilterHtml(customer)).append($('<hr/>'));
            });

            if (!response.length) {
                $filterCustomers.find('.results').append(
                    $('<em/>', {
                        'text': lang('no_records_found'),
                    }),
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': lang('load_more'),
                    'click': () => {
                        filterLimit += 20;
                        App.Pages.Customers.filter(keyword, selectId, show);
                    },
                }).appendTo('#filter-customers .results');
            }

            if (pendingSlug) {
                const slugToSelect = pendingSlug;
                pendingSlug = null;
                selectBySlug(slugToSelect);
                return;
            }

            if (response.length) {
                const defaultId = selectId ?? response[0].id;
                const shouldShow = show || !selectId;
                App.Pages.Customers.select(defaultId, shouldShow);
            } else {
                setRecordDetailsVisible(false);
            }
        });
    }

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} customer Contains the customer data.
     *
     * @return {String} Returns the record HTML code.
     */
    function getFilterHtml(customer) {
        const name = (customer.first_name || '[No First Name]') + ' ' + (customer.last_name || '[No Last Name]');

        let info = customer.email || '[No Email]';

        info = customer.phone_number ? info + ', ' + customer.phone_number : info;

        return $('<div/>', {
            'class': 'customer-row entry',
            'data-id': customer.id,
            'data-slug': customer.slug || undefined,
            'html': [
                $('<strong/>', {
                    'text': name,
                }),
                $('<br/>'),
                $('<small/>', {
                    'class': 'text-muted',
                    'text': info,
                }),
                $('<br/>'),
            ],
        });
    }

    /**
     * Select a specific record from the current filter results.
     *
     * If the customer id does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record on the form.
     */
    function select(id, show = false) {
        $('#filter-customers .selected').removeClass('selected');

        $('#filter-customers .entry[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const customer = filterResults.find((filterResult) => Number(filterResult.id) === Number(id));

            App.Pages.Customers.display(customer);

            $('#edit-customer, #delete-customer').prop('disabled', false);
            setRecordDetailsVisible(true);
        }
    }

    function selectBySlug(slug) {
        if (!slug || typeof App.Http.Customers.findBySlug !== 'function') {
            return;
        }

        App.Http.Customers.findBySlug(slug)
            .then((customer) => {
                App.Pages.Customers.display(customer);
                $('#edit-customer, #delete-customer').prop('disabled', false);
                setRecordDetailsVisible(true);
                updateProfileUrl(customer?.slug, true);
                if (customer?.slug) {
                    $('#filter-customers .selected').removeClass('selected');
                    $('#filter-customers .entry[data-slug="' + customer.slug + '"]').addClass('selected');
                }
            })
            .fail(() => {
                App.Layouts.Backend.displayNotification(lang('no_records_found'));
            });
    }

    function updateProfileUrl(slug, replace = false) {
        if (!slug) {
            return;
        }

        const url = App.Utils.Url.siteUrl(`customers/profile/${slug}`);

        if (replace) {
            window.history.replaceState({ slug }, '', url);
            return;
        }

        window.history.pushState({ slug }, '', url);
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        App.Pages.Customers.resetForm();
        App.Pages.Customers.addEventListeners();
        userFilesManager = App.Components.UserFiles.create($customerFilesPanel, {
            userType: 'customer',
            getUserId: () => $id.val(),
            canUpload: Number($customerFilesPanel.data('can-upload')) === 1,
            canDelete: Number($customerFilesPanel.data('can-delete')) === 1,
        });
        if ($customerFormsPanel.length) {
            userFormsManager = App.Components.UserForms.create($customerFormsPanel, {
                userType: 'customer',
                getUserId: () => $id.val(),
                canReset: Number($customerFormsPanel.data('can-reset')) === 1,
            });
        }
        pendingSlug = vars('selected_record_slug') || null;
        App.Pages.Customers.filter('');
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        remove,
        validate,
        getFilterHtml,
        resetForm,
        display,
        select,
        selectBySlug,
        addEventListeners,
    };
})();
