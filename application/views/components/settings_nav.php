<?php
/**
 * Local variables.
 *
 * @var string|null $active_menu
 */
$current_route = $active_menu ?? uri_string();
$active_route_map = [
    'general_settings' => 'general_settings',
    'booking_settings' => 'booking_settings',
    'business_settings' => 'business_settings',
    'legal_settings' => 'legal_settings',
    'integrations' => 'integrations',
    'api_settings' => 'integrations',
    'ldap_settings' => 'integrations',
    'google_analytics_settings' => 'integrations',
    'matomo_analytics_settings' => 'integrations',
    'stripe_settings' => 'stripe_settings',
    'stripe' => 'stripe_settings',
];
$active_route = $active_route_map[$current_route] ?? $current_route;
$link_base_class = 'nav-link settings-nav-link';
?>

<h4 class="settings-nav-title text-black-50 mb-4 border-bottom fw-light">
    <?= lang('settings') ?>
</h4>

<ul id="settings-nav" class="nav flex-column settings-nav">
    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_route === 'general_settings' ? ' active' : '') ?>"
           href="<?= site_url('general_settings') ?>">
            <?= lang('general_settings') ?>
        </a>
    </li>

    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_route === 'booking_settings' ? ' active' : '') ?>"
           href="<?= site_url('booking_settings') ?>">
            <?= lang('booking_settings') ?>
        </a>
    </li>

    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_route === 'business_settings' ? ' active' : '') ?>"
           href="<?= site_url('business_settings') ?>">
            <?= lang('business_logic') ?>
        </a>
    </li>

    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_route === 'legal_settings' ? ' active' : '') ?>"
           href="<?= site_url('legal_settings') ?>">
            <?= lang('legal_contents') ?>
        </a>
    </li>

    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_route === 'integrations' ? ' active' : '') ?>"
           href="<?= site_url('integrations') ?>">
            <?= lang('integrations') ?>
        </a>
    </li>

    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_route === 'stripe_settings' ? ' active' : '') ?>"
           href="<?= site_url('stripe_settings') ?>">
            Stripe
        </a>
    </li>
</ul>
