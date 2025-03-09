<?php

namespace Backpack\Views;

use Backpack\Bread;
use Backpack\Form;
use Backpack\Models\BlogCategory;
use Hyper\Query;

/**
 * The ManageBlogCategories class is used to define the
 * admin page management page for blog categories.
 *
 * @package Backpack\Blog
 */
class ManageBlogCategories
{
    /**
     * Index action for blog category management.
     *
     * This method configures the BREAD interface for managing blog categories,
     * including setting up routes, titles, permissions, activity logs, customizations,
     * bulk actions, search functionality, and form handling. It also defines actions
     * for editing and deleting categories.
     *
     * @param  Bread  $bread  The BREAD instance for the blog category model.
     * @return mixed The rendered index view for blog categories.
     */
    public function index(Bread $bread)
    {
        $bread->configure(new BlogCategory(), [
            // The route for the admin page management page.
            'route' => 'admin.cms.blogs.categories',

            // The title for the admin page management page.
            'title' => __('Blog Categories'),
            'title_singular' => __('Blog Category'),

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
                'target_type' => 'blog_categories',
                'actions' => [
                    'create' => 'new blog category %s created',
                    'update' => 'blog category %s updated',
                    'delete' => 'blog category %s deleted',
                    'bulk_delete' => '%s and %d more blog categories deleted',
                ]
            ],

            // The customizations for the admin page management page.
            'customize' => [
                'width' => [
                    'table' => '3xl',
                    'form' => '2xl',
                    'details' => '4xl',
                ],
            ],

            // The bulk actions for the admin page management page.
            'bulk_actions' => [
                'delete' => [
                    'title' => __('delete selected'),
                    'callback' => function (array $ids) {
                        redirect(
                            route_url('admin.cms.blogs.categories') . '/delete?ids=' . join(',', $ids)
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

            'onSave' => function (Form $form) {
                if (empty($form->getValue('slug')) && !empty($form->getValue('name'))) {
                    $form->setValue('slug', slugify($form->getValue('name')));
                }

                $form->setValue('slug', trim($form->getValue('slug'), '/'));
                return $form->save();
            },

            'sort' => ['name' => __('name')],

            'onQuery' => function (Query $query) {
                $query->where(['term_type' => 'category'])->orderDesc();
            },

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
        ]);

        // Render the admin users list
        return $bread->renderIndex();
    }
}