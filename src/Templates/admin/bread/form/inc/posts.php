<?php

$form = $bread->getForm();

$form->configure(dir_path(__DIR__ . '/configurator.php'));

foreach ($form->getFields() as $field) {
    if ($field['type'] !== 'hidden') {
        continue;
    }

    echo <<<INPUT
        <input type="hidden" name="{$field['name']}" value="{$field['value']}">
    INPUT;
}

$seo_settings = array_filter($form->getModel()->seo_settings ?? []);
$post_type ??= 'page';
?>
<input type="hidden" name="post_type" value="<?= _e($post_type) ?>">
<div class="grid md:grid-cols-6 gap-6">

    <div class="md:col-span-4">
        <div class="bg-white border border-primary-200 shadow-xs sm:rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><?= $form->renderField('title') ?></div>
                <div><?= $form->renderField('slug') ?></div>
                <div class="col-span-2">
                    <?= $form->renderField('thumbnail') ?>
                </div>
            </div>
        </div>
        <div class="shadow-xs"><?= $form->renderField('content') ?></div>
    </div>

    <div class="md:col-span-2">
        <?php if ($form->hasField('id')): ?>
            <div class="bg-white border border-primary-200 shadow-xs sm:rounded-lg p-6 mb-6">
                <p class="text-[0.9rem]">
                    <span class="font-medium"><?= __('created at') ?></span>
                    <br>
                    <?= _e(pretty_time($form->getValue('created_at'))) ?>
                    <br><br>
                    <span class="font-medium"><?= __('last modified at') ?></span>
                    <br>
                    <?= _e(pretty_time($form->getValue('updated_at'))) ?>
                </p>
            </div>
        <?php endif ?>
        <div class="bg-white border border-primary-200 shadow-xs sm:rounded-lg p-6 mb-6">
            <?php if ($post_type === 'blog'): ?>
                <?php if ($form->hasField('id')): ?>
                    <input type="hidden" name="users_id" value="<?= _e($form->getValue('users_id')) ?>">
                <?php else: ?>
                    <input type="hidden" name="users_id" value="<?= _e(user('id')) ?>">
                <?php endif ?>
                <div class="mb-4">
                    <?= $form->renderField('posts_terms') ?>
                </div>
            <?php endif ?>
            <div>
                <?= $form->renderField('status') ?>
            </div>
        </div>
        <div class="bg-white border border-primary-200 shadow-xs sm:rounded-lg" x-data="{
            /** time: <?= _e(microtime()) ?> */
            expanded: <?= _e(!empty($seo_settings) ? 'true' : 'false') ?>,
            seo: <?= _e(json_encode($seo_settings, JSON_FORCE_OBJECT)) ?>
        }">
            <div class="flex items-center justify-between py-5 px-6 cursor-pointer border-b border-primary-200"
                x-on:click="expanded = !expanded">
                <h3 class="font-semibold"><?= __e('seo settings') ?></h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-5 text-primary-600 transform transition duration-100 ease-in-out"
                    :class="{ 'rotate-180': expanded }">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
            <div x-show="expanded" x-collapse>
                <div class="p-5 grid grid-cols-1 gap-3">
                    <div>
                        <?= $form->renderField('meta_title', [
                            'type' => 'text',
                            'name' => 'meta_title',
                            'info' => __('Customize the SEO title for this page'),
                            'value' => $seo_settings['meta_title'] ?? '',
                            'attrs' => ['x-model' => 'seo.meta_title']
                        ]) ?>
                    </div>
                    <div>
                        <?= $form->renderField('meta_description', [
                            'type' => 'textarea',
                            'name' => 'meta_description',
                            'info' => __('Craft a unique meta description to enhance search engine visibility'),
                            'value' => $seo_settings['meta_description'] ?? '',
                            'attrs' => ['x-model' => 'seo.meta_description']
                        ]) ?>
                    </div>
                    <div>
                        <?= $form->renderField('no_index', [
                            'type' => 'switch',
                            'name' => 'no_index',
                            'placeholder' => 'Hide from search engines',
                            'info' => __('Control whether the page should be indexed by search engines'),
                            'value' => $seo_settings['no_index'] ?? '',
                            'attrs' => ['x-model' => 'seo.no_index']
                        ]) ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="seo_settings" :value="JSON.stringify(seo)">
        </div>
    </div>

</div>