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

App.Pages.Billing = (function () {
    const $billingPage = $('#billing-page');

    function notify(message) {
        if (App.Layouts?.Backend?.displayNotification) {
            App.Layouts.Backend.displayNotification(message);
            return;
        }

        window.alert(message);
    }

    function setBusy($row, busy) {
        $row.find('button, input, textarea, select').prop('disabled', busy);
    }

    function updateStatusBadges($row, billingStatus, paymentStatus) {
        const billingClass = ['paid', 'paid_by_phone'].includes(billingStatus)
            ? 'bg-success'
            : billingStatus === 'partially_refunded'
              ? 'bg-info'
              : 'bg-warning';
        $row.find('.billing-status-badge')
            .removeClass('bg-success bg-warning bg-secondary bg-info')
            .addClass(billingClass)
            .text(billingStatus.replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()));

        const paymentClass =
            paymentStatus === 'paid' ? 'bg-success' : paymentStatus === 'pending' ? 'bg-info' : 'bg-secondary';
        $row.find('.payment-status-badge')
            .removeClass('bg-success bg-info bg-secondary')
            .addClass(paymentClass)
            .text(paymentStatus);
    }

    async function refundAppointment($row, refundMode, refundValue, refundReason) {
        const appointmentId = Number($row.data('appointment-id'));
        setBusy($row, true);

        try {
            const response = await App.Utils.Http.request('POST', 'billing/refund', {
                appointment_id: appointmentId,
                refund_mode: refundMode,
                refund_value: refundValue,
                refund_reason: refundReason || '',
            });

            updateStatusBadges($row, response.billing_status, response.payment_status || 'refunded');
            $row.find('.billing-status-select').val(response.billing_status);

            if (response.refund_reference) {
                $row.find('.billing-reference-input').val(response.refund_reference);
            }

            notify(`Refund processed: ${response.refund_amount}`);
        } catch (error) {
            notify(error.message || 'Could not process refund.');
        } finally {
            setBusy($row, false);
        }
    }

    function showRefundDialog($row) {
        const paidAmount = ($row.find('td').eq(3).text() || '').trim();

        const content = `
            <div class="d-grid gap-3">
                <div>
                    <label for="refund-mode" class="form-label">Refund Type</label>
                    <select id="refund-mode" class="form-select form-select-sm">
                        <option value="amount">Amount</option>
                        <option value="percent">Percent</option>
                    </select>
                </div>
                <div>
                    <label for="refund-value" class="form-label">Value</label>
                    <input id="refund-value" type="number" min="0.01" step="0.01" class="form-control form-control-sm" />
                    <div class="form-text">Paid amount: ${paidAmount}</div>
                </div>
                <div>
                    <label for="refund-reason" class="form-label">Reason (optional)</label>
                    <textarea id="refund-reason" class="form-control form-control-sm" rows="2"></textarea>
                </div>
            </div>
        `;

        App.Utils.Message.show('Refund Payment', content, [
            {
                text: 'Cancel',
                className: 'btn btn-outline-secondary',
                click: (event, messageModal) => messageModal.hide(),
            },
            {
                text: 'Refund',
                className: 'btn btn-danger',
                click: async (event, messageModal) => {
                    const refundMode = $('#refund-mode').val();
                    const refundValue = Number($('#refund-value').val());
                    const refundReason = String($('#refund-reason').val() || '');

                    if (!refundValue || refundValue <= 0) {
                        notify('Please provide a valid refund value.');
                        return;
                    }

                    if (refundMode === 'percent' && refundValue > 100) {
                        notify('Percent refund cannot exceed 100.');
                        return;
                    }

                    messageModal.hide();
                    await refundAppointment($row, refundMode, refundValue, refundReason);
                },
            },
        ]);
    }

    async function createPaymentLink(appointmentId) {
        return App.Utils.Http.request('POST', 'billing/create_payment_link', {
            appointment_id: appointmentId,
        });
    }

    async function saveBillingStatus($row, billingStatusOverride = null) {
        const appointmentId = Number($row.data('appointment-id'));
        const billingStatus = billingStatusOverride || $row.find('.billing-status-select').val();
        const billingReference = $row.find('.billing-reference-input').val();
        const billingNotes = $row.find('.billing-notes-input').val();

        setBusy($row, true);

        try {
            const response = await App.Utils.Http.request('POST', 'billing/update_status', {
                appointment_id: appointmentId,
                billing_status: billingStatus,
                billing_reference: billingReference,
                billing_notes: billingNotes,
            });

            updateStatusBadges($row, response.billing_status, response.payment_status || 'not-paid');
            notify('Billing status updated.');
        } catch (error) {
            notify(error.message || 'Could not update billing status.');
        } finally {
            setBusy($row, false);
        }
    }

    function addEventListeners() {
        $billingPage.on('click', '.js-save-billing-status', async (event) => {
            const $row = $(event.currentTarget).closest('tr');
            await saveBillingStatus($row);
        });

        $billingPage.on('click', '.js-mark-paid-phone', async (event) => {
            const $row = $(event.currentTarget).closest('tr');
            $row.find('.billing-status-select').val('paid_by_phone');
            await saveBillingStatus($row, 'paid_by_phone');
        });

        $billingPage.on('click', '.js-open-payment-link', async (event) => {
            const $row = $(event.currentTarget).closest('tr');
            const appointmentId = Number($row.data('appointment-id'));
            setBusy($row, true);
            try {
                const response = await createPaymentLink(appointmentId);
                window.open(response.payment_link, '_blank', 'noopener,noreferrer');
                updateStatusBadges($row, 'payment_link_sent', 'pending');
                $row.find('.billing-status-select').val('payment_link_sent');
                notify('Payment link opened.');
            } catch (error) {
                notify(error.message || 'Could not open payment link.');
            } finally {
                setBusy($row, false);
            }
        });

        $billingPage.on('click', '.js-copy-payment-link', async (event) => {
            const $row = $(event.currentTarget).closest('tr');
            const appointmentId = Number($row.data('appointment-id'));
            setBusy($row, true);
            try {
                const response = await createPaymentLink(appointmentId);
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(response.payment_link);
                } else {
                    window.prompt('Copy payment link:', response.payment_link);
                }
                updateStatusBadges($row, 'payment_link_sent', 'pending');
                $row.find('.billing-status-select').val('payment_link_sent');
                notify('Payment link copied.');
            } catch (error) {
                notify(error.message || 'Could not copy payment link.');
            } finally {
                setBusy($row, false);
            }
        });

        $billingPage.on('click', '.js-send-payment-link-email', async (event) => {
            const $row = $(event.currentTarget).closest('tr');
            const appointmentId = Number($row.data('appointment-id'));
            setBusy($row, true);
            try {
                await App.Utils.Http.request('POST', 'billing/send_payment_link_email', {
                    appointment_id: appointmentId,
                });
                updateStatusBadges($row, 'payment_link_sent', 'pending');
                $row.find('.billing-status-select').val('payment_link_sent');
                notify('Payment link email sent.');
            } catch (error) {
                notify(error.message || 'Could not send payment link email.');
            } finally {
                setBusy($row, false);
            }
        });

        $billingPage.on('click', '.js-refund', (event) => {
            const $row = $(event.currentTarget).closest('tr');
            showRefundDialog($row);
        });
    }

    function initialize() {
        addEventListeners();
    }

    return {
        initialize,
    };
})();

window.addEventListener('DOMContentLoaded', () => {
    App.Pages.Billing.initialize();
});
