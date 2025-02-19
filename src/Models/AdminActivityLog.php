<?php

namespace Backpack\Models;

use Hyper\Helpers\Orm;
use Hyper\Model;

/**
 * Class AdminActivityLog
 * 
 * Represents an activity log entry made by an admin user.
 * 
 * @package Backpack\Models
 */
class AdminActivityLog extends Model
{
    use Orm;

    /**
     * The name of the table
     *
     * @var string
     */
    protected string $table = 'admin_activity_log';

    /**
     * The id of the admin user who made the action
     *
     * @var int
     */
    public int $admin_users_id;

    /**
     * The id of the target model
     *
     * @var int
     */
    public ?int $target_id;

    /**
     * The type of the target model
     *
     * @var string
     */
    public string $target_type;

    /**
     * The action that was done
     *
     * @var array|string
     */
    public array|string $action;

    /**
     * The time when the action was done
     *
     * @var string|null
     */
    public ?string $created_at;

    /**
     * Returns an array of the ORM relationships.
     *
     * @return array
     */
    protected function orm(): array
    {
        return [
            'user' => ['has' => 'one', 'model' => AdminUser::class],
        ];
    }

    /**
     * Returns a string representation of the log entry.
     *
     * If the action is an array, it is passed to the translation function
     * using the splat operator. Otherwise, the action is returned as is.
     *
     * @return string
     */
    public function __toString()
    {
        return is_array($this->action) ? __(...$this->action) : $this->action;
    }
}