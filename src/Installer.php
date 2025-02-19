<?php

namespace Backpack;

use Hyper\Request;

/**
 * Class Installer
 *
 * Manages the installation process for Backpack.
 *
 * This class is responsible for displaying the installation steps and
 * handling the user's input.
 *
 * @package Backpack
 */
class Installer
{
    /**
     * Constructor for the Installer class.
     *
     * @param array $steps An array of steps for the installation process.
     */
    public function __construct(private array $steps)
    {
    }

    /**
     * Create a new instance of the Installer class.
     *
     * @param array $steps An array of steps for the installation process.
     * 
     * @return self A new instance of the Installer class.
     */
    public static function make(array $steps): self
    {
        return new static($steps);
    }

    /**
     * Run the installation process.
     *
     * @param \Hyper\Request $request The Hyper request object.
     *
     * @return void
     */
    public function install(Request $request): void
    {
        // Check if the request is a POST request and
        // if the __installer_step_callback is set
        if ($request->isMethod('post') && $request->post('__installer_step_callback') >= 1) {
            // Get the step number
            $step = intval($request->post('__installer_step_callback')) - 1;

            // Get the callback for the step
            $callback = ($this->steps[$step] ?? [])['callback'] ?? null;

            // If the callback is set and is callable
            if ($callback !== null) {
                // Call the callback
                $resp = container()->call($callback);
            } else {
                // If the callback is not set, return an error
                $resp = ['error' => __('Installer step not found')];
            }

            // Send the json response
            response()->json($resp)->send();
            exit; // Exit the script
        }

        // Render the installer template
        echo backpack_template('installer', ['steps' => $this->steps])
            ->send();
        exit; // Exit the script
    }
}