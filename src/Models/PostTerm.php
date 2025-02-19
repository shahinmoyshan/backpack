<?php

namespace Backpack\Models;

use Hyper\Model;

/**
 * Class PostTerm
 *
 * Represents a term in the system, providing methods for handling 
 * ORM relationships and file uploads specific to terms.
 *
 * @package Backpack\Models
 */
class PostTerm extends Model
{
    protected string $table = 'terms';

    /**
     * Term's name
     * @var string
     */
    public string $name;

    /**
     * Term's slug
     * @var string
     */
    public string $slug;

    /**
     * Term's description
     * @var string
     */
    public string $description;

    /**
     * Term's type
     * @var string
     */
    public string $term_type;

    /**
     * Term's parent ID
     * @var int|null
     */
    public ?int $terms_id;
}
