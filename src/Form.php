<?php

namespace Backpack;

use Backpack\Lib\InputGenerator;
use Exception;
use Hyper\Model;
use Hyper\Request;
use Hyper\Utils\Validator;

/**
 * Class form
 * 
 * This class is used to build, validate, and render forms dynamically.
 * 
 * @package Hyper\Utils
 * @author Shahin Moyshan <shahin.moyshan2@gmail.com>
 */
class Form
{
    /**
     * @var array $fields Stores form field configurations.
     */
    private array $fields = [];

    /**
     * @var array $config Form configuration options.
     */
    private array $config = [];

    /**
     * The model instance that this form is associated with. This is used to
     * validate and save the form data.
     *
     * @var Model $model
     */
    private Model $model;

    /**
     * Form constructor.
     * 
     * @param Request $request The request instance for this form. This is used
     *                         to access input data and other request-specific
     *                         information.
     */
    public function __construct(private Request $request)
    {
    }

    /**
     * Set the model for this form.
     *
     * Optionally, generate form fields from model properties using the inputGenerator helper.
     *
     * @param Model $model The model to set for this form.
     * @param bool $generateInputs Set to false if you do not want to generate form fields from the model properties.
     *
     * @return self
     */
    public function setModel(Model $model, bool $generateInputs = true): self
    {
        $this->model = $model;

        // Generate form fields from model properties
        if ($generateInputs) {
            foreach (InputGenerator::generate($model) as $field) {
                $this->add(...$field);
            }
        }

        return $this;
    }

    /**
     * Adds a field to the form.
     * 
     * @param string $type Field type (e.g., text, checkbox, etc.)
     * @param string $name Field name.
     * @param bool $required Whether the field is required.
     * @param int|null $min Minimum value/length.
     * @param int|null $max Maximum value/length.
     * @param null|array|int|bool|string $value Default value for the field.
     * @param array $options Options for select, radio, etc.
     * @param string|null $placeholder Placeholder text.
     * @param string|null $label Field label.
     * @param string|null $description Field description.
     * @param string|null $info Help & Info text.
     * @param bool $multiple Whether multiple values are allowed.
     * @param array $attrs Additional HTML attributes.
     * @param array|null $rule Additional Validation rule default is null.
     * @param string|null $id HTML ID attribute.
     * @param array $class Additional CSS classes.
     * @param bool $hasError Indicates if the field has validation errors.
     * @param array $errors Validation error messages.
     * 
     * @return self
     */
    public function add(
        string $type,
        string $name,
        bool $required = false,
        ?int $min = null,
        ?int $max = null,
        null|array|int|bool|float|string $value = null,
        array $options = [],
        ?string $placeholder = null,
        ?string $label = null,
        ?string $description = null,
        ?string $info = null,
        bool $multiple = false,
        array $attrs = [],
        ?array $rule = null,
        ?string $id = null,
        array $class = [],
        bool $hasError = false,
        array $errors = [],
    ): self {
        $this->fields[$name] = get_defined_vars();
        return $this;
    }

    /**
     * Adds a heading to the form fields.
     *
     * This method appends a heading to the form, allowing you to include
     * static text sections or titles within the form structure.
     *
     * @param string $heading The heading text to be added.
     *
     * @return self
     */
    public function heading(string $heading): self
    {
        $this->fields[] = ['type' => 'heading', 'value' => $heading];
        return $this;
    }

    /**
     * Extends an existing field with additional configurations.
     * 
     * @param string $name Field name to extend.
     * @param array $field Additional field configurations.
     * 
     * @return self
     */
    public function merge(string $name, array $field): self
    {
        $this->fields[$name] = array_merge($this->fields[$name] ?? [], $field);
        return $this;
    }

