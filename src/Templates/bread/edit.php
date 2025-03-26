<section
    class="<?= ['full' => 'w-full', '7xl' => 'max-w-7xl', '6xl' => 'max-w-6xl', '5xl' => 'max-w-5xl', '4xl' => 'max-w-4xl', '3xl' => 'max-w-3xl', '2xl' => 'max-w-2xl', 'xl' => 'max-w-xl', 'lg' => 'max-w-lg', 'mx' => 'max-w-md', 'sm' => 'max-w-sm'][$bread->config['customize']['width']['form'] ?? 'full'] ?> mx-auto">
    <!-- Bread Page Heading Part START -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between gap-3">
        <h2 class="font-bold text-2xl text-primary-800 leading-tight">
            <?= _e($bread->getModel()) ?>
        </h2>
        <?php if (
            isset($bread->getConfig('action', [])['delete']) &&
            (!isset($bread->getConfig('action', [])['delete']['when'])) ||
            call_user_func($bread->getConfig('action')['delete']['when'], $bread->getModel())
        ): ?>
            <a href="<?= _e(route_url($bread->getConfig('route')) . '/' . sprintf($bread->getConfig('action')['delete']['route'], $bread->getModel()->id)) ?>"
                class="px-3 inline-block w-max py-2 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-red-600 shadow-sm shadow-red-200 rounded-lg hover:bg-red-500 focus:outline-hidden focus:ring-3 focus:ring-red-300/80">
                <?= _e($bread->getConfig('action')['delete']['title'] ?? __('delete')) ?>
            </a>
        <?php endif ?>
    </div> <!-- Bread Page Heading Part END -->

    <?php
    // Include custom menu partial
    if ($bread->getConfig('show_menu', true)) {
        if (isset($bread->getConfig('partials_inc', [])['menu'])) {
            echo $bread->partial($bread->getConfig('partials_inc', [])['menu'], ['bread' => $bread]);
        } else {
            // Include default menu partial
            echo $bread->partial(__DIR__ . '/inc/menu', ['bread' => $bread]);
        }
    }

    ?>
    <!-- Bread Create Form START -->
    <form action="<?= request_url() ?>" method="post" enctype="multipart/form-data" x-ref="breadForm">
        <?= csrf() ?>
        <?= $bread->partial(__DIR__ . '/inc/form', ['bread' => $bread]) ?>
        <?php $submit_buttons = $bread->getConfig('submit_buttons', ['save', 'save_and_continue', 'cancel']); ?>
        <!-- Form action buttons -->
        <div class="flex flex-col md:flex-row gap-3 md:items-center mt-8 py-2">
            <input type="hidden" x-ref="breadFormAction" name="_save" value="1">
            <button type="submit" x-ref="breadFormSubmit" class="hidden"></button>
            <?php if (in_array('save', $submit_buttons)): ?>
                <button type="button" x-on:click="$refs.breadFormAction.name = '_save', $refs.breadFormSubmit.click()"
                    class="px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-accent-600 shadow-sm shadow-accent-200 rounded-lg hover:bg-accent-500 focus:outline-hidden focus:ring-3 focus:ring-accent-300/80"><?= _e(__('save changes')) ?></button>
            <?php endif ?>
            <?php if (in_array('save_and_continue', $submit_buttons)): ?>
                <button type="button" x-on:click="$refs.breadFormAction.name = '_save_edit', $refs.breadFormSubmit.click()"
                    class="px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow-sm shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-hidden focus:ring-3 focus:ring-primary-300/80"><?= _e(__('save & continue editing')) ?></button>
            <?php endif ?>
            <?php if (in_array('cancel', $submit_buttons)): ?>
                <a href="<?= _e(route_url($bread->config['route'])) ?>"
                    class="text-center md:text-left px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow-sm shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-hidden focus:ring-3 focus:ring-primary-300/80"><?= _e(__('cancel')) ?></a>
            <?php endif ?>
        </div>
    </form> <!-- Bread Create Form END -->
</section>