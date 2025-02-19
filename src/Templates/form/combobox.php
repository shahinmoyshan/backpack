<?php

// check if the field is ajax searchable, if so, add the search option
if (isset($field['attrs']['ajax_url']) && $field['attrs']['ajax_url']) {
    $field['attrs']['searchable'] = true;

    if(!empty($field['value']) && empty($field['options']) && isset($field['attrs']['model'])) {
        $field['options'] = collect(
            $field['attrs']['model']::get()->where(['id' => $field['value']])->result()
        )
            ->mapK(fn($d) => [$d->id => (string) $d])
            ->all();
    }
}

if (!isset($field['id'])) {
    $field['id'] = 'combobox-' . uniqid();
}

$field = array_merge(['multiple' => false, 'value' => null, 'options' => []], $field);

$x_fieldName = 'selectedOptions_' . uniqid();

?>

<div x-data="{
        isOpen: false,
        openedWithKeyboard: false,
        <?= $x_fieldName ?>: <?= _e($field['multiple'] ? (empty(array_filter((array) $field['value'] ?? [])) ? '[]' : '[\'' . implode('\',\'', (array) $field['value']) . '\']') : ('\'' . $field['value'] . '\'')) ?>,
        setLabelText() {
            const count = this.<?= $x_fieldName ?>.length;
            // if there are no selected options, then return the placeholder
            if (count === 0) return '<?= _e(isset($field['placeholder']) && !empty($field['placeholder']) ? $field['placeholder'] : __('please select')) ?>';            

            // escape special characters in the selected options
            const sanitizeSelector = (value) => value.replace(/([!#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~])/g, '\\$1');

            // join the selected options with a comma
            <?php if ($field['multiple']): ?>
                // get the labels of the selected options
                const labels = this.<?= $x_fieldName ?>.map((option) => {
                    <?php if (isset($field['attrs']['ajax_url'])): ?>
                        // if the option is in the dynamicOptions object, use its label
                        if (this.dynamicOptions.hasOwnProperty(option)) {
                            return this.dynamicOptions[option].trim();
                        }
                    <?php endif ?>
                    // otherwise, get the label from the DOM
                    const label = $el.querySelector(`label[id=combobox_<?= _e($field['id']) ?>_option_${sanitizeSelector(option)}]`);
                    // if the label exists, return its text, otherwise return the option value as label
                    return label ? label.innerText.trim() : option;
                })
                return labels.join(', ');
            <?php else: ?>
                <?php if(isset($field['attrs']['ajax_url'])):?>
                    const dynamic_label = this.dynamicOptions[this.<?= $x_fieldName ?>] || false;
                    if (dynamic_label) {
                        return dynamic_label ? dynamic_label.trim() : this.<?= $x_fieldName ?>;
                    }
                <?php endif?>
                const label = $el.querySelector(`label[id=combobox_<?= _e($field['id']) ?>_option_${sanitizeSelector(this.<?= $x_fieldName ?>)}]`);
                // if the label exists, return its text, otherwise return the selected option value as label
                return label ? label.innerText.trim() : this.<?= $x_fieldName ?>;
            <?php endif ?>
        },
        <?php if (isset($field['attrs']['searchable']) && $field['attrs']['searchable']): ?>
            notFound: <?= _e(empty($field['options']) ? 'true' : 'false') ?>,
            searchKeyword: '',
            <?php if (isset($field['attrs']['ajax_url'])): ?>
                isSearching: false,
                dynamicOptions: <?= _e(json_encode($field['options'], JSON_FORCE_OBJECT)) ?>,
                searchResults: {},
                <?php if(isset($field['options']) && !empty($field['options'])):?>
                init() {
                    /** time: <?= microtime()?>: this hack make this combobox refreshed each time in fireline. */
                    this.searchResults = this.dynamicOptions;
                },
                <?php endif?>
                filteredComboboxSearchOptions() {
                    this.isSearching = true;
                    const keyword = this.searchKeyword.trim();
                    // fetch search results from the server
                    fetch('<?= _e($field['attrs']['ajax_url']) ?>', {
                        method: 'POST',
                        body: JSON.stringify({ keyword: keyword, _token: '<?= _e(csrf_token()) ?>' })
                    })
                        .then(response => response.json())
                        .then(results => {
                            this.isSearching = false;
                            this.notFound = Object.keys(results).length === 0;

                            <?php if ($field['multiple']): ?>
                                // backup old options before replacing searchResults
                                this.<?= $x_fieldName ?>.forEach(selectedId => {
                                    if (this.searchResults.hasOwnProperty(selectedId)) {
                                        this.dynamicOptions[selectedId] = this.searchResults[selectedId];
                                    }
                                });
                            <?php endif?>

                            // replace searchResults
                            this.searchResults = results;

                            <?php if ($field['multiple']): ?>
                                // restore old options when searchKeyword is empty
                                if (this.notFound && keyword.length === 0 && Object.keys(this.dynamicOptions).length > 0) {
                                    this.searchResults = this.dynamicOptions;
                                    this.dynamicOptions = {};
                                    this.notFound = false;
                                }
                            <?php endif?>
                        });
                },
            <?php else: ?>
                filteredComboboxSearchOptions() {
                    // get all the options available in this combobox
                    const options = $el.querySelectorAll('label[option]');
                    let found = 0;

                    // loop through all the options
                    for(var i = 0; i < options.length; i++) {
                        const option = options[i];
                        // if option contains the search keyword, show it
                        if(option.innerText.toLowerCase().includes(this.searchKeyword.toLowerCase())) {
                            found++;
                            option.classList.remove('hidden');
                        } else {
                            // otherwise hide it
                            option.classList.add('hidden');
                        }
                    }

                    // if no options are found, show the not found message
                    this.notFound = found === 0;
                },
            <?php endif ?>
            handleSearchedOnOptions(event) {
                // if Enter pressed, prevent form submission
                if (event.key === 'Enter') event.preventDefault();

                // if the user presses backspace or the alpha-numeric keys, focus on the search field
                if ((event.keyCode >= 65 && event.keyCode <= 90) || (event.keyCode >= 48 && event.keyCode <= 57) || event.keyCode === 8) {
                    this.$refs.searchField.focus()
                }
            },
        <?php else: ?>
            highlightFirstMatchingOption(pressedKey) {
                // if Enter pressed, do nothing
                if (pressedKey === 'Enter') return;
    
                // find and focus the option that starts with the pressed key
                const options = this.$el.querySelectorAll('label[option]');
                for (var i = 0; i < options.length; i++) {
                    const option = options[i];
                    // if option starts with the pressed key, focus it
                    if (option.innerText.toLowerCase().startsWith(pressedKey.toLowerCase())) {
                        option.focus();
                        break;
                    }
                }
            },
        <?php endif ?>
    }" <?php if (isset($field['attrs']['watch'])): ?>
            x-init="$watch('<?= $x_fieldName ?>', value => <?= _e($field['attrs']['watch'])?>)"
        <?php endif?>
        <?php if (isset($field['attrs']['searchable']) && $field['attrs']['searchable']): ?>
            x-on:keydown="handleSearchedOnOptions($event)"
        <?php else: ?>
            x-on:keydown="highlightFirstMatchingOption($event.key)"
        <?php endif ?> x-on:keydown.esc.window="isOpen = false, openedWithKeyboard = false">
    <div class="relative">

        <!-- trigger button  -->
        <button type="button" role="combobox" class="inline-flex w-full items-center {inputClass}"
            aria-haspopup="listbox" aria-controls="<?= _e($field['id']) ?>" x-on:click="isOpen = ! isOpen"
            x-on:keydown.down.prevent="openedWithKeyboard = true" x-on:keydown.enter.prevent="openedWithKeyboard = true"
            x-on:keydown.space.prevent="openedWithKeyboard = true" :aria-label="setLabelText()"
            :aria-expanded="isOpen || openedWithKeyboard">
            <span class="w-full font-normal text-start text-ellipsis whitespace-nowrap overflow-hidden"
                x-text="setLabelText()"></span>
            <?php if (isset($field['attrs']['clear']) && $field['attrs']['clear']): ?>
                <!-- Clear Button -->
                <span x-cloak x-show="<?= $x_fieldName ?>.length > 0" class="text-primary-600 hover:text-primary-900 transition"
                    x-on:click.stop="<?= $x_fieldName ?>=<?= _e($field['multiple'] ? '[]' : '\'\'') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </span>
            <?php endif ?>
            <!-- Chevron  -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                class="size-6 text-primary-600 transform transition"
                :class="isOpen || openedWithKeyboard ? 'rotate-180' : ''">
                <path fill-rule="evenodd"
                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
                    s clip-rule="evenodd" />
            </svg>
        </button>

        <!-- hidden input to grab the selected value  -->
        <input id="<?= _e($field['id']) ?>" name="<?= _e($field['name']) ?>" type="hidden" :value="<?= $x_fieldName ?>" />

        <!-- combobox modal -->
        <div x-cloak x-show="isOpen || openedWithKeyboard"
            class="absolute z-10 left-0 top-11 w-full border rounded-lg border-primary-300 bg-white shadow" role="listbox"
            x-on:click.outside="isOpen = false, openedWithKeyboard = false" x-on:keydown.down.prevent="$focus.wrap().next()"
            x-on:keydown.up.prevent="$focus.wrap().previous()" x-transition x-trap="openedWithKeyboard">

            <?php if (isset($field['attrs']['searchable']) && $field['attrs']['searchable']): ?>
                <!-- Search  -->
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none"
                        stroke-width="1.5" class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-primary-700/50"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text"
                        class="w-full border-0 border-b border-primary-300 bg-primary-50 py-2.5 pl-11 pr-4 text-sm text-primary-700 focus:outline-none focus:ring-0 focus:border-primary-300 rounded-t-lg"
                        aria-label="Search"
                        x-on:input.debounce.250ms="searchKeyword= $el.value, filteredComboboxSearchOptions()"
                        placeholder="<?= _e(__('search')) ?>" x-ref="searchField" />
                </div>
            <?php endif ?>

            <!-- combobox options -->
            <div class="py-1.5 max-h-56 overflow-y-auto">
                <ul class="flex flex-col" <?php if (($field['multiple'] ?? false) === false) : ?> x-init="$el.addEventListener('click', event => {
                    const label = event.target.closest('label');
                    if (label) {
                        isOpen = false;
                        openedWithKeyboard = false;
                    }
                })" <?php endif ?> x-ref="comboboxOptions">
                    <!-- option  -->
                    <?php if (isset($field['attrs']['ajax_url'])): ?>
                        <template x-for="(label, value) in searchResults" :key="value">
                            <li role="option">
                                <label
                                    class="flex cursor-pointer items-center gap-2 px-4 py-3 text-sm font-medium hover:bg-primary-100 has-[:focus]:bg-primary-100 [&:has(input:checked)]:text-black [&:has(input:checked)]:bg-primary-50 border-l-2 border-transparent [&:has(input:checked)]:border-l-accent-600 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75 select-none"
                                    :id="'combobox_<?= _e($field['id']) ?>_option_'+ value" option>
                                    <div class="relative flex items-center">
                                        <input <?= isset($field['attrs']['disabled']) && $field['attrs']['disabled'] ? 'disabled' : '' ?>
                                            type="<?= _e($field['multiple'] ? 'checkbox' : 'radio') ?>" :value="value"
                                            x-model="<?= $x_fieldName ?>" x-on:keydown.enter.prevent="$el.click()" />
                                    </div>
                                    <span x-text="label"></span>
                                </label>
                            </li>
                        </template>
                    <?php else: ?>
                        <?php foreach ($field['options'] as $value => $label): ?>
                            <li role="option">
                                <label
                                    class="flex cursor-pointer items-center gap-2 px-4 py-3 text-sm font-medium hover:bg-primary-100 has-[:focus]:bg-primary-100 [&:has(input:checked)]:text-black [&:has(input:checked)]:bg-primary-50 border-l-2 border-transparent [&:has(input:checked)]:border-l-accent-600 [&:has(input:disabled)]:cursor-not-allowed [&:has(input:disabled)]:opacity-75 select-none"
                                    id="combobox_<?= _e($field['id']) ?>_option_<?= _e($value) ?>" option>
                                    <div class="relative flex items-center">
                                        <input <?= isset($field['attrs']['disabled']) && $field['attrs']['disabled'] ? 'disabled' : '' ?>
                                            type="<?= _e($field['multiple'] ? 'checkbox' : 'radio') ?>"
                                            value="<?= _e($value) ?>" x-model="<?= $x_fieldName ?>"
                                            x-on:keydown.enter.prevent="$el.click()" />
                                    </div>
                                    <span><?= _e($label) ?></span>
                                </label>
                            </li>
                        <?php endforeach ?>
                    <?php endif ?>
                </ul>
                <?php if (isset($field['attrs']['searchable']) && $field['attrs']['searchable']): ?>
                    <!-- combobox messages -->
                    <p x-cloak x-show="notFound" class="px-4 py-2 text-sm text-primary-700">
                        <?php if (isset($field['attrs']['ajax_url'])): ?>
                            <span x-cloak x-show="isSearching"><?= _e(__('searching...')) ?></span>
                            <span x-cloak x-show="!isSearching && searchKeyword"
                                x-text="'<?= _e(__('no matches for: #')) ?>'.replace('#', searchKeyword)"></span>
                            <span x-cloak
                                x-show="!isSearching && !searchKeyword"><?= _e(__('start typing to search...')) ?></span>
                        <?php else: ?>
                            <span><?= _e(__('no matches found')) ?></span>
                        <?php endif ?>
                    </p>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>