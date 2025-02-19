<?php

namespace Backpack\Middlewares;

use Hyper\Utils\Auth;

/**
 * Class LoggedInMiddleware
 *
 * Checks if the current user is logged in. If the user is a guest, it
 * redirects him to the default route for guests.
 */
class LoggedInMiddleware
{
    /**
     * @var Auth
     */
    private Auth $auth;

    /**
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handles the middleware logic.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->auth->isGuest()) {
            redirect($this->auth->getGuestRoute());
        }
    }
}
