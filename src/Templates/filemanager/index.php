<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', 'filemanager');

$byteConverter = function ($bytes) {
    $K_UNIT = 1024; // Kilobyte unit
    $SIZES = ['B', 'KB', 'MB', 'GB', 'TB', 'PB']; // Units of measurement

    // Return '0 B' if the input is 0
    if ($bytes == 0) {
        return '0 B';
    }

    // Determine the appropriate unit by using logarithms
    $i = floor(log($bytes) / log($K_UNIT));

    // Calculate the size in the determined unit and format it
    return ceil($bytes / pow($K_UNIT, $i)) . ' ' . $SIZES[$i];
};

?>

<!-- Filemanager Page Start -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Title -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="font-bold text-2xl text-primary-800 leading-tight"><?= _e(__('filemanager')) ?></h2>
        <div class="flex items-center gap-3">
            <div x-data="{ showFolderForm: false, /** time: <?= _e(microtime()) ?> */ }">
                <button type="button" x-on:click="showFolderForm=true"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold flex items-center gap-2 py-2 px-4 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 10.5v6m3-3H9m4.06-7.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                    <?= __('New Folder') ?></button>
                <!-- New Folder Modal -->
                <div x-cloak x-show="showFolderForm"
                    class="fixed inset-0 z-40 bg-black/30 w-full h-full flex items-center justify-center">
                    <form action="<?= _e(request_url()) ?>" method="post"
                        class="bg-white max-w-lg w-96 p-6 sm:rounded-lg shadow-lg"
                        x-on:click.away="showFolderForm=false" x-on:keyup.escape.window="showFolderForm=false">
                        <?= csrf() ?>
                        <input type="hidden" name="__redirect" value="<?= _e(request_url()) ?>">
                        <h3 class="text-lg font-semibold mb-2"><?= _e(__('Create New Folder')) ?></h3>
                        <input type="text" name="new_folder"
                            class="w-full border-primary-300 focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50 rounded-md">
                        <div class="mt-6 flex gap-2 items-center">
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white transition-colors duration-300 transform bg-accent-600 rounded-md hover:bg-accent-500 focus:outline-none focus:ring focus:ring-accent-300 focus:ring-opacity-80"><?= _e(__('Create Folder')) ?></button>
                            <button type="button" x-on:click="showFolderForm=false"
                                class="px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80"><?= _e(__('cancel')) ?></button>
                        </div>
                    </form>
                </div>
            </div>
            <form action="<?= _e(request_url()) ?>" x-ref="filesUploadForm" method="post" enctype="multipart/form-data">
                <?= csrf() ?>
                <input type="file" multiple name="upload_files[]" x-ref="filesUploadInput"
                    x-on:change="$fire.formSubmit($refs.filesUploadForm)" style="display:none;">
                <input type="hidden" name="__redirect" value="<?= _e(request_url()) ?>">
                <button type="button" x-on:click="$refs.filesUploadInput.click()"
                    class="bg-accent-600 hover:bg-accent-500 text-white text-sm font-semibold flex items-center gap-2 py-2 px-4 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                    </svg>
                    <?= __('Upload Files') ?></button>
            </form>
        </div>
    </div>

    <!-- Delete Form -->
    <form action="<?= _e(request_url()) ?>" x-ref="deleteForm" method="post">
        <?= csrf() ?>
        <input type="hidden" name="delete_file">
        <input type="hidden" name="__redirect" value="<?= _e(request_url()) ?>">
    </form>

    <?php if (count($folders) > 1): ?>
        <a href="<?= _e(substr(request_url(), 0, strrpos(request_url(), '/'))) ?>"
            class="inline-flex items-center gap-2 mb-4 text-amber-600 hover:text-amber-700 hover:underline font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M7.49 12 3.74 8.248m0 0 3.75-3.75m-3.75 3.75h16.5V19.5" />
            </svg>
            <?= __e('Back') ?>
        </a>
    <?php endif ?>

    <!-- Files & Folders Table START -->
    <table class="w-full bg-white shadow-lg rounded-sm" x-data="{
        deleteResource(name) {
            if (!confirm('<?= __e('are you sure') ?>')) {
                return ;
            }

            this.$refs.deleteForm.delete_file.value = name;
            this.$fire.formSubmit(this.$refs.deleteForm);
        }
    }">
        <thead>
            <tr class="bg-primary-50">
                <th width="10%" class="text-sm font-medium uppercase text-left px-6 py-3"><?= __e('Icon') ?></th>
                <th width="35%" class="text-sm font-medium uppercase text-left px-6 py-3"><?= __e('name') ?></th>
                <th width="15%" class="text-sm font-medium uppercase text-left px-6 py-3"><?= __e('Size') ?></th>
                <th width="20%" class="text-sm font-medium uppercase text-left px-6 py-3"><?= __e('Date') ?></th>
                <th width="20%" class="text-sm font-medium uppercase text-left px-6 py-3"><?= __e('action') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($files as $file): ?>
                <tr class="border-t hover:bg-primary-50">
                    <td class="px-6 py-3">
                        <?php if ($file['type'] === 'dir'): ?>
                            <a href="<?= _e(request_url() . '/' . $file['name']) ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-8 text-accent-600">
                                    <path
                                        d="M19.906 9c.382 0 .749.057 1.094.162V9a3 3 0 0 0-3-3h-3.879a.75.75 0 0 1-.53-.22L11.47 3.66A2.25 2.25 0 0 0 9.879 3H6a3 3 0 0 0-3 3v3.162A3.756 3.756 0 0 1 4.094 9h15.812ZM4.094 10.5a2.25 2.25 0 0 0-2.227 2.568l.857 6A2.25 2.25 0 0 0 4.951 21H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-2.227-2.568H4.094Z" />
                                </svg>
                            </a>
                        <?php else: ?>
                            <a href="<?= _e($file['url']) ?>" target="_blank">
                                <?php if (in_array($file['extension'], ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico'])): ?>
                                    <img src="<?= _e($file['url']) ?>"
                                        class="max-h-10 object-contain rounded border p-0.5 shadow-sm" />
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="size-8 text-primary-500/90">
                                        <path
                                            d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z" />
                                        <path
                                            d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                                    </svg>
                                <?php endif ?>
                            </a>
                        <?php endif ?>
                    </td>
                    <td class="px-6 py-3">
                        <?php if ($file['type'] === 'dir'): ?>
                            <a href="<?= _e(request_url() . '/' . $file['name']) ?>"
                                class="text-sm font-medium hover:underline flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                    <path fill-rule="evenodd"
                                        d="M20.24 20.249a.75.75 0 0 0-.75-.75H8.989V5.56l2.47 2.47a.75.75 0 0 0 1.06-1.061l-3.75-3.75a.75.75 0 0 0-1.06 0l-3.75 3.75a.75.75 0 1 0 1.06 1.06l2.47-2.469V20.25c0 .414.335.75.75.75h11.25a.75.75 0 0 0 .75-.75Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <?= _e($file['name']) ?>
                            </a>
                        <?php else: ?>
                            <a target="_blank" href="<?= _e($file['url']) ?>" class="text-sm hover:underline">
                                <?= _e($file['name']) ?>
                            </a>
                        <?php endif ?>
                    </td>
                    <td class="px-6 py-3">
                        <?php if ($file['type'] === 'dir'): ?>
                            &mdash;
                        <?php else: ?>
                            <span
                                class="text-[0.8rem] text-primary-600"><?= $byteConverter($file['size'] ?? 0) ?? '&mdash;' ?></span>
                        <?php endif ?>
                    </td>
                    <td class="px-6 py-3">
                        <span class="text-sm text-primary-600"><?= _e($file['date']) ?></span>
                    </td>
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-1">
                            <?php if (has_permission('delete_file_manager') && ($file['type'] === 'file' || ($file['type'] === 'dir' && $file['is_empty']))): ?>
                                <button class="text-[0.8rem] text-red-600 hover:text-red-800 hover:underline"
                                    x-on:click="deleteResource('<?= _e($file['name']) ?>')">
                                    <span><?= __e('delete') ?></span>
                                </button>
                                <span class="text-primary-600">|</span>
                            <?php endif ?>
                            <?php if ($file['type'] === 'dir'): ?>
                                <a href="<?= _e(request_url() . '/' . $file['name']) ?>"
                                    class="text-accent-600 hover:underline text-[0.8rem]">
                                    <?= __e('Open') ?>
                                </a>
                            <?php else: ?>
                                <a target="_blank" href="<?= $file['url'] ?? '' ?>" target="_blank"
                                    class="text-accent-600 hover:underline text-[0.8rem]">
                                    <?= _e(__('View')) ?>
                                </a>
                            <?php endif ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            <?php if (empty($files)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 border-t">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-11 mx-auto text-primary-400 mb-1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                            </svg>
                            <p class="text-[0.8rem] text-primary-600"><?= __e('no files found') ?></p>
                        </div>
                    </td>
                </tr>
            <?php endif ?>
        </tbody>
    </table> <!-- Files & Folder Table END -->
</div>
<!-- Filemanager Page End -->