    /**
     * Loads form data from a request into this form.
     *
     * @param array $data Request data.
     */
    public function load(array $data): void
    {
        $parseInput = function (array $data, array $field, string $name) {
            return isset($data[$name])
                ? ('' === $data[$name] && !$field['required'] ? null : $data[$name]) : null;
        };

        foreach ($this->fields as $name => $field) {
            // Update each field with its corresponding value from the request data
            $this->fields[$name] = array_merge($field, [
                'value' => match ($field['type']) {
                    // For 'switch' type fields, set true if the value is 'on', otherwise false
                    'switch' => isset($data[$name]) && strtolower($data[$name]) === 'on',

                    // For 'combobox' type fields, handle multiple selections or single value
                    'combobox' => isset($data[$name]) && !empty($data[$name]) && $field['multiple']
                    ? explode(',', $data[$name])
                    : $parseInput($data, $field, $name),

                    // Default case for other field types
                    default => $parseInput($data, $field, $name),
                },
            ]);
        }
    }

    /**
     * Validates form data.
     * 
     * @return bool True if validation passes, false otherwise.
     */
    public function validate(): bool
    {
        // Load input values from request into this form.
        $this->load(
            $this->request->all()
        );

        // Create a validator instance.
        $validator = new Validator();
        $rules = [];

        // Add validator ruled dynamically.
        foreach ($this->fields as $field) {
            // Get rule for this field, else add dynamic rule for this field.
            if (isset($field['rule'])) {
                $rule = $field['rule'];
            } else {
                $rule = [];

                // Add required rule.
                if ($field['required']) {
                    $rule[] = 'required';
                }

                // Add input type rule.
                $rule[] = match ($field['type']) {
                    'email', 'url', 'number' => $field['type'],
                    'switch' => 'boolean',
                    'file', 'upload' => 'array',
                    'checkbox', 'combobox', 'select' => $field['multiple'] ? 'array' : 'text',
                    default => 'text',
                };

                // Add minimum input length rule.
                if ($field['min']) {
                    $rule[] = 'min:' . $field['min'];
                }

                // Add maximum input length rule.
                if ($field['max']) {
                    $rule[] = 'max:' . $field['max'];
                }
            }

            // Push this rule into validator.
            $rules[$field['name']] = $rule;
        }

        // Validate this form and add errors into input items. 
        if (!$validator->validate($rules, $this->getData())) {
            foreach ($validator->getErrors() as $name => $errors) {
                $this->merge($name, ['hasError' => true, 'errors' => $errors]);
            }

            // Return fails and render the form with error, when validation is failed.
            return false;
        }

        // Returns true when this for is passed with input validation.
        return true;
    }

    /**
     * Retrieves all form fields.
     * 
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Retrieves data from the form.
     * 
     * @return array
     */
    public function getData(): array
    {
        // Holds input items values.
        $data = [];

        // Extract all values from input items.
        foreach ($this->fields as $field) {
            $data[$field['name']] = $field['value'];
        }

        // Return key=>value array of from inputs.
        return $data;
    }

    /**
     * Retrieves the value of a form field.
     * 
     * @param string $name Field name.
     * @param mixed $default Default value if the field is empty.
     * 
     * @return mixed
     */
    public function getValue(string $name, mixed $default = null): mixed
    {
        return $this->getField($name)['value'] ?? $default;
    }

    /**
     * Retrieves a form field by name.
     * 
     * @param string $name Field name.
     * 
     * @return array
     */
    public function getField(string $name): array
    {
        return $this->fields[$name] ?? [];
    }

    /**
     * Checks if a form field with the given name exists.
     * 
     * @param string $name Name of the field to check.
     * @return bool True if the field exists, false otherwise.
     */
    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * Saves the form data to the associated model.
     * 
     * Optionally updates the timestamp fields if they exist.
     * 
     * @param bool $timestamp Whether to update timestamp fields (created_at, updated_at).
     * 
     * @return int|bool The ID of the saved record or false on failure.
     */
    public function save(bool $timestamp = true): int|bool
    {
        // Update timestamp fields if applicable
        if ($timestamp && ($this->hasField('created_at') || $this->hasField('updated_at'))) {
            if (!$this->hasField('id') && $this->hasField('created_at')) {
                // Set created_at field if not already set
                $this->merge('created_at', ['value' => date('Y-m-d H:i:s')]);
            }

            if ($this->hasField('updated_at')) {
                // Update updated_at field
                $this->merge('updated_at', ['value' => date('Y-m-d H:i:s')]);
            }
        }

        // Dynamically load a model from input values
        $this->model = $this->model->load(
            $this->getData()
        );

        // Save the model and return the ID or false on failure
        return $this->model->save();
    }

