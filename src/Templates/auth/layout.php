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

        <!-- Tailwind Global Style Config -->
        <?= tailwind() ?>

        <!-- Importing Vite Scripts -->
        <?= panel()->getConfig('vite', '') ?>
    </head>

    <body class="relative min-h-screen flex items-center justify-center bg-primary-100">
        <div id="app" class="w-full max-w-sm mx-auto bg-white rounded-lg shadow-md">
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