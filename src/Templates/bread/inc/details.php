<div class="bg-white border border-primary-200 shadow-lg w-full sm:rounded-lg">
    <?php
    // Generate input fields from model
    $details = collect(\Backpack\Lib\InputGenerator::generate($bread->getModel(), false))
        ->mapK(fn($item) => [$item['name'] => $item])
        ->filter(function ($field) {
            if ($field['type'] === 'hidden' && strpos($field['name'], '_') === 0) {
                return false;
            }
            return true;
        });

    // Call onDetails hook
    if (!empty($bread->getConfig('onDetails'))) {
        call_user_func($bread->getConfig('onDetails'), $details);
    }
    ?>
    <?php foreach ($details->all() as $item): ?>
        <div class="border-t border-primary-200 py-4 px-8 first:border-0 flex flex-col gap-1">
            <!-- Render label -->
            <h3 class="font-bold text-primary-800">
                <?= _e(__(strtolower(pretty_text($item['label'] ?? $item['name'])))) ?>
            </h3>
            <!-- Render value -->
            <div class="text-primary-600 flex flex-wrap gap-3 md:gap-4 overflow-hidden">
                <?php
                if (!isset($item['value']) || empty($item['value'])) {
                    echo '<span class="text-sm text-primary-500 opacity-65">' . __('empty') . '</span>';
                } elseif ($item['type'] === 'datetime-local' || in_array($item['name'], ['created_at', 'updated_at', 'joined_at', 'deleted_at', 'last_login', 'last_active', 'last_seen'])) {
                    echo date('d M, Y g:i A', strtotime($item['value']));
                } elseif ($item['type'] === 'date' || in_array($item['name'], ['date', 'date_of_birth'])) {
                    echo date('d M, Y', strtotime($item['value']));
                } elseif ($item['type'] === 'time') {
                    echo date('g:i A', strtotime($item['value']));
                } elseif (in_array($item['type'], ['file', 'upload'])) {
                    foreach ((array) $item['value'] as $file) {
                        $file = strpos($file, 'http') === 0 ? $file : media_url($file);
                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                        echo <<<HTML
                                <a href="{$file}" target="_blank" class="block">
                            HTML;
                        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
                            echo <<<HTML
                                <img src="{$file}" class="max-w-28 max-h-28 md:max-w-36 md:max-h-36 object-contain rounded-sm border border-primary-200 p-1 shadow-xs" />
                            HTML;
                        } else {
                            echo <<<HTML
                                <div class="text-center text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16 md:size-20">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span class="mt-0.5">{$extension}</span>
                                </div>
                            HTML;
                        }
                        echo '</a>';
                    }
                } elseif ($item['type'] === 'password') {
                    echo str_repeat('*', strlen($item['value']));
                } elseif ($item['type'] === 'radio') {
                    echo _e($item['options'][$item['value']] ?? $item['value']);
                } elseif (in_array($item['type'], ['select', 'combobox', 'checkbox'])) {
                    if (is_array($item['value'])) {
                        echo '<ul class="list-disc pl-6">';
                        foreach ($item['value'] as $value) {
                            $value = $item['options'][$value] ?? $value;
                            echo "<li>{$value}</li>";
                        }
                        echo '</ul>';
                    } elseif ($item['type'] === 'checkbox') {
                        echo _e($item['placeholder'] . ': ' . ($item['value'] ?? 'N/A'));
                    } else {
                        echo _e($item['options'][$item['value']] ?? $item['value']);
                    }
                } elseif ($item['type'] === 'switch') {
                    if (isset($item['value']) && boolval($item['value'])) {
                        echo <<<HTML
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            HTML;
                    } else {
                        echo <<<HTML
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            HTML;
                    }
                } elseif ($item['type'] === 'richtext') {
                    echo <<<HTML
                            <div class="prose max-w-full">
                                {$item['value']}
                            </div>
                        HTML;
                } elseif (is_array($item['value'])) {
                    echo '<ul class="list-disc pl-6">';
                    foreach ($item['value'] as $name => $value) {
                        if (is_array($value)) {
                            $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }
                        if (is_numeric($name)) {
                            echo "<li>{$value}</li>";
                        } else {
                            $name = pretty_text($name);
                            echo "<li><span class='capitalize'>{$name}</span>: {$value}</li>";
                        }
                    }
                    echo '</ul>';
                } elseif (isset($item['value']) && !empty($item['value'])) {
                    echo _e($item['value']);
                } else {
                    echo '<span class="text-sm text-primary-500 opacity-65">' . __('n/a') . '</span>';
                }
                ?>
            </div>
        </div>
    <?php endforeach ?>
</div>