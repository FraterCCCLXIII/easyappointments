<?php
/**
 * Local variables.
 *
 * @var string $active_nav
 * @var bool $show_forms_nav
 */
$active_nav = $active_nav ?? 'account';
$show_forms_nav = $show_forms_nav ?? false;
$link_base_class = 'nav-link settings-nav-link';
?>

<h4 class="settings-nav-title text-black-50 mb-4 border-bottom fw-light">
    <?= lang('account') ?>
</h4>

<ul class="nav flex-column settings-nav">
    <li class="nav-item mb-2">
        <a class="<?= $link_base_class . ($active_nav === 'account' ? ' active' : '') ?>"
           href="<?= site_url('account') ?>">
            <?= lang('account') ?>
        </a>
    </li>
    <?php if ($show_forms_nav): ?>
        <li class="nav-item mb-2">
            <a class="<?= $link_base_class . ($active_nav === 'account/forms' ? ' active' : '') ?>"
               href="<?= site_url('account/forms') ?>">
                Forms
            </a>
        </li>
    <?php endif; ?>
</ul>
