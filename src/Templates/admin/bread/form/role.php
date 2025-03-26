<div class="bg-white border border-primary-200 shadow-xs sm:rounded-lg px-6 py-2 mb-8">
    <h3 class="text-xl mb-2 border-b border-primary-300 py-3"><?= __e('General') ?></h3>
    <div><?= $form->renderField('name') ?></div>
    <div><?= $form->renderField('description', ['type' => 'textarea']) ?></div>
</div>

<?php
// Render hidden fields
foreach ($form->getFields() as $field) {
    if ($field['type'] !== 'hidden') {
        continue;
    }

    echo <<<INPUT
        <input type="hidden" name="{$field['name']}" value="{$field['value']}">
    INPUT;
}

// Render role permissions
if (!($form->hasField('id') && intval($form->getValue('id', 0)) === user('admin_roles_id'))):
    $roleField = $form->getField('role_permissions');
    $permissions = collect($roleField['options'] ?? [])
        ->map(function ($name, $id) {
            // check, if the name contains "Change {string} Settings". Then the group will be "Settings"
            if (stripos($name, 'change ') === 0 && stripos($name, ' settings') !== false) {
                $group = 'Settings';
            } elseif (stripos($name, 'view analytics') === 0 || stripos($name, 'view overview') === 0) {
                $group = 'analytics';
            } else {
                $group = trim(str_ireplace(['change ', 'create ', 'view ', 'edit ', 'delete '], '', $name));
            }
            return ['name' => $name, 'id' => $id, 'group' => $group];
        })
        ->group('group')
        ->all();

    $checkedPermission = (array) $roleField['value'] ?? [];
    ?>
    <div class="bg-white border border-primary-200 shadow-sm sm:rounded-lg px-6 py-2" permissiongroup x-init="$el.querySelectorAll('button[permission-all]').forEach(b => b.addEventListener('click', (event) => {
        const action = b.getAttribute('permission-all');
        const group = event.target.closest('div[permissiongroup]');
        if (group) {
            const inputs = group.querySelectorAll(`input[${action}]`);
            if (inputs) {
                inputs.forEach(i => i.checked = true);
            }
        }
    }))">
        <?php if (!empty($permissions)): ?>
            <div class="flex mb-2 border-b border-primary-300 py-3 items-center justify-between">
                <h3 class="text-xl"><?= __e('Permissions') ?></h3>
                <div class="flex items-center gap-2">
                    <button permission-all="allow" type="button"
                        class="text-sm px-2.5 py-1 font-medium hover:bg-zinc-300 rounded-xs border border-zinc-400/75 bg-zinc-200 text-zinc-800"><?= __e('Allow All') ?></button>
                    <button permission-all="deny" type="button"
                        class="text-sm px-2.5 py-1 font-medium hover:bg-zinc-300 rounded-xs border border-zinc-400/75 bg-zinc-200 text-zinc-800"><?= __e('Deny All') ?></button>
                </div>
            </div>
            <?php foreach ($permissions as $group => $permission):
                $groupId = slugify($group); ?>
                <div class="mb-4" permissiongroup>
                    <?php if (count($permission) > 1): ?>
                        <div class="mb-2 border-b border-primary-200 py-2 flex items-center justify-between">
                            <h3 class="font-medium"><?= _e(ucwords($group)) ?></h3>
                            <div class="flex justify-end items-center gap-2">
                                <button permission-all="allow" type="button"
                                    class="text-[0.8rem] px-1.5 py-0.5 font-medium hover:bg-zinc-300 rounded-xs border border-zinc-400/75 bg-zinc-200 text-zinc-800"><?= __e('Allow All') ?></button>
                                <button permission-all="deny" type="button"
                                    class="text-[0.8rem] px-1.5 py-0.5 font-medium hover:bg-zinc-300 rounded-xs border border-zinc-400/75 bg-zinc-200 text-zinc-800"><?= __e('Deny All') ?></button>
                            </div>
                        </div>
                    <?php endif ?>
                    <div class="space-y-2 <?= _e(count($permission) > 1 ? 'md:ml-2 xl:ml-4' : 'font-medium border-t pt-4') ?>">
                        <?php foreach ($permission as $perm):
                            $checked = in_array($perm['id'], $checkedPermission);
                            ?>
                            <div class="flex items-center justify-between">
                                <p class="text-sm"><?= _e(ucwords($perm['name'])) ?></p>
                                <div class="flex justify-end items-center gap-4">
                                    <label class="flex items-center gap-1 text-sm">
                                        <input allow type="radio" <?= _e($checked ? 'checked' : '') ?>
                                            name="role_permissions[<?= _e($perm['id']) ?>]" value="<?= _e($perm['id']) ?>">
                                        <?= __e('Allow') ?>
                                    </label>
                                    <label class="flex items-center gap-1 text-sm">
                                        <input deny type="radio" <?= _e(!$checked ? 'checked' : '') ?>
                                            name="role_permissions[<?= _e($perm['id']) ?>]" value="">
                                        <?= __e('Deny') ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <p class="py-2 text-center text-sm text-primary-600"><?= __e('No permissions found') ?></p>
        <?php endif ?>
    </div>
<?php endif ?>