<td class="px-4 py-4">
    <a href="<?= _e(route_url('admin.cms.blogs') . '/' . $item->id . '/edit') ?>">
        <?php if (!empty($item->thumbnail)): ?>
            <img src="<?= media_url($item->thumbnail[1]) ?>" class="w-16 h-14 object-cover rounded-sm" alt="Blog Thumbnail">
        <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-14 text-primary-600">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
        <?php endif ?>
    </a>
</td>
<td class="px-4 py-4">
    <a href="<?= _e(route_url('blogs.details', ['slug' => $item->slug])) ?>"
        class="text-primary-800 hover:underline hover:text-primary-900 font-medium" target="_blank">
        <?= _e($item->title) ?>
    </a>
</td>
<td class="p-4">
    <?= $item->user ?? '&mdash;' ?>
</td>
<td class="p-4">
    <span
        class="px-1.5 text-[0.8rem] py-1 rounded-sm <?= ['published' => 'bg-accent-100 text-accent-700', 'draft' => 'bg-slate-200 text-slate-800'][$item->status] ?? 'bg-primary-100 text-primary-700' ?>"><?= __e($item->status) ?></span>
</td>
<td class="p-4">
    <?= pretty_time($item->created_at) ?>
</td>