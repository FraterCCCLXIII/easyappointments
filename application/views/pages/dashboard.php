<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>
<div>
    <div class="frame-container">
        <h2 class="frame-title">Welcome <?= e(vars('customer')['first_name'] ?? '') ?>!</h2>

        <div class="flex flex-col gap-4 mt-6">
            <a href="<?= site_url('booking') ?>" class="booking-card !flex flex-col items-start p-6 no-underline">
                <div class="text-slate-400 mb-2">
                    <i data-lucide="calendar-plus" class="w-7 h-7"></i>
                </div>
                <div class="text-left">
                    <div class="font-bold text-xl mb-0.5">
                        Book Session
                    </div>
                    <div class="booking-card-subtitle text-sm">
                        Schedule a new appointment
                    </div>
                </div>
            </a>

            <a href="<?= site_url('customer/bookings') ?>" class="booking-card !flex flex-col items-start p-6 no-underline">
                <div class="text-slate-400 mb-2">
                    <i data-lucide="list" class="w-7 h-7"></i>
                </div>
                <div class="text-left">
                    <div class="font-bold text-xl mb-0.5">
                        My Bookings
                    </div>
                    <div class="booking-card-subtitle text-sm">
                        View and manage your sessions
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php end_section('content'); ?>
