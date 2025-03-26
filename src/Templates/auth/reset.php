<?php

$template->layout('auth/layout')
    ->set('title', 'reset password');
?>

<!-- Reset Password Area START -->
<?php if (isset($token) && !empty($token)): ?>
    <!-- Lock Icon -->
    <div class="flex justify-center mx-auto bg-accent-700 px-6 py-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-12 text-white">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
    </div>

    <!-- Error and Success Message -->
    <?php if (isset($error)) {
        echo $template->include('auth/message', ['error' => $error]);
    } ?>

    <!-- Forget Password Form -->
    <div class="px-6 py-4">
        <!-- Form Title -->
        <h3 class="mt-3 mb-1 text-xl font-semibold text-center text-primary-600"><?= _e(__('reset password')) ?></h3>
        <p class="text-center text-xs text-primary-600">
            <?= __e('Enter your new password below to reset your password.') ?>
        </p>
        <form action="<?= _e(route_url('admin.auth.reset')) ?>" method="POST">
            <!-- CSRF Token -->
            <?= csrf() ?>

            <!-- New Password Input -->
            <label class="block w-full mt-6 relative">
                <span class="absolute top-1/2 left-3 -translate-y-1/2 text-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </span>
                <input
                    class="block w-full px-10 py-2.5 mt-2 text-[0.9375rem] text-primary-700 placeholder-primary-500 bg-white border border-primary-300 rounded-md focus:border-accent-300 focus:outline-hidden focus:ring-3 focus:ring-accent-300/40"
                    type="password" name="password" placeholder="<?= _e(__('new password')) ?>" aria-label="New Password" />
            </label>

            <!-- Confirm Password Input -->
            <label class="block w-full mt-4 relative">
                <span class="absolute top-1/2 left-3 -translate-y-1/2 text-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                </span>
                <input
                    class="block w-full px-10 py-2.5 mt-2 text-[0.9375rem] text-primary-700 placeholder-primary-500 bg-white border border-primary-300 rounded-md focus:border-accent-300 focus:outline-hidden focus:ring-3 focus:ring-accent-300/40"
                    type="password" name="confirm_password" placeholder="<?= _e(__('confirm password')) ?>"
                    aria-label="Confirm Password" />
            </label>

            <!-- Hidden Token -->
            <input type="hidden" name="token" value="<?= _e($token->token) ?>">

            <!-- Set Password Button -->
            <div class="mt-6">
                <button
                    class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-primary-800 rounded-md hover:bg-primary-700 focus:outline-hidden focus:ring-3 focus:ring-primary-300/50">
                    <?= _e(__('set password')) ?>
                </button>
            </div>
        </form>
    </div>
    <!-- Reset Password Area END -->
<?php elseif (isset($success)): ?>
    <!-- Reset Success START -->
    <div class="px-6 py-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-14 mx-auto text-green-500">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
        </svg>
        <h3 class="mt-3 text-lg font-medium text-center text-primary-700"><?= _e($success) ?></h3>
    </div><!-- Reset Success END -->
<?php else: ?>
    <!-- Invalid Token Message START -->
    <div class="px-6 py-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-12 mx-auto text-amber-500">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
        <h3 class="mt-3 text-lg font-medium text-center text-primary-600"><?= _e(__('invalid token')) ?></h3>
    </div> <!-- Invalid Token Message END -->
<?php endif ?>
<!-- Back to Link -->
<div class="flex items-center justify-center py-4 text-center bg-primary-50">
    <a href="<?= _e(route_url('admin.auth.login')) ?>"
        class="mx-2 text-sm font-bold text-accent-500 hover:underline hover:text-accent-600 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-6">
            <path fill-rule="evenodd"
                d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z"
                clip-rule="evenodd" />
        </svg>
        <?= _e(__('back to login')) ?>
    </a>
</div>