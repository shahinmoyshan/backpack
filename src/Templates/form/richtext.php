<?php

if (!isset($field['attrs']['toolbar'])) {
    $field['attrs']['toolbar'] = ['ai', 'p', 'h1', 'h2', 'b', 'i', 'strike', 'list', 'quote', 'highlight', 'table', 'image', 'youtube', 'source', 'undo', 'redo'];
}

$toolbar = $field['attrs']['toolbar'] ?? [];

?>

<div class="bg-white border border-primary-300 rounded-lg" x-data="tipTapEditor(/** time: <?= _e(microtime()) ?> */)">
    <!-- Editor Toolbar -->
    <template x-if="createEditor()">
        <div x-data="{btnActiveClass:'bg-accent-600 text-white', btnNonActiveClass:'hover:bg-primary-100 text-primary-600 hover:text-primary-800'}"
            class="border-b border-primary-300 px-4 py-2.5 flex items-center gap-2 overflow-x-auto w-full">
            <?php if (in_array('ai', $toolbar)): ?>
                <!-- Ai Assistance -->
                <button x-on:click="ai_assistance = true" title="<?= __e('ask ai to generate content') ?>" type="button"
                    class="px-1.5 py-1 rounded-md transition duration-75 hover:bg-primary-100 text-primary-600 hover:text-primary-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('p', $toolbar)): ?>
                <!-- Paragraph -->
                <button title="<?= __e('paragraph') ?>" type="button"
                    x-on:click="editor().chain().focus().setParagraph().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('paragraph', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9 16h2v4h2V6h2v14h2V6h3V4H9c-3.309 0-6 2.691-6 6s2.691 6 6 6zM9 6h2v8H9c-2.206 0-4-1.794-4-4s1.794-4 4-4z">
                        </path>
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('h1', $toolbar)): ?>
                <!-- Heading 1 -->
                <button title="<?= __e('Heading 1') ?>" type="button"
                    x-on:click="editor().chain().toggleHeading({level: 1}).focus().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('heading', { level: 1 }, updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd"
                            d="M2.75 4a.75.75 0 0 1 .75.75v4.5h5v-4.5a.75.75 0 0 1 1.5 0v10.5a.75.75 0 0 1-1.5 0v-4.5h-5v4.5a.75.75 0 0 1-1.5 0V4.75A.75.75 0 0 1 2.75 4ZM13 8.75a.75.75 0 0 1 .75-.75h1.75a.75.75 0 0 1 .75.75v5.75h1a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1 0-1.5h1v-5h-1a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('h2', $toolbar)): ?>
                <!-- Heading 2 -->
                <button title="<?= __e('Heading 2') ?>" type="button"
                    x-on:click="editor().chain().toggleHeading({level: 2}).focus().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('heading', { level: 2 }, updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21.75 19.5H16.5v-1.609a2.25 2.25 0 0 1 1.244-2.012l2.89-1.445c.651-.326 1.116-.955 1.116-1.683 0-.498-.04-.987-.118-1.463-.135-.825-.835-1.422-1.668-1.489a15.202 15.202 0 0 0-3.464.12M2.243 4.492v7.5m0 0v7.502m0-7.501h10.5m0-7.5v7.5m0 0v7.501" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('b', $toolbar)): ?>
                <!-- Bold -->
                <button title="<?= __e('Bold') ?>" type="button" x-on:click="editor().chain().focus().toggleBold().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('bold', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linejoin="round"
                            d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 0 0 0-8.25H6.75Zm0 0v.38m0 16.122h6.747a4.5 4.5 0 0 0 0-9.001h-7.5v9h.753Zm0 0v-.37m0-15.751h6a3.75 3.75 0 1 1 0 7.5h-6m0-7.5v7.5m0 0v8.25m0-8.25h6.375a4.125 4.125 0 0 1 0 8.25H6.75m.747-15.38h4.875a3.375 3.375 0 0 1 0 6.75H7.497v-6.75Zm0 7.5h5.25a3.75 3.75 0 0 1 0 7.5h-5.25v-7.5Z" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('i', $toolbar)): ?>
                <!-- Italic -->
                <button title="<?= __e('Italic') ?>" type="button"
                    x-on:click="editor().chain().toggleItalic().focus().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('italic', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd"
                            d="M8 2.75A.75.75 0 0 1 8.75 2h7.5a.75.75 0 0 1 0 1.5h-3.215l-4.483 13h2.698a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3.215l4.483-13H8.75A.75.75 0 0 1 8 2.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('strike', $toolbar)): ?>
                <!-- Strikethrough -->
                <button title="<?= __e('Strikethrough') ?>" type="button"
                    x-on:click="editor().chain().toggleStrike().focus().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('strike', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 12a8.912 8.912 0 0 1-.318-.079c-1.585-.424-2.904-1.247-3.76-2.236-.873-1.009-1.265-2.19-.968-3.301.59-2.2 3.663-3.29 6.863-2.432A8.186 8.186 0 0 1 16.5 5.21M6.42 17.81c.857.99 2.176 1.812 3.761 2.237 3.2.858 6.274-.23 6.863-2.431.233-.868.044-1.779-.465-2.617M3.75 12h16.5" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('list', $toolbar)): ?>
                <!-- Bullet List -->
                <button title="<?= __e('Bullet List') ?>" type="button"
                    x-on:click="editor().chain().focus().toggleBulletList().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('bulletList', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('quote', $toolbar)): ?>
                <!-- Blockquote -->
                <button title="<?= __e('Blockquote') ?>" type="button"
                    x-on:click="editor().chain().focus().toggleBlockquote().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('blockquote', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="m21.95 8.721-.025-.168-.026.006A4.5 4.5 0 1 0 17.5 14c.223 0 .437-.034.65-.065-.069.232-.14.468-.254.68-.114.308-.292.575-.469.844-.148.291-.409.488-.601.737-.201.242-.475.403-.692.604-.213.21-.492.315-.714.463-.232.133-.434.28-.65.35l-.539.222-.474.197.484 1.939.597-.144c.191-.048.424-.104.689-.171.271-.05.56-.187.882-.312.317-.143.686-.238 1.028-.467.344-.218.741-.4 1.091-.692.339-.301.748-.562 1.05-.944.33-.358.656-.734.909-1.162.293-.408.492-.856.702-1.299.19-.443.343-.896.468-1.336.237-.882.343-1.72.384-2.437.034-.718.014-1.315-.028-1.747a7.028 7.028 0 0 0-.063-.539zm-11 0-.025-.168-.026.006A4.5 4.5 0 1 0 6.5 14c.223 0 .437-.034.65-.065-.069.232-.14.468-.254.68-.114.308-.292.575-.469.844-.148.291-.409.488-.601.737-.201.242-.475.403-.692.604-.213.21-.492.315-.714.463-.232.133-.434.28-.65.35l-.539.222c-.301.123-.473.195-.473.195l.484 1.939.597-.144c.191-.048.424-.104.689-.171.271-.05.56-.187.882-.312.317-.143.686-.238 1.028-.467.344-.218.741-.4 1.091-.692.339-.301.748-.562 1.05-.944.33-.358.656-.734.909-1.162.293-.408.492-.856.702-1.299.19-.443.343-.896.468-1.336.237-.882.343-1.72.384-2.437.034-.718.014-1.315-.028-1.747a7.571 7.571 0 0 0-.064-.537z">
                        </path>
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('highlight', $toolbar)): ?>
                <!-- Highlight -->
                <button title="<?= __e('Highlight') ?>" type="button"
                    x-on:click="editor().chain().focus().toggleHighlight().run()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('highlight', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('table', $toolbar)): ?>
                <!-- Table -->
                <div x-data="{modal: false}">
                    <button x-on:click="modal=true" title="<?= __e('Table') ?>" type="button"
                        class="px-1.5 py-1 rounded-md transition duration-75"
                        :class="editor().isActive('table', updatedAt) ? btnActiveClass : btnNonActiveClass">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                        </svg>
                    </button>
                    <div x-cloak x-show="modal" x-on:click="modal=false" x-on:keyup.escape.window="modal = false"
                        class="fixed inset-0 z-40 bg-black/30 w-full h-full flex items-center justify-center">
                        <div class="max-h-96 overflow-y-auto bg-white max-w-sm w-full p-6 sm:rounded-lg shadow-lg">
                            <div class="space-y-2">
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()">
                                    Insert table
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().addColumnBefore().run()">
                                    Add column before
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().addColumnAfter().run()">
                                    Add column after
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().deleteColumn().run()">
                                    Delete column
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().addRowBefore().run()">
                                    Add row before
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().addRowAfter().run()">
                                    Add row after
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().deleteRow().run()">
                                    Delete row
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().deleteTable().run()">
                                    Delete table
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().mergeCells().run()">
                                    Merge cells
                                </button>
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-primary-800 transition-colors duration-300 transform rounded-sm  border border-primary-200 hover:bg-primary-100 hover:text-primary-900 focus:outline-hidden"
                                    x-on:click="editor().chain().focus().splitCell().run()">
                                    Split cell
                                </button>
                                <hr class="text-primary-200">
                                <button type="button"
                                    class="w-full px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow-sm shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-hidden focus:ring-3 focus:ring-primary-300/80"><?= __e('Close') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (in_array('image', $toolbar)): ?>
                <!-- Image -->
                <button title="<?= __e('Image') ?>" type="button" x-on:click="addImage()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('image', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('youtube', $toolbar)): ?>
                <!-- Image -->
                <button title="<?= __e('Add Youtube Video') ?>" type="button" x-on:click="addYoutube()"
                    class="px-1.5 py-1 rounded-md transition duration-75"
                    :class="editor().isActive('youtube', updatedAt) ? btnActiveClass : btnNonActiveClass">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M21.593 7.203a2.506 2.506 0 0 0-1.762-1.766C18.265 5.007 12 5 12 5s-6.264-.007-7.831.404a2.56 2.56 0 0 0-1.766 1.778c-.413 1.566-.417 4.814-.417 4.814s-.004 3.264.406 4.814c.23.857.905 1.534 1.763 1.765 1.582.43 7.83.437 7.83.437s6.265.007 7.831-.403a2.515 2.515 0 0 0 1.767-1.763c.414-1.565.417-4.812.417-4.812s.02-3.265-.407-4.831zM9.996 15.005l.005-6 5.207 3.005-5.212 2.995z">
                        </path>
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('source', $toolbar)): ?>
                <!-- Source Code -->
                <div x-data="{modal: false, html: ''}">
                    <button x-on:click="modal = true, html = editor().getHTML()" title="<?= __e('Source Code') ?>"
                        type="button" x-on:click="addSource()" class="px-1.5 py-1 rounded-md transition duration-75"
                        :class="btnNonActiveClass">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                        </svg>
                    </button>
                    <!-- Source Code Modal -->
                    <div x-cloak x-show="modal"
                        class="fixed inset-0 z-40 bg-black/30 w-full h-full flex items-center justify-center">
                        <div class="bg-white max-w-2xl w-full p-6 sm:rounded-lg shadow-lg" x-on:click.away="modal=false"
                            x-on:keyup.escape.window="modal = false">
                            <label>
                                <h3 class="font-medium mb-2"><?= __e('Source Code') ?></h3>
                                <textarea rows="5" x-model="html"
                                    class="w-full p-3 border border-primary-300 rounded-sm  shadow-xs focus:border-accent-300 focus:ring-3 focus:ring-accent-200/50"></textarea>
                            </label>
                            <div class="mt-6 flex gap-2 items-center">
                                <button type="button" x-on:click="addSource(html), modal = false, html = ''"
                                    class="px-4 py-2 text-sm font-medium text-white transition-colors duration-300 transform bg-accent-600 rounded-md hover:bg-accent-500 focus:outline-hidden focus:ring-3 focus:ring-accent-300/80"><?= __e('Insert') ?></button>
                                <button type="button" x-on:click="modal = false"
                                    class="px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors duration-300 transform bg-primary-700 shadow-sm shadow-primary-200 rounded-lg hover:bg-primary-800 focus:outline-hidden focus:ring-3 focus:ring-primary-300/80"><?= __e('Close') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            <?php if (in_array('undo', $toolbar)): ?>
                <!-- Undo -->
                <button title="<?= __e('Undo') ?>" class="px-1.5 py-1 rounded-md transition duration-75" type="button"
                    :class="!editor().can(updatedAt).chain().focus().undo().run() && 'opacity-50'"
                    x-on:click="editor().chain().focus().undo().run()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                </button>
            <?php endif ?>
            <?php if (in_array('redo', $toolbar)): ?>
                <!-- Redo -->
                <button title="<?= __e('Redo') ?>" class="px-1.5 py-1 rounded-md transition duration-75" type="button"
                    :class="!editor().can(updatedAt).chain().focus().redo().run() && 'opacity-50'"
                    x-on:click="editor().chain().focus().redo().run()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                    </svg>
                </button>
            <?php endif ?>
        </div>
    </template>

    <!-- Hidden Input -->
    <textarea x-ref="target" name="<?= _e($field['name']) ?>" <?= $form->renderAttributes($field['attrs'] ?? []) ?>
        id="<?= _e($field['id']) ?>" class="hidden"><?= $field['value'] ?? '' ?></textarea>

    <!-- Editor Element -->
    <div x-ref="element"></div>

    <?php if (in_array('ai', $toolbar)): ?>
        <!-- AI Assistance Modal -->
        <div x-cloak x-show="ai_assistance" x-data="{
            isLoading: false,
            errorMessage: null,
            hasApiKey: <?= _e(!empty(env('gemini_api_key')) ? 'true' : 'false') ?>,
            apiKey: '',
            askAi() {
                const prompt = this.$refs.commandPrompt.value.trim();
                if (prompt.length <= 10) {
                    alert('<?= __e('Please enter a prompt with more than 10 characters') ?>');
                    this.$refs.commandPrompt.focus();
                    return;
                }

                this.isLoading = true;
                fetch('<?= _e(route_url('admin.ai.content')) ?>', {
                    method: 'POST',
                    body: JSON.stringify({ prompt: prompt, _token: '<?= _e(csrf_token()) ?>' })
                })
                    .then(response => response.json())
                    .then(resp => {
                        if (resp && !resp.error) {
                            this.editor().commands.setContent(resp.content, true);
                            this.editor().commands.focus('all');
                            this.closePrompt();
                        } else {
                            this.errorMessage = resp.message || '<?= __e('An error occurred. Please try again.') ?>';
                            this.isLoading = false;
                        }
                    });
            },
            connectGeminiApi() {
                this.isLoading = true;
                fetch('<?= _e(route_url('admin.ai.connect')) ?>', {
                    method: 'POST',
                    body: JSON.stringify({ key: this.apiKey, _token: '<?= _e(csrf_token()) ?>' })
                })
                    .then(response => response.json())
                    .then(resp => {
                        if (resp && !resp.error && resp.connected) {
                            this.hasApiKey = true;
                        } else {
                            this.errorMessage = resp.message || '<?= __e('An error occurred. Please try again.') ?>';
                        }
                        this.isLoading = false;
                    });
            },
            closePrompt() {
                this.$refs.commandPrompt.value = '';
                this.errorMessage = null;
                this.isLoading = false;
                this.ai_assistance = false;
            }
        }" class="fixed inset-0 z-50 overflow-y-auto w-full h-full bg-black/30 flex items-center justify-center">
            <div class="w-full max-w-lg max-h-full overflow-y-auto p-8 bg-white rounded-lg shadow-xs"
                x-on:click.away="closePrompt()" x-on:keydown.escape.window="closePrompt()">
                <div class="text-center mb-6">
                    <span class="mb-0.5 inline-block bg-accent-600 text-white p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M21 10.975V8a2 2 0 0 0-2-2h-6V4.688c.305-.274.5-.668.5-1.11a1.5 1.5 0 0 0-3 0c0 .442.195.836.5 1.11V6H5a2 2 0 0 0-2 2v2.998l-.072.005A.999.999 0 0 0 2 12v2a1 1 0 0 0 1 1v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5a1 1 0 0 0 1-1v-1.938a1.004 1.004 0 0 0-.072-.455c-.202-.488-.635-.605-.928-.632zM7 12c0-1.104.672-2 1.5-2s1.5.896 1.5 2-.672 2-1.5 2S7 13.104 7 12zm8.998 6c-1.001-.003-7.997 0-7.998 0v-2s7.001-.002 8.002 0l-.004 2zm-.498-4c-.828 0-1.5-.896-1.5-2s.672-2 1.5-2 1.5.896 1.5 2-.672 2-1.5 2z">
                            </path>
                        </svg>
                    </span>
                    <h3 class="font-bold text-primary-800"><?= __e('AI Content Generator') ?></h3>
                </div>

                <div x-show="hasApiKey && !isLoading && !errorMessage">
                    <textarea x-ref="commandPrompt"
                        class="block w-full p-3 px-4 border-primary-300 rounded-lg shadow-xs focus:border-accent-300 focus:ring-3 focus:ring-accent-200/50"
                        rows="6" placeholder="<?= __e('enter a prompt') ?>"></textarea>

                    <div class="mt-5 flex items-center justify-center gap-3">
                        <button x-on:click="askAi" type="button"
                            class="bg-accent-600 text-primary-100 focus-visible:outline-accent-600 rounded-lg inline-flex cursor-pointer items-center justify-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-medium tracking-wide transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 active:opacity-100 active:outline-offset-0 disabled:cursor-not-allowed disabled:opacity-75">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                aria-hidden="true" class="size-4">
                                <path fill-rule="evenodd"
                                    d="M5 4a.75.75 0 0 1 .738.616l.252 1.388A1.25 1.25 0 0 0 6.996 7.01l1.388.252a.75.75 0 0 1 0 1.476l-1.388.252A1.25 1.25 0 0 0 5.99 9.996l-.252 1.388a.75.75 0 0 1-1.476 0L4.01 9.996A1.25 1.25 0 0 0 3.004 8.99l-1.388-.252a.75.75 0 0 1 0-1.476l1.388-.252A1.25 1.25 0 0 0 4.01 6.004l.252-1.388A.75.75 0 0 1 5 4ZM12 1a.75.75 0 0 1 .721.544l.195.682c.118.415.443.74.858.858l.682.195a.75.75 0 0 1 0 1.442l-.682.195a1.25 1.25 0 0 0-.858.858l-.195.682a.75.75 0 0 1-1.442 0l-.195-.682a1.25 1.25 0 0 0-.858-.858l-.682-.195a.75.75 0 0 1 0-1.442l.682-.195a1.25 1.25 0 0 0 .858-.858l.195-.682A.75.75 0 0 1 12 1ZM10 11a.75.75 0 0 1 .728.568.968.968 0 0 0 .704.704.75.75 0 0 1 0 1.456.968.968 0 0 0-.704.704.75.75 0 0 1-1.456 0 .968.968 0 0 0-.704-.704.75.75 0 0 1 0-1.456.968.968 0 0 0 .704-.704A.75.75 0 0 1 10 11Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <?= __e('generate') ?>
                        </button>
                        <button type="button" x-on:click="closePrompt()"
                            class="bg-primary-700 text-primary-100 focus-visible:outline-primary-700 rounded-lg inline-flex cursor-pointer items-center justify-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-medium tracking-wide transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 active:opacity-100 active:outline-offset-0 disabled:cursor-not-allowed disabled:opacity-75">
                            <?= __e('close') ?>
                        </button>
                    </div>
                </div>

                <div x-show="!hasApiKey && !isLoading && !errorMessage">
                    <div>
                        <label>
                            <span class="text-sm font-medium"><?= _e('Enter Gemini Api Key') ?></span>
                            <input type="text" x-model="apiKey"
                                class="block w-full px-4 py-2 mt-1 border-primary-300 rounded-sm  shadow-xs focus:border-accent-300 focus:ring-3 focus:ring-accent-200/50">
                        </label>
                        <a class="inline-block mt-0.5 text-xs text-primary-600"
                            href="https://aistudio.google.com/app/apikey"
                            target="_blank"><?= __e('Click here to get api key') ?></a>
                        <button type="button" x-on:click="connectGeminiApi()"
                            class="block w-full mt-4 text-center text-white bg-accent-700 hover:bg-accent-800 focus:ring-4 focus:ring-accent-300 font-medium rounded-sm  text-sm px-5 py-2.5"><?= __e('Connect') ?></button>
                    </div>
                </div>

                <div x-show="!isLoading && errorMessage">
                    <div class="text-center py-4 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-8 mb-2 mx-auto">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p x-text="errorMessage" class="text-sm font-medium mt-2 block"></p>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" x-on:click="closePrompt()"
                            class="bg-primary-700 text-primary-100 focus-visible:outline-primary-700 rounded-lg inline-flex cursor-pointer items-center justify-center gap-2 whitespace-nowrap px-4 py-2 text-sm font-medium tracking-wide transition hover:opacity-75 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 active:opacity-100 active:outline-offset-0 disabled:cursor-not-allowed disabled:opacity-75">
                            <?= __e('close') ?>
                        </button>
                    </div>
                </div>
                <div x-show="isLoading">
                    <div class="text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                            class="size-8 mx-auto fill-primary-600 motion-safe:animate-spin">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                opacity=".25" />
                            <path
                                d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                        <span class="text-sm font-medium mt-2 block text-primary-800"><?= __e('Generating') ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>