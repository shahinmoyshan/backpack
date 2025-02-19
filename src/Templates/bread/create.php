<section
    class="<?= ['full' => 'w-full', '7xl' => 'max-w-7xl', '6xl' => 'max-w-6xl', '5xl' => 'max-w-5xl', '4xl' => 'max-w-4xl', '3xl' => 'max-w-3xl', '2xl' => 'max-w-2xl', 'xl' => 'max-w-xl', 'lg' => 'max-w-lg', 'mx' => 'max-w-md', 'sm' => 'max-w-sm'][$bread->config['customize']['width']['form'] ?? 'full'] ?> mx-auto">

    <!-- Bread Page Heading Part START -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between gap-3">
        <h2 class="font-bold text-2xl text-primary-800 leading-tight">
            <?= _e(__('create %s', $bread->getConfig('title_singular'))) ?>
        </h2>
    </div> <!-- Bread Page Heading Part END -->

    <!-- Bread Create Form START -->
    <form action="<?= request_url() ?>" method="post" enctype="multipart/form-data" x-ref="breadForm">
        <?= csrf() ?>
        <?= $bread->partial(__DIR__ . '/inc/form', ['bread' => $bread]) ?>
        <?php $submit_buttons = $bread->getConfig('submit_buttons', ['save', 'save_and_continue', 'cancel']); ?>
        <div class="flex flex-col md:flex-row gap-3 md:items-center mt-8 py-2">
            <input type="hidden" x-ref="breadFormAction" name="_create" value="1">
            <button type="submit" x-ref="breadFormSubmit" class="hidden"></button>
            <?php if (in_array('save', $submit_buttons)): ?>
                <button type="button" x-on:click="$refs.breadFormAction.name = '_create', $refs.breadFormSubmit.click()"
                    class="px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-accent-600 shadow shadow-accent-200 rounded-lg hover:bg-accent-500 focus:outline-none focus:ring focus:ring-accent-300 focus:ring-opacity-80"><?= _e(__('create')) ?></button>
            <?php endif ?>
            <?php if (in_array('save_and_continue', $submit_buttons)): ?>
                <button type="button" x-on:click="$refs.breadFormAction.name = '_create_add', $refs.breadFormSubmit.click()"
                    class="px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80"><?= _e(__('create & create another')) ?></button>
            <?php endif ?>
            <?php if (in_array('cancel', $submit_buttons)): ?>
                <a href="<?= _e(route_url($bread->config['route'])) ?>"
                    class="text-center md:text-left px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80"><?= _e(__('cancel')) ?></a>
            <?php endif ?>
        </div>
    </form> <!-- Bread Create Form END -->
</section>