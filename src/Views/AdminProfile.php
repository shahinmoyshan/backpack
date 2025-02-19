<?php

namespace Backpack\Views;

use Exception;
use Hyper\Request;
use Hyper\Response;
use Hyper\Utils\Hash;

/**
 * Class AdminProfile
 *
 * This class handles the operations related to the admin user's profile.
 * It provides methods for updating profile details, uploading profile pictures,
 * and other related functionality.
 * 
 * @package Backpack\Views
 */
class AdminProfile
{
    /**
     * Handles the profile update form submission.
     *
     * This method will validate the form inputs, update the admin user's
     * profile, log the action, and redirect to the profile page with a
     * success message if successful.
     *
     * @param Request $request The request object containing input data.
     * @param Response $response The response object to modify if necessary.
     * @param Hash $hash The hashing utility class.
     */
    public function profile(Request $request, Response $response, Hash $hash)
    {
        if ($request->getMethod() === 'POST') {
            try {
                // validate the form inputs
                $input = validator(rules: [
                    'full_name' => ['required', 'min:3', 'max:100'],
                    'email' => ['required', 'email'],
                    'old_password' => ['min:6', 'max:100'],
                    'new_password' => ['min:6', 'max:100'],
                    'confirm_password' => ['equal:new_password'],
                ], data: $request->all(['full_name', 'email', 'old_password', 'new_password', 'confirm_password']));

                // get the admin user
                $user = auth()->getUser();

                // update the admin user's profile
                $user->full_name = $input->text(key: 'full_name', stripTags: true);
                $user->email = $input->email(key: 'email');

                // update the admin user's password if the old password is correct
                if (!empty($input->text(key: 'new_password', stripTags: true))) {
                    if (!$hash->validatePassword($input->text(key: 'old_password', stripTags: true), $user->password)) {
                        throw new Exception(__('old password is incorrect'));
                    }

                    $user->password = $hash->hashPassword($input->text(key: 'new_password', stripTags: true));
                }

                // save the admin user
                if ($user->save()) {
                    // clear the cached admin user
                    auth()->clearCache();
                    // set success message
                    session()->set('success_message', __('profile updated successfully'));
                } else {
                    // set error message
                    session()->set('warning_message', __('nothing to update'));
                }
            } catch (Exception $e) {
                // set error message
                session()->set('error_message', $e->getMessage());
            }

            // redirect to profile
            $response->redirect(route_url('admin.profile'));
        }

        // return the profile template
        return backpack_template('admin/profile');
    }

    /**
     * Handles the profile picture upload for the admin user.
     *
     * This method receives the profile picture file from the request, updates the
     * admin user's profile picture, and redirects to the profile page with a
     * success or error message.
     *
     * @param Request $request The request object containing the image file.
     * @param Response $response The response object to modify if necessary.
     */
    public function profilePicture(Request $request, Response $response)
    {
        try {
            // get the admin user
            $user = auth()->getUser();

            // set the profile picture
            $user->image = $request->file('image');

            // save the admin user
            if ($user->save()) {
                // clear the cached admin user
                auth()->clearCache();
                // set success message
                session()->set('success_message', __('profile picture uploaded'));
            } else {
                session()->set('error_message', __('failed to upload profile picture'));
            }
        } catch (Exception $e) {
            session()->set('error_message', $e->getMessage());
        }

        // redirect to profile
        $response->redirect(route_url('admin.profile'));
    }
}