<?php $menuItems = $bread->getConfig('menu_items', ['view', 'edit', 'history']); ?>

<div
    class="mb-6 flex items-center justify-center gap-2 w-max bg-white p-1.5 border border-primary-200 rounded-lg mx-auto">
    <?php if (in_array('view', $menuItems)): ?>
        <!-- View model menu -->
        <a href="<?= _e(route_url($bread->getConfig('route')) . '/' . $bread->getModel()->id) ?>"
            class="flex gap-1 items-center text-sm font-medium <?= $bread->getAction() === 'view' ? 'bg-primary-100 text-accent-700' : 'hover:bg-primary-100 text-primary-600' ?> px-2 py-1.5 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-6 sm:size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <span class="hidden sm:block"><?= _e(__('view %s', $bread->getConfig('title_singular', ''))) ?></span>
        </a>
    <?php endif ?>

    <!-- edit model menu if enabled for this model -->
    <?php if (
        in_array('edit', $menuItems) && isset($bread->getConfig('action', [])['edit']) &&
        (!isset($bread->getConfig('action', [])['edit']['when'])) ||
        call_user_func($bread->getConfig('action', [])['edit']['when'], $bread->getModel())
    ): ?>
        <a href="<?= _e(route_url($bread->getConfig('route')) . '/' . $bread->getModel()->id . '/edit') ?>"
            class="flex gap-1 items-center text-sm font-medium <?= $bread->getAction() === 'edit' ? 'bg-primary-100 text-accent-700' : 'hover:bg-primary-100 text-primary-600' ?> px-2 py-1.5 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-6 sm:size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            <span class="hidden sm:block"><?= _e(__('edit %s', $bread->getConfig('title_singular', ''))) ?></span>
        </a>
    <?php endif ?>

    <!-- log/history if enabled by model -->
    <?php if (in_array('history', $menuItems) && isset($bread->config['activity_log'])): ?>
        <a href="<?= _e(route_url($bread->getConfig('route')) . '/' . $bread->getModel()->id . '/history') ?>"
            class="flex gap-1 items-center text-sm font-medium <?= $bread->getAction() === 'history' ? 'bg-primary-100 text-accent-700' : 'hover:bg-primary-100 text-primary-600' ?> px-2 py-1.5 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6 sm:size-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 8v5h5v-2h-3V8z"></path>
                <path
                    d="M21.292 8.497a8.957 8.957 0 0 0-1.928-2.862 9.004 9.004 0 0 0-4.55-2.452 9.09 9.09 0 0 0-3.626 0 8.965 8.965 0 0 0-4.552 2.453 9.048 9.048 0 0 0-1.928 2.86A8.963 8.963 0 0 0 4 12l.001.025H2L5 16l3-3.975H6.001L6 12a6.957 6.957 0 0 1 1.195-3.913 7.066 7.066 0 0 1 1.891-1.892 7.034 7.034 0 0 1 2.503-1.054 7.003 7.003 0 0 1 8.269 5.445 7.117 7.117 0 0 1 0 2.824 6.936 6.936 0 0 1-1.054 2.503c-.25.371-.537.72-.854 1.036a7.058 7.058 0 0 1-2.225 1.501 6.98 6.98 0 0 1-1.313.408 7.117 7.117 0 0 1-2.823 0 6.957 6.957 0 0 1-2.501-1.053 7.066 7.066 0 0 1-1.037-.855l-1.414 1.414A8.985 8.985 0 0 0 13 21a9.05 9.05 0 0 0 3.503-.707 9.009 9.009 0 0 0 3.959-3.26A8.968 8.968 0 0 0 22 12a8.928 8.928 0 0 0-.708-3.503z">
                </path>
            </svg>
            <span class="hidden sm:block"><?= _e(__('history')) ?></span>
        </a>
    <?php endif ?>
</div>