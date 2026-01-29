<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="about-page" class="container backend-page">
    <div class="row settings-layout">
        <div class="col-sm-3 settings-nav-panel ps-0">
            <?php component('settings_nav'); ?>
        </div>
        <div class="col-sm-9 settings-content">
            <div id="about">

        <div class="text-center mb-5 d-flex flex-column align-items-center">
            <svg role="img" aria-label="PegasusCal Logo" xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 241.07 169.55" width="180" height="126" class="mb-4"
                 fill="currentColor">
                <path
                    d="M241.07,0v8.86c0,23.94-17.54,43.85-40.45,47.57-3.43,19.84-19.03,35.57-38.81,39.19-3.85,22.75-23.69,40.13-47.52,40.13H33.81l17.72-17.72h62.76c13.73,0,25.36-9.12,29.17-21.62h-70.31l17.72-17.73h62.24c13.73,0,25.36-9.12,29.17-21.62h-69.79l17.72-17.72h62.65c13.73,0,25.37-9.12,29.17-21.62H59.63c-19.38,0-41.91,21.72-41.91,49.72v84.39L0,169.55v-102.11C0,32.14,28.42,0,59.63,0h181.44Z"/>
            </svg>
            <h1 class="display-4 fw-semibold mb-0" style="letter-spacing: -1px">PegasusCal</h1>
            <span class="badge rounded-pill border border-[var(--bs-border-color,#e2e8f0)] bg-[var(--bs-secondary-bg,#f8fafc)] text-slate-900 mt-3">
                <?= lang('current_version') ?>: <?= config('version') ?>
            </span>
            <p class="form-text text-muted mb-0 mt-4">
                <?= lang('about_app_info') ?>
            </p>
        </div>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5 mb-5">
            <h4 class="fw-medium text-dark mb-3 fs-4">
                <?= lang('support') ?>
            </h4>

            <p class="form-text text-muted mb-4">
                <?= lang('about_app_support') ?>
            </p>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <a class="btn btn-outline-secondary d-block" href="https://easyappointments.org" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        <?= lang('official_website') ?>
                    </a>
                </div>

                <div class="col-lg-6 mb-4">
                    <a class="btn btn-outline-secondary d-block"
                       href="https://groups.google.com/forum/#!forum/easy-appointments" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        <?= lang('support_group') ?>
                    </a>
                </div>

                <div class="col-lg-6 mb-4">
                    <a class="btn btn-outline-secondary d-block"
                       href="https://github.com/alextselegidis/easyappointments/issues" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        <?= lang('project_issues') ?>
                    </a>
                </div>

                <div class="col-lg-6 mb-4">
                    <a class="btn btn-outline-secondary d-block" href="https://facebook.com/easyappts" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Facebook
                    </a>
                </div>

                <div class="col-lg-6 mb-4">
                    <a class="btn btn-outline-secondary d-block" href="https://x.com/easyappts" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        X.com
                    </a>
                </div>

                <div class="col-lg-6 mb-4">
                    <a class="btn btn-outline-secondary d-block" href="https://easyappointments.org/get-a-free-quote"
                       target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>
                        Customize E!A
                    </a>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-[var(--bs-border-color,#e2e8f0)] bg-white p-5 mb-5">
            <h4 class="fw-medium text-dark mb-3 fs-4">
                <?= lang('license') ?>
            </h4>

            <p class="form-text text-muted mb-4">
                <?= lang('about_app_license') ?>
            </p>

            <div>
                <a class="btn btn-outline-secondary d-block w-50 m-auto mb-2"
                   href="https://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>
                    GPL-3.0
                </a>
            </div>
        </section>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>
