<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="account-forms-page" class="container backend-page">
    <div class="row settings-layout">
        <div class="col-sm-3 settings-nav-panel ps-0">
            <?php component('account_nav', [
                'active_nav' => 'account/forms',
                'show_forms_nav' => vars('show_forms_nav', false),
            ]); ?>
        </div>
        <div class="col-sm-9 settings-content">
            <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                <h4 class="text-black-50 mb-0 fw-light">Forms</h4>
            </div>
            <div class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-3">
                <table class="w-full text-left text-sm" id="account-forms-table">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Form</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody id="account-forms-body" class="divide-y divide-[var(--bs-border-color,#e2e8f0)]">
                        <!-- JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/forms_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/account_forms.js') ?>"></script>

<?php end_section('scripts'); ?>
