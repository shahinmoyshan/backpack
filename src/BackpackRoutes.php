<?php

namespace Backpack;

use Backpack\Views\AdminAuth;
use Backpack\Views\AdminProfile;
use Hyper\Router;

/**
 * A class to handle routes for the Backpack web component.
 *
 * This class registers all of the routes for the Backpack web component using
 * the Hyper router. It provides a simple and easy-to-use interface for adding
 * routes to the system.
 *
 * It is a good idea to keep all of the routes for the system in a single file
 * like this one, so that they can be easily viewed and edited.
 * 
 * @package Backpack
 */
class BackpackRoutes
{
    /**
     * Returns an array of routes for the admin profile page.
     *
     * The routes returned by this method are:
     *
     * - /admin/profile (GET, POST): The admin profile page.
     * - /admin/profile/picture (POST): Uploads a new profile picture.
     *
     * @param string $prefix The prefix for the routes.
     *
     * @return array An array of routes.
     */
    public static function profileRoutes(string $prefix = '/admin/'): array
    {
        return [
            ['path' => "{$prefix}profile", 'method' => ['GET', 'POST'], 'callback' => [AdminProfile::class, 'profile'], 'name' => 'admin.profile', 'middleware' => 'logged'],
            ['path' => "{$prefix}profile/picture", 'method' => 'POST', 'callback' => [AdminProfile::class, 'profilePicture'], 'name' => 'admin.profile.picture', 'middleware' => 'logged'],
        ];
    }

    /**
     * Returns an array of routes for admin authentication.
     *
     * These routes are responsible for handling the login, password reset, and logout
     * functionality for the admin users.
     *
     * @param string $prefix The prefix for the routes.
     *
     * @return array An array of routes for admin authentication.
     */
    public static function authRoutes(string $prefix = '/admin/'): array
    {
        return [
            ['path' => "{$prefix}login", 'method' => ['GET', 'POST'], 'callback' => [AdminAuth::class, 'login'], 'name' => 'admin.auth.login', 'middleware' => 'guest'],
            ['path' => "{$prefix}forget", 'method' => ['GET', 'POST'], 'callback' => [AdminAuth::class, 'forget'], 'name' => 'admin.auth.forget', 'middleware' => 'guest'],
            ['path' => "{$prefix}reset", 'method' => ['GET', 'POST'], 'callback' => [AdminAuth::class, 'reset'], 'name' => 'admin.auth.reset', 'middleware' => 'guest'],
            ['path' => "{$prefix}logout", 'method' => 'POST', 'callback' => [AdminAuth::class, 'logout'], 'name' => 'admin.auth.logout', 'middleware' => 'logged'],
        ];
    }

    /**
     * Returns an array of all routes for the Backpack web component.
     *
     * This method merges the routes returned by {@see profileRoutes()} and
     * {@see authRoutes()} using the specified prefix.
     *
     * @param string $prefix The prefix for the routes.
     *
     * @return array An array of all routes for the Backpack web component.
     */
    public static function all(string $prefix = '/admin/'): array
    {
        return array_merge(self::profileRoutes($prefix), self::authRoutes($prefix));
    }

    /**
     * Registers all routes for the Backpack web component.
     *
     * This method uses the hyper router to add all routes returned by the
     * {@see all()} method.
     *
     * @param string $prefix The prefix for the routes.
     */
    public static function register(string $prefix = '/admin/'): void
    {
        foreach (self::all($prefix) as $route) {
            get(Router::class)->add(...$route);
        }
    }
}