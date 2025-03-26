<?php

// Get the form for create and edit model information
$form = $bread->getForm()
    ->configure(__DIR__ . '/configurator.php');

// Apply onFormRender event
if (!empty($bread->getConfig('onFormRender'))) {
    call_user_func($bread->getConfig('onFormRender'), $form);
}

// Include custom form partial if exists
if (isset($bread->getConfig('partials_inc', [])['form'])) {
    echo $bread->partial($bread->getConfig('partials_inc', [])['form'], ['bread' => $bread, 'form' => $form]);
} else {
    // render default form
    echo <<<FORM
        <div class="bg-white border border-primary-200 shadow-lg w-full sm:rounded-lg px-8 md:px-12">
            {$form}
        </div>
    FORM;
}
