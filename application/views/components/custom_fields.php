<?php
/**
 * Local variables.
 *
 * @var bool $disabled (false)
 * @var array $values ([])
 * @var string|null $name_prefix (null)
 * @var bool $show_all (false)
 * @var array|null $fields (null)
 */

$disabled = $disabled ?? false;
$values = $values ?? [];
$name_prefix = $name_prefix ?? null;
$show_all = $show_all ?? false;
$fields = $fields ?? null;
?>

<?php if (is_array($fields)): ?>
    <?php foreach ($fields as $field): ?>
        <?php
        $display = (bool) ($field['is_displayed'] ?? true);
        if (!$display && !$show_all) {
            continue;
        }
        $hidden_class = !$display ? ' d-none custom-field-hidden' : '';
        $field_id = (int) ($field['id'] ?? 0);
        ?>
        <div class="mb-3 custom-field-item<?= $hidden_class ?>" data-custom-field-id="<?= $field_id ?>">
            <label for="custom-field-<?= $field_id ?>" class="form-label">
                <?= $field['label'] ? e($field['label']) : lang('custom_field') ?>
                <?php if (!empty($field['is_required'])): ?>
                    <span class="text-danger" <?= $disabled ? 'hidden' : '' ?>>*</span>
                <?php endif; ?>
            </label>
            <input type="text"
                   id="custom-field-<?= $field_id ?>"
                   class="<?= !empty($field['is_required']) ? 'required' : '' ?> form-control custom-field-input"
                   maxlength="255"
                   data-custom-field-id="<?= $field_id ?>"
                   <?= $name_prefix ? 'name="' . e($name_prefix) . '[' . $field_id . ']"' : '' ?>
                   value="<?= e($values[$field_id] ?? '') ?>"
                   <?= $disabled ? 'disabled' : '' ?>/>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php
        $display = (bool) setting('display_custom_field_' . $i);
        if (!$display && !$show_all) {
            continue;
        }
        $hidden_class = !$display ? ' d-none custom-field-hidden' : '';
        ?>
            <div class="mb-3 custom-field-item<?= $hidden_class ?>" data-custom-field-index="<?= $i ?>">
                <label for="custom-field-<?= $i ?>" class="form-label">
                    <?= setting('label_custom_field_' . $i) ?: lang('custom_field') . ' #' . $i ?>
                    <?php if (setting('require_custom_field_' . $i)): ?>
                        <span class="text-danger" <?= $disabled ? 'hidden' : '' ?>>*</span>
                    <?php endif; ?>
                </label>
                <input type="text" id="custom-field-<?= $i ?>"
                       class="<?= setting('require_custom_field_' . $i) ? 'required' : '' ?> form-control"
                       maxlength="120"
                       <?= $name_prefix ? 'name="' . e($name_prefix) . '[custom_field_' . $i . ']"' : '' ?>
                       value="<?= e($values['custom_field_' . $i] ?? '') ?>"
                       <?= $disabled ? 'disabled' : '' ?>/>
            </div>
    <?php endfor; ?>
<?php endif; ?>
