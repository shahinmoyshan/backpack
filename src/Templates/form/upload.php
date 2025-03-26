<?php

// check if the file is temporary uploaded and if so, set it to null
if (isset($field['value']['tmp_name'])) {
    $field['value'] = null;
}

$random_id = 'upload-' . uniqid();
?>
<div class="col-span-4" x-data="{
    newFiles: [],
    init() {
        /** time: <?= _e(microtime()) ?> */
    },
    byteConverter(bytes) {
        const K_UNIT = 1024; // Kilobyte unit
        const SIZES = ['B', 'KB', 'MB', 'GB', 'TB', 'PB']; // Units of measurement

        // Return '0 B' if the input is 0
        if (bytes === 0) return '0 B';
    
        // Determine the appropriate unit by using logarithms
        let i = Math.floor(Math.log(bytes) / Math.log(K_UNIT));
        
        // Calculate the size in the determined unit and format it
        return parseFloat((bytes / Math.pow(K_UNIT, i)).toFixed(2)) + ' ' + SIZES[i];
    },
    removeFileFromUpload(file) {
        const newFileList = Array.from(this.$refs.fileInput.files).filter(f => f !== file);

        // Create a new DataTransfer object to mimic the updated FileList
        const dataTransfer = new DataTransfer();
        newFileList.forEach(f => dataTransfer.items.add(f));

        // Set the input's FileList to the updated one
        this.$refs.fileInput.files = dataTransfer.files;

        // Triggering the input's change event, which will emit the x-on:change event
        this.$refs.fileInput.dispatchEvent(new Event('change'));
    },
}">
    <!-- File Upload Trigger Button START -->
    <label for="upload_<?= _e($field['id'] ?? $random_id) ?>"
        class="block relative border border-primary-200 text-center font-medium text-primary-600 rounded-lg bg-white hover:bg-primary-50"
        x-on:dragover="$el.classList.add('bg-primary-50')" x-on:drop="$el.classList.remove('bg-primary-50')"
        x-on:dragleave="$el.classList.remove('bg-primary-50')">
        <div class="px-6 py-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-9 mx-auto">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
            </svg>
            <p class="text-[0.85rem]"
                x-data="{messages: ['<?= _e(__('# file chosen')) ?>', '<?= _e(__('# files chosen')) ?>']}">
                <span
                    x-show="newFiles.length === 0"><?= __(isset($field['multiple']) && $field['multiple'] ? 'drag & drop your files or %s' : 'drag & drop your file or %s', '<span class="text-accent-700">' . __('browse') . '</span>') ?></span>
                <span x-show="newFiles.length > 0"
                    x-text="(newFiles.length === 1 ? messages[0] : messages[1]).replace('#', newFiles.length)"></span>
            </p>
        </div>
        <!-- File Input hidden -->
        <input x-ref="fileInput" type="file" <?= !isset($field['value']) && isset($field['required']) && $field['required'] ? 'required' : '' ?> <?= $form->renderAttributes($field['attrs'] ?? []) ?>
            name="<?= _e($field['name'] . (isset($field['multiple']) && $field['multiple'] ? '[]' : '')) ?>"
            <?= _e(isset($field['multiple']) && $field['multiple'] ? 'multiple' : '') ?>
            class="opacity-0 absolute z-10 inset-0 w-full h-full" x-on:change="newFiles = $el.files"
            id="upload_<?= _e($field['id'] ?? $random_id) ?>">
        <?php if (isset($field['value'])): ?>
            <!-- Hidden input for existing files -->
            <input type="hidden" name="_<?= _e($field['name']) ?>" value="<?= _e(implode(',', (array) $field['value'])) ?>">
        <?php endif ?>
    </label> <!-- File Upload Trigger Button END -->
    <!-- File List -->
    <div <?= empty($field['value'] ?? []) ? 'x-cloak x-show="newFiles.length > 0"' : '' ?>
        class="mt-4 border border-primary-200 rounded-lg max-h-48 overflow-y-auto">
        <div>
            <!-- New Files -->
            <template x-for="file in newFiles" :key="file.name">
                <div class="border-b border-primary-200 last:border-b-0 px-4 py-3 hover:bg-primary-50 transition">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 mr-2 hidden md:block">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span x-text="file.name"
                            class="text-accent-700 text-sm font-normal max-w-40 md:max-w-56 lg:max-w-64 xl:max-w-72 truncate"></span>
                        <span class="ml-1.5 text-xs text-primary-600" x-text="byteConverter(file.size || 0)"></span>
                        <span class="ml-auto cursor-pointer text-primary-500 hover:text-primary-800"
                            x-on:click="removeFileFromUpload(file)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </span>
                    </div>
                </div>
            </template>
        </div>
        <!-- Existing Files -->
        <div x-show="!newFiles.length">
            <?php
            if (isset($field['value']) && !empty($field['value'])):
                foreach ((array) $field['value'] as $file): ?>
                    <?php
                    // if file does not have meta data, then generate meta data as array
                    $is_private = strpos($file, 'private') === 1;
                    $file_path = $is_private ? storage_dir($file) : upload_dir($file);
                    if (file_exists($file_path)) {
                        $file = [
                            'name' => basename($file),
                            'size' => filesize($file_path),
                            'path' => $file,
                            'type' => mime_content_type($file_path),
                        ];
                    }

                    if (is_string($file) && strpos($file, '://') !== false) {
                        $file = [
                            'name' => basename($file),
                            'path' => $file,
                        ];
                    }

                    // if file is not an array or does not have path key, then skip
                    if (!isset($file['path'])) {
                        continue;
                    }
                    ?>
                    <div
                        class="border-t border-primary-200 first:border-t-0 px-4 py-3 <?= !$is_private ? 'hover:bg-primary-50 transition' : '' ?>">
                        <?php if ($is_private): ?>
                            <div class="flex items-center">
                            <?php else: ?>
                                <a target="_blank"
                                    href="<?= _e(strpos($file['path'], 'http') === 0 ? $file['path'] : media_url($file['path'])) ?>"
                                    class="flex items-center">
                                <?php endif ?>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-5 mr-2 hidden md:block">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <span
                                    class="text-accent-700 text-sm font-normal max-w-40 md:max-w-64 lg:max-w-72 xl:max-w-80 truncate">
                                    <?= _e(trim_characters($file['name'], 40, '...', true)) ?>
                                </span>
                                <span class="ml-1.5 text-xs text-primary-600"
                                    x-text="byteConverter(<?= _e($file['size'] ?? 0) ?>)"></span>
                                <?php if ($is_private): ?>
                                    <span class="ml-1.5 text-xs text-primary-700 font-medium">(<?= __e('private') ?>)</span>
                            </div>
                        <?php else: ?>
                            </a>
                        <?php endif ?>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>
</div>