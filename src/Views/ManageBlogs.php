<?php

namespace Backpack\Views;

use Backpack\Bread;
use Backpack\Models\Blog;
use Hyper\Query;
use Backpack\Form;

/**
 * This class is responsible for setting up the BREAD interface for blogs.
 *
 * The BREAD interface is a simple CRUD interface for managing the data in the
 * admin page model. It provides a basic interface for creating, reading,
 * updating, and deleting data in the admin page model.
 *
 * @package Backpack\Views
 */
class ManageBlogs
{
    /**
     * This method sets up the BREAD interface for the admin page management page.
     *
     * The BREAD interface is a simple CRUD interface for managing the data in the
     * admin page model. It provides a basic interface for creating, reading,
     * updating, and deleting data in the admin page model.
     *
     * @param Backpack\Bread $bread The bread instance for the admin page model.
     *
     * @return Backpack\Bread The bread instance for the admin page model.
     */
    public function index(Bread $bread)
    {
        $bread->configure(new Blog(), [
            // The route for the admin page management page.
            'route' => 'admin.cms.blogs',

            // The title for the admin page management page.
            'title' => __('blogs'),
            'title_singular' => __('blog'),

            // The permissions for the admin page management page.
            'permissions' => [
                'view' => 'view_blogs',
                'edit' => 'edit_blogs',
                'delete' => 'delete_blogs',
                'create' => 'create_blogs',
            ],

            // The activity log for the admin page model.
            // This is used to track changes to the admin page model.
            'activity_log' => [
                'target_type' => 'blogs',
                'actions' => [
                    'create' => 'new blog %s created',
                    'update' => 'blog %s updated',
                    'delete' => 'blog %s deleted',
                    'bulk_delete' => '%s and %d more blogs deleted',
                ]
            ],

            // The customizations for the admin page management page.
            'customize' => [
                'width' => [
                    'table' => '7xl',
                    'form' => '6xl',
                    'details' => '4xl',
                ],
            ],

            'menu_items' => ['edit', 'history'],

            // The bulk actions for the admin page management page.
            'bulk_actions' => [
                'delete' => [
                    'title' => __('delete selected'),
                    'callback' => function (array $ids) {
                        redirect(
                            route_url('admin.cms.blogs') . '/delete?ids=' . join(',', $ids)
                        );
                    },
                    'when' => fn() => has_permission('delete_blogs'),
                ],
            ],

            'search' => function (Query $query, string $keyword) {
                return $query->where(
                    "(title like '%$keyword%')"
                );
            },

            'sort' => ['title' => __('title')],

            // The filters for the admin page management page.
            'filter' => [
                'published' => [
                    'title' => __('published pages'),
                    'query' => function (Query $query) {
                        $query->where(['status' => 'published']);
                    }
                ],
                'draft' => [
                    'title' => __('draft pages'),
                    'query' => function (Query $query) {
                        $query->where(['status' => 'draft']);
                    }
                ],
            ],

            // The actions for the admin page management page.
            'action' => [
                'edit' => [
                    'title' => __('edit'),
                    'route' => '%s/edit',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>',
                    'when' => fn() => has_permission('edit_blogs'),
                ],
                'delete' => [
                    'title' => __('delete'),
                    'route' => 'delete?ids=%d',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>',
                    'when' => fn($item) => has_permission('delete_blogs'),
                ]
            ],

            // Custom buttons to be displayed at the top of the admin users list
            'buttons' => [
                [
                    'title' => __('Blog Categories'),
                    'url' => route_url('admin.cms.blogs.categories'),
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                <path fill-rule="evenodd" d="M4.5 2A2.5 2.5 0 0 0 2 4.5v3.879a2.5 2.5 0 0 0 .732 1.767l7.5 7.5a2.5 2.5 0 0 0 3.536 0l3.878-3.878a2.5 2.5 0 0 0 0-3.536l-7.5-7.5A2.5 2.5 0 0 0 8.38 2H4.5ZM5 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                            </svg>',
                    'when' => fn() => has_any_permission(['create_blogs']),
                ]
            ],

            'onSave' => function (Form $form) {
                if (empty($form->getValue('slug')) && !empty($form->getValue('title'))) {
                    $form->merge('slug', ['value' => slugify($form->getValue('title'))]);
                }

                $form->merge('slug', ['value' => trim($form->getValue('slug'), '/')]);
                return $form->save();
            },

            'onQuery' => function (Query $query) {
                $query->where(['post_type' => 'blog'])->orderDesc();
            },

            'with' => ['user'],

            'columns' => ['thumbnail' => __('Thumbnail'), 'title' => __('Title'), 'user' => __('User'), 'status' => __('Status'), 'Created At'],

            'partials' => [
                'list' => backpack_templates_dir('admin/bread/list/blog'),
            ],

            'partials_inc' => [
                'form' => backpack_templates_dir('admin/bread/form/blog'),
            ],
        ]);

        // Render the admin users list
        return $bread->renderIndex();
    }
}