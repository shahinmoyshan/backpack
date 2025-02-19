<?php

namespace Backpack;

use Backpack\Models\AdminActivityLog;
use Exception;
use Hyper\Model;
use Hyper\Request;
use Hyper\Response;
use Hyper\Utils\Paginator;

/**
 * Class bread
 *
 * The base class for all bread (admin backend) classes.
 *
 * It provides methods for creating, editing, deleting, and listing models in the admin backend.
 * 
 * B - Browse
 * R - Read
 * E - Edit
 * D - Delete
 * 
 * BREAD - Browse, Read, Edit, and Delete Records from Single Data-Table
 *
 * @package app\modules\backend
 * @author Shahin Moyshan <shahin.moyshan2@gmail.com>
 * @version 1.0.0
 *
 * @property Paginator $paginator The paginator instance
 * @property Form $form The form instance
 * @property string $action The current action (create, edit, delete, etc.)
 * @property int $modelId The model id
 */
class Bread
{
    /**
     * @var Paginator The paginator instance
     */
    private Paginator $paginator;

    /**
     * @var Form The form instance
     */
    private Form $form;

    /**
     * @var string The current action (create, edit, delete, view, etc.)
     */
    private string $action;

    /**
     * @var int The model id
     */
    private int $modelId;

    /**
     * @var array The configuration array for this bread class.
     */
    private array $config;

    /**
     * @var Model The model instance for this bread class.
     */
    private Model $model;

    /**
     * The constructor for the bread class. This function is responsible for
     * determining the current action and model ID from the URL, and setting
     * the action and modelId properties accordingly.
     *
     * @param Request $request The request instance used for this bread
     * @param Response $response The response instance used for this bread
     */
    public function __construct(private Request $request, private Response $response)
    {
        // Determine the current bread page action from the URL
        $this->action = trim($request->getRouteParam(0, ''), '/');

        // Parse action and model ID from the URL if applicable
        if (strpos($this->action, '/') !== false) {
            $parts = explode('/', $this->action);
            if (count($parts) === 2) {
                $this->action = $parts[1];
                $this->modelId = intval($parts[0]);
            }
        } elseif (is_numeric($this->action) && intval($this->action) > 0) {
            $this->modelId = intval($this->action);
            $this->action = 'view';
        }
    }

    /**
     * Configure the bread class after instantiation.
     *
     * This function sets the model and config properties of the bread class, and
     * handles permissions, onInit callbacks, bulk actions, form submissions, and
     * model loading (if necessary) based on the action and model ID.
     *
     * @param Model $model The model instance to use for this bread class
     * @param array $config The configuration array for this bread class
     */
    public function configure(Model $model, array $config): void
    {
        $this->model = $model;
        $this->config = $config;

        // Handle permissions, redirect if not allowed
        match ($this->action) {
            'create' => isset($this->config['permissions']['create']) && apply_permissions($this->config['permissions']['create']),
            'edit' => isset($this->config['permissions']['edit']) && apply_permissions($this->config['permissions']['edit']),
            'delete' => isset($this->config['permissions']['delete']) && apply_permissions($this->config['permissions']['delete']),
            default => isset($this->config['permissions']['view']) && apply_permissions($this->config['permissions']['view']),
        };

        // Handle onInit callback
        if (isset($this->config['onInit'])) {
            call_user_func($this->config['onInit'], $this);
        }

        // Handle bulk actions
        if ($this->action === 'bulk_action') {
            $action = $this->config['bulk_actions'][$this->request->query('action')] ?? null;

            if ($action !== null && isset($action['callback'])) {
                call_user_func(
                    $action['callback'],
                    explode(',', $this->request->query('ids'))
                );
            }

            $this->response->redirect(route_url($this->config['route']));
        }

        // Handle form submissions for create, edit, or delete actions
        if ($this->request->getMethod() === 'POST') {
            if (in_array($this->action, ['create', 'edit'], true)) {
                $this->saveForm(); // Save form data for create/edit actions
            } elseif ($this->action === 'delete') {
                $this->deleteModels(
                    explode(',', $this->request->post('delete', '')) // Delete specified models
                );
            }
        }

        // Load the model by ID if necessary
        if (isset($this->modelId)) {
            $model = $this->model->find($this->modelId);
            if ($model === false) {
                // Set error message and redirect if model not found
                session()->set('error_message', __('%s not found', $this->config['title_singular']));
                $this->response->redirect(route_url($this->config['route']));
            }

            if (isset($this->config['onFind'])) {
                call_user_func($this->config['onFind'], $model);
            }

            $this->model = $model; // Update the model with the found instance
        }
    }

