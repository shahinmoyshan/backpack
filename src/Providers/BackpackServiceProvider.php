<?php

namespace Backpack\Providers;

use Backpack\Bread;
use Backpack\Form;
use Backpack\Lib\Mailer;
use Backpack\Lib\PrettyTime;
use Backpack\Lib\Settings;
use Backpack\Middlewares\GuestMiddleware;
use Backpack\Middlewares\LoggedInMiddleware;
use Backpack\Models\AdminUser;
use Backpack\OptionsManager;
use Hyper\Container;
use Hyper\Middleware;
use Hyper\Utils\Auth;
use Hyper\Utils\Session;

/**
 * This class is responsible for registering the services provided by the
 * Backpack package. It will register the services that are required by the
 * Backpack package, like the Mailer and the Form classes.
 * 
 * @package Backpack
 */
class BackpackServiceProvider
{
    /**
     * This method is responsible for registering the services provided by the
     * Backpack package.
     *
     * @param Container $container The container that will hold the registered
     * services.
     */
    public function register(Container $container)
    {
        /**
         * Register the Auth service. This service is responsible for
         * authenticating the user and for the user session.
         */
        $container->singleton(Auth::class, function (Container $container) {
            return new Auth(
                session: $container->get(Session::class),
                userModel: AdminUser::class
            );
        });

        /**
         * Register the Settings service. This service is responsible for
         * managing the application settings.
         */
        $container->singleton(Settings::class);

        /** 
         * Register the PrettyTime service. This service is responsible for
         * formatting time differences into a "pretty" format.
         */
        $container->singleton(PrettyTime::class, function () {
            return new PrettyTime([
                'moments_ago' => __('Moments ago'),
                'seconds_from_now' => __('Seconds from now'),
                'minute' => __('minute'),
                'hour' => __('hour'),
                'day' => __('day'),
                'week' => __('week'),
                'month' => __('month'),
                'year' => __('year'),
                'yesterday' => __('Yesterday'),
                'tomorrow' => __('Tomorrow'),
                'ago' => __('ago'),
                'in' => __('In'),
            ]);
        });

        /**
         * Register the Mailer service. This service is responsible for sending
         * the e-mail to the user.
         */
        $container->bind(Mailer::class);

        /**
         * Register the Form service. This service is responsible for handling
         * the form data.
         */
        $container->bind(Form::class);

        /**
         * Register the Bread service. This service is responsible for handling
         * Model CRUD operations.
         */
        $container->bind(Bread::class);

        /**
         * Register the OptionsManager service. This service is responsible for
         * handling the options settings or CMS.
         */
        $container->bind(OptionsManager::class);
    }

    /**
     * This method is responsible for registering the middleware. Middleware is a way
     * to filter the incoming HTTP requests entering the admin panel.
     *
     * @param Container $container
     *   The container that will hold the registered services.
     *
     * @return void
     */
    public function boot(Container $container)
    {
        /**
         * Register the middleware. Middleware is a way to filter the
         * incoming HTTP requests entering the admin panel.
         */
        $container->get(Middleware::class)
            ->register('logged', LoggedInMiddleware::class)
            ->register('guest', GuestMiddleware::class);
    }
}
