<td class="px-4 py-4">
    <a href="<?= _e(route_url('admin.super.users') . '/' . $item->id) ?>"
        class="flex w-max items-center gap-2 md:gap-3">
        <?php if (!empty($item->image)): ?>
            <img src="<?= media_url($item->image) ?>" class="w-8 h-8 rounded-full" alt="user avatar">
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-8 text-primary-600">
                <path fill-rule="evenodd"
                    d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                    clip-rule="evenodd" />
            </svg>
        <?php endif ?>
        <div>
            <span class="flex items-center gap-2">
                <span class="block text-sm"><?= _e($item->full_name ?? $item->username) ?></span>
                <?php if ($item->status === 'inactive'): ?>
                    <span
                        class="bg-red-600 inline-block rounded text-white px-1.5 text-[0.8rem]"><?= __e('inactive') ?></span>
                <?php endif ?>
            </span>
            <span class="block text-xs text-primary-600"><?= _e($item->email) ?></span>
        </div>
    </a>
</td>
<td class="px-4 py-4">
    <div class="w-max">
        <?php if (isset($item->role?->name)): ?>
            <span class="block text-xs bg-indigo-600 text-white px-1.5 py-0.5 w-max mb-1 rounded">
                <?= _e($item->role->name) ?>
            </span>
        <?php else: ?>
            <span class="block text-xs bg-primary-200 text-primary-900 px-1.5 py-0.5 w-max mb-1 rounded">
                <?= _e(__('no role')) ?>
            </span>
        <?php endif ?>
        <span class="block text-xs text-primary-600" title="<?= _e(__('last login')) ?>">
            <?php if (isset($item->last_login)): ?>
                <?= date('d M, Y g:i A', strtotime($item->last_login)) ?>
            <?php else: ?>
                <span class="opacity-75 text-[0.7rem]"><?= _e(__('not logged yet')) ?></span>
            <?php endif ?>
        </span>
    </div>
</td>