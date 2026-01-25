<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div>
    <div class="frame-container">
        <h2 class="frame-title">Welcome <?= e(vars('customer')['first_name'] ?? '') ?>!</h2>

        <div class="row g-4 mt-2 flex-column align-items-center">
            <div class="col-12 col-md-8 col-lg-6">
                <a href="<?= site_url('booking') ?>"
                   class="dashboard-card card text-center text-decoration-none p-5 shadow-sm border border-slate-200 bg-white transition-all hover:border-slate-300">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-wrapper mb-4 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-calendar-plus fa-2x text-primary"></i>
                        </div>
                        <h3 class="h4 mb-0 text-dark">Book Session</h3>
                    </div>
                </a>
            </div>

            <div class="col-12 col-md-8 col-lg-6">
                <a href="<?= site_url('customer/bookings') ?>"
                   class="dashboard-card card text-center text-decoration-none p-5 shadow-sm border border-slate-200 bg-white transition-all hover:border-slate-300">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div class="icon-wrapper mb-4 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-list-ul fa-2x text-primary"></i>
                        </div>
                        <h3 class="h4 mb-0 text-dark">My Bookings</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        background-color: var(--bs-body-bg);
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        background-color: var(--bs-tertiary-bg);
    }

    .icon-wrapper {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }

    .icon-wrapper i {
        color: var(--bs-primary) !important;
    }
</style>
<?php end_section('content'); ?>
