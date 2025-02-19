<?php

namespace Backpack\Models;

use Hyper\Model;

/**
 * Class Option
 * 
 * Represents an option in the system. This class is used to handle
 * the storage and retrieval of option data.
 *
 * @package Backpack\Models
 */
class Option extends Model
{
    /**
     * @var string The name of the database table associated with the model.
     */
    protected string $table = 'options';

    /**
     * @var string The name of the option.
     */
    public string $name;

    /**
     * @var array|string The value of the option, which can be an array or string.
     */
    public null|array|string $value;

    /**
     * @var bool Indicates whether the option should be preloaded.
     */
    public bool $preload = false;
}
