<?php

namespace Backpack\Middlewares;

use Hyper\Utils\Auth;

/**
 * GuestMiddleware
 *
 * Checks if the current user is a guest. If the user is not a guest, it
 * redirects him to the default route for logged-in users.
 */
class GuestMiddleware
{
    /**
     * @var Auth
     */
    public function __construct(private Auth $auth)
    {
    }

    /**
     * Handles the middleware logic.
     *
     * @return void
     */
    public function handle()
    {
        // If the user is not a guest, redirect him to the default route for
        // logged-in users.
        if (!$this->auth->isGuest()) {
            redirect($this->auth->getLoggedInRoute());
        }
    }
}
