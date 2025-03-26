<?php

// set layout, title, and breadcrumb
$template->layout('master')
    ->set('title', 'menu builder')
    ->set('breadcrumb', ['__active' => 'menu builder']);

$menuItems = $manager->getSyncedMenuItems($manager->getLocation());
?>

<!-- Render the Menu Builder START -->
<div class="w-[1160px] mx-auto px-4 sm:px-6 lg:px-8 select-none overflow-x-auto">
    <div class="bg-white border border-primary-200 rounded-lg overflow-hidden shadow-xs"
        x-data="menuManager(/** time: <?= _e(microtime()) ?> */)">
        <div class="bg-primary-50 flex border-b border-primary-200 items-center justify-between px-4 py-4">
            <select
                class="py-2 text-sm rounded-sm focus:border-accent-300 border-primary-300 focus:ring-3 focus:ring-accent-200/50"
                x-on:change="$fire.navigate('<?= _e(route_url($manager->getConfig('route'), ['location' => '__menu_location'])) ?>'.replace('__menu_location', $el.value))">
                <?php foreach ($manager->getLocations() as $locationId => $location): ?>
                    <option <?= _e($manager->getLocation() == $locationId ? 'selected' : '') ?> value="<?= _e($locationId) ?>">
                        <?= __e($location) ?>
                    </option>
                <?php endforeach ?>
            </select>
            <form action="<?= request_url() ?>" method="post">
                <?= csrf() ?>
                <input type="hidden" name="__menuItems" :value="JSON.stringify(menuItems)">
                <button
                    class="px-4 py-2 font-medium text-white transition-colors duration-300 text-[0.8rem] transform bg-accent-600 rounded-sm hover:bg-accent-500 focus:outline-hidden focus:ring-3 focus:ring-accent-300/80"><?= __e('save changes') ?></button>
                <button type="button" x-on:click="discardChanges()"
                    class="px-4 py-2 font-medium text-white transition-colors duration-300 text-[0.8rem] transform bg-primary-700 rounded-sm hover:bg-primary-800 focus:outline-hidden focus:ring-3 focus:ring-primary-300/80"><?= __e('reload') ?></button>
            </form>
        </div>
        <div class="grid grid-cols-9">
            <div class="col-span-3">
                <?php
                require __DIR__ . '/inc/custom_menu_item.php';
                foreach ($manager->getMenuItemProviders() as $provider) {
                    require __DIR__ . '/inc/menu_items_providers.php';
                }
                ?>
            </div>
            <div class="col-span-6 border-l border-primary-200 px-6 py-4 h-[75vh] overflow-y-auto">
                <?php require __DIR__ . '/inc/menu_items.php' ?>
                <div x-cloak x-show="menuItems.length === 0">
                    <div class="flex flex-col items-center px-6 py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-8 text-primary-500 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <p class="text-center text-sm text-primary-500"><?= __e('No menu items found.') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Render the Menu Builder END -->

<script>
    function menuManager() {
        return {
            menuItems: <?= json_encode($menuItems) ?>,
            tab: null,
            isEditing: false,
            init() {
                this.changeTab('custom');
                this.childRelationshipValidator();
            },
            __getLinkType(type) {
                return <?= json_encode(
                    array_merge(
                        ['custom' => __('Custom Link')],
                        collect($manager->getMenuItemProviders())
                            ->mapK(fn($provider) => [$provider['id'] => $provider['label']])
                            ->all()
                    ),
                    JSON_FORCE_OBJECT
                ) ?>[type];
            },
            discardChanges() {
                if (!confirm('<?= __e('Are you sure you want to discard your changes?') ?>'))
                    return;

                this.$fire.reload();
            },
            deleteMenuItem(menuItem) {
                if (!confirm('<?= __e('Are you sure you want to delete this menu item?') ?>'))
                    return;

                if (menuItem.id === this.menuItemModel.id) {
                    this.isEditing = false;
                    this.resetMenuItemModel({ type: this.tab });
                }

                this.menuItems = this.menuItems.filter(item => item.id !== menuItem.id);

                this.$nextTick(() => {
                    this.childRelationshipValidator();
                });
            },
            changeTab(tab) {
                if (this.tab === tab) {
                    this.tab = null;
                    return;
                }
                this.tab = tab;
                this.isEditing = false;
                this.resetMenuItemModel({ type: tab });
            },
            menuItemModel: {},
            resetMenuItemModel(menuItem) {
                this.menuItemModel = {
                    id: Math.random().toString(36).slice(2),
                    type: 'custom',
                    title: '',
                    value: '',
                    target: 'self',
                    order: this.menuItems.length + 1,
                    parent: null,
                    ...menuItem
                };
            },
            addMenuItem() {
                if (!this.menuItemModel.title || !this.menuItemModel.value) {
                    alert('<?= __e('Please fill all the required fields') ?>');
                    return;
                }

                if (this.isEditing) {
                    this.isEditing = false;
                } else {
                    this.menuItems.push(this.menuItemModel);
                }

                this.resetMenuItemModel({ type: this.tab });
            },
            updateExistingSelectedMenuItem($el) {
                const option = $el.querySelector(`option[value="${this.menuItemModel.value}"]`);
                if (option) {
                    this.menuItemModel.title = option.innerText.trim();
                }
            },
            editLink(menuItem) {
                this.tab = menuItem.type;
                this.isEditing = true;
                this.menuItemModel = menuItem;
            },
            makeChild(index) {
                const menuItem = this.menuItems[index];
                const parentItem = this.menuItems.slice(0, index)
                    .reverse()
                    .find(item => item.parent === null);

                if (parentItem) {
                    menuItem.parent = parentItem.id;
                    this.menuItems.map(item => {
                        if (item.parent === menuItem.id) {
                            item.parent = parentItem.id;
                        }
                    });
                }
            },
            removeChild(index) {
                const menuItem = this.menuItems[index];
                menuItem.parent = null;
                this.menuItems.slice(index + 1).forEach(item => {
                    if (item.parent === null) {
                        return;
                    }
                    item.parent = menuItem.id;
                });
            },
            handleSort($el) {
                // Update the order of each menu item based on their position in the DOM
                $el.querySelectorAll('[data-id]').forEach((el, index) => {
                    this.menuItems.find(item => item.id === el.dataset.id).order = index;
                });

                // Re-sort items to maintain consistency
                this.menuItems.sort((a, b) => a.order - b.order);

                // Validate parent-child relationships
                this.$nextTick(() => {
                    this.childRelationshipValidator();
                });
            },
            childRelationshipValidator() {
                this.menuItems.map((item, index) => {
                    if (item.parent !== null) {
                        const parentItem = this.menuItems.slice(0, index)
                            .reverse()
                            .find(item => item.parent === null);

                        if (!parentItem) {
                            item.parent = null;
                            return;
                        }

                        if (parentItem && parentItem.id !== item.parent) {
                            item.parent = parentItem.id;
                        }
                    }
                });
            },
        };
    }
</script>