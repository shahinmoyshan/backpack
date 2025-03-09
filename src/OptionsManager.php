<?php

namespace Backpack;

use Backpack\Models\Option;
use Exception;
use Hyper\Helpers\Uploader;
use Hyper\Request;
use Hyper\Response;
use Throwable;

/**
 * Class SettingManager
 *
 * @package Backpack
 */
class OptionsManager
{
    use Uploader;

    /**
     * The configuration array.
     *
     * @var array
     */
    private array $config = [];

    /**
     * The sections array.
     *
     * @var array
     */
    private array $sections = [];

    /**
     * The form instance.
     *
     * @var Form
     */
    private Form $form;

    /**
     * The ID of the settings.
     * @var null|string
     */
    private ?string $id;

    /**
     * The action hook array.
     * @var array
     */
    private array $actions = [];

    /**
     * SettingsManager constructor.
     *
     * Initializes the settings manager with a request, response, and optional ID.
     *
     * @param Request $request The request instance.
     * @param Response $response The response instance.
     * @param string|null $id Optional ID for specific settings.
     */
    public function __construct(private Request $request, private Response $response)
    {
        $this->id = $request->getRouteParam('id');
    }

    /**
     * Sets the actions for the options manager.
     *
     * @param array $actions An array of actions to set.
     * @return void
     */
    public function setActions(string $id, array $actions): void
    {
        $this->actions[$id] = $actions;
    }

    /**
     * Applies a specific action to the options manager.
     *
     * @param string $id The ID of the action to apply.
     * @param string $action The action to apply.
     *
     * @return void
     */
    public function applyAction(string $id, string $action): void
    {
        if (isset($this->actions[$id]) && $this->actions[$id][$action]) {
            call_user_func($this->actions[$id][$action], $this);
        }
    }

    /**
     * Retrieves the current request object.
     *
     * @return Request The current request.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Retrieves the current response object.
     *
     * @return Response The current response.
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Get the uploader configurations.
     *
     * Iterates over the settings fields and returns an array of uploader
     * configurations for the fields of type 'file' or 'upload'.
     *
     * @return array The uploader configurations.
     */
    protected function uploader(): array
    {
        $uploads = [];
        foreach ($this->getFields() as $field) {
            // Check if the field type is 'file' or 'upload'
            if (!in_array($field['type'], ['file', 'upload'])) {
                continue;
            }

            // Get the uploader configuration for the field
            $config = [
                // The name of the field
                'name' => $field['name'],
                // Whether the field is multiple or not
                'multiple' => isset($field['multiple']) && $field['multiple'],
                // The directory to upload the files to
                'uploadTo' => 'settings' . ($this->getConfig('cms', false) ? '/cms' : ''),
                // The allowed extensions
                'extensions' => [],
                // The maximum size of the file
                'maxSize' => null, // apply php max_upload_filesize limit
            ];

            // If the field has an uploader_config attribute, merge it with the config
            if (isset($field['attrs']['uploader_config'])) {
                $config = array_merge($config, $field['attrs']['uploader_config']);
            }

            // Add the config to the array of uploader configurations
            $uploads[] = $config;
        }

        // Return the array of uploader configurations
        return $uploads;
    }


    /**
     * Configure the setting manager.
     *
     * @param array $config
     * @return $this
     */
    public function configure(array $config): self
    {
        if (isset($config['id']) && !isset($this->id)) {
            $this->id = $config['id'];
        }

        $this->config = $config;
        return $this;
    }

    /**
     * Add a section to the settings manager.
     *
     * @param string $id
     * @param string $title
     * @param string|null $description
     * @param callable|array $fields
     * @return $this
     */
    public function addSection(string $id, string $title, ?string $icon, ?string $description = null, callable|array $fields = []): self
    {
        $this->sections[] = compact(['id', 'title', 'icon', 'description', 'fields']);
        return $this;
    }

    /**
     * Get a section by ID.
     *
     * @param string $id Section ID.
     * @return array|null Section array or null if not found.
     */
    public function getSection(string $id): ?array
    {
        return collect(
            $this->sections
        )
            ->find(
                fn($section) => $section['id'] === $id
            );
    }

