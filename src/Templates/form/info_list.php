<?php

if (isset($field['value']) && is_string($field['value'])) {
    $field['value'] = (array) json_decode($field['value'], true);
}

$setup = array_merge(['columns' => [], 'form' => []], $field['attrs']['setup'] ?? []);
$value = collect((array) $field['value'] ?? [])
    ->filter(fn($item) => is_array($item))
    ->map(fn($item) => array_merge(['id' => uniqid()], $item))
    ->all();
?>
<div x-data="{
    /** time: <?= __e(microtime()) ?> */
    list: <?= _e(json_encode($value)) ?>,
    form: {},
    showModal: false,
    editItem(item) {
        this.showModal = true;
        this.form = { ...item };
    },
    deleteItem(id) {
        if (!confirm('<?= __e('are you sure') ?>')) {
            return;
        }

        this.list = this.list.filter(item => item.id !== id);
    },
    saveItem() {
        if (this.form.id) {
            this.list = this.list.map(item => item.id === this.form.id ? this.form : item);
        } else {
            this.list.push({ id: Math.random().toString(36).slice(2), ...this.form });
        }

        this.showModal = false;
        this.form = {};
    }
}">
    <!-- Info List Header -->
    <div class="flex items-center justify-between mb-2">
        <button type="button" x-on:click="showModal=true"
            class="bg-accent-600 hover:bg-accent-700 text-sm font-medium text-white px-2 py-1 rounded flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <?= __e('add') ?>
        </button>
        <span class="text-sm font-medium"
            x-text="'<?= __e('%s items', '{total}') ?>'.replace('{total}', list.length)"></span>
    </div>

    <!-- Items List -->
    <table class="w-full border">
        <thead>
            <tr class="bg-primary-50 text-sm">
                <?php foreach ($setup['columns'] as $column): ?>
                    <th class="px-3 py-1.5 font-semibold text-left"><?= _e($column) ?></th>
                <?php endforeach ?>
                <th class="px-3 py-1.5 font-semibold text-left"><?= __e('action') ?></th>
            </tr>
        </thead>
        <tbody>
            <template x-for="item in list" :key="item.id">
                <tr class="border-t hover:bg-primary-50">
                    <?php foreach (array_keys($setup['columns']) as $column): ?>
                        <td class="px-3 py-1.5">
                            <span class="text-sm" x-text="item.<?= $column ?>"></span>
                        </td>
                    <?php endforeach ?>
                    <td class="px-3 py-1.5">
                        <span class="flex flex-wrap gap-x-2 gap-y-1">
                            <button x-on:click="editItem(item)" type="button" title="<?= __e('edit') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-5 text-amber-600 hover:text-amber-700">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>
                            <button type="button" x-on:click="deleteItem(item.id)" title="<?= __e('edit') ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-5 text-red-600 hover:text-red-700">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </span>
                    </td>
                </tr>
            </template>
            <tr x-show="!list.length">
                <td colspan="<?= count($setup['columns']) + 1 ?>" class="border-t py-2 px-4">
                    <p class="text-sm text-primary-500 text-center"><?= __e('no items') ?></p>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Modal: for Form -->
    <div x-cloak x-show="showModal"
        class="fixed inset-0 z-40 bg-black/30 w-full h-full flex items-center justify-center">
        <div class="bg-white w-full max-w-xl p-6 sm:rounded-lg shadow-lg"
            x-on:keyup.escape.window="showModal=false, form= {}" x-on:keydown.enter.prevent="saveItem()">
            <h3 class="font-semibold mb-2"><?= _e($field['placeholder'] ?? __('Add new Item')) ?></h3>
            <input type="hidden" x-model="form.id">
            <?php foreach ($setup['form'] as $input):
                if (!in_array($input['type'], ['select', 'textarea', 'text', 'email', 'url', 'password', 'number', 'date', 'time', 'datetime-local', 'color'])) {
                    continue;
                }
                $inputClass = 'w-full text-sm border-primary-300 focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50 rounded-md';
                ?>
                <label class="mt-5 text-sm flex flex-col md:flex-row gap-2 items-start md:items-center">
                    <span
                        class="md:w-4/12"><?= _e($columns[$input['name']] ?? ($input['label'] ?? __(pretty_text($input['name'])))) ?></span>
                    <div class="md:w-8/12">
                        <?php if (strtolower($input['type']) === 'select'): ?>
                            <select class="<?= $inputClass ?>" x-model="form.<?= _e($input['name']) ?>">
                                <?php foreach ($input['options'] ?? [] as $opt_val => $opt_text): ?>
                                    <option value="<?= _e($opt_val) ?>"><?= _e($opt_text) ?></option>
                                <?php endforeach ?>
                            </select>
                        <?php elseif (strtolower($input['type']) === 'textarea'): ?>
                            <textarea class="<?= $inputClass ?>" placeholder="<?= _e($input['placeholder'] ?? '') ?>"
                                x-model="form.<?= _e($input['name']) ?>"></textarea>
                        <?php else: ?>
                            <input type="<?= _e($input['type']) ?>" placeholder="<?= _e($input['placeholder'] ?? '') ?>"
                                class="<?= $inputClass ?>" x-model="form.<?= _e($input['name']) ?>">
                        <?php endif ?>
                        <?php if (isset($input['description'])): ?>
                            <small class="block w-full"><?= _e($input['description']) ?></small>
                        <?php endif ?>
                    </div>
                </label>
            <?php endforeach ?>
            <div class="mt-6 flex gap-2 items-center">
                <button type="button" x-on:click="saveItem()"
                    class="px-4 py-2 text-sm font-medium text-white transition-colors duration-300 transform bg-accent-600 rounded-md hover:bg-accent-500 focus:outline-none focus:ring focus:ring-accent-300 focus:ring-opacity-80"><?= _e(__('save')) ?></button>
                <button type="button" x-on:click="showModal=false, form= {}"
                    class="px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80"><?= _e(__('cancel')) ?></button>
            </div>
        </div>
    </div>

    <!-- Hidden Input to pass info list data -->
    <input type="hidden" name="<?= _e($field['name']) ?>" :value="JSON.stringify(list)">
</div>