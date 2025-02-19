<?php

use Backpack\Models\AdminActivityLog;

// Get all logs related to this model
$logs = AdminActivityLog::with('user')
    ->orderDesc()
    ->where(
        [
            'target_id' => $bread->getModel()->id,
            'target_type' => $bread->getConfig('activity_log', [])['target_type']
        ]
    )
    ->result();

?>

<div class="bg-white shadow-lg w-full sm:rounded-lg border">
    <?php foreach ($logs as $log): ?>
        <!-- Render History/Logs -->
        <div class="px-4 py-3 text-sm border-b last:border-b-0 even:bg-primary-50">
            <p class="font-medium mb-0.5">
                <?php
                if (is_array($log->action)) {
                    echo _e(__(...$log->action));
                } else {
                    echo _e(__($log->action));
                }
                ?>
            </p>
            <!-- the user -->
            <span class="text-xs text-primary-600 block ml-1">
                -
                <?= _e(__(
                    'by %s at %s',
                    [
                        $log->user->id === user('id') ? __('self') : ($log->user->full_name ?? $log->user->username),
                        date('d M, Y g:i A', strtotime($log->created_at))
                    ]
                )) ?>
            </span>
        </div>
    <?php endforeach ?>
    <?php if (empty($logs)): ?>
        <!-- Show empty history message -->
        <div class="px-8 py-6 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-primary-300 mx-auto mb-2" viewBox="0 0 24 24"
                fill="currentColor">
                <path d="M12 8v5h5v-2h-3V8z"></path>
                <path
                    d="M21.292 8.497a8.957 8.957 0 0 0-1.928-2.862 9.004 9.004 0 0 0-4.55-2.452 9.09 9.09 0 0 0-3.626 0 8.965 8.965 0 0 0-4.552 2.453 9.048 9.048 0 0 0-1.928 2.86A8.963 8.963 0 0 0 4 12l.001.025H2L5 16l3-3.975H6.001L6 12a6.957 6.957 0 0 1 1.195-3.913 7.066 7.066 0 0 1 1.891-1.892 7.034 7.034 0 0 1 2.503-1.054 7.003 7.003 0 0 1 8.269 5.445 7.117 7.117 0 0 1 0 2.824 6.936 6.936 0 0 1-1.054 2.503c-.25.371-.537.72-.854 1.036a7.058 7.058 0 0 1-2.225 1.501 6.98 6.98 0 0 1-1.313.408 7.117 7.117 0 0 1-2.823 0 6.957 6.957 0 0 1-2.501-1.053 7.066 7.066 0 0 1-1.037-.855l-1.414 1.414A8.985 8.985 0 0 0 13 21a9.05 9.05 0 0 0 3.503-.707 9.009 9.009 0 0 0 3.959-3.26A8.968 8.968 0 0 0 22 12a8.928 8.928 0 0 0-.708-3.503z">
                </path>
            </svg>
            <p class="text-sm font-medium text-primary-400"><?= _e(__('no activity yet')) ?></p>
        </div>
    <?php endif ?>
</div>