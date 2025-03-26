<?php

namespace Backpack\Models;

use Hyper\Helpers\Orm;
use Hyper\Model;

/**
 * Class AdminRole
 *
 * Represents an admin role, which is a role that can be assigned to an admin
 * user. Roles are used to control access to certain parts of the admin
 * backend.
 *
 * @package Backpack\Models
 */
class AdminRole extends Model
{
    use Orm;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table = 'admin_roles';

    /**
     * The name of the role.
     *
     * @var string
     */
    public string $name;

    /**
     * The description of the role.
     *
     * @var string|null
     */
    public ?string $description;

    /**
     * The cached permissions for the role.
     *
     * @var array
     */
    protected array $cachedPermissions;

    /**
     * The relations of the model.
     *
     * @return array
     */
    protected function orm(): array
    {
        return [
            'permissions' => ['has' => 'many-x', 'model' => Permission::class, 'table' => 'role_permissions']
        ];
    }

    /**
     * Retrieves the permissions associated with the admin role.
     *
     * If the role ID is set, it attempts to load the cached permissions for
     * the role from the cache. If the permissions are not cached, it will
     * fetch them from the database and cache them for 30 minutes.
     *
     * @return array The permissions associated with the role.
     */
    public function getPermissions(): array
    {
        if (isset($this->id)) {
            return $this->cachedPermissions ??= cache('permissions')
                ->load($this->id, fn() => $this->permissions, '30 minutes');
        }

        return [];
    }

    /**
     * After removing the admin role, erase the cached permissions associated
     * with the role so that the next time the role is loaded, the permissions
     * will be re-cached.
     *
     * @return void
     */
    protected function afterRemove()
    {
        cache('permissions')->erase($this->id);
    }

    /**
     * After saving the admin role, erase the cached permissions associated
     * with the role so that the next time the role is loaded, the permissions
     * will be re-cached.
     *
     * @return void
     */
    protected function afterSave()
    {
        cache('permissions')->erase($this->id);
    }

    /**
     * Return the name of the admin role as a string, for use cases where an
     * admin role object needs to be converted to a string.
     *
     * @return string The name of the admin role.
     */
    public function __toString()
    {
        return $this->name;
    }
}