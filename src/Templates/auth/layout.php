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

        <style>
            #preloader {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 9999;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: rgb(var(--color-primary-100) / 100);
            }

            .lds-ellipsis,
            .lds-ellipsis div {
                box-sizing: border-box;
            }

            .lds-ellipsis {
                display: inline-block;
                position: relative;
                width: 80px;
                height: 80px;
            }

            .lds-ellipsis div {
                position: absolute;
                top: 33.33333px;
                width: 13.33333px;
                height: 13.33333px;
                border-radius: 50%;
                background: rgb(var(--color-accent-700) / 100);
                animation-timing-function: cubic-bezier(0, 1, 1, 0);
            }

            .lds-ellipsis div:nth-child(1) {
                left: 8px;
                animation: lds-ellipsis1 0.6s infinite;
            }

            .lds-ellipsis div:nth-child(2) {
                left: 8px;
                animation: lds-ellipsis2 0.6s infinite;
            }

            .lds-ellipsis div:nth-child(3) {
                left: 32px;
                animation: lds-ellipsis2 0.6s infinite;
            }

            .lds-ellipsis div:nth-child(4) {
                left: 56px;
                animation: lds-ellipsis3 0.6s infinite;
            }

            @keyframes lds-ellipsis1 {
                0% {
                    transform: scale(0);
                }

                100% {
                    transform: scale(1);
                }
            }

            @keyframes lds-ellipsis3 {
                0% {
                    transform: scale(1);
                }

                100% {
                    transform: scale(0);
                }
            }

            @keyframes lds-ellipsis2 {
                0% {
                    transform: translate(0, 0);
                }

                100% {
                    transform: translate(24px, 0);
                }
            }
        </style>

        <script>
            window.addEventListener('load', function () {
                const preloader = document.getElementById('preloader');
                if (preloader) {
                    preloader.remove();
                }
            });
        </script>

        <!-- Importing Vite Scripts -->
        <?= panel()->getConfig('vite', '') ?>
    </head>

    <body class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-accent-50 to-accent-300/75">
        <!-- Preloader -->
        <div id="preloader">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

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