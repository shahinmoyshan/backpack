<?php

namespace Backpack\Models;

use Hyper\Model;

/**
 * Class PostMeta
 *
 * Represents metadata associated with a post.
 *
 * @package Backpack\Models
 */
class PostMeta extends Model
{
    /**
     * The name of the table associated with the model.
     *
     * @var string
     */
    protected string $table = 'posts_meta';

    /**
     * The ID of the post associated with the metadata.
     *
     * @var int
     */
    public int $posts_id;

    /**
     * The metadata key.
     *
     * @var string
     */
    public string $meta_key;

    /**
     * The metadata value.
     *
     * @var string
     */
    public string $meta_value;
}

