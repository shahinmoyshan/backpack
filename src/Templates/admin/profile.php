<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', 'profile')
    ->set('breadcrumb', ['__active' => 'profile']);

?>

<!-- Profile Settings Page Start -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Title -->
    <h2 class="font-bold text-2xl text-primary-800 leading-tight mb-6"><?= _e(__('Profile Settings')) ?></h2>

    <!-- Profile Settings Card START -->
    <div class="bg-white shadow-lg sm:rounded-lg py-10 px-16">
        <div class="flex flex-col-reverse items-center md:items-start md:flex-row gap-16 md:gap-20">
            <!-- Profile Settings Form START -->
            <div class="w-full md:w-8/12 max-w-lg">
                <form action="<?= _e(route_url('admin.profile')) ?>" method="POST">
                    <?= csrf() ?>
                    <!-- Update Profile Full Name Field -->
                    <div class="mb-4">
                        <label for="name" class="block mb-2"><?= _e(__('full name')) ?></label>
                        <input type="text" id="name" name="full_name" value="<?= _e(user('full_name', '')) ?>"
                            class="block w-full px-6 py-3 text-primary-900 placeholder-primary-700 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300">
                    </div>

                    <!-- Update Profile Email Field -->
                    <div class="mb-6">
                        <label for="email" class="block mb-2"><?= _e(__('email address')) ?></label>
                        <input type="email" id="email" name="email" value="<?= _e(user('email', '')) ?>"
                            class="block w-full px-6 py-3 text-primary-900 placeholder-primary-700 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300">
                    </div>

                    <!-- Change Password START-->
                    <div class="mb-4">
                        <label for="old_password"
                            class="block mb-2 font-medium"><?= _e(__('change password')) ?></label>
                        <input type="password" id="old_password" name="old_password"
                            placeholder="<?= _e(__('old password')) ?>"
                            class="block w-full px-6 py-3 text-primary-900 placeholder-primary-700 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300">
                    </div>
                    <div class="mb-6 flex flex-col md:flex-row gap-4">
                        <!-- New Password -->
                        <input type="password" name="new_password" placeholder="<?= _e(__('new password')) ?>"
                            class="block w-full px-6 py-3 text-primary-900 placeholder-primary-700 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300">
                        <!-- Confirm Password -->
                        <input type="password" name="confirm_password" placeholder="<?= _e(__('confirm password')) ?>"
                            class="block w-full px-6 py-3 text-primary-900 placeholder-primary-700 bg-white border border-primary-300 rounded-lg focus:border-accent-300 focus:ring-opacity-40 focus:outline-none focus:ring focus:ring-accent-300">
                    </div> <!-- Change Password END-->

                    <!-- Save Changes Button -->
                    <button
                        class="px-6 py-3 w-full font-medium tracking-wide text-white transition-colors duration-300 transform bg-accent-600 shadow shadow-accent-200 rounded-lg hover:bg-accent-500 focus:outline-none focus:ring focus:ring-accent-300 focus:ring-opacity-80">
                        <?= _e(__('save')) ?>
                    </button>
                </form>
            </div> <!-- Profile Settings Form END -->

            <!-- Profile Settings Picture START -->
            <div class="w-full md:w-4/12 max-w-60">
                <!-- Preview Existing Profile Picture -->
                <div class="text-center mb-4">
                    <?php if (!empty(user('image'))): ?>
                        <img src="<?= _e(media_url(user('image'))) ?>" class="w-24 h-24 object-cover mx-auto rounded-full"
                            alt="profile picture">
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="size-32 mx-auto text-primary-400">
                            <path fill-rule="evenodd"
                                d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                clip-rule="evenodd" />
                        </svg>
                    <?php endif ?>
                </div>

                <!-- Upload New Profile Picture Button -->
                <form action="<?= _e(route_url('admin.profile.picture')) ?>" enctype="multipart/form-data" method="POST"
                    x-ref="profilePictureForm">
                    <?= csrf() ?>
                    <!-- Hidden Input for send new picture and old profile image to be deleted upon new profile picture is uploaded -->
                    <input type="hidden" name="_image" value="<?= _e(user('image', '')) ?>">
                    <input type="file" accept="image/*" name="image"
                        x-on:change="$fire.formSubmit($refs.profilePictureForm)" x-ref="profilePictureInput"
                        style="display: none;">

                    <!-- Upload a Picture button -->
                    <button type="button" x-on:click="$refs.profilePictureInput.click()"
                        class="px-6 py-3 w-full font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80">
                        <?= _e(__('upload a picture')) ?>
                    </button>
                </form>
            </div> <!-- Profile Settings Picture END -->
        </div>
    </div> <!-- Profile Settings Card END -->
</div>
<!-- Profile Settings Page End -->