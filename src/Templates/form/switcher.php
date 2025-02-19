<?php
$attrs = $field['attrs'] ?? [];
$required = isset($field['required']) && $field['required'] ? 'required' : '';

?>

<div
    class="grid <?= _e([1 => 'grid-cols-1', 2 => 'grid-cols-2', 3 => 'grid-cols-3', 4 => 'grid-cols-4'][$attrs['grid']['cols'] ?? 2]) ?> <?= _e([4 => 'gap-4', 6 => 'gap-6', 8 => 'gap-8'][$attrs['grid']['gap'] ?? 4]) ?>">
    <?php foreach ($field['options'] as $key => $val) {
        $checked = strval($field['value'] ?? '') === strval($key) ? 'checked' : '';
        ?>
        <label
            class="bg-white hover:bg-primary-50 rounded border p-3 cursor-pointer shadow-sm [&:has(input:checked)]:bg-accent-50/50 [&:has(input:checked)]:border-accent-600 [&:has(input:checked)]:ring-2 [&:has(input:checked)]:ring-accent-600/40 <?= _e($field['class'] ?? '') ?>">
            <input type="radio" name="<?= _e($field['name']) ?>" <?= _e($checked) ?> class="hidden" <?= _e($required) ?>
                <?= _e($form->renderAttributes($attrs)) ?> value="<?= _e($key) ?>" />
            <?= $val ?>
        </label>
        <?php
    } ?>
</div>