    /**
     * Returns the model associated with the form.
     * 
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Returns the current request instance.
     * 
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Configure the form.
     *
     * Merges the given configuration with the existing form configuration.
     *
     * @param string|array $config The configuration to merge. If a string, it
     *     is treated as a path to a PHP file that returns an array of
     *     configuration options.
     *
     * @return self The form instance.
     */
    public function configure(string|array $config): self
    {
        if (is_string($config)) {
            $config = require $config;
        }

        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Returns a configuration value by key.
     * 
     * @param string $key The key of the configuration value.
     * @param mixed $default The default value if the key does not exist.
     * 
     * @return mixed The configuration value for the given key, or the default value if the key does not exist.
     */
    public function getConfig(string $key, $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Renders a single form field as HTML.
     * 
     * @param array $field Field configuration.
     * 
     * @return string Rendered HTML.
     */
    public function inputRender(array $field): string
    {
        // Parse the field data to extract the various parts.
        $field = $this->parseFieldData($field);

        // Check if the field is a heading then return the heading HTML.
        if ($field['type'] === 'heading') {
            return <<<HTML
                <div class="{headingGroupClass}">
                    <h3 class="{headingClass}">{$field['name']}</h3>
                    <p class="{headingDescriptionClass}">{$field['placeholder']}</p>
                </div>
            HTML;
        }

        // Get the boilerplate HTML template from the configuration.
        $boilerplate = $field['boilerplate'] ?? $this->getConfig('boilerplate', null);

        // If the boilerplate is not set, use the default.
        if ($boilerplate === null) {
            $boilerplate = <<<HTML
                <div class="{groupClass}">
                    <label for="{id}" class="{labelClass}">{label} {info}</label>
                    {field}
                    {description}
                    {errors}
                </div>
            HTML;
        }

        // Replace the placeholders in the boilerplate with the field data.
        return str_ireplace(
            ['{id}', '{label}', '{info}', '{field}', '{description}', '{errors}'],
            [
                $field['id'],
                $field['label'],
                $this->renderTooltip($field['info'] ?? ''),
                $this->renderFieldHtml($field),
                $this->renderDescription($field['description'] ?? ''),
                $this->renderErrors($field),
            ],
            // If the field is hidden, only render the field.
            $field['type'] == 'hidden' ? '{field}' : $boilerplate
        );
    }

    /**
     * Renders a tooltip as HTML.
     * 
     * This method replaces the placeholder in the tooltip template with the
     * provided text and returns the resulting HTML string. If the text is
     * empty, it returns an empty string.
     * 
     * @param string $text The text to display in the tooltip.
     * 
     * @return string The rendered HTML for the tooltip.
     */
    public function renderTooltip(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return str_ireplace(
            ['{text}'],
            [$text],
            $this->getConfig('tooltip', '<span title="{text}" style="opacity:0.75;margin-left:2px;">?</span>')
        );
    }

    /**
     * Renders a description as HTML.
     * 
     * This method takes the provided text and wraps it in a span with the
     * configured class for field descriptions. If the text is empty, it
     * returns an empty string.
     * 
     * @param string $text The text to display in the description.
     * 
     * @return string The rendered HTML for the description.
     */
    public function renderDescription(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        return <<<HTML
            <span class="{fieldDescriptionClass}">{$text}</span>
        HTML;
    }

    /**
     * Adds input classes to the form output.
     * 
     * This method takes the rendered form output and adds classes to the
     * form elements based on the configuration.
     * 
     * @param string $output Rendered form output.
     * 
     * @return string Form output with added classes.
     */
    public function addInputClass(string $output, array $field = []): string
    {
        $class = $this->getConfig('class', []);

        // Replace the placeholders with the actual class names.
        return str_ireplace(
            [
                '{groupClass}',
                '{headingGroupClass}',
                '{headingClass}',
                '{headingDescriptionClass}',
                '{fieldDescriptionClass}',
                '{labelClass}',
                '{inputClass}',
                '{inputErrorClass}',
                '{checkboxClass}',
                '{checkboxGroupClass}',
                '{checkboxErrorClass}',
                '{checkboxInputClass}',
                '{textareaClass}',
                '{textareaErrorClass}',
                '{selectClass}',
                '{selectErrorClass}',
                '{radioGroupClass}',
                '{radioClass}',
                '{radioErrorClass}',
                '{errorListClass}',
                '{errorListItemClass}',
            ],
            [
                $field['class']['groupClass'] ?? ($class['groupClass'] ?? ''),
                $field['class']['headingGroupClass'] ?? ($class['headingGroupClass'] ?? ''),
                $field['class']['headingClass'] ?? ($class['headingClass'] ?? ''),
                $field['class']['headingDescriptionClass'] ?? ($class['headingDescriptionClass'] ?? ''),
                $field['class']['fieldDescriptionClass'] ?? ($class['fieldDescriptionClass'] ?? ''),
                $field['class']['labelClass'] ?? ($class['labelClass'] ?? ''),
                $field['class']['inputClass'] ?? ($class['inputClass'] ?? ''),
                $field['class']['inputErrorClass'] ?? ($class['inputErrorClass'] ?? ''),
                $field['class']['checkboxClass'] ?? ($class['checkboxClass'] ?? ''),
                $field['class']['checkboxGroupClass'] ?? ($class['checkboxGroupClass'] ?? ''),
                $field['class']['checkboxErrorClass'] ?? ($class['checkboxErrorClass'] ?? ''),
                $field['class']['checkboxInputClass'] ?? ($class['checkboxInputClass'] ?? ''),
                $field['class']['textareaClass'] ?? ($class['textareaClass'] ?? ''),
                $field['class']['textareaErrorClass'] ?? ($class['textareaErrorClass'] ?? ''),
                $field['class']['selectClass'] ?? ($class['selectClass'] ?? ''),
                $field['class']['selectErrorClass'] ?? ($class['selectErrorClass'] ?? ''),
                $field['class']['radioGroupClass'] ?? ($class['radioGroupClass'] ?? ''),
                $field['class']['radioClass'] ?? ($class['radioClass'] ?? ''),
                $field['class']['radioErrorClass'] ?? ($class['radioErrorClass'] ?? ''),
                $field['class']['errorListClass'] ?? ($class['errorListClass'] ?? ''),
                $field['class']['errorListItemClass'] ?? ($class['errorListItemClass'] ?? ''),
            ],
            // Replace the placeholders in the rendered form output.
            $output
        );
    }

    /**
     * Renders a form field as HTML with added input classes.
     * 
     * This function takes a field name and optionally a field configuration array,
     * merges them with the existing field data, and renders the field as HTML
     * with the configured input classes applied.
     * 
     * @param string $name The name of the field to render.
     * @param array $field Optional field configuration to override existing field data.
     * 
     * @return string The rendered HTML for the form field.
     */
    public function renderField(string $name, array $field = []): string
    {
        $field = array_merge($this->getField($name), $field);
        return $this->addInputClass(
            $this->inputRender(
                $field
            ),
            $field
        );
    }

    /**
     * Renders the form fields and returns the html string.
     * 
     * This method iterates over the form fields and calls the inputRender method for each field.
     * Then it adds the input element class names dynamically and returns the html as a string.
     * 
     * @return string
     */
    public function __toString(): string
    {
        // Holds all input elements in this array.
        $output = [];

        // Add input html element for each form element.
        foreach ($this->fields as $field) {
            $output[] = $this->inputRender($field);
        }

        // Dynamically add input element class names, and returns as string.
        return $this->addInputClass(
            implode('', $output)
        );
    }

    /** @Add helper methods for this form object */

    /**
     * Parses field data for rendering.
     * 
     * @param array $field Field configuration.
     * 
     * @return array Parsed field data.
     */
    private function parseFieldData(array $field): array
    {
        $field['id'] ??= $field['name'];
        $field['label'] = __($field['label'] ?? strtolower(pretty_text($field['name'])));
        $field['placeholder'] = !empty($field['placeholder'] ?? '') ? __($field['placeholder']) : '';

        return $field;
    }

    /**
     * Generates HTML for the field based on its type.
     * 
     * @param array $field Field configuration.
     * 
     * @return string Rendered field HTML.
     */
    private function renderFieldHtml(array $field): string
    {
        if ($field['type'] === 'file') {
            if (isset($field['value']['tmp_name'])) {
                $field['value'] = null;
            }

            // Get the old file value if it exists.
            $field['value'] = ($oldFile = $this->request->post('_' . $field['name'])) !== null ?
                (is_string($oldFile) && strpos($oldFile, ',') !== false ? explode(',', $oldFile) : $oldFile)
                : ($field['value'] ?? null);
        }

        return match ($field['type']) {
            'text', 'hidden', 'number', 'email', 'url', 'color', 'password', 'range', 'search', 'datetime-local', 'date', 'time' => $this->renderInputField($field),
            'file' => $this->renderFileField($field),
            'checkbox' => $this->renderCheckbox($field),
            'radio' => $this->renderRadio($field),
            'select' => $this->renderSelect($field),
            'textarea' => $this->renderTextarea($field),
            'combobox', 'upload', 'richtext', 'switch', 'switcher', 'info_list' => $this->renderFormComponent($field['type'], $field),
            default => throw new Exception('Invalid field type: ' . $field['type']),
        };
    }

    /**
     * Renders a file form field.
     * 
     * This method renders a file field with the given field configuration.
     * It handles the display of uploaded files and the 'multiple' attribute
     * for multiple file uploads.
     * 
     * @param array $field The field configuration array.
     * 
     * @return string The rendered file field HTML.
     */
    private function renderFileField(array $field): string
    {
        $attrs = $this->renderAttributes($field['attrs'] ?? []);
        $required = !isset($field['value']) && isset($field['required']) && $field['required'] ? 'required' : '';
        $errorClass = isset($field['hasError']) && $field['hasError'] ? '{inputErrorClass}' : '';
        $fieldName = isset($field['multiple']) && $field['multiple'] ? $field['name'] . '[]' : $field['name'];
        $multiple = isset($field['multiple']) && $field['multiple'] ? 'multiple' : '';
        $totalUploaded = isset($field['value']) && is_array($field['value']) ? sprintf('<h3 style="margin-top:5px;">%s</h3>', __('%d files uploaded', count($field['value']))) : '';
        $oldFiles = isset($field['value']) ? '<input type="hidden" name="_' . $field['name'] . '" value="' . implode(',', (array) $field['value']) . '">' : '';
        $uploadedFiles = isset($field['value']) ? ('<p style="font-size:12px;margin-top: 2px;display:flex; flex-direction:column; gap:2px;">' . implode('', array_map(fn($file) => '<a href="' . media_url($file) . '" target="_blank">' . $file . '</a>', (array) $field['value'])) . '</p>') : '';

        return <<<HTML
            <input type="file" name="{$fieldName}" id="{$field['id']}" {$attrs} class="{inputClass} {$errorClass}" {$multiple} {$required}>
            {$totalUploaded}
            {$oldFiles}
            {$uploadedFiles}
        HTML;
    }

    /**
     * Renders an input form field.
     * 
     * @param array $field The field configuration array.
     * 
     * @return string The rendered input HTML.
     */
    private function renderInputField(array $field): string
    {
        $field = array_merge(['value' => '', 'required' => false, 'attrs' => []], $field);
        $attrs = $this->renderAttributes($field['attrs'] ?? []);
        $required = $field['required'] ? 'required' : '';
        $errorClass = isset($field['hasError']) && $field['hasError'] ? '{inputErrorClass}' : '';
        $class = implode(' ', (array) ($field['class'] ?? []));

        return <<<HTML
            <input type="{$field['type']}" name="{$field['name']}" id="{$field['id']}" value="{$field['value']}" placeholder="{$field['placeholder']}" {$attrs} class="{inputClass} {$errorClass} {$class}" {$required}>
        HTML;
    }

    /**
     * Renders a textarea form field.
     * 
     * @param array $field The field configuration array.
     * 
     * @return string The rendered HTML for the textarea field.
     */
    private function renderTextarea(array $field): string
    {
        $field = array_merge(['value' => '', 'required' => false, 'attrs' => []], $field);
        $attrs = $this->renderAttributes($field['attrs']);
        $required = $field['required'] ? 'required' : '';
        $errorClass = isset($field['hasError']) && $field['hasError'] ? '{textareaErrorClass}' : '';
        $class = implode(' ', (array) ($field['class'] ?? []));

        return <<<HTML
            <textarea name="{$field['name']}" id="{$field['id']}" placeholder="{$field['placeholder']}" {$attrs} class="{textareaClass} {$errorClass} {$class}" {$required}>{$field['value']}</textarea>
        HTML;
    }


    /**
     * Renders a checkbox form field.
     * 
     * @param array $field The field configuration array.
     * 
     * @return string The rendered checkbox HTML.
     */
    private function renderCheckbox(array $field): string
    {
        $field = array_merge(['multiple' => false, 'required' => false, 'options' => [], 'attrs' => []], $field);
        $attrs = $this->renderAttributes($field['attrs']);
        $required = !$field['multiple'] && $field['required'] ? 'required' : '';
        $errorClass = isset($field['hasError']) && $field['hasError'] ? '{checkboxErrorClass}' : '';
        $fieldName = $field['name'] . ($field['multiple'] ? '[]' : '');

        $checkboxes = [];

        // If field is not multiple, set options to a single value with its placeholder.
        if (!$field['multiple']) {
            $field['options'] = [$field['value'] => $field['placeholder']];
        }

        // Iterate over the options to generate each checkbox input.
        foreach ((array) $field['options'] ?? [] as $value => $label) {
            $checked = $field['multiple'] ? (in_array($value, (array) $field['value']) ? 'checked' : '') :
                (!empty($field['value']) && strval($field['value']) === strval($value) ? 'checked' : '');

            $checkboxes[] = <<<HTML
                <label class="{checkboxClass}">
                    <input type="checkbox" class="{checkboxInputClass}" name="{$fieldName}" value="{$value}" {$checked} {$required} {$attrs}> {$label}
                </label>
            HTML;
        }

        // Combine all checkboxes into a single string.
        $checkboxes = implode('', $checkboxes);

        // Return the rendered checkbox group HTML.
        return <<<HTML
            <div class="{checkboxGroupClass} {$errorClass}">
                {$checkboxes}
            </div>
        HTML;
    }

    /**
     * Renders a select element for a field.
     * 
     * @param array $field Field configuration.
     * 
     * @return string Rendered select HTML.
     */
    private function renderSelect(array $field): string
    {
        $field = array_merge(['multiple' => false, 'options' => [], 'required' => false, 'value' => ''], $field);
        $attrs = $this->renderAttributes($field['attrs'] ?? []);
        $required = $field['required'] ? 'required' : '';

        // If a placeholder is specified and the field is not multiple, add an option for it.
        $options = [];
        if (isset($field['placeholder']) && !empty($field['placeholder']) && !$field['multiple']) {
            $options[] = sprintf('<option value>%s</option>', $field['placeholder']);
        }

        // Iterate over the options and determine which one is selected.
        foreach ($field['options'] ?? [] as $key => $val) {
            $selected = isset($field['value']) && ($field['multiple'] ? in_array($key, (array) $field['value']) : strval($field['value']) === strval($key)) ? 'selected' : '';
            $options[] = sprintf('<option value="%s" %s>%s</option>', $key, $selected, $val);
        }

        // Add options from x-options attribute
        if (isset($field['attrs']['x-options'])) {
            $option = $field['attrs']['x-options'];
            $selected = isset($field['attrs']['x-model']) ? sprintf(':selected="value === %s"', $field['attrs']['x-model']) : '';
            $options[] = <<<HTML
                <template x-for="(label, value) in {$option}">
                    <option :value="value" {$selected} x-text="label"></option>
                </template>
            HTML;
        }

        // Render the select element.
        return sprintf(
            '<select name="%s" id="%s" class="{$class} {selectClass} %s" %s %s %s>%s</select>',
            $field['multiple'] ? "{$field['name']}[]" : $field['name'],
            $field['id'],
            isset($field['hasError']) && $field['hasError'] ? '{selectErrorClass}' : '',
            $required,
            $attrs,
            $field['multiple'] ? 'multiple' : '',
            implode('', $options)
        );
    }

    /**
     * Renders radio buttons for a field.
     * 
     * @param array $field Field configuration.
     * 
     * @return string Rendered radio HTML.
     */
    private function renderRadio(array $field): string
    {
        $attrs = $this->renderAttributes($field['attrs'] ?? []);
        $required = isset($field['required']) && $field['required'] ? 'required' : '';
        $errorClass = isset($field['hasError']) && $field['hasError'] ? '{radioErrorClass}' : '';
        $radios = [];

        foreach ($field['options'] as $key => $val) {
            $checked = strval($field['value'] ?? '') === strval($key) ? 'checked' : '';
            $radios[] = <<<HTML
                <label class="{radioClass}">
                    <input type="radio" name="{$field['name']}" value="{$key}" {$checked} {$required} {$attrs}> {$val}
                </label>
            HTML;
        }

        $radios = implode('', $radios);

        return <<<HTML
            <div class="{radioGroupClass} {$errorClass}">
                {$radios}
            </div>
        HTML;
    }

    /**
     * Converts an array of attributes to a string.
     * 
     * @param array $attrs HTML attributes.
     * 
     * @return string Attributes as a string.
     */
    private function renderAttributes(array $attrs): string
    {
        return implode(' ', array_map(
            fn($key, $value) => is_string($key) && !is_array($value) ? sprintf('%s="%s"', $key, $value) : '',
            array_keys($attrs),
            $attrs
        ));
    }

    /**
     * Renders error messages for a field.
     * 
     * @param array $field Field configuration.
     * 
     * @return string Rendered error messages HTML.
     */
    private function renderErrors(array $field): string
    {
        if (empty($field['errors'])) {
            return '';
        }

        $errorMessages = implode('', array_map(
            fn($error) => sprintf(
                '<li class="{errorListItemClass}">%s</li>',
                __($error)
            ),
            $field['errors']
        ));

        return <<<HTML
            <ul class="{errorListClass}">
                {$errorMessages}
            </ul>
        HTML;
    }

    /**
     * Renders a form component.
     * 
     * This method takes a type of form component and the field configuration,
     * and renders the form component as HTML.
     * 
     * @param string $type The type of form component.
     * @param array $field The field configuration.
     * 
     * @return string Rendered form component HTML.
     */
    private function renderFormComponent(string $type, array $field): string
    {
        // Pass the current form instance to the template
        $form = $this;

        // Render the form component
        ob_start();
        include dir_path(__DIR__ . '/Templates/form/' . $type . '.php');
        return ob_get_clean();
    }
}
