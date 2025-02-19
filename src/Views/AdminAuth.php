<?php

namespace Backpack\Views;

use Backpack\Lib\Mailer;
use Backpack\Models\AdminActivityLog;
use Backpack\Models\AdminUser;
use Backpack\Models\ResetLink;
use Exception;
use Hyper\Request;
use Hyper\Response;
use Hyper\Template;
use Hyper\Utils\Auth;
use Hyper\Utils\Hash;

/**
 * Class AdminAuth
 *
 * Handles the login, logout, and password reset for the admin users.
 *
 * @package Backpack\Views
 */
class AdminAuth
{
    /**
     * Login form for the admin user.
     *
     * This function will render the login form if the request method is GET.
     * If the request method is POST, it will validate the form data and
     * check if the user exists with the given username or email. If the user
     * exists and the password is correct, it will log the user in and
     * redirect to the admin homepage.
     *
     * @param Request $request The incoming request object.
     * @param Response $response The response object to modify if necessary.
     * @param Hash $hash The hashing utility class.
     * @param Auth $auth The authentication manager class.
     */
    public function login(Request $request, Response $response, Hash $hash, Auth $auth)
    {
        $error = null;

        if ($request->getMethod() === 'POST') {
            try {
                // Validate the login form inputs.
                $input = validator(rules: [
                    'user' => ['required', 'min:3', 'max:100'],
                    'password' => ['required', 'min:6', 'max:100'],
                ], data: $request->all(['user', 'password']));

                // Check if the user exists with the given username or email.
                $user = AdminUser::limit(1)
                    ->where(['username' => $input->text(key: 'user', stripTags: true)])
                    ->orWhere(['email' => $input->email(key: 'user')])
                    ->first();

                if ($user !== false) {
                    // Check if the user is active.
                    if ($user->status === 'inactive') {
                        $error = __('this admin user is inactive');
                    } elseif ($hash->validatePassword($input->text(key: 'password', stripTags: true), $user->password) === false) {
                        // Check if the password is correct.
                        $error = __('incorrect password, please try again');
                    } else {
                        // Update the user's last login time.
                        $user->last_login = date('Y-m-d H:i:s');
                        $user->save();

                        // Log the user in.
                        $auth->login($user);

                        // If the request accepts JSON, return a JSON response.
                        if ($request->accept('application/json')) {
                            return $response->json(['redirect' => $auth->getLoggedInRoute()]);
                        }

                        // Redirect to the admin homepage.
                        $response->redirect($auth->getLoggedInRoute());
                    }
                } else {
                    // User does not exist with the given username or email.
                    $error = __('admin does not exist with this username or email');
                }
            } catch (Exception $e) {
                // Handle any errors that may occur.
                $error = $e->getMessage();
            }
        }

        // Render the login page
        return backpack_template('auth/login', ['error' => $error]);
    }

    /**
     * Log the admin user out and redirect to the login page.
     *
     * This function will delete the "admin_user_logged_id" session variable
     * and erase the cache for the logged in user. It will then redirect the
     * user to the login page.
     *
     * @param Response $response The response object to modify if necessary.
     * @param Request $request The incoming request object.
     */
    public function logout(Request $request, Response $response, Auth $auth)
    {
        // Delete the "admin_user_logged_id" session variable.
        $auth->logout();

        // If the request accepts JSON, return a JSON response.
        if ($request->accept('application/json')) {
            return $response->json(['redirect' => $auth->getGuestRoute()]);
        }

        // Redirect to the login page.
        $response->redirect($auth->getGuestRoute());
    }

