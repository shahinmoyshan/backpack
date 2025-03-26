<?php

$template->layout('auth/layout')
    ->set('title', 'forget password');

?>
<?php if (isset($success)): ?>
    <!-- Reset Link Send Message START -->
    <div class="px-6 py-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-14 mx-auto text-green-500">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21.75 9v.906a2.25 2.25 0 0 1-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 0 0 1.183 1.981l6.478 3.488m8.839 2.51-4.66-2.51m0 0-1.023-.55a2.25 2.25 0 0 0-2.134 0l-1.022.55m0 0-4.661 2.51m16.5 1.615a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V8.844a2.25 2.25 0 0 1 1.183-1.981l7.5-4.039a2.25 2.25 0 0 1 2.134 0l7.5 4.039a2.25 2.25 0 0 1 1.183 1.98V19.5Z" />
        </svg>
        <p class="mt-4 text-center text-primary-800"><?= _e($success) ?></p>
    </div><!-- Reset Link Send Message END -->
<?php else: ?>
    <!-- Key Icon -->
    <div class="flex justify-center mx-auto bg-accent-700 px-6 py-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-12 text-white">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
        </svg>
    </div>

    <!-- Error and Success Message -->
    <?php if (isset($error)) {
        echo $template->include('auth/message', ['error' => $error]);
    } ?>

    <!-- Forget Password Form -->
    <div class="px-6 py-4">
        <!-- Form Title -->
        <h3 class="mt-3 mb-1 text-xl font-semibold text-center text-primary-600"><?= _e(__('forget password?')) ?>
        </h3>
        <p class="text-center text-xs text-primary-600">
            <?= __e('Enter your email or username below to receive your password reset link.') ?>
        </p>
        <form action="<?= _e(route_url('admin.auth.forget')) ?>" method="POST">
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
                    type="text" name="user" placeholder="<?= _e(__('username or email')) ?>"
                    aria-label="Username or Email" />
            </label>

            <!-- Forget Password Button -->
            <div class="mt-6">
                <button
                    class="w-full px-6 py-3 text-sm font-medium tracking-wide text-white capitalize transition-colors duration-300 transform bg-primary-800 rounded-md hover:bg-primary-700 focus:outline-hidden focus:ring-3 focus:ring-primary-300/50">
                    <?= _e(__('send reset link')) ?>
                </button>
            </div>
        </form>
    </div>
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