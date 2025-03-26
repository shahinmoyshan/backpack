<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', $manager->getConfig('title', ''))
    ->set('breadcrumb', $manager->getBreadcrumb());

?>

<!-- Render the settings index START -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Title -->
    <h2 class="font-bold text-2xl text-primary-800 leading-tight mb-6"><?= _e($manager->getConfig('title')) ?></h2>

    <!-- Settings Form START -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($manager->getSections() as $section): ?>
            <a href="<?= _e(route_url($manager->getConfig('route'), ['id' => $section['id']])) ?>"
                class="bg-white border border-primary-200 hover:bg-primary-50 shadow-xs hover:shadow-sm px-6 py-5 rounded-sm grid grid-cols-8 group">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-7 sm:size-8 text-primary-700">
                        <?php if (isset($section['icon'])): ?>
                            <?= $section['icon'] ?>
                        <?php else: ?>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                        <?php endif ?>
                    </svg>
                </div>
                <div class="col-span-6">
                    <h3 class="font-bold text-lg text-primary-800 leading-tight mb-1"><?= _e($section['title']) ?></h3>
                    <p class="text-sm text-primary-600"><?= _e($section['description'] ?? '') ?></p>
                </div>
                <div class="flex items-center justify-end opacity-50 group-hover:opacity-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
            </a>
        <?php endforeach ?>
    </div> <!-- Settings Form END -->
</div>
<!-- Render the settings index END -->