<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', $manager->getConfig('title', ''))
    ->set('breadcrumb', $manager->getBreadcrumb());

$form = $manager->getForm()
    ->configure(__DIR__ . '/../bread/inc/configurator.php');

?>

<!-- Render the settings index START -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Title -->
    <h2 class="font-bold text-2xl text-primary-800 leading-tight mb-6"><?= _e($manager->getConfig('title')) ?></h2>

    <!-- Settings Form START -->
    <div class="bg-white shadow-lg sm:rounded-lg py-6 px-8 md:py-8 md:px-12 lg:py-10 lg:px-16">
        <form action="<?= request_url() ?>" method="post" enctype="multipart/form-data">
            <?php $section = $manager->getCurrentSection() ?>
            <div class="mb-6">
                <h3 class="font-bold text-lg text-primary-800 leading-tight mb-1.5"><?= $section['title'] ?></h3>
                <p class="text-sm text-primary-600"><?= $section['description'] ?? '' ?></p>
            </div>
            <?php foreach ($manager->getFields() as $field) {
                if (!isset($field['name'])) {
                    continue;
                }
                echo $form->renderField(
                    $field['name'],
                    array_merge(['value' => setting($field['name'])], $field)
                );
            } ?>
            <?= csrf() ?>
            <button type="submit"
                class="px-6 py-3 font-medium w-full mt-4 tracking-wide text-white transition-colors duration-300 transform bg-accent-600 shadow shadow-accent-200 rounded-lg hover:bg-accent-500 focus:outline-none focus:ring focus:ring-accent-300 focus:ring-opacity-80"><?= _e(__('save settings')) ?></button>
        </form>
    </div> <!-- Settings Form END -->
</div>
<!-- Render the settings index END -->