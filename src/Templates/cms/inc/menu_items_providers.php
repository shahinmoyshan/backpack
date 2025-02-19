<div x-on:click="changeTab('<?= _e($provider['id']) ?>')"
    class="flex justify-between items-center px-6 py-4 border-b cursor-pointer">
    <h3 class="font-medium"><?= _e($provider['label']) ?></h3>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
        class="size-5 text-primary-600 transform transition duration-100 ease-in-out"
        :class="{ 'rotate-180': tab == '<?= _e($provider['id']) ?>' }">
        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
    </svg>
</div>
<div x-show="tab === '<?= _e($provider['id']) ?>'" x-collapse>
    <div class="px-6 py-4">
        <div class="mb-4">
            <label for="<?= _e($provider['id']) ?>_item"
                class="block mb-2 font-medium text-[0.9rem]"><?= __e('please select') ?>
                <span class="text-red-500">*</span></label>
            <select id="<?= _e($provider['id']) ?>_item" x-model="menuItemModel.value"
                x-on:change="updateExistingSelectedMenuItem($el)"
                class="block w-full py-2 px-3 rounded-md border border-primary-300 shadow-sm focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50">
                <option value="">---</option>
                <?php foreach ($manager->getProviderItems($provider['id']) as $item): ?>
                    <option value="<?= _e($item['id']) ?>">
                        <?= _e($item['title']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="<?= _e($provider['id']) ?>_target"
                class="block mb-2 font-medium text-[0.9rem]"><?= __e('Open this link in:') ?></label>
            <select id="<?= _e($provider['id']) ?>_target" x-model="menuItemModel.target"
                class="block w-full py-2 px-3 rounded-md border border-primary-300 shadow-sm focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50">
                <option value="self"><?= __e('Same Tab') ?></option>
                <option value="blank"><?= __e('New Tab') ?></option>
            </select>
        </div>
        <div class="flex justify-end">
            <button x-on:click="addMenuItem()"
                class="px-4 py-2 font-medium text-white transition-colors duration-300 text-[0.8rem] transform bg-accent-600 rounded hover:bg-accent-500 focus:outline-none focus:ring focus:ring-accent-300 focus:ring-opacity-80">
                <span x-text="isEditing ? '<?= __e('Done') ?>' : '<?= __e('Add Menu Item') ?>'"></span>
            </button>
        </div>
    </div>
</div>