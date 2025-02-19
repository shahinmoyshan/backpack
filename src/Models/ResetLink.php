<?php

namespace Backpack\Models;

use Hyper\Model;

/**
 * Class ResetLink
 *
 * Represents a reset link in the system, providing methods for creating
 * and validating reset links for users and admins.
 *
 * @package Backpack\Models
 */
class ResetLink extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table = 'password_reset_links';

    /**
     * The target ID that the reset link belongs to.
     *
     * @var int
     */
    public int $target_id;

    /**
     * The type of the target ID. Can be 'user' or 'admin'.
     *
     * @var string
     */
    public string $target_type;

    /**
     * The token that will be used to validate the reset link.
     *
     * @var string
     */
    public string $token;

    /**
     * The date and time when the reset link was created.
     *
     * @var string|null
     */
    public ?string $created_at;
}