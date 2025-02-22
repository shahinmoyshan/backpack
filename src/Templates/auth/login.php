<?php

$template->layout('auth/layout')
    ->set('title', 'login');

?>
<!-- Shop Icon or Logo -->
<a href="<?= _e(url('/')) ?>" native class="flex justify-center mx-auto bg-accent-700 px-6 py-4">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="size-12 text-white">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
    </svg>
</a>

<!-- Error Message -->
<?php if (isset($error)) {
    echo $template->include('auth/message', ['error' => $error]);
} ?>

<!-- Login Form -->
<div class="px-6 py-4">
    <!-- Form Title -->
    <h3 class="mt-3 text-xl font-medium text-center text-primary-600"><?= _e(__('admin login')) ?></h3>
    <form action="<?= _e(route_url('admin.auth.login')) ?>" method="POST">
        <!-- CSRF Token -->
        <?= csrf() ?>

        <!-- Email or Username Input -->
        <div class="w-full mt-4">
            <input
                class="block text-center w-full px-4 py-2 mt-2 text-primary-700 placeholder-primary-500 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300"
                type="text" name="user" value="<?= _e(request()->post('user', '')) ?>"
                placeholder="<?= _e(__('username or email')) ?>" aria-label="Username or Email" />
        </div>

        <!-- Password Input -->
        <div class="w-full mt-4">
            <input
                class="block text-center w-full px-4 py-2 mt-2 text-primary-700 placeholder-primary-500 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300"
                type="password" name="password" placeholder="<?= _e(__('password')) ?>" aria-label="Password" />
        </div>

        <!-- Login Button -->
        <div class="mt-6">
            <button
                class="w-full px-6 py-2.5 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-primary-800 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-50">
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