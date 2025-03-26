<?php
if (!isset($field['id'])) {
    $field['id'] = 'switch_' . uniqid();
}
?>
<label
    class="inline-flex items-center cursor-pointer select-none <?= _e(isset($field['hasError']) && $field['hasError'] ? '{checkboxErrorClass}' : '') ?>">
    <!-- hidden switch value -->
    <input type="checkbox" value="on" <?= _e(isset($field['value']) && $field['value'] ? 'checked' : '') ?>
        <?= $form->renderAttributes($field['attrs'] ?? []) ?> <?= _e(isset($field['required']) && $field['required'] ? 'required' : '') ?> name="<?= _e($field['name'] ?? '') ?>" id="<?= _e($field['id']) ?>" class="sr-only peer">
    <!-- Switch -->
    <div
        class="relative w-9 h-5 bg-primary-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-primary-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-accent-600">
    </div>
    <!-- Label of the switch -->
    <span class="ms-2.5 text-sm font-medium text-primary-800"><?= _e($field['placeholder'] ?? '') ?></span>
</label>