<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', $manager->getConfig('title', ''))
    ->set('breadcrumb', $manager->getBreadcrumb());

$form = $manager->getForm()
    ->configure(__DIR__ . '/../bread/inc/configurator.php');
?>

<!-- Render the settings index START -->
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Settings Form START -->
    <div class="grid grid-cols-8 bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="col-span-2 bg-primary-800 max-h-[85vh] overflow-y-auto">
            <?php foreach ($manager->getSections() as $section): ?>
                <?php $is_active = $manager->getId() === $section['id'] ?>
                <a href="<?= _e(route_url($manager->getConfig('route'), ['id' => $section['id']])) ?>"
                    class="flex items-center gap-2 px-4 py-4 <?= $is_active ? 'bg-primary-700/50' : 'hover:bg-primary-700/50' ?> border-primary-700/75 border-b last:border-0 text-white group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 opacity-75">
                        <?php if (isset($section['icon'])): ?>
                            <?= $section['icon'] ?>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                        <?php endif ?>
                    </svg>
                    <h3 class="font-medium text-[0.95rem]"><?= _e($section['title']) ?></h3>
                    <span class="ml-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="size-4 <?= _e($is_active ? 'opacity-100' : 'opacity-50 group-hover:opacity-100') ?> transition">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </span>
                </a>
            <?php endforeach ?>
        </div>
        <div class="col-span-6 border border-primary-200 border-l-0 sm:rounded-r-lg">
            <?php $section = $manager->getCurrentSection(); ?>
            <form action="<?= request_url() ?>" method="post" enctype="multipart/form-data">
                <?= csrf() ?>
                <div class="px-6 py-4 flex items-center justify-between border-b border-primary-300">
                    <div>
                        <h3 class="font-medium text-lg"><?= _e($section['title']) ?></h3>
                        <p class="text-sm text-primary-600"><?= _e($section['description'] ?? '') ?></p>
                    </div>
                    <button
                        class="px-4 py-2 font-medium text-white transition-colors duration-300 text-sm transform bg-accent-600 rounded-sm hover:bg-accent-500 focus:outline-hidden focus:ring-3 focus:ring-accent-300/80"><?= __e('save changes') ?></button>
                </div>
                <div class="px-8 h-[75vh] overflow-y-auto">
                    <?php foreach ($manager->getFields() as $field) {
                        if (!isset($field['name'])) {
                            continue;
                        }

                        echo $form->renderField(
                            $field['name'],
                            array_merge(['value' => setting($field['name'])], $field)
                        );
                    } ?>
                </div>
            </form>
        </div>
    </div> <!-- Settings Form END -->
</div>
<!-- Render the settings index END -->