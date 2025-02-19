<?php

namespace Backpack\Models;

use Hyper\Model;

/**
 * Class Permission
 *
 * This class represents a permission in the system. It is a unique name that
 * can be assigned to a role, and it can be used to determine if a user has a
 * specific permission.
 *
 * @package Backpack\Models
 */
class Permission extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table = 'permissions';

    /**
     * The name of the permission.
     *
     * @var string
     */
    public string $name;

    /**
     * Returns the name of the permission in a human-readable format.
     *
     * @return string
     */
    public function __toString()
    {
        return __(strtolower(pretty_text($this->name)));
    }

}