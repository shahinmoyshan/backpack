<!-- Alert Message START -->
<div class="w-full text-white bg-red-500" x-data x-ref="message">
    <div class="container flex items-center justify-between px-6 py-4 mx-auto">
        <div class="flex">
            <!-- Alter Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                <path fill-rule="evenodd"
                    d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                    clip-rule="evenodd" />
            </svg>
            <!-- Alter Message -->
            <p class="mx-2 flex-1 text-sm"><?= _e($error) ?></p>
        </div>
        <!-- Close Button START -->
        <button x-on:click="$refs.message.remove()"
            class="p-1 transition-colors duration-300 transform rounded-md hover:bg-primary-600/25 focus:outline-hidden">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 18L18 6M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button> <!-- Close Button END -->
    </div>
</div>
<!-- Alert Message END -->