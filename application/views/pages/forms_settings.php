<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="forms-settings" class="container backend-page">
    <div class="row settings-layout">
        <div class="col-sm-3 settings-nav-panel ps-0">
            <?php component('settings_nav'); ?>
        </div>
        <div class="col-sm-9 settings-content">
            <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom mb-4 pb-2">
                <h4 class="text-black-50 mb-0 fw-light">
                    Forms
                </h4>
                <a href="<?= site_url('forms_settings/create') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-plus-square me-2"></i>
                    New Form
                </a>
            </div>

            <div class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-3">
                <div class="mb-3">
                    <input type="text" id="forms-search" class="form-control" placeholder="Search forms">
                </div>
                <div id="forms-list" class="d-flex flex-column gap-2">
                    <!-- JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/forms_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/forms_settings_list.js') ?>"></script>

<?php end_section('scripts'); ?>