    /**
     * Get a config value.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Get all sections.
     *
     * @return array
     */
    public function getSections(): array
    {
        return $this->sections;
    }

    /**
     * Handle the index request.
     *
     * @return Response
     */
    public function handleIndex(): Response
    {
        if ($this->request->getMethod() === 'POST') {
            $this->saveSettings();
        }

        $template = $this->getConfig('cms', false) ? 'cms/panel' :
            (!isset($this->id) ? 'cms/index' : 'cms/form');

        return backpack_template($template, ['manager' => $this]);
    }

    /**
     * Get the breadcrumb for the current page.
     *
     * @return array Breadcrumb items.
     */
    public function getBreadcrumb(): array
    {
        if (isset($this->id)) {
            $breadcrumb = [
                $this->getConfig('route') => $this->getConfig('title', 'Settings'),
                '__active' => $this->getSection($this->id)['title'],
            ];
        } else {
            $breadcrumb = ['__active' => $this->getConfig('title', 'Settings')];
        }
        return $breadcrumb;
    }

    /**
     * Get the form instance.
     *
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form ??= get(Form::class);
    }

    /**
     * Save the settings.
     *
     * @return void
     */
    public function saveSettings(): void
    {
        $fields = collect($this->getFields())
            ->filter(fn($field) => !in_array($field['type'], ['heading', 'divider']))
            ->all();

        $data = $this->request->all(
            array_column($fields, 'name')
        );

        try {
            // Sync Changes of files/images
            $data = $this->uploadChanges($data);

            // update all settings
            $status = Option::insert(
                collect($data)
                    ->map(fn($value, $name) => [
                        'name' => $name,
                        'value' => !empty($value) ? (is_array($value) ? json_encode($value) : $value) : null,
                        'preload' => $this->isPreload($name)
                    ])
                    ->all(),
                ['conflict' => ['name'], 'update' => ['value' => 'value']]
            );

            if ($status) {
                // clear cache
                if ($this->getConfig('cms', false)) {
                    cache('cms')
                        ->reload()
                        ->erase($this->id);
                } else {
                    cache()
                        ->reload()
                        ->erase('options:preload');
                }

                // reload settings from database
                getSettings()->reload();

                // apply "on saved" action
                $this->applyAction($this->id, 'changed');

                session()->set('success_message', __('%s saved successfully', $this->getConfig('title', '')));
            } else {
                throw new Exception(__('failed to save %s', $this->getConfig('title', '')));
            }
        } catch (Throwable $e) {
            session()->set('error_message', $e->getMessage());
        }

        $this->response->redirect(route_url($this->getConfig('route', '#'), ['id' => $this->id]));
    }

    /**
     * Get the current section.
     *
     * @return array The current section's data. If the current section does not exist, an empty array is returned.
     */
    public function getCurrentSection(): array
    {
        return $this->getSection($this->id);
    }

    /**
     * Get the ID of the current section.
     *
     * @return string|null The ID of the current section if it exists, otherwise null.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get the fields for the current section.
     *
     * @return array The fields of the current section. If the current section does not exist, an empty array is returned.
     */
    public function getFields(): array
    {
        // Get the fields for the current section, or an empty array if the section does not exist
        $fields = $this->getSection($this->id)['fields'] ?? [];

        // If the fields are callable, call them and use the result
        if (is_callable($fields)) {
            $fields = call_user_func($fields);
        }

        // If CMS is enabled, prefix the fields with 'cms_'
        if ($this->getConfig('cms', false)) {
            $fields = array_map(
                function ($field) {
                    // Prefix the field name with 'cms_{id}'
                    $field['name'] = 'cms_' . $this->id . '_' . $field['name'];
                    return $field;
                },
                $fields
            );
        }

        return $fields;
    }

    /**
     * Check if the given name is preloaded.
     *
     * @param string $name
     * @return bool
     */
    public function isPreload(string $name): bool
    {
        $preloads = $this->getConfig('preload', []);

        if (is_string($preloads) && $preloads === '*') {
            return true;
        }

        return in_array($name, $preloads);
    }
}
