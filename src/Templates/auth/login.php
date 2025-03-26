<?php

$template->layout('auth/layout')
    ->set('title', 'login');

?>
<!-- Shop Icon or Logo -->
<a href="<?= _e(url('/')) ?>" native
    class="flex justify-center items-center gap-2 mx-auto bg-accent-700 text-primary-50 text-2xl font-medium px-6 py-4 group">
    <span class="bg-accent-50/20 transition group-hover:bg-accent-50/30 p-1 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path fill-rule="evenodd"
                d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z"
                clip-rule="evenodd" />
        </svg>
    </span>
    <?= _e(panel()->getConfig('site_name', 'Website')) ?>
</a>

<!-- Error Message -->
<?php if (isset($error)) {
    echo $template->include('auth/message', ['error' => $error]);
} ?>

<!-- Login Form -->
<div class="px-6 py-4">
    <!-- Form Title -->
    <h3 class="mt-3 mb-1 text-xl font-semibold text-center text-primary-600"><?= _e(__('admin login')) ?></h3>
    <p class="text-center text-xs text-primary-600">
        <?= __e('Welcome! Please Enter your details below to sign in into your account.') ?>
    </p>
    <form action="<?= _e(route_url('admin.auth.login')) ?>" method="POST">
        <!-- CSRF Token -->
        <?= csrf() ?>

        <!-- Email or Username Input -->
        <label class="block w-full mt-6 relative">
            <span class="absolute top-1/2 left-3 -translate-y-1/2 text-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
            </span>
            <input
                class="block w-full px-10 py-2.5 mt-2 text-[0.9375rem] text-primary-700 placeholder-primary-500 bg-white border border-primary-300 rounded-md focus:border-accent-300 focus:outline-hidden focus:ring-3 focus:ring-accent-300/40"
                type="text" name="user" value="<?= _e(request()->post('user', '')) ?>"
                placeholder="<?= _e(__('username or email')) ?>" aria-label="Username or Email" />
        </label>

        <!-- Password Input -->
        <label class="block w-full mt-4 relative" x-data="{type: 'password'}">
            <span class="absolute top-1/2 left-3 -translate-y-1/2 text-primary-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                </svg>
            </span>
            <input
                class="block w-full px-10 py-2.5 mt-2 text-[0.9375rem] text-primary-700 placeholder-primary-500 bg-white border border-primary-300 rounded-md focus:border-accent-300 focus:outline-hidden focus:ring-3 focus:ring-accent-300/40"
                :type="type" name="password" placeholder="<?= _e(__('password')) ?>" aria-label="Password" />

            <!-- Show/Hide Password -->
            <button x-on:click="type = type === 'password' ? 'text' : 'password'" type="button"
                class="absolute top-1/2 right-3 -translate-y-1/2">
                <svg x-show="type === 'password'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="text-primary-700 size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <svg x-cloak x-show="type === 'text'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="text-primary-700 size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
        </label>

        <!-- Login Button -->
        <div class="mt-6">
            <button
                class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-primary-800 rounded-md hover:bg-primary-700 focus:outline-hidden focus:ring-3 focus:ring-primary-300/50">
                <?= _e(__('login')) ?>
            </button>
        </div>
    </form>
</div>

<!-- Forget Password Link -->
<div class="flex items-center justify-center py-4 text-center bg-primary-50">
    <span class="text-sm text-primary-600"><?= _e(__('forget password?')) ?></span>
    <a href="<?= _e(route_url('admin.auth.forget')) ?>"
        class="mx-2 text-sm font-bold text-accent-500 hover:underline hover:text-accent-600"><?= _e(__('reset')) ?></a>
</div>