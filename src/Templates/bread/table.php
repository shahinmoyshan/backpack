<?php

if (!function_exists('parse_anchor_url')) {
    /**
     * Parse an anchor URL by adding the given key-value pair to the current
     * request query parameters.
     *
     * @param Hyper\Request $request The current request object.
     * @param string $key The key to add to the query parameters.
     * @param string $value The value to add to the query parameters.
     *
     * @return string The parsed anchor URL.
     */
    function parse_anchor_url($request, string $key, string $value): string
    {
        return $request->getPath() . '?' . http_build_query(
            collect($request->getQueryParams())
                ->except(['page'])
                ->add($key, $value)
                ->all()
        );
    }
}

?>
<section
    class="<?= ['full' => 'w-full', '7xl' => 'max-w-7xl', '6xl' => 'max-w-6xl', '5xl' => 'max-w-5xl', '4xl' => 'max-w-4xl', '3xl' => 'max-w-3xl', '2xl' => 'max-w-2xl', 'xl' => 'max-w-xl', 'lg' => 'max-w-lg', 'mx' => 'max-w-md', 'sm' => 'max-w-sm'][$bread->config['customize']['width']['table'] ?? 'full'] ?> mx-auto">
    <!-- Bread Page Heading Part START -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between gap-3">
        <h2 class="font-bold text-2xl text-primary-800 leading-tight"><?= _e($bread->getConfig('title', '')) ?></h2>
        <div class="flex flex-wrap md:justify-end gap-3">
            <?php if (!empty($bread->getConfig('buttons'))): ?>
                <?php foreach ($bread->getConfig('buttons') as $button): ?>
                    <?php if (isset($button['when']) && !call_user_func($button['when'])) {
                        continue;
                    } ?>
                    <a href="<?= _e($button['url']) ?>"
                        class="px-4 py-2 flex items-center gap-1 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow-sm shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-hidden focus:ring-3 focus:ring-primary-300/80">
                        <?= $button['icon'] ?? '' ?>
                        <span><?= _e($button['title']) ?></span>
                    </a>
                <?php endforeach ?>
            <?php endif ?>
            <?php if (!isset($bread->getConfig('permissions', [])['create']) || has_permission($bread->getConfig('permissions', [])['create'])): ?>
                <a href="<?= route_url($bread->getConfig('route')) . '/create' ?>"
                    class="px-4 py-2 flex text-sm items-center gap-1 font-medium tracking-wide text-white transition-colors duration-300 transform bg-accent-600 shadow-sm shadow-accent-200 rounded-lg hover:bg-accent-500 focus:outline-hidden focus:ring-3 focus:ring-accent-300/80">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span><?= _e(__('new %s', $bread->getConfig('title_singular', ''))) ?></span>
                </a>
            <?php endif ?>
        </div>
    </div> <!-- Bread Page Heading Part END -->
    <div <?php if (!empty($bread->getConfig('bulk_actions'))): ?> x-data="{
        /** time: <?= _e(microtime()) ?> */
        checked: [],
        total_items: <?= _e(count($bread->getPaginator()->getData())) ?>,
        toggleAllItems() {
            if (this.checked.length == this.total_items) {
                this.checked = [];
            } else {
                $el.querySelectorAll('input[type=checkbox][value]').forEach(checkbox => {
                    if (!this.checked.includes(checkbox.value)) {
                        this.checked.push(checkbox.value);
                    }
                });
            }
        },
        applyBulkAction(action) {
            const actionRoute = '<?= _e(route_url($bread->getConfig('route')) . '/bulk_action?action={action}&ids={ids}&__get_redirect=' . serialize(request()->getQueryParams())) ?>';
            const actionUrl = actionRoute.replace('{action}', action).replace('{ids}', this.checked.join(','));
            if (actionUrl.includes('__ignore_fire')) {
                window.location = actionUrl;
            } else {
                $fire.navigate(actionUrl);
            }
        }
    }" <?php endif ?> class="bg-white border border-primary-200 shadow-lg sm:rounded-lg">
        <!-- Bread Header Part START -->
        <div
            class="bg-primary-50 sm:rounded-t-lg flex flex-col md:flex-row gap-y-3 items-start md:items-center justify-between px-4 py-3 border-b border-primary-300">
            <div class="flex items-center gap-2.5">
                <?php if (!empty($bread->getConfig('bulk_actions')) && empty($bread->getConfig('columns'))): ?>
                    <input x-cloak x-show="total_items" x-on:click="toggleAllItems()"
                        title="<?= _e(__('select/unselect all')) ?>" :checked="total_items === checked.length"
                        type="checkbox" class="mr-2">
                <?php endif ?>
                <?php if (!empty($bread->getConfig('sort'))): ?>
                    <select
                        x-on:change="$fire.navigate('<?= _e(parse_anchor_url($bread->getRequest(), 'sortColumn', 'sortColumn')) ?>'.replace('sortColumn=sortColumn', `sortColumn=${$event.target.value}`))"
                        class="rounded-md text-sm py-1.5 shadow-xs focus:border-accent-300 border-primary-300 focus:ring-3 focus:ring-accent-200/50">
                        <option value=""><?= _e(__('sort by -')) ?></option>
                        <?php foreach ($bread->config['sort'] as $sortColumn => $sortTitle): ?>
                            <option <?= $bread->getRequest()->query('sortColumn', '') === $sortColumn ? 'selected' : '' ?>
                                value="<?= _e($sortColumn) ?>"><?= _e($sortTitle) ?></option>
                        <?php endforeach ?>
                    </select>
                <?php endif ?>
                <?php if (!empty($bread->getRequest()->query('sortColumn'))): ?>
                    <?php $sortDirection = $bread->getRequest()->query('sortDirection', 'asc') ?>
                    <select
                        x-on:change="$fire.navigate('<?= _e(parse_anchor_url($bread->getRequest(), 'sortDirection', 'sortDirection')) ?>'.replace('sortDirection=sortDirection', `sortDirection=${$event.target.value}`))"
                        class="rounded-md text-sm py-1.5 shadow-xs focus:border-accent-300 border-primary-300 focus:ring-3 focus:ring-accent-200/50">
                        <option <?= _e($sortDirection === 'asc' ? 'selected' : '') ?> value="asc">
                            <?= _e(__('ascending')) ?>
                        </option>
                        <option <?= _e($sortDirection === 'desc' ? 'selected' : '') ?> value="desc">
                            <?= _e(__('descending')) ?>
                        </option>
                    </select>
                <?php endif ?>
            </div>
            <div class="flex gap-2 flex-wrap">
                <?php if (!empty($bread->getConfig('filter'))):
                    $filters = collect($bread->getConfig('filter'))
                        ->mapK(function ($filter, $key) {
                            if (!isset($filter['group'])) {
                                $filter['group'] = '';
                            }
                            return [$key => $filter];
                        })
                        ->group('group')
                        ->all();
                    ?>
                    <select
                        x-on:change="$fire.navigate('<?= _e(parse_anchor_url($bread->getRequest(), 'filter', 'filter')) ?>'.replace('filter=filter', `filter=${$event.target.value}`))"
                        class="rounded-md text-[0.8rem] py-1 shadow-xs focus:border-accent-300 border-primary-300 focus:ring-3 focus:ring-accent-200/50">
                        <option value=""><?= _e(__('filter by -')) ?></option>
                        <?php foreach ($filters as $group => $filterItems): ?>
                            <?php if (!empty($group)): ?>
                                <optgroup label="<?= _e($group) ?>">
                                <?php endif ?>
                                <?php foreach ($filterItems as $filterKey => $filterSetup): ?>
                                    <option <?= $bread->getRequest()->query('filter') === $filterKey ? 'selected' : '' ?>
                                        value="<?= _e($filterKey) ?>"><?= _e($filterSetup['title']) ?></option>
                                <?php endforeach ?>
                                <?php if (!empty($group)): ?>
                                </optgroup>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select>
                <?php endif ?>
                <?php if (!empty($bread->getConfig('search'))): ?>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>
                        <input
                            x-on:input.debounce.500ms="$fire.navigate('<?= _e(parse_anchor_url($bread->getRequest(), 'q', 'qq')) ?>'.replace('q=qq', `q=${$event.target.value}`))"
                            type="text" value="<?= _e($bread->getRequest()->query('q', '')) ?>"
                            class="w-48 py-1.5 text-sm pl-8 pr-4 text-primary-800 bg-white border border-primary-300 rounded-md focus:border-accent-300 focus:outline-hidden focus:ring-3 focus:ring-accent-200/50"
                            placeholder="<?= _e(__('search')) ?>">
                    </div>
                <?php endif ?>
            </div>
        </div> <!-- Bread Header Part END -->
        <!-- Bread Bulk Actions START -->
        <?php if (!empty($bread->getConfig('bulk_actions'))): ?>
            <div x-cloak x-show="checked.length"
                class="bg-primary-50 px-4 py-3 border-b border-primary-300 flex justify-between items-center"
                x-data="{msg_singular: '<?= _e(__('%s record selected', ' ')) ?>', msg_plural: '<?= _e(__('%s records selected', ' ')) ?>'}">
                <select x-on:change="applyBulkAction($el.value)"
                    class="rounded-md text-[0.8rem] py-0.5 shadow-xs focus:border-accent-300 border-primary-300 focus:ring-3 focus:ring-accent-200/50">
                    <option><?= _e(__('bulk actions -')) ?></option>
                    <?php foreach ($bread->getConfig('bulk_actions') as $action => $actionSetting): ?>
                        <?php if (isset($actionSetting['when']) && !call_user_func($actionSetting['when'])) {
                            continue;
                        } ?>
                        <option value="<?= _e($action) ?>"><?= _e($actionSetting['title']) ?></option>
                    <?php endforeach ?>
                </select>
                <span class="hidden md:block font-medium text-sm text-primary-700"
                    x-text="checked.length + (checked.length == 1 ? msg_singular : msg_plural)"></span>
            </div>
        <?php endif ?> <!-- Bread Bulk Actions END -->
        <!-- Bread Table Part START -->
        <?php if ($bread->getPaginator()->hasData()): ?>
            <table class="w-full block overflow-x-auto table-auto text-[0.9rem]">
                <?php if (!empty($bread->getConfig('columns'))):
                    $columnsWidth = $bread->getConfig('columns_width', []); ?>
                    <thead>
                        <tr class="bg-primary-50 border-b border-primary-300">
                            <?php if (!empty($bread->getConfig('bulk_actions'))): ?>
                                <th width="<?= _e($columnsWidth['checkbox'] ?? '') ?>" class="text-left px-4 py-3 font-medium">
                                    <input x-on:click="toggleAllItems()" title="<?= _e(__('select/unselect all')) ?>"
                                        :checked="total_items === checked.length" type="checkbox" class="mr-2">
                                </th>
                            <?php endif ?>
                            <?php foreach ($bread->getConfig('columns') as $column): ?>
                                <th width="<?= _e($columnsWidth[$column] ?? '') ?>" class="text-left px-4 py-3 font-medium">
                                    <?= _e($column) ?>
                                </th>
                            <?php endforeach ?>
                            <?php if (empty($bread->getConfig('actions'))): ?>
                                <th width="<?= _e($columnsWidth['action'] ?? '') ?>" class="text-right px-4 py-3 font-medium">
                                    <?= _e(__('action')) ?>
                                </th>
                            <?php endif ?>
                        </tr>
                    </thead>
                <?php endif ?>
                <tbody <?php if (!empty($bread->getConfig('bulk_actions'))): ?> x-init="$el.addEventListener('click', (event) => {
                const checkbox = event.target.closest('input[type=checkbox]');
                if (checkbox) {
                    if(checked.includes(checkbox.value)){
                        checked = checked.filter(item => item !== checkbox.value);
                    } else {
                        checked.push(checkbox.value);
                    }
                }
            })" <?php endif ?>>
                    <?php foreach ($bread->getPaginator()->getData() as $key => $item): ?>
                        <tr
                            :class="checked.includes('<?= _e($item->id) ?>') ? 'bg-primary-50 border-l-2 border-l-accent-600 border-t first:border-t-0 hover:bg-primary-50 group border-primary-200' : 'border-t first:border-t-0 hover:bg-primary-50 group border-primary-200'">
                            <?php if (!empty($bread->getConfig('bulk_actions'))): ?>
                                <td class="py-4 px-4" width="1%">
                                    <input :checked="checked.includes('<?= _e($item->id) ?>')" value="<?= _e($item->id) ?>"
                                        type="checkbox" class="mr-2">
                                </td>
                            <?php endif ?>
                            <?php

                            $view_url = route_url($bread->getConfig('route')) . '/' . $item->id . $bread->getConfig('view_url_suffix', '');

                            if (isset($bread->getConfig('partials', [])['list'])) {
                                echo $bread->partial($bread->getConfig('partials', [])['list'], ['key' => $key, 'item' => $item]);
                            } elseif (!empty($bread->getConfig('columns'))) {
                                $index = 0;
                                foreach ($bread->getConfig('columns') as $columnKey => $column) {
                                    $index++;
                                    $field = $item->{$columnKey} ?? '';

                                    if (isset($bread->getConfig('formatter', [])[$columnKey])) {
                                        $field = call_user_func($bread->getConfig('formatter', [])[$columnKey], $field);
                                    }

                                    if (is_array($field)) {
                                        $field = json_encode($field, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                    }

                                    if ($index === 1) {
                                        $field = <<<ANCHOR
                                            <a class="hover:underline" href="{$view_url}">{$field}</a>
                                        ANCHOR;
                                    }

                                    if (empty($field)) {
                                        $field = '<span class="opacity-60">&mdash;</span>';
                                    }

                                    echo <<<TD
                                        <td class="px-4 py-4">{$field}</td>
                                    TD;
                                }
                            } else {
                                echo <<<TD
                                    <td class="px-4 py-4"><a class="hover:underline" href="{$view_url}">{$item}</a></td>
                                TD;
                            }
                            ?>
                            <?php if (!empty($bread->getConfig('action'))): ?>
                                <td class="py-4 px-4">
                                    <div
                                        class="<?= empty($bread->getConfig('columns')) ? 'md:invisible md:group-hover:visible' : '' ?> flex items-center justify-end flex-wrap gap-y-1 gap-x-2">
                                        <?php foreach ($bread->getConfig('action') as $actionType => $actionSetting):
                                            if (!is_array($actionSetting) && is_callable($actionSetting)) {
                                                $actionSetting = call_user_func($actionSetting, $item);
                                            }
                                            ?>
                                            <?php
                                            if (!is_array($actionSetting) || (isset($actionSetting['when']) && !call_user_func($actionSetting['when'], $item))) {
                                                continue;
                                            }
                                            $actionUrl = isset($actionSetting['url']) ? is_callable($actionSetting['url']) ? call_user_func($actionSetting['url'], $item) : $actionSetting['url'] : route_url($bread->getConfig('route')) . '/' . sprintf($actionSetting['route'], $item->id);
                                            $tooltip = $actionSetting['tooltip'] ?? '';
                                            ?>
                                            <div class="relative">
                                                <a href="<?= _e($actionUrl) ?>" target="<?= _e($actionSetting['target'] ?? '_self') ?>"
                                                    class="<?= _e(!empty($tooltip) ? 'peer' : '') ?> flex items-center gap-1 text-[0.8rem] font-medium hover:underline <?= ['edit' => 'text-amber-600 hover:text-amber-700', 'delete' => 'text-red-600 hover:text-red-700', 'view' => 'text-accent-600 hover:text-accent-700'][$actionType] ?? '' ?>">
                                                    <?= $actionSetting['icon'] ?>
                                                    <span><?= _e($actionSetting['title'] ?? '') ?></span>
                                                </a>
                                                <?php if (!empty($tooltip)): ?>
                                                    <div class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 z-10 flex w-max max-w-48 flex-col gap-1 rounded-sm bg-primary-800 px-2 py-1.5 text-[0.8rem] text-primary-50 opacity-0 transition-all ease-out peer-hover:opacity-100 peer-focus:opacity-100"
                                                        role="tooltip"><?= _e($tooltip) ?></div>
                                                <?php endif ?>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                </td>
                            <?php endif ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table> <!-- Bread Table Part END -->
            <!-- Bread Footer Part START -->
            <div
                class="bg-primary-50 sm:rounded-b-lg flex items-center justify-center md:justify-between px-4 py-3 border-t border-primary-300">
                <p class="text-sm text-primary-800 w-4/12 hidden md:block">
                    <?php
                    $start = ($bread->getPaginator()->getPage() - 1) * $bread->getPaginator()->limit + 1;
                    $limit = $bread->getPaginator()->getPage() * $bread->getPaginator()->limit;
                    $total = $bread->getPaginator()->total;
                    echo _e(__(
                        'showing %d to %d of %s results',
                        [
                            $start,
                            $limit > $total ? $total : $limit,
                            number_format($total)
                        ]
                    )) ?>
                </p>
                <div class="md:w-4/12 text-center hidden md:block">
                    <?php
                    $perPage = $bread->getRequest()->query('perPage', '');
                    ?>
                    <select
                        x-on:change="$fire.navigate('<?= _e(parse_anchor_url($bread->getRequest(), 'perPage', 'perPage')) ?>'.replace('perPage=perPage', `perPage=${$event.target.value}`))"
                        class="rounded-md text-[0.8rem] py-0.5 shadow-xs border-primary-300 focus:border-accent-300 focus:ring-3 focus:ring-accent-200/50">
                        <option value=""><?= _e(__('per page')) ?></option>
                        <?php foreach ([25, 50, 100, 500] as $count): ?>
                            <option <?= $perPage == $count ? 'selected' : '' ?> value="<?= _e($count) ?>">
                                <?= _e($count) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="md:w-4/12">
                    <?php
                    $links = $bread->getPaginator()->getLinks(
                        links: 1,
                        classes: [
                            'ul' => 'flex flex-wrap justify-center md:justify-end gap-x-1 gap-y-2',
                            'li' => '',
                            'a' => 'text-sm px-2 py-1 block text-primary-800 min-w-8 h-8 flex items-center justify-center border transition rounded-md {anchor_class}',
                            'a.current' => '{active_class}',
                        ],
                        entity: [
                            'prev' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                    </svg>',
                            'next' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>'
                        ]
                    );

                    $links = str_ireplace(['{anchor_class} {active_class}', '{anchor_class}'], ['bg-accent-600 border-accent-600 text-white', 'bg-primary-50 hover:bg-primary-100 border-primary-200 hover:text-primary-900'], $links);
                    echo $links;
                    ?>
                </div>
            </div>
            <!-- Bread Footer Part END -->
        <?php else: ?>
            <div class="px-6 py-6 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="size-12 mx-auto mb-2 text-primary-400">
                    <path fill-rule="evenodd"
                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                        clip-rule="evenodd" />
                </svg>
                <h3 class="text-lg font-medium text-primary-500"><?= _e(__('no data')) ?></h3>
            </div>
        <?php endif ?>
    </div>
</section>