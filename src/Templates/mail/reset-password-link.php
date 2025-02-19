<?php

$template->layout('mail/layout')
    ->set('title', $subject ?? '')
    ->set('heading', $subject ?? '');

?>
<h3><?= _e(__('hello %s,', $name)) ?></h3>
<p><?= _e(__('__reset-password-email-message')) ?></p>

<div style="text-align: center; margin: 20px 0;">
    <a href="<?= _e($link) ?>"
        style="display: inline-block; padding: 10px 20px; background-color: #1d4ed8; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">
        <?= _e(__('reset password')) ?>
    </a>
</div>

<p><?= _e(__('Note: This link will expire in 24 hours.')) ?></p>