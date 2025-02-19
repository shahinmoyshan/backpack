<?php

// get the selected objects
$objects = $bread->getModel()->where([
    'id' => explode(',', $bread->getRequest()->query('ids', ''))
])
    ->result();

// check if there are any objects
if (!empty($objects)):
    ?>
    <section
        class="<?= ['full' => 'w-full', '7xl' => 'max-w-7xl', '6xl' => 'max-w-6xl', '5xl' => 'max-w-5xl', '4xl' => 'max-w-4xl', '3xl' => 'max-w-3xl', '2xl' => 'max-w-2xl', 'xl' => 'max-w-xl', 'lg' => 'max-w-lg', 'mx' => 'max-w-md', 'sm' => 'max-w-sm'][$bread->config['customize']['width']['details'] ?? 'full'] ?> mx-auto">
        <!-- Bread Page Heading Part START -->
        <div class="mb-6">
            <h2 class="font-medium text-2xl text-primary-800 leading-tight mb-3">
                <?= _e(__('are you sure')) ?>
            </h2>
            <p class="mb-4 md:mb-6 text-[0.9rem]">
                <?= _e(__('__delete_confirmation_message', $bread->getConfig('title', ''))) ?>
            </p>
        </div> <!-- Bread Page Heading Part END -->

        <!-- Bread Delete Summary Part START -->
        <b class="mb-2 block text-lg"><?= __('summary') ?></b>
        <ul class="list-disc text-sm pl-10 mb-4 md:mb-6">
            <li class="capitalize mb-1">
                <?= _e($bread->getConfig('title_singular', '')) ?>: <?= count($objects) ?>
            </li>
            <?php if (method_exists($bread->getModel(), 'getRegisteredOrm')): ?>
                <?php foreach ($bread->getModel()->getRegisteredOrm() as $with => $rel): ?>
                    <?php if (
                        !in_array($rel['has'], ['many', 'many-x']) ||
                        !isset($rel['onDelete']) ||
                        strtolower($rel['onDelete']) !== 'cascade'
                    ) {
                        continue;
                    } ?>
                    <?php $totalRelObjs = array_sum(collect($objects)->map(fn($o) => count($o->{$with}))->all()); ?>
                    <?php if ($totalRelObjs > 0): ?>
                        <li class="capitalize mb-1">
                            <?= $rel['has'] == 'many-x' ? str_replace('_', '-', $rel['table']) . ' relationship' : (new ReflectionClass($rel['model']))->getShortName() ?>:
                            <?= $totalRelObjs ?>
                        </li>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        </ul> <!-- Bread Delete Summary Part End -->

        <!-- Bread Delete Objects Part START -->
        <b class="mb-2 block text-lg"><?= __('objects') ?></b>
        <ul class="list-disc break-all text-sm pl-10">
            <?php foreach ($objects as $object): ?>
                <li class="capitalize mb-2">
                    <p class="mb-1">
                        <?= _e($bread->getConfig('title_singular')) ?>: <?= $object ?>
                    </p>
                    <!-- Related objects to be deleted -->
                    <?php if (method_exists($bread->getModel(), 'getRegisteredOrm')): ?>
                        <?php foreach ($bread->getModel()->getRegisteredOrm() as $with => $rel): ?>
                            <?php if (
                                !in_array($rel['has'], ['many', 'many-x']) ||
                                !isset($rel['onDelete']) ||
                                strtolower($rel['onDelete']) !== 'cascade'
                            ) {
                                continue;
                            } ?>
                            <?php
                            $relObjs = $object->{$with};
                            ?>
                            <?php if (!empty($relObjs)): ?>
                                <ul class="pl-6 list-disc mb-1">
                                    <?php foreach (array_slice($relObjs, 0, 100) as $relObj): ?>
                                        <?php $relModelName = (new ReflectionClass($relObj))->getShortName() ?>
                                        <?php if ($rel['has'] == 'many'): ?>
                                            <li class="capitalize mb-1">
                                                <?= $relModelName ?>: <?= $relObj ?>
                                            </li>
                                        <?php else: ?>
                                            <li class="capitalize mb-1">
                                                <?= str_replace('_', '-', $rel['table']) . ' relationship for: ' . $relObj ?>
                                            </li>
                                        <?php endif ?>
                                        <!-- Uploaded files in related objects -->
                                        <?php if ($rel['has'] == 'many' && method_exists($relObj, 'uploads')): ?>
                                            <?php foreach ($relObj->uploads() as $u): ?>
                                                <?php if (!empty($ufs = array_filter((array) $relObj->{$u['name']}))): ?>
                                                    <ul class="pl-6 list-disc mb-1">
                                                        <?php foreach ($ufs as $f):
                                                            if (!file_exists(env('upload_dir') . $f)) {
                                                                continue;
                                                            } ?>
                                                            <li class="mb-1">
                                                                <?= _e(__(strtolower(str_replace(['_', '-'], ' ', $u['name'])))) ?>: <a target="_blank"
                                                                    class="text-accent-600 hover:underline" href="<?= media_url($f) ?>"><?= $f ?></a>
                                                            </li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    <?php if (count($relObjs) > 100): ?>
                                        <li class="mb-1">
                                            ...<?= _e(__('+ %d more %s', [count($relObjs) - 1, $relModelName])) ?>
                                        </li>
                                    <?php endif ?>
                                </ul>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    <!-- Primary Model Uploads -->
                    <?php if (method_exists($bread->getModel(), 'uploads')): ?>
                        <?php foreach ($bread->getModel()->uploads() as $u): ?>
                            <?php if (!empty($ufs = array_filter((array) $object->{$u['name']}))): ?>
                                <ul class="pl-6 list-disc mb-1">
                                    <?php foreach ($ufs as $f):
                                        if (!file_exists(env('upload_dir') . $f)) {
                                            continue;
                                        } ?>
                                        <li class="mb-1">
                                            <?= _e(__(strtolower(str_replace(['_', '-'], ' ', $u['name'])))) ?>: <a target="_blank"
                                                class="text-accent-600 hover:underline" href="<?= media_url($f) ?>"><?= $f ?></a>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul> <!-- Bread Delete Objects Part START -->

        <!-- Bread Delete Buttons Part START -->
        <form action="<?= $bread->getRequest()->getPath() ?>" method="post" class="flex gap-4 items-center mt-8">
            <?= csrf() ?>
            <input type="hidden" name="delete" value="<?= $bread->getRequest()->query('ids', '') ?>">
            <button
                class="px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-red-600 shadow shadow-red-200 rounded-lg hover:bg-red-500 focus:outline-none focus:ring focus:ring-red-300 focus:ring-opacity-80"
                type="submit">
                <?= __('yes, i am sure') ?>
            </button>
            <a class="text-center md:text-left px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80"
                href="<?= _e(route_url($bread->getConfig('route'))) ?>">
                <?= __('cancel') ?>
            </a>
        </form>
        <!-- Bread Delete Buttons Part END -->
    </section>
<?php else: ?>
    <!-- Nothing to delete -->
    <h3 class="mb-6 text-primary-600"><?= _e(__('nothing to delete')) ?></h3>
    <a class="inline-block text-center md:text-left px-4 py-2.5 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-none focus:ring focus:ring-primary-300 focus:ring-opacity-80"
        href="<?= _e(route_url($bread->getConfig('route'))) ?>">
        <?= __('go back') ?>
    </a>
<?php endif ?>