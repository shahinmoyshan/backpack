<?php

namespace Backpack\Models;

use Hyper\Helpers\Orm;
use Hyper\Helpers\Uploader;
use Hyper\Model;

/**
 * Class Post
 *
 * Represents a post in the system, providing methods for handling 
 * ORM relationships and file uploads specific to posts.
 *
 * @package Backpack\Models
 */
class Post extends Model
{
    use Uploader, Orm;

    /**
     * The table associated with the Post model.
     *
     * @var string
     */
    protected string $table = 'posts';

    /**
     * The title of the post.
     *
     * @var string
     */
    public string $title;

    /**
     * The slug of the post.
     *
     * @var string
     */
    public string $slug;

    /**
     * The thumbnail of the post.
     *
     * @var null|string|array
     */
    public null|string|array $thumbnail;

    /**
     * The content of the post.
     *
     * @var string
     */
    public string $content;

    /**
     * The status of the post.
     *
     * @var string
     */
    public string $status;

    /**
     * The type of the post.
     *
     * @var string
     */
    public string $post_type;

    /**
     * The SEO settings for the post.
     *
     * @var string|array
     */
    public string|array $seo_settings;

    /**
     * The date and time the post was created.
     *
     * @var ?string
     */
    public ?string $created_at;

    /**
     * The date and time the post was updated.
     *
     * @var ?string
     */
    public ?string $updated_at;

    /**
     * Returns an array of the ORM relationships.
     *
     * @return array An array of ORM relationships.
     */
    protected function orm(): array
    {
        return [
            'meta' => ['has' => 'many', 'model' => PostMeta::class, 'formIgnore' => true],
            'terms' => ['has' => 'many-x', 'model' => PostTerm::class, 'table' => 'posts_terms', 'formIgnore' => true],
        ];
    }

    /**
     * Defines the form fields for the Post model.
     *
     * @return array An array of form field configurations, including post type, slug, status, and content.
     */
    public function form(): array
    {
        return [
            'post_type' => ['type' => 'hidden', 'value' => 'page'],
            'slug' => ['required' => false],
            'status' => ['type' => 'select', 'options' => ['published' => __('published'), 'draft' => __('draft')]],
            'content' => ['type' => 'richtext', 'label' => false],
        ];
    }

    /**
     * Define the uploader configuration for the model.
     *
     * @return array The uploader configuration.
     */
    protected function uploader(): array
    {
        return [
            [
                'name' => 'thumbnail',
                'uploadTo' => '/posts/' . date('m-Y'),
                'extensions' => ['jpg', 'jpeg', 'png'],
                'compress' => 75,
                'maxSize' => 2097152, // 2MB per image
                'resize' => [1920 => 1080], // resize main image
                'resizes' => [150 => 150, 800 => 800], // create resized images
            ]
        ];
    }

    /**
     * Returns the title of the post as a string representation.
     *
     * @return string The title of the post.
     */
    public function __toString(): string
    {
        return $this->title;
    }
}