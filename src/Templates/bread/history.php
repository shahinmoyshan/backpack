<section
    class="<?= ['full' => 'w-full', '7xl' => 'max-w-7xl', '6xl' => 'max-w-6xl', '5xl' => 'max-w-5xl', '4xl' => 'max-w-4xl', '3xl' => 'max-w-3xl', '2xl' => 'max-w-2xl', 'xl' => 'max-w-xl', 'lg' => 'max-w-lg', 'mx' => 'max-w-md', 'sm' => 'max-w-sm'][$bread->config['customize']['width']['details'] ?? 'full'] ?> mx-auto">
    <!-- Bread Page Heading Part START -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between gap-3">
        <h2 class="font-bold text-2xl text-primary-800 leading-tight">
            <?= _e($bread->getModel()) ?>
        </h2>
    </div> <!-- Bread Page Heading Part END -->

    <?php

    // Include menu partial
    if ($bread->getConfig('show_menu', true)) {
        if (isset($bread->getConfig('partials_inc', [])['menu'])) {
            echo $bread->partial($bread->getConfig('partials_inc', [])['menu'], ['bread' => $bread]);
        } else {
            // Include default menu partial
            echo $bread->partial(__DIR__ . '/inc/menu', ['bread' => $bread]);
        }
    }

    ?>

    <?php

    // Include log partial
    if (isset($bread->getConfig('partials_inc', [])['log'])) {
        echo $bread->partial($bread->getConfig('partials_inc', [])['log'], ['bread' => $bread]);
    } else {
        echo $bread->partial(__DIR__ . '/inc/log', ['bread' => $bread]);
    }

    ?>
</section>