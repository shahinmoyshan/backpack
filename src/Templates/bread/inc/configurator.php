<?php

return [
    'boilerplate' => '<div class="flex flex-col md:flex-row md:items-center {groupClass}">
                <label for="{id}" class="w-full md:w-4/12 mb-3 md:mb-0 flex items-center gap-1 {labelClass}">{label} {info}</label>
                <div class="w-full md:w-8/12">
                    {field}
                    {description}
                    {errors}
                </div>
            </div>',
    'tooltip' => '<span x-cloak class="inline-block cursor-pointer relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="peer size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" /></svg>
            <div class="pointer-events-none absolute bottom-full mb-2 left-1/2 -translate-x-1/2 z-10 flex w-48 flex-col gap-1 rounded bg-primary-800 p-2.5 text-[0.8rem] text-primary-50 opacity-0 transition-all ease-out peer-hover:opacity-100 peer-focus:opacity-100" role="tooltip">{text}</div>
        </span>',
    'class' => [
        'groupClass' => 'my-2 border-t py-6 md:py-4 first:border-0 border-primary-200',
        'headingClass' => 'mb-2 text-primary-800 font-semibold text-lg',
        'headingGroupClass' => 'mt-4 mb-6',
        'headingDescriptionClass' => 'text-sm text-primary-600',
        'fieldDescriptionClass' => 'block text-xs mt-1 text-primary-600',
        'labelClass' => 'font-medium text-[0.9rem]',
        'inputClass' => 'block w-full py-2 px-3 rounded-md border border-primary-300 shadow-sm focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50',
        'inputErrorClass' => 'border-red-600 text-red-800',
        'checkboxClass' => 'flex items-center gap-1',
        'checkboxGroupClass' => 'flex flex-wrap gap-y-2 gap-x-4',
        'checkboxErrorClass' => 'text-red-600',
        'textareaClass' => 'block py-2 px-3 w-full rounded-md border border-primary-300 shadow-sm focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50',
        'textareaErrorClass' => 'border-red-200 bg-red-100',
        'selectClass' => 'block w-full rounded-md border border-primary-300 shadow-sm focus:border-accent-300 focus:ring focus:ring-accent-200 focus:ring-opacity-50',
        'selectErrorClass' => 'border-red-200 bg-red-100',
        'radioGroupClass' => 'flex flex-wrap gap-y-3 gap-x-4',
        'radioClass' => 'flex items-center gap-2 border shadow-sm px-3 py-2 rounded-lg hover:bg-primary-50',
        'radioErrorClass' => 'text-red-600',
        'errorListClass' => 'px-2 py-1 w-full rounded-lg bg-red-100 border border-red-200 mt-1',
        'errorListItemClass' => 'text-sm text-red-800 mt-0.5',
    ],
];