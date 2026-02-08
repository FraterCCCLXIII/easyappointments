<?php
/**
 * Local variables.
 *
 * @var string $subject
 * @var string $message
 * @var array $settings
 * @var string $account_url
 * @var array $forms
 */
?>
<html lang="en">
<head>
    <title><?= $subject ?> | Easy!Appointments</title>
</head>
<body style="font: 13px arial, helvetica, tahoma;">

<div class="email-container" style="width: 650px; border: 1px solid var(--bs-border-color, #e2e8f0); margin: 30px auto;">
    <div id="header"
         style="background-color: <?= $settings['company_color'] ?? '#429a82' ?>; height: 45px; padding: 10px 15px;">
        <strong id="logo" style="color: white; font-size: 20px; margin-top: 10px; display: inline-block">
            <?= e($settings['company_name']) ?>
        </strong>
    </div>

    <div id="content" style="padding: 10px 15px; min-height: 320px">
        <h2>
            <?= $subject ?>
        </h2>
        <p>
            <?= $message ?>
        </p>
        <p>
            <a href="<?= e($account_url) ?>" style="color: #1f2937; font-weight: 600;">
                Complete your profile
            </a>
        </p>

        <?php if (!empty($forms)): ?>
            <p style="margin-top: 16px; font-weight: 600;">Forms to complete:</p>
            <ul style="padding-left: 18px;">
                <?php foreach ($forms as $form): ?>
                    <li>
                        <a href="<?= e($form['url']) ?>" style="color: #1f2937;">
                            <?= e($form['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div id="footer" style="padding: 10px; text-align: center; margin-top: 10px;
                border-top: 1px solid var(--bs-border-color, #e2e8f0); background: #FAFAFA;">
        <a href="<?= $settings['company_link'] ?>" style="text-decoration: none;">
            <?= e($settings['company_name']) ?>
        </a>
    </div>
</div>

</body>
</html>
