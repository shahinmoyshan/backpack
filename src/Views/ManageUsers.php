<?php

namespace Backpack\Views;

use Backpack\Bread;
use Backpack\Models\AdminUser;
use Hyper\Query;
use Backpack\Form;
use Hyper\Utils\Hash;

/**
 * Class ManageUsers
 * 
 * A class containing a single method that acts as a controller for the
 * manage users page.
 *
 * @package Backpack\Views
 */
class ManageUsers
{
    /**
     * Index action for admin user management.
     *
     * This method sets up the bread instance for the admin user model, setting up
     * routes, title, permissions, search, sorting, and actions for admin user management.
     * It also defines form rendering behavior and bulk actions for admin users.
     *
     * @param  Bread  $bread  The bread instance for the admin user model.
     */
    public function index(Bread $bread)
    {
        $bread->configure(new AdminUser, [
            // The route where the breadcrumbs will be rendered
            'route' => 'admin.super.users',

            // The title to be displayed for the breadcrumb
            'title' => __('users'),

            // The title to be displayed for the singular breadcrumb
            'title_singular' => __('user'),

            // The permissions required to view, edit, delete, and create
            // admin users
            'permissions' => [
                'view' => 'view_admin_users',
                'edit' => 'edit_admin_users',
                'delete' => 'delete_admin_users',
                'create' => 'create_admin_users',
            ],

            // The activity log for the admin user model
            // This is used to track changes to the admin user model
            'activity_log' => [
                'target_type' => 'users',
                'actions' => [
                    'create' => 'new user %s created',
                    'update' => 'user %s updated',
                    'delete' => 'user %s deleted',
                    'bulk_delete' => '%s and %d more users deleted',
                ]
            ],

            // Customization options for the users bread pages
            'customize' => [
                'width' => [
                    'table' => '5xl',
                    'form' => '3xl',
                    'details' => '3xl',
                ],
            ],

            // Custom bulk actions for this bread
            'bulk_actions' => [
                'delete' => [
                    'title' => __('delete selected'),
                    'callback' => function (array $ids) {
                        // Filter out the current user's ID
                        $ids = collect($ids)
                            ->filter(fn($id) => intval($id) !== user('id'))
                            ->toString(',');
                        // Redirect to the delete route with the filtered IDs
                        redirect(
                            route_url('admin.super.users') . '/delete?ids=' . $ids
                        );
                    },
                    'when' => fn() => has_permission('delete_admin_users'),
                ],
            ],

            // The search query to be executed when searching for admin users
            'search' => function (Query $query, string $keyword) {
                return $query->where(
                    "(username like '%$keyword%' OR full_name like '%$keyword%' OR email like '%$keyword%')"
                );
            },

            // The sort order for the admin users list
            'sort' => ['full_name' => __('full name'), 'email' => __('email'), 'last_login' => __('last login')],

            // The filter queries to be executed for the admin users list
            'filter' => [
                'active' => [
                    'title' => __('active users'),
                    'query' => function (Query $query) {
                        $query->where(['status' => 'active']);
                    }
                ],
                'inactive' => [
                    'title' => __('inactive users'),
                    'query' => function (Query $query) {
                        $query->where(['status' => 'inactive']);
                    }
                ],
            ],

            // The actions to be displayed for each admin user in the list
            'action' => [
                'edit' => [
                    'title' => __('edit'),
                    'route' => '%s/edit',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>',
                    'when' => fn() => has_permission('edit_admin_users'),
                ],
                'delete' => [
                    'title' => __('delete'),
                    'route' => 'delete?ids=%d',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>',
                    'when' => fn($item) => has_permission('delete_admin_users') && $item->id !== user('id'),
                ]
            ],

            // A callback function to be executed when rendering the form
            'onFormRender' => function (Form $form) {
                if ($form->hasField('id')) {
                    // role field should be hidden when editing self
                    if (intval($form->getValue('id', 0)) === user('id')) {
                        $form->merge('admin_roles_id', ['type' => 'hidden']);
                        $form->merge('status', ['type' => 'hidden']);
                    }

                    // password field should be hidden when editing existing user
                    $form->merge('password', ['type' => 'hidden']);
                }
            },

            // A callback function to be executed after validating the form
            'afterFormValidate' => function (Form $form) {
                // encrypt password when creating new user
                if (!$form->hasField('id')) {
                    $form->merge('password', ['value' => get(Hash::class)->hashPassword($form->getValue('password'))]);
                }
            },

            // A callback function to be executed when querying the admin users
            'onQuery' => function (Query $query) {
                $query->orderDesc();
            },

            // fetch result with role relation
            'with' => ['role'],

            // The template to be used for rendering the admin users list
            'partials' => [
                'list' => backpack_templates_dir('admin/bread/list/user'),
            ],

            // Custom buttons to be displayed at the top of the admin users list
            'buttons' => [
                [
                    'title' => __('Manage Roles'),
                    'url' => route_url('admin.super.roles'),
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>',
                    'when' => fn() => has_any_permission(['create_roles']),
                ]
            ]
        ]);

        // Render the admin users list
        return $bread->renderIndex();
    }
}