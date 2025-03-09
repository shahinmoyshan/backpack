<?php

namespace Backpack\Models;

use Backpack\Models\Post;
use Hyper\Helpers\Orm;

/**
 * A simple blog model.
 *
 * The blog model represents a blog post in the database. It contains properties
 * such as the post title, content, and author.
 *
 * The model also contains methods for managing posts, such as creating, updating,
 * and deleting posts.
 *
 * @since 1.0.0
 * @package Backpack\Models
 */
class Blog extends Post
{
    use Orm;

    /**
     * Define the relationships and fields for the model.
     *
     * @return array
     */
    protected function orm(): array
    {
        return array_merge(parent::orm(), [
            // A post can have many categories.
            'categories' => ['has' => 'many-x', 'model' => PostTerm::class, 'table' => 'posts_terms'],

            // A post belongs to a user.
            'user' => ['has' => 'one', 'model' => AdminUser::class, 'foreignKey' => 'users_id', 'formIgnore' => true],
        ]);
    }

    /**
     * Define the form fields for the model.
     *
     * @return array
     */
    public function form(): array
    {
        return array_merge(parent::form(), [
            // Add a field for the categories.
            'posts_terms' => ['label' => 'Post Categories', 'required' => false, 'attrs' => ['clear' => true]],
        ]);
    }
}
