<?php

namespace Backpack\Models;

use Hyper\Helpers\Orm;
use Hyper\Helpers\Uploader;
use Hyper\Model;

/**
 * Class AdminUser
 *
 * Represents an admin user in the system, providing methods for handling 
 * ORM relationships and file uploads specific to admin users.
 * 
 * @package Backpack\Models
 */
class AdminUser extends Model
{
    use Orm, Uploader;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected string $table = 'admin_users';

    /**
     * The full name of the admin user.
     *
     * @var string|null
     */
    public ?string $full_name;

    /**
     * The username of the admin user.
     *
     * @var string
     */
    public string $username;

    /**
     * The email address of the admin user.
     *
     * @var string
     */
    public string $email;

    /**
     * The image associated with the admin user.
     *
     * @var string|null|array
     */
    public null|string|array $image;

    /**
     * The password of the admin user.
     *
     * @var string
     */
    public string $password;

    /**
     * The status of the admin user.
     *
     * @var string
     */
    public string $status;

    /**
     * The ID of the admin role associated with the admin user.
     *
     * @var int|null
     */
    public ?int $admin_roles_id;

    /**
     * The timestamp of the last time the admin user logged in.
     *
     * @var string|null
     */
    public ?string $last_login;

    /**
     * The timestamp of when the admin user was last updated.
     *
     * @var string|null
     */
    public ?string $updated_at;

    /**
     * The timestamp of when the admin user was created.
     *
     * @var string|null
     */
    public ?string $created_at;

    /**
     * The relations of the model.
     *
     * @return array
     */
    protected function orm(): array
    {
        return [
            'role' => ['has' => 'one', 'model' => AdminRole::class],
            'logs' => ['has' => 'many', 'model' => AdminActivityLog::class],
        ];
    }

    /**
     * Returns an array of uploaders that can be used to upload files to the admin user's profile.
     *
     * The first element of the array is an array with the following keys:
     *
     * - `name`: The name of the field that the file will be uploaded to.
     * - `uploadTo`: The directory where the file will be uploaded.
     * - `extensions`: An array of allowed file extensions.
     * - `compress`: The level of compression to apply to the uploaded image (0-100).
     * - `resize`: An associative array with the keys being the width and height of the resized image, and the values being the width and height of the resized image.
     *
     * @return array
     */
    protected function uploader(): array
    {
        return [
            [
                'name' => 'image',
                'uploadTo' => '/admin/profile',
                'extensions' => ['jpg', 'jpeg', 'png'],
                'compress' => 60,
                'resize' => [120 => 120],
            ]
        ];
    }

    /**
     * Returns an array of fields that can be used to generate a form for the admin user model.
     *
     * The form fields are:
     *
     * - `created_at`: A hidden field that stores the timestamp of when the admin user was created.
     * - `updated_at`: A hidden field that stores the timestamp of when the admin user was last updated.
     * - `last_login`: A hidden field that stores the timestamp of when the admin user was last logged in.
     * - `admin_roles_id`: A dropdown field that stores the ID of the admin role that the admin user belongs to.
     * - `status`: A dropdown field that stores the status of the admin user (active or inactive).
     * - `password`: A password field that stores the password of the admin user.
     * - `email`: An email field that stores the email address of the admin user.
     *
     * @return array An array of fields that can be used to generate a form for the admin user model.
     */
    public function form(): array
    {
        return [
            'created_at' => ['ignore' => true],
            'updated_at' => ['ignore' => true],
            'last_login' => ['ignore' => true],
            'admin_roles_id' => ['placeholder' => '---', 'type' => 'select'],
            'status' => [
                'type' => 'select',
                'options' => ['active' => __('active'), 'inactive' => __('inactive')],
                'placeholder' => false
            ],
            'password' => ['type' => 'password'],
            'email' => ['type' => 'email'],
        ];
    }

    /**
     * Returns a string representation of the admin user model.
     *
     * If the admin user has a full name, it will be returned as the string representation.
     * Otherwise, the username will be returned.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->full_name ?? $this->username;
    }
}