    /**
     * Handle the password reset request for admin users.
     *
     * This method processes both GET and POST requests. For GET requests,
     * it checks for a 'success' query parameter to display a success message
     * if the password reset link has been sent. For POST requests, it validates
     * the input, checks if the user exists and is active, generates a password
     * reset token, and sends a reset link to the user's email. If any errors occur,
     * they are caught and set in the error variable.
     *
     * @param Request $request The request object containing query and POST data.
     * @param Response $response The response object for handling redirections.
     * @param Mailer $mailer The mailer object used for sending emails.
     */
    public function forget(Request $request, Response $response, Mailer $mailer)
    {
        $error = null;
        $success = null;

        if ($request->query('success') === '1') {
            $success = __('password reset link has been sent to your email');
        }

        if ($request->getMethod() === 'POST') {
            try {
                // Validate the login form inputs.
                $input = validator(rules: [
                    'user' => ['required', 'min:3', 'max:100'],
                ], data: $request->all(['user']));

                // Check if the user exists with the given username or email.
                $user = AdminUser::limit(1)
                    ->where(['username' => $input->text(key: 'user', stripTags: true)])
                    ->orWhere(['email' => $input->email(key: 'user')])
                    ->first();

                if ($user !== false) {
                    // Check if the user is active.
                    if ($user->status === 'inactive') {
                        $error = __('this admin user is inactive');
                    } else {

                        // Generate a random 32 bytes token for the password reset link.
                        $token = bin2hex(random_bytes(32));

                        // Prepare the email configuration.
                        $config = [
                            'email' => $user->email,
                            'name' => $user->full_name ?? $user->username,
                            'subject' => __('reset your admin user password'),
                            'link' => route_url('admin.auth.reset') . '?token=' . $token,
                        ];

                        // Use the "reset-password-link" email template to send the password reset link.
                        $mailer->content(
                            get(Template::class)
                                ->setPath(backpack_templates_dir())
                                ->render('mail/reset-password-link', $config),
                            true
                        );

                        // Send the password reset link to the user's email.
                        if (
                            $mailer->send($config) && ResetLink::insert([
                                'target_id' => $user->id,
                                'target_type' => 'admin',
                                'token' => $token,
                                'created_at' => date('Y-m-d H:i:s'),
                            ])
                        ) {
                            $response->redirect(route_url('admin.auth.forget') . '?success=1');
                        } else {
                            $error = __('failed to send password reset link to your email, please try again later');
                        }
                    }
                } else {
                    // User does not exist with the given username or email.
                    $error = __('admin does not exist with this username or email');
                }
            } catch (Exception $e) {
                // Handle any errors that may occur.
                $error = $e->getMessage();
            }
        }

        // Render the "forget" page.
        return backpack_template('auth/forget', ['error' => $error, 'success' => $success]);
    }


    /**
     * Handles the password reset process for admin users.
     *
     * This function validates the password reset token and form inputs, updates the
     * admin user's password, logs the action, and redirects to the login page with
     * a success message if successful.
     *
     * @param Request $request The request object containing input data.
     * @param Response $response The response object to modify if necessary.
     * @param Hash $hash The hashing utility class.
     */
    public function reset(Request $request, Response $response, Hash $hash)
    {
        $error = null;
        $success = null;

        // Check if a success message should be displayed.
        if ($request->query('success') === '1') {
            $success = __('password reset successfully');
        }

        // Retrieve and sanitize the token from the request.
        $token = filter_var(trim(
            $request->query(
                'token',
                $request->post('token', '')
            )
        ), FILTER_UNSAFE_RAW);

        // Validate the token and check if it has expired.
        if (!empty($token)) {
            $token = ResetLink::where(['token' => $token, 'target_type' => 'admin'])->limit(1)->last();
            if (
                $token === false ||
                time() - strtotime($token->created_at) > 86400
            ) {
                // Remove the expired token from the database.
                if (isset($token->id)) {
                    $token->remove();
                }
                $token = null;
            }
        } else {
            $token = null;
        }

        // Process the password reset if the token is valid and the request is POST.
        if (isset($token) && isset($token->id) && $request->getMethod() === 'POST') {
            try {
                // Validate the password reset form inputs.
                $input = validator(rules: [
                    'password' => ['required', 'min:6', 'max:100'],
                    'confirm_password' => ['required', 'equal:password']
                ], data: $request->all(['password', 'confirm_password']));

                // Update the admin user's password.
                if (
                    AdminUser::update([
                        'password' => $hash->hashPassword(
                            $input->text('password')
                        )
                    ], ['id' => $token->target_id])
                ) {
                    // Remove the reset link from the database.
                    ResetLink::where(['target_id' => $token->target_id, 'target_type' => 'admin'])->delete();

                    // Log the password reset activity.
                    AdminActivityLog::insert([
                        'admin_users_id' => $token->target_id,
                        'target_type' => 'password_reset',
                        'action' => 'password reset successfully',
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    // Redirect to the login page with a success message.
                    $response->redirect(route_url('admin.auth.reset') . '?success=1');
                } else {
                    $error = __('failed to reset password, please try again later');
                }
            } catch (Exception $e) {
                // Handle any errors that may occur during the reset process.
                $error = $e->getMessage();
            }
        }

        // Render the reset password template with error/success messages.
        return backpack_template('auth/reset', [
            'error' => $error,
            'success' => $success,
            'token' => $token
        ]);
    }
}