<td class="px-4 py-4">
    <div class="flex w-max items-center gap-2 md:gap-3">
        <a href="<?= _e(route_url('admin.cms.pages') . '/' . $item->id . '/edit') ?>">
            <?php if (!empty($item->thumbnail)): ?>
                <img src="<?= media_url($item->thumbnail[1]) ?>" class="w-12 h-12 rounded-sm" alt="user avatar">
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-12 text-primary-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            <?php endif ?>
        </a>
        <div>
            <p class="flex items-center gap-2 mb-0.5">
                <span class="block text-sm font-medium"><?= _e($item->title) ?></span>
                <?php if ($item->status === 'draft'): ?>
                    <span
                        class="bg-primary-600 inline-block rounded-sm text-white px-1.5 text-[0.8rem]"><?= __e('draft') ?></span>
                <?php endif ?>
            </p>
            <a href="<?= _e(url($item->slug)) ?>"
                class="flex items-center gap-1 text-[0.8rem] text-primary-700 hover:underline hover:text-primary-800"
                target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                </svg>
                <?= _e($item->slug) ?>
            </a>
        </div>
    </div>
</td>