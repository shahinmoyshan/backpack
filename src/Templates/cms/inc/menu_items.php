<div x-cloak x-show="menuItems.length > 0" x-sort
    x-sort:config="{handle: 'button[handle]', onSort: () => handleSort($el)}">
    <template x-for="(menuItem, index) in menuItems" :key="index + '_' + menuItem.id">
        <div>
            <div :data-id="menuItem.id" :style="menuItem.parent && 'margin-left: 40px;'"
                class="flex items-center justify-between border rounded-sm px-4 py-3 border-primary-300/75 hover:border-primary-300 hover:bg-primary-50/45 transition mb-3">
                <div class="flex items-center gap-2">
                    <button handle class="cursor-grab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-primary-500/75">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                        </svg>
                    </button>
                    <h3 class="font-medium text-[0.9rem] max-w-72 truncate" x-text="menuItem.title">
                    </h3>
                </div>
                <div class="flex items-center gap-3">
                    <span x-text="menuItem.parent && '<?= __e('Child Item') ?>'"
                        class="text-xs text-primary-500"></span>
                    <span x-text="__getLinkType(menuItem.type)" class="text-xs text-primary-500"></span>
                    <template x-if="!menuItem.parent && index > 0">
                        <button x-on:click="makeChild(index)" title="<?= __e('Move Right') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 text-primary-500 hover:text-primary-900 transition">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </template>
                    <template x-if="menuItem.parent">
                        <button x-on:click="removeChild(index)" title="<?= __e('Move Left') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 text-primary-500 hover:text-primary-900 transition">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                        </button>
                    </template>
                    <button x-on:click="editLink(menuItem)" title="<?= __e('edit') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-primary-500 hover:text-primary-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </button>
                    <button x-on:click="deleteMenuItem(menuItem)" title="<?= __e('delete') ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-red-500 hover:text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>