    /**
     * Get the current action being performed.
     *
     * @return string The current action (e.g., create, edit, delete).
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Returns the ID of the model being manipulated.
     *
     * @return string The model ID.
     */
    public function getModelId(): string
    {
        return $this->modelId;
    }

    /**
     * Returns the model instance that is currently being managed.
     *
     * The model is either loaded from the database by ID or created as a new instance.
     *
     * @return Model The model instance.
     */
    public function getModel(): Model
    {
        return $this->model;
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
     * @return Response The current response instance.
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Retrieves a configuration value by its key.
     *
     * @param string $key The key for the configuration value.
     * @param mixed $default The default value to return if the key is not found.
     *
     * @return mixed The configuration value, or the default value if the key doesn't exist.
     */
    public function getConfig(string $key, $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Returns the breadcrumb items for the current page.
     *
     * The default implementation returns an array with the bread route and title
     * as the first item, and the current action as the second item, unless the
     * action is 'list', in which case the second item is not included.
     *
     * If the 'breadcrumbs' key is set in the bread config, its value is called
     * as a function with the bread instance as an argument, and its return value
     * is used as the breadcrumb items.
     *
     * @return array The breadcrumb items.
     */
    public function getBreadcrumbItems(): array
    {
        if (isset($this->config['breadcrumbs'])) {
            return call_user_func($this->config['breadcrumbs'], $this);
        }

        return array_filter([
            $this->config['route'] => $this->config['title'],
            '__active' => match ($this->action) {
                'create' => 'create',
                'edit', 'view', 'history' => $this->model,
                'delete' => 'delete',
                default => 'list'
            }
        ]);
    }


    /**
     * Returns the page title based on the current action and configuration.
     *
     * If a custom pageTitle is defined in the config, it will use that.
     * Otherwise, it defaults to a title based on the current action.
     *
     * @return string The page title.
     */
    public function getPageTitle(): string
    {
        // Check if a custom pageTitle callback is set in the config
        if (isset($this->config['pageTitle'])) {
            return call_user_func($this->config['pageTitle'], $this);
        }

        // Return a default title based on the current action
        return match ($this->action) {
            'create' => __('create %s', $this->config['title_singular']),
            'edit', 'view', 'history' => __('%s - %s', [$this->config['title_singular'], $this->model]),
            'delete' => __('delete %s', $this->config['title_singular']),
            default => __('%s list', $this->config['title_singular']),
        };
    }

    /**
     * Returns the paginator instance.
     *
     * The paginator instance is created only once and cached, so calling this method
     * multiple times will return the same instance.
     *
     * The paginator instance is created by calling the {@see paginateData} method.
     *
     * @return Paginator The paginator instance.
     */
    public function getPaginator(): Paginator
    {
        return $this->paginator ??= $this->paginateData();
    }

    /**
     * Render the bread partial.
     *
     * The action is used to determine the partial file to include.
     * If the action is not recognized, the table partial is used.
     *
     * @return string The rendered partial.
     */
    public function render(): string
    {
        // Determine the partial based on the current action
        $partial = match ($this->action) {
            'create', 'edit', 'view', 'delete', 'history' => $this->action,
            default => 'table'
        };

        // Override the default partial with the one specified in the config
        $partial = $this->config['partials'][$partial] ?? __DIR__ . "/Templates/bread/{$partial}";

        // Pass the bread instance to the partial as a variable
        return $this->partial($partial, ['bread' => $this]);
    }

    /**
     * Renders the index page for the bread instance.
     *
     * This method utilizes the backpack_template function to render the 'bread/index' template,
     * passing the current bread instance as context to the template.
     *
     * @return Response The response object containing the rendered index page.
     */
    public function renderIndex(): Response
    {
        return backpack_template('bread/index', ['bread' => $this]);
    }

    /**
     * Renders a partial view.
     *
     * This method extracts the provided context variables, captures the output of the included PHP file,
     * and returns it as a string.
     *
     * @param string $path The path to the partial PHP file, without the file extension.
     * @param array $context An associative array of variables to extract into the scope of the partial.
     * @return string The rendered content of the partial.
     */
    public function partial(string $path, array $context = []): string
    {
        // Extract variables from the context array into the current symbol table
        extract($context);

        // Start output buffering
        ob_start();

        // Include the specified partial PHP file
        include str_replace('.php', '', $path) . '.php';

        // Get the contents of the buffer and clean it
        return ob_get_clean();
    }

    /**
     * Retrieves the form instance for the current request.
     *
     * If the form instance is not already initialized, this method creates a new
     * form instance using the request and model associated with the bread instance.
     * It also triggers the 'onFormInit' event if configured.
     *
     * @return Form The form instance.
     */
    public function getForm(): Form
    {
        // Check if the form is already initialized
        if (!isset($this->form)) {
            // Initialize the form with the request and model
            $this->form = get(Form::class)
                ->setModel($this->model);

            // Trigger the onFormInit event if defined in the configuration
            if (isset($this->config['onFormInit'])) {
                call_user_func($this->config['onFormInit'], $this->form);
            }
        }

        // Return the form instance
        return $this->form;
    }

    /** @internal */

    /**
     * Paginates the data based on the current request and configuration.
     *
     * This method applies various query modifications, like ordering,
     * searching, and filtering, before paginating the data.
     *
     * @return Paginator The paginator instance containing the paginated data.
     */
    private function paginateData(): Paginator
    {
        // Initialize the query from the model
        $query = isset($this->config['with']) ?
            $this->model->with($this->config['with']) :
            $this->model->select();

        // Apply custom query modifications if provided
        if (isset($this->config['onQuery'])) {
            call_user_func($this->config['onQuery'], $query);
        }

        // Apply search criteria if a search query is provided
        if (!empty($this->request->query('q', ''))) {
            $query = call_user_func(
                $this->config['search'],
                $query,
                $this->request->query('q')
            );
        }

        // Apply filtering based on the filter parameter in the request
        if (!empty($this->request->query('filter')) && isset($this->config['filter'])) {
            call_user_func(
                $this->config['filter'][$this->request->query('filter')]['query'],
                $query
            );
        }

        // Apply sorting based on the sort parameters in the request
        if (!empty($this->request->query('sortColumn'))) {
            $query->order($this->request->query('sortColumn') . ' ' . $this->request->query('sortDirection', 'asc'));
        }

        // Determine the number of items per page, defaulting to 2
        $perPage = !empty($this->request->query('perPage', '')) ? $this->request->query('perPage') : 10;

        // Modify query before pagination if a callback is provided
        if (isset($this->config['beforePagination'])) {
            call_user_func($this->config['beforePagination'], $query);
        }

        // Paginate the query and get the paginator instance
        $paginator = $query->paginate($perPage);

        // Perform any actions after pagination if a callback is provided
        if (isset($this->config['afterPagination'])) {
            call_user_func($this->config['afterPagination'], $paginator);
        }

        // Return the paginator instance
        return $paginator;
    }

    /**
     * Save the form data for create or edit actions.
     *
     * This method validates the form, saves the data, and sets the appropriate
     * success or error messages.
     */
    private function saveForm(): void
    {
        // Call beforeSave event if defined
        if (isset($this->config['beforeSave'])) {
            call_user_func($this->config['beforeSave'], $this->getForm());
        }

        // Validate the form
        if ($this->getForm()->validate()) {
            // Call afterFormValidate event if defined
            if (isset($this->config['afterFormValidate'])) {
                call_user_func($this->config['afterFormValidate'], $this->getForm());
            }

            try {
                // Save the form data
                if (
                    (isset($this->config['onSave']) && call_user_func($this->config['onSave'], $this->getForm())) ||
                    (!isset($this->config['onSave']) && $this->getForm()->save())
                ) {
                    // Call afterSave event if defined
                    if (isset($this->config['afterSave'])) {
                        call_user_func($this->config['afterSave'], $this->getForm()->getModel());
                    }

                    // Set the appropriate success message
                    if ($this->request->post('_save') !== null || $this->request->post('_save_edit') !== null) {
                        $this->createActivityLog(
                            'update',
                            $this->getForm()->getModel()->id,
                            strval($this->getForm()->getModel())
                        );
                        session()->set('success_message', __('%s saved successfully', $this->config['title_singular']));
                    } else {
                        $this->createActivityLog(
                            'create',
                            $this->getForm()->getModel()->id,
                            strval($this->getForm()->getModel())
                        );
                        session()->set('success_message', __('%s created successfully', $this->config['title_singular']));
                    }

                    // Redirect to the appropriate page
                    if ($this->request->post('_create_add') !== null) {
                        $this->response->redirect(route_url($this->config['route']) . '/create');
                    } elseif ($this->request->post('_save_edit') !== null) {
                        $this->response->redirect(route_url($this->config['route']) . '/' . $this->getForm()->getValue('id') . '/edit');
                    } else {
                        $this->response->redirect(route_url($this->config['route']));
                    }
                } else {
                    // Set an error message if save fails
                    session()->set('warning_message', __('nothing to save %s', $this->config['title_singular']));
                }
            } catch (Exception $e) {
                session()->set('error_message', $e->getMessage());
            }
        } else {
            session()->set('warning_message', __('you have some errors in your form, please check it'));
        }
    }

    /**
     * Delete multiple models using their IDs.
     *
     * This method calls the beforeDelete and afterDelete events, and also calls
     * the beforeDeleteItem and afterDeleteItem events for each model. If the
     * model has file uploads, it will delete the related objects and files.
     *
     * @param array $ids The list of IDs to delete
     */
    private function deleteModels(array $ids): void
    {
        $deleted = [];
        $objects = $this->model->where(['id' => $ids])->result();

        // Call beforeDelete event if defined
        if (isset($this->config['beforeDelete'])) {
            call_user_func($this->config['beforeDelete'], $objects);
        }

        foreach ($objects as $object) {
            // delete related/child objects related using orm.
            if (method_exists($object, 'getRegisteredOrm')) {
                foreach ($object->getRegisteredOrm() as $with => $rel) {
                    if (
                        // Check if the relationship type is many-to-many or many-to-one
                        // and if the onDelete action is set to 'cascade'
                        !in_array($rel['has'], ['many', 'many-x']) ||
                        !isset($rel['onDelete']) ||
                        strtolower($rel['onDelete']) !== 'cascade'
                    ) {
                        continue;
                    }

                    // delete every related object if the model contains file uploads
                    if (
                        method_exists($rel['model'], 'uploads') ||
                        method_exists($rel['model'], 'onRemove')
                    ) {
                        foreach ($object->{$with} as $relObj) {
                            // This remove method will also delete the related uploaded files as well 
                            $relObj->remove();
                        }
                    }
                }
            }

            // Call beforeDeleteItem event if defined
            if (isset($this->config['beforeDeleteItem'])) {
                call_user_func($this->config['beforeDeleteItem'], $object);
            }

            // delete current object.
            if ($object->remove()) {
                // Call afterDeleteItem event if defined
                if (isset($this->config['afterDeleteItem'])) {
                    call_user_func($this->config['afterDeleteItem'], $object);
                }
                $deleted[] = $object->id;
            }
        }

        if (count($deleted) > 0) {

            if (isset($this->config['activity_log']) && !empty($this->config['activity_log'])) {
                // delete activity log related to the deleted object
                AdminActivityLog::where([
                    'target_id' => $deleted,
                    'target_type' => $this->config['activity_log']['target_type'],
                ])->delete();
            }

            $object = collect($objects)->find(fn($ob) => $ob->id === $deleted[0]);

            if (count($deleted) > 1) {
                $this->createActivityLog(
                    'bulk_delete',
                    $object->id,
                    strval($object),
                    count($deleted) - 1,
                );
            } else {
                $this->createActivityLog(
                    'delete',
                    $object->id,
                    strval($object)
                );
            }

            // Call afterDelete event if defined
            if (isset($this->config['afterDelete'])) {
                call_user_func($this->config['afterDelete'], $objects, $deleted);
            }
            session()->set('success_message', __('deleted %d %s successfully', count($deleted), [count($deleted), $this->config['title_singular']], [count($deleted), $this->config['title']]));
        } else {
            session()->set('error_message', __('failed to delete %s', $this->config['title']));
        }

        if ($this->request->accept('application/json')) {
            $this->response->json(['navigate' => route_url($this->config['route'])])
                ->send();
            exit;
        }

        $this->response->redirect(route_url($this->config['route']));
    }

    /**
     * Creates a new activity log entry based on the given action type and target
     * ID.
     *
     * This method is used to create a new activity log entry based on the given
     * action type and target ID. It takes the action type and target ID as
     * parameters and creates a new activity log entry based on the
     * configuration defined in the bread instance.
     *
     * @param string $action_type The type of action to log.
     * @param int $target_id The ID of the target model.
     * @param mixed ...$args Any additional arguments to be passed to the
     *     activity log.
     */
    private function createActivityLog(string $action_type, int $target_id, ...$args): void
    {
        // Check if the configuration for activity log is defined
        if (isset($this->config['activity_log']) && !empty($this->config['activity_log'])) {
            // Get the action from the configuration
            $action = $this->config['activity_log']['actions'][$action_type] ?? null;

            // If the action is not null, create a new activity log entry
            if ($action !== null) {
                AdminActivityLog::insert([
                    'admin_users_id' => user('id'),
                    'target_id' => $target_id,
                    'target_type' => $this->config['activity_log']['target_type'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'action' => json_encode([$action, $args]),
                ], ['ignore' => true]);
            }
        }
    }
}