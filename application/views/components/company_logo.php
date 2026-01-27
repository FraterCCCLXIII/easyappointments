<?php
/**
 * Company logo.
 *
 * Local variables.
 *
 * @var string $company_name
 * @var string $company_logo
 * @var int|string|null $height
 * @var string|null $class
 */

$logo_height = $height ?? 32;
$logo_class = $class ?? '';
$logo_style = is_numeric($logo_height) ? "height: {$logo_height}px;" : "height: {$logo_height};";
?>

<?php if (!empty($company_logo)): ?>
    <img src="<?= e($company_logo) ?>" alt="<?= e($company_name) ?>" style="<?= e($logo_style) ?>"
         class="<?= e($logo_class) ?>">
<?php else: ?>
    <svg role="img" aria-label="<?= e($company_name) ?>" xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 241.07 169.55" style="<?= e($logo_style) ?>" class="<?= e($logo_class) ?>"
         fill="currentColor">
        <path
            d="M241.07,0v8.86c0,23.94-17.54,43.85-40.45,47.57-3.43,19.84-19.03,35.57-38.81,39.19-3.85,22.75-23.69,40.13-47.52,40.13H33.81l17.72-17.72h62.76c13.73,0,25.36-9.12,29.17-21.62h-70.31l17.72-17.73h62.24c13.73,0,25.36-9.12,29.17-21.62h-69.79l17.72-17.72h62.65c13.73,0,25.37-9.12,29.17-21.62H59.63c-19.38,0-41.91,21.72-41.91,49.72v84.39L0,169.55v-102.11C0,32.14,28.42,0,59.63,0h181.44Z"/>
    </svg>
<?php endif; ?>
