<?php

$template->set('title', __($title ?? 'login') . ' - ' . __('admin') . ' | ' . panel()->getConfig('title', ''));
$isAjax = request()->accept('application/json');

if (!$isAjax):
    ?>
    <!DOCTYPE html>
    <html lang="<?= env('lang', 'en') ?>">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $template->get('title', '') ?></title>

        <?php
        $favicon = panel()->getConfig('favicon', '');
        if (!empty($favicon)): ?>
            <link rel="icon" type="image/x-icon"
                href="<?= _e(strpos($favicon, 'http') === false ? media_url($favicon) : $favicon) ?>">
        <?php endif ?>

        <!-- Tailwind Global Style Config -->
        <?= tailwind() ?>
    </head>

    <body class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-accent-50 to-accent-300/75">
        <!-- Preloader -->
        <?= tailwind()->getPreloaderElement() ?>

        <!-- Main Container -->
        <div id="app" class="w-full max-w-sm mx-auto bg-white rounded-md shadow-md">
        <?php endif ?>
        <div>
            <!-- Auth Pages Content START -->
            <?= $content ?>
            <!-- Auth Pages Content END -->
        </div>
        <?php if (!$isAjax): ?>
        </div>
    </body>

    </html>
<?php endif ?>