<?php
namespace Backpack\Views;

use Backpack\Bread;
use Backpack\Models\AdminRole;
use Hyper\Query;
use Backpack\Form;

/**
 * Class ManageRoles
 * 
 * This class handles the admin role management page. It sets up the
 * bread instance for the admin role model, setting up routes, title,
 * permissions, search, sorting, and actions for role management. It
 * also defines form rendering behavior and bulk actions for roles.
 * 
 * @package Backpack\Views
 */
class ManageRoles
{
    public function index(Bread $bread)
    {
        $bread->configure(new AdminRole(), [
            // The route for the admin role management page.
            'route' => 'admin.super.roles',

            // The title for the admin role management page.
            'title' => __('roles'),

            // The title for a single role.
            'title_singular' => __('role'),

            // The permissions required for each action.
            'permissions' => [
                'view' => 'view_roles',
                'edit' => 'edit_roles',
                'delete' => 'delete_roles',
                'create' => 'create_roles',
            ],

            // The activity log for the admin user model
            // This is used to track changes to the admin user model
            'activity_log' => [
                'target_type' => 'roles',
                'actions' => [
                    'create' => 'new role %s created',
                    'update' => 'role %s updated',
                    'delete' => 'role %s deleted',
                    'bulk_delete' => '%s and %d more roles deleted',
                ]
            ],

            // Customization options for the admin role management page.
            'customize' => [
                'width' => [
                    'table' => '5xl',
                    'form' => '3xl',
                    'details' => '3xl',
                ],
            ],

            // The search function for the admin role management page.
            // It searches for roles by their name.
            'search' => function (Query $query, string $keyword) {
                return $query->where(
                    "(name like '%$keyword%')"
                );
            },

            // The sorting options for the admin role management page.
            // It sorts the roles by their name.
            'sort' => ['name' => __('role')],

            // The actions for the admin role management page.
            'action' => [
                'edit' => [
                    'title' => __('edit'),
                    'route' => '%s/edit',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>',
                    'when' => fn() => has_permission('edit_roles'),
                ],
                'delete' => [
                    'title' => __('delete'),
                    'route' => 'delete?ids=%d',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>',
                    'when' => fn($item) => $item->id !== user('admin_roles_id') && has_permission('delete_roles'),
                ]
            ],

            // Filter the roles before saving..
            'onSave' => function (Form $form) {
                $value = array_values(
                    array_filter((array) $form->getValue('role_permissions', []))
                );
                $form->setValue('role_permissions', $value);
                return $form->save();
            },

            // The bulk actions for the admin role management page.
            'bulk_actions' => [
                'delete' => [
                    'title' => __('delete selected'),
                    'callback' => function (array $ids) {
                        $ids = collect($ids)
                            ->filter(fn($id) => intval($id) !== user('admin_roles_id'))
                            ->toString(',');
                        redirect(
                            route_url('admin.super.roles') . '/delete?ids=' . $ids
                        );
                    },
                    // The condition for when the delete bulk action is displayed.
                    'when' => fn() => has_permission('delete_roles'),
                ],
            ],

            // The partials for the admin role management page.
            'partials_inc' => [
                'form' => backpack_templates_dir('admin/bread/form/role')
            ],

            // The columns for the admin role management page.
            'columns' => ['name' => __('name'), 'description' => __('description')],
        ]);

        // Render the admin role management page
        return $bread->renderIndex();
    }
}