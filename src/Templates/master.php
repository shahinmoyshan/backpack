<?php

$template->set('title', trim(__($title ?? 'admin') . ' - ' . panel()->getConfig('title', ''), ' - '));

$isAjax = request()->accept('application/json');

if (!$isAjax):
    ?>
    <!DOCTYPE html>
    <html lang="<?= env('lang', 'en') ?>">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name=”robots” content="noindex, nofollow">
        <title><?= _e($template->get('title')) ?></title>

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

    <body class="antialiased bg-primary-100 text-primary-800 print:bg-white" x-data="{mobileMenuOpen: false}">

        <!-- Preloader -->
        <div id="preloader">
            <div class="lds-ellipsis">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>

        <div id="app">
        <?php endif ?>
        <?php $hide_sidebar ??= request()->query('hide_sidebar', false) !== false; ?>
        <div>
            <!-- Header Part START -->
            <header
                class="print:hidden fixed z-30 top-0 right-0 left-0 <?= _e(!$hide_sidebar ? 'lg:left-64' : '') ?> flex items-center justify-between h-14 md:h-16 px-4 md:px-6 bg-white/85 backdrop-blur border-b">
                <!-- Header Breadcrumb START -->
                <nav class="flex items-center gap-1 md:gap-1.5 max-w-48 sm:max-w-full truncate">
                    <!-- Dashboard Link -->
                    <a href="<?= _e(route_url('admin.dashboard')) ?>"
                        class="transition-colors duration-300 text-primary-700 hover:bg-primary-100 hover:text-primary-800 p-1 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 md:size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                    </a>
                    <!-- Dynamic Breadcrumb Items -->
                    <?php
                    if (isset($breadcrumb) && !empty($breadcrumb)):
                        $index = 0;
                        ?>
                        <?php foreach ($breadcrumb as $route_name => $title): ?>
                            <?php $index++ ?>
                            <!-- Breadcrumb Arrow -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="<?= $index > 1 ? 'hidden sm:block' : '' ?> size-4 opacity-45">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                            <?php if ($route_name === '__active'): ?>
                                <!-- Active Breadcrumb -->
                                <span
                                    class="<?= $index > 1 ? 'hidden sm:block' : '' ?> text-[0.8rem] md:text-sm px-1 md:px-2"><?= _e(__($title)) ?></span>
                            <?php else: ?>
                                <!-- Breadcrumb Link -->
                                <a href="<?= _e(route_url($route_name)) ?>"
                                    class="<?= $index > 1 ? 'hidden sm:block' : '' ?> transition-colors duration-300 text-primary-700 hover:bg-primary-100 hover:text-primary-800 px-1 py-0.5 font-semibold text-[0.8rem] md:text-sm rounded-lg">
                                    <?= _e(__($title)) ?>
                                </a>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </nav> <!-- Header Breadcrumb END -->

                <!-- User Menu START -->
                <div class="flex items-center gap-2" x-data="{ open: false }" x-on:click.away="open && (open = false)">
                    <div class="relative flex items-center">
                        <!-- User Menu Button -->
                        <button x-on:click="open = !open"
                            class="transition-colors duration-300 rounded-lg sm:px-2 sm:py-1 focus:outline-none hover:bg-primary-100">
                            <span class="sr-only"><?= _e(__('user menu')) ?></span>
                            <div class="flex items-center md:-mx-2">
                                <div class="hidden md:mx-2 md:flex md:flex-col md:items-end md:leading-tight">
                                    <!-- User Name -->
                                    <span
                                        class="font-semibold text-[0.8rem] text-primary-800"><?= _e(user('full_name', user('username'))) ?></span>
                                    <!-- User Role -->
                                    <span class="text-xs text-primary-600"><?= _e(user()->role->name) ?></span>
                                </div>
                                <?php if (!empty(user('image'))): ?>
                                    <img class="flex-shrink-0 w-8 h-8 overflow-hidden bg-primary-100 rounded-full md:mx-2"
                                        src="<?= _e(media_url(user('image'))) ?>" alt="<?= _e(user('username')) ?> photo">
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-7 md:size-8 text-primary-800">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                <?php endif ?>
                            </div>
                        </button>

                        <!-- User Menu Popup START -->
                        <div x-cloak x-show="open" x-transition
                            class="absolute right-0 z-50 w-56 py-2 bg-white border shadow-sm rounded-lg top-12 sm:top-14">
                            <!-- User Profile Page -->
                            <a href="<?= _e(route_url('admin.profile')) ?>"
                                class="w-full flex items-center p-3 text-sm text-primary-600 capitalize transition-colors duration-300 transform hover:bg-primary-50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 mx-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                <span class="mx-1"><?= _e(__('edit profile')) ?></span>
                            </a>
                            <hr class="border-primary-200">
                            <!-- Sign Out Button -->
                            <form action="<?= _e(route_url('admin.auth.logout')) ?>" method="POST">
                                <?= csrf() ?>
                                <button type="submit"
                                    class="w-full flex items-center p-3 text-sm text-primary-600 capitalize transition-colors duration-300 transform hover:bg-primary-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mx-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                                    </svg>
                                    <span class="mx-1"><?= _e(__('sign out')) ?></span>
                                </button>
                            </form>
                        </div> <!-- User Menu Popup END -->
                    </div>

                    <!-- Mobile Menu Button -->
                    <button x-on:click="mobileMenuOpen = true"
                        class="<?= _e(!$hide_sidebar ? 'lg:hidden' : '') ?> p-1.5 text-primary-700 rounded-lg focus:outline-none hover:bg-primary-100">
                        <!-- Open Menu Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2" x-show="!mobileMenuOpen">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>

                        <!-- Close Menu Icon -->
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" x-cloak x-show="mobileMenuOpen""><path stroke-linecap=" round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div> <!-- User Menu END -->
            </header> <!-- Header Part END -->

            <!-- Sidebar Overlay -->
            <div :class="'fixed inset-0 z-30 bg-black/30 ' + (mobileMenuOpen ? '<?= _e(!$hide_sidebar ? 'lg:hidden block' : '') ?>' : 'lg:hidden hidden')"
                x-on:click="mobileMenuOpen && (mobileMenuOpen = false)"></div>

            <!-- NOTE: in this menu used heroicons (https://heroicons.com/outline) without <svg></svg> tag -->
            <!-- Sidebar START -->
            <aside simplescroll x-cloak
                :class="'print:hidden fixed inset-y-0 left-0 z-40 flex flex-col w-60 md:w-64 min-h-screen overflow-y-auto text-primary-100 transition duration-200 transform bg-primary-800 <?= _e(!$hide_sidebar ? 'lg:translate-x-0' : '') ?> ' + (mobileMenuOpen ? 'translate-x-0 ease-in' : '-translate-x-full ease-out')">
                <!-- Shop Page URL -->
                <a href="<?= _e(url('/')) ?>" native
                    class="flex gap-4 items-center justify-start w-full px-6 pt-6 mb-6 group">
                    <?php
                    $image = panel()->getConfig('sidebar_image');
                    $icon = panel()->getConfig('sidebar_icon');
                    if (!empty($image)): ?>
                        <img src="<?= $image ?>" class="w-12 h-12 object-contain" alt="Sidebar Image">
                    <?php else: ?>
                        <span class="bg-white rounded-lg w-11 h-11 flex items-center justify-center">
                            <?= $icon ?? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-7 text-accent-600" fill="currentColor"><path d="M16.997 20c-.899 0-1.288-.311-1.876-.781-.68-.543-1.525-1.219-3.127-1.219-1.601 0-2.446.676-3.125 1.22-.587.469-.975.78-1.874.78-.897 0-1.285-.311-1.872-.78C4.444 18.676 3.601 18 2 18v2c.898 0 1.286.311 1.873.78.679.544 1.523 1.22 3.122 1.22 1.601 0 2.445-.676 3.124-1.219.588-.47.976-.781 1.875-.781.9 0 1.311.328 1.878.781.679.543 1.524 1.219 3.125 1.219s2.446-.676 3.125-1.219C20.689 20.328 21.1 20 22 20v-2c-1.602 0-2.447.676-3.127 1.219-.588.47-.977.781-1.876.781zM6 8.5 4 9l2 8h.995c1.601 0 2.445-.676 3.124-1.219.588-.47.976-.781 1.875-.781.9 0 1.311.328 1.878.781.679.543 1.524 1.219 3.125 1.219H18l.027-.107.313-1.252L20 9l-2-.5V5.001a1 1 0 0 0-.804-.981L13 3.181V2h-2v1.181l-4.196.839A1 1 0 0 0 6 5.001V8.5zm2-2.681 4-.8 4 .8V8l-4-1-4 1V5.819z"></path></svg>' ?>
                        </span>
                    <?php endif ?>
                    <span
                        class="text-xl font-medium opacity-90 group-hover:opacity-100 transition"><?= _e(panel()->getConfig('site_name', 'admin')) ?></span>
                </a>

                <!-- Menu Items START -->
                <nav class="flex flex-col space-y-2 px-4">
                    <?php
                    // Get current path.
                    $current_path = request()->getPath();
                    foreach (panel()->getMenu() as $route_name => $item):
                        // Check if route is active.
                        $route_path = router()->route($route_name);
                        $is_active = strpos($current_path, $route_path) !== false;
                        if ($route_path === '/admin' && $current_path !== '/admin') {
                            $is_active = false;
                        }

                        if (isset($item['permissions']) && !has_any_permission($item['permissions'])) {
                            continue;
                        }
                        ?>
                        <?php if (isset($item['submenu'])): // Has Submenu ?>
                            <!-- Menu Item With Submenu START -->
                            <div class="relative group <?= $is_active ? 'bg-white' : 'hover:bg-white' ?> rounded-lg"
                                x-data="{open: <?= $is_active ? 'true' : 'false' ?>}">
                                <!-- Submenu Title and Toggler Button -->
                                <button x-on:click="open = !open"
                                    class="p-3 flex items-center gap-3 focus:outline-none <?= $is_active ? 'text-accent-700' : 'group-hover:text-accent-700' ?> w-full">
                                    <?php if (isset($item['icon']) && strpos($item['icon'], '<svg ') === 0): ?>
                                        <?= $item['icon'] ?>
                                    <?php elseif (isset($item['icon'])): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="group-hover:stroke-accent-700 size-6">
                                            <?= $item['icon'] ?>
                                        </svg>
                                    <?php endif ?>
                                    <span class="font-medium text-sm truncate"><?= _e($item['title']) ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                        :class="open ? 'size-4 ml-auto opacity-85 transform duration-100 rotate-180' : 'size-4 ml-auto opacity-85 transform duration-100'">
                                        <path fill-rule="evenodd"
                                            d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div class="flex flex-col gap-1 px-4 pb-4" x-cloak x-show="open" x-transition>
                                    <?php
                                    // Loop through submenu items and render them.
                                    foreach ($item['submenu'] as $route_name => $subItem):
                                        // Check if submenu item is active.
                                        $child_route_path = router()->route($route_name);
                                        $is_child_active = strpos($current_path, $child_route_path) !== false;

                                        if (isset($subItem['permissions']) && !has_any_permission($subItem['permissions'])) {
                                            continue;
                                        }
                                        ?>
                                        <!-- Submenu Item -->
                                        <a class="<?= $is_active ? ($is_child_active ? 'font-medium text-accent-700' : 'text-primary-800') : 'group-hover:text-primary-800' ?> <?= $is_child_active ? 'bg-primary-100 font-medium' : 'hover:bg-primary-100 group-hover:hover:text-accent-700' ?> rounded-lg text-sm p-2 flex items-center gap-2 truncate"
                                            href="<?= _e(url($child_route_path)) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <?= $subItem['icon'] ?>
                                            </svg>
                                            <?= _e($subItem['title']) ?>
                                        </a>
                                    <?php endforeach ?>
                                </div>
                            </div> <!-- Menu Item With Submenu END -->
                        <?php else: // No Submenu ?>
                            <a href="<?= _e(url($route_path)) ?>"
                                class="p-3 rounded-lg flex items-center gap-3 group <?= $is_active ? 'bg-white text-accent-700' : 'hover:bg-white hover:text-accent-700' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="group-hover:stroke-accent-700 size-6">
                                    <?= $item['icon'] ?>
                                </svg>
                                <span class="font-medium text-sm truncate"><?= _e($item['title']) ?></span>
                                <!-- Menu Badge in right side -->
                                <?php if (isset($item['badge']) && !empty($item['badge'])): ?>
                                    <span
                                        class="ml-auto text-xs font-medium px-1.5 py-0.5 rounded-lg bg-accent-50 border border-accent-600 text-accent-700"><?= _e($item['badge']) ?></span>
                                <?php endif ?>
                            </a>
                        <?php endif ?>
                    <?php endforeach ?>
                </nav> <!-- Menu Items END -->
            </aside> <!-- Sidebar END -->

            <!-- Main Content Part Start -->
            <main class="<?= _e(!$hide_sidebar ? 'lg:ml-64' : '') ?> mt-14 md:mt-16 print:m-0">
                <!-- Render Flash Messages START -->
                <?php
                // Check if there are any flash messages.
                if (session()->has('error_message')) {
                    panel()->addData(
                        'notice',
                        ['type' => 'error', 'message' => session()->get('error_message'), 'close' => true]
                    );
                    session()->delete('error_message');
                }

                if (session()->has('warning_message')) {
                    panel()->addData(
                        'notice',
                        ['type' => 'warning', 'message' => session()->get('warning_message'), 'close' => true]
                    );
                    session()->delete('warning_message');
                }

                if (session()->has('success_message')) {
                    panel()->addData(
                        'notice',
                        ['type' => 'success', 'message' => session()->get('success_message'), 'close' => true]
                    );
                    session()->delete('success_message');
                }

                // Render flash messages, if there are any.
                if (panel()->hasData('notice')):
                    foreach (panel()->getData('notice') as $key => $notice):
                        $notice = array_merge(['icon' => true, 'type' => 'info', 'close' => false, 'message' => ''], $notice);
                        if (empty($notice['message'])) {
                            continue;
                        }
                        ?>
                        <div class="w-full text-white <?= ['error' => 'bg-red-500', 'warning' => 'bg-amber-500', 'success' => 'bg-emerald-500'][$notice['type']] ?? 'bg-slate-600' ?>"
                            x-ref="flashMessage_<?= _e($key) ?>">
                            <div class="container flex items-center justify-between px-6 py-4 mx-auto">
                                <div class="flex">
                                    <?php if ($notice['icon']): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <?php if ($notice['type'] === 'error'): ?>
                                                <path fill-rule="evenodd"
                                                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                    clip-rule="evenodd" />
                                            <?php elseif ($notice['type'] === 'warning'): ?>
                                                <path fill-rule="evenodd"
                                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                    clip-rule="evenodd" />
                                            <?php elseif ($notice['type'] === 'success'): ?>
                                                <path fill-rule="evenodd"
                                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                                    clip-rule="evenodd" />
                                            <?php else: ?>
                                                <path
                                                    d="M11.953 2C6.465 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.493 2 11.953 2zM13 17h-2v-2h2v2zm0-4h-2V7h2v6z">
                                                </path>
                                            <?php endif ?>
                                        </svg>
                                        <p class="mx-3 flex-1"><?= $notice['message'] ?></p>
                                    <?php else: ?>
                                        <p><?= $notice['message'] ?></p>
                                    <?php endif ?>
                                </div>
                                <?php if (isset($notice['close']) && $notice['close']): ?>
                                    <button x-on:click="$refs.flashMessage_<?= _e($key) ?>.remove()"
                                        class="p-1 transition-colors duration-300 transform rounded-md hover:bg-opacity-25 hover:bg-primary-600 focus:outline-none">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                <?php endif ?>
                            </div>
                        </div>
                    <?php endforeach; endif; ?> <!-- Render Flash Messages END -->
                <!-- Including Template Content START -->
                <div class="py-6 md:py-8">
                    <?= $content ?? '' ?>
                </div> <!-- Including Template Content END -->
            </main> <!-- Main Content Part End -->
        </div>
        <?php if (!$isAjax): ?>
        </div>

        <!-- HyperJs Error Handler -->
        <script>
            document.addEventListener('fireError', () => {
                window.FireLine.context.replaceHtml(
                    `<div>
                    <div class="grid h-screen place-content-center bg-white px-4">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-red-500 size-24 mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="M6.787 7h10.426c-.108-.158-.201-.331-.318-.481l2.813-2.812-1.414-1.414-2.846 2.846a6.575 6.575 0 0 0-.723-.454 5.778 5.778 0 0 0-5.45 0c-.25.132-.488.287-.722.453L5.707 2.293 4.293 3.707l2.813 2.812c-.118.151-.21.323-.319.481zM5.756 9H2v2h2.307c-.065.495-.107.997-.107 1.5 0 .507.042 1.013.107 1.511H2v2h2.753c.013.039.021.08.034.118.188.555.421 1.093.695 1.6.044.081.095.155.141.234l-2.33 2.33 1.414 1.414 2.11-2.111a7.477 7.477 0 0 0 2.068 1.619c.479.253.982.449 1.496.58.204.052.411.085.618.118V16h2v5.914a6.23 6.23 0 0 0 .618-.118 6.812 6.812 0 0 0 1.496-.58c.465-.246.914-.55 1.333-.904.258-.218.5-.462.734-.716l2.111 2.111 1.414-1.414-2.33-2.33c.047-.08.098-.155.142-.236.273-.505.507-1.043.694-1.599.013-.039.021-.079.034-.118H22v-2h-2.308c.065-.499.107-1.004.107-1.511 0-.503-.042-1.005-.106-1.5H22V9H5.756z"></path></svg>
                            <p class="mt-4 text-2xl font-bold tracking-tight text-primary-600 sm:text-4xl"><?= __e('Error') ?></p>
                            <p class="mt-4 text-primary-500"><?= __e('That page could not be loaded, Please go back.') ?></p>
                            <button x-on:click="$fire.reload()" class="mt-6 inline-block rounded-lg bg-accent-600 px-5 py-3 text-sm font-medium text-white hover:bg-accent-700 focus:outline-none focus:ring">&larr; <?= __e('go back') ?></button>
                        </div>
                    </div>
                </div>`
                );
            });
            window.addEventListener('offline', () => {
                window.FireLine.context.replaceHtml(
                    `<div>
                    <div class="grid h-screen place-content-center bg-white px-4">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-primary-500 size-24 mx-auto" fill="currentColor" viewBox="0 0 24 24"><path d="m1.293 8.395 1.414 1.414c.504-.504 1.052-.95 1.622-1.359L2.9 7.021c-.56.422-1.104.87-1.607 1.374zM6.474 5.06 3.707 2.293 2.293 3.707l18 18 1.414-1.414-5.012-5.012.976-.975a7.86 7.86 0 0 0-4.099-2.148L11.294 9.88c2.789-.191 5.649.748 7.729 2.827l1.414-1.414c-2.898-2.899-7.061-3.936-10.888-3.158L8.024 6.61A13.366 13.366 0 0 1 12 6c3.537 0 6.837 1.353 9.293 3.809l1.414-1.414C19.874 5.561 16.071 4 12 4a15.198 15.198 0 0 0-5.526 1.06zm-2.911 6.233 1.414 1.414a9.563 9.563 0 0 1 2.058-1.551L5.576 9.697c-.717.451-1.395.979-2.013 1.596zm2.766 3.014 1.414 1.414c.692-.692 1.535-1.151 2.429-1.428l-1.557-1.557a7.76 7.76 0 0 0-2.286 1.571zm7.66 3.803-2.1-2.1a1.996 1.996 0 1 0 2.1 2.1z"></path></svg>
                            <p class="mt-4 text-xl font-semibold tracking-tight text-primary-600 sm:text-2xl"><?= __e('No Internet') ?></p>
                            <p class="mt-2 text-primary-500"><?= __e('You are offline') ?></p>
                        </div>
                    </div>
                </div>`
                );
            });
            window.addEventListener('online', () => {
                window.FireLine.context.reload();
            });
        </script>
    </body>

    </html>
<?php endif ?>