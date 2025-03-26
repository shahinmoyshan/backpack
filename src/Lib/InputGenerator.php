<?php

namespace Backpack\Lib;

use Hyper\Model;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Helper class for generating form fields from a model.
 * 
 * This class is responsible for generating form input fields from a given model.
 * It utilizes reflection to extract properties and determine their types, 
 * and can handle custom field settings and ORM relations.
 * 
 * @package Backpack
 */
class InputGenerator
{
    /**
     * Initializes the input generator.
     *
     * @param Model $model The model to generate form fields for.
     */
    public function __construct(protected Model $model)
    {
    }

    /**
     * Static method to generate form fields from a model.
     * 
     * This method creates an instance of the inputGenerator class using the provided model
     * and calls the generateFields method to produce an array of form fields.
     * 
     * @param Model $model The model from which form fields are generated.
     * @param bool $ignore If true, certain fields may be ignored based on additional logic.
     * 
     * @return array An array of form fields generated from the model.
     */
    public static function generate(Model $model, bool $ignore = true): array
    {
        $self = new self($model);
        return $self->generateFields($ignore);
    }

    /**
     * Generates an array of form fields based on class properties and configurations.
     * 
     * This method reflects on the class properties, determines the field types, and
     * merges any custom field settings provided by the `form` method.
     * 
     * @return array An array of associative arrays, each representing a form field.
     */
    public function generateFields(bool $ignore = true): array
    {
        $fields = [];
        $fieldSetup = [];

        if (method_exists($this->model, 'form')) {
            $fieldSetup = $this->model->form();
        }

        // Extract Upload fields of enabled by model.
        $uploads = collect(
            method_exists($this->model, 'uploads') ? $this->model->uploads() : []
        );

        // Extract ignored fields of enabled by model.
        $ignoredFields = $this->model->ignoredFields();

        // Extract each field dynamically from model.
        foreach ($this->extractModelProperties() as $name => $field) {
            $type = 'text';
            $multiple = false;

            if ($ignore && in_array($name, $ignoredFields)) {
                continue;
            } elseif ($ignore && isset($fieldSetup[$name]['ignore']) && $fieldSetup[$name]['ignore']) {
                continue;
            } elseif ($name == 'id') {
                if (isset($field['default']) || request('id') != null) {
                    $type = 'hidden';
                } else {
                    continue;
                }
            } elseif (in_array('int', $field['type'])) {
                $type = 'number';
            } elseif (in_array('bool', $field['type'])) {
                $type = 'switch';
            } elseif ($uploads->pluck('name')->in($name)) {
                $upload = $uploads->find(fn($upload) => $upload['name'] == $name);
                $type = 'upload';
                $multiple = isset($upload['multiple']) && $upload['multiple'];
            } elseif (in_array($name, ['created_at', 'updated_at'])) {
                $type = 'hidden';
            }

            // Add new field item.
            $fields[] = array_merge([
                'type' => $type,
                'name' => $name,
                'multiple' => $multiple,
                'required' => !in_array('null', $field['type']),
                'value' => $field['default'],
            ], $fieldSetup[$name] ?? []);
        }

        // Extract ORM fields of enabled from model.
        if (method_exists($this->model, 'getRegisteredOrm')) {
            $fields = array_merge($fields, $this->extractOrmFields());
        }

        return $fields;
    }

    /**
     * Extracts public properties of the class and determines their types and default values.
     * 
     * Uses reflection to analyze class properties, identify their types (including union types),
     * and retrieve their default values.
     * 
     * @return array An associative array of properties, with keys as property names and values 
     *               containing 'type' and 'default' keys.
     */
    protected function extractModelProperties(): array
    {
        $reflector = new ReflectionClass($this->model);
        $result = [];

        foreach ($reflector->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            $type = 'mixed';
            $default = $this->model->{$name} ?? null;

            if ($property->hasType()) {
                $type = $property->getType();
                if ($type instanceof ReflectionUnionType) {
                    $type = array_map(fn($t) => $t->getName(), $type->getTypes());
                } elseif ($type instanceof ReflectionNamedType) {
                    $type = [$type->getName(), ($type->allowsNull() ? 'null' : 'notNull')];
                } else {
                    $type = 'mixed';
                }
            }

            $result[$name] = [
                'type' => (array) $type,
                'default' => $default,
            ];
        }

        return $result;
    }

    /**
     * Extracts fields from the model's relations.
     * 
     * Extracts fields from the model's relations, which are defined in the model's
     * `getRegisteredOrm` method. The method returns an array of fields, each
     * containing the name of the relation, the type of the field, the options
     * for the field, and the value of the field.
     * 
     * @return array An array of associative arrays, each representing a field.
     */
    protected function extractOrmFields(): array
    {
        $fields = [];
        foreach ($this->model->getRegisteredOrm() as $with => $config) {
            if (!in_array($config['has'], ['many-x', 'one']) || ($config['formIgnore'] ?? false)) {
                continue;
            }

            $model = new $config['model'];
            $options = $config['form_list_items'] ?? (
                (isset($config['form_list_callback']) && is_callable($config['form_list_callback'])) ?
                call_user_func($config['form_list_callback'], $model) : collect(
                    $model->get()->result()
                )
                    ->mapK(fn($d) => [$d->id => (string) $d])
                    ->all()
            );

            $field = [
                'type' => 'combobox',
                'required' => true,
                'label' => pretty_text($with),
                'options' => $options,
                'attrs' => [
                    'searchable' => count($options) >= 20
                ],
            ];

            switch ($config['has']) {
                case 'one':
                    $fields[] = array_merge($field, [
                        'name' => "{$model->table()}_id",
                        'value' => $this->model->{"{$model->table()}_id"} ?? null,
                    ]);
                    break;
                case 'many-x':
                    $values = isset($this->model->id) ?
                        collect($this->model->{$with})->pluck('id')->all() :
                        array_filter(explode(
                            ',',
                            request()->post("_{$config['table']}", '')
                        ));

                    $fields[] = array_merge($field, [
                        'name' => $config['table'],
                        'multiple' => true,
                        'value' => $values,
                        'attrs' => array_merge($field['attrs'], ['clear' => true]),
                    ]);

                    if (!empty($values)) {
                        $fields[] = [
                            'type' => 'hidden',
                            'name' => "_{$config['table']}",
                            'value' => implode(',', $values)
                        ];
                    }
                    break;
            }
        }

        $form = method_exists($this->model, 'form') ? $this->model->form() : [];
        foreach ($fields as &$field) {
            $field = array_merge($field, $form[$field['name']] ?? []);
        }

        return $fields;
    }
}
