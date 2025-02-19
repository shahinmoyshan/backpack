<?php

namespace Backpack\Lib;

use Backpack\Models\Option;
use Hyper\Utils\Collect;
use Throwable;

/**
 * Class Settings
 * 
 * Manages the settings by loading options from the database and providing
 * methods to access them. Utilizes the 'Collect' utility for handling collections.
 *
 * @package Backpack\Lib
 */
class Settings
{
    /** @var Collect $options Stores the preloaded options as a collection */
    private Collect $options;

    /**
     * Settings constructor.
     * 
     * Initializes the settings by loading preloaded options from the database
     * and mapping them into a collection.
     */
    public function __construct()
    {
        $this->reload();
    }

    /**
     * Reloads the preloaded options from the database and updates the cache.
     *
     * @return void
     */
    public function reload(): void
    {
        try {
            $options = cache()
                ->load(
                    'options:preload',
                    fn() => Option::where(['preload' => true])->result(),
                    '30 minutes'
                );
        } catch (Throwable $e) {
            $options = [];
        }

        $this->options = collect($options)
            ->mapK(fn($option) => [$option->name => $option->value]);
    }

    /**
     * Get an option by key.
     *
     * @param string $key The key of the option to retrieve.
     * @param mixed $default The default value to return if the key doesn't exist.
     * @return mixed The value of the option, or the default value.
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->options->get($key, $default);
    }

    /**
     * Get a CMS option by ID and key.
     *
     * @param string $id The ID of the CMS page to get the option for.
     * @param string $key The key of the option to get.
     * @param mixed $default The default value to return if the option doesn't exist.
     *
     * @return mixed The value of the option, or the default value.
     */
    public function getCms(string $id, string $key, $default = null)
    {
        $key = "cms_{$id}_$key";

        // If the option is not in the collection, try to load it from the database
        if (!$this->options->has($key)) {
            $this->loadCms($id);
        }

        // Return the option value, or the default value if it doesn't exist.
        return $this->get($key, $default);
    }

    /**
     * Check if an option exists by key.
     *
     * @param string $key The key of the option to check.
     * @return bool True if the option exists, false otherwise.
     */
    public function has($key): bool
    {
        // If the option is not in the collection, try to load it from the database
        if (!$this->options->has($key)) {
            $this->load([$key]);
        }

        // Return true if the option exists, false otherwise
        return $this->options->has($key);
    }

    /**
     * Load options from the database by name.
     *
     * @param array $names An array of option names to load.
     * @return void
     */
    public function load(array $names = [])
    {
        // Check if names is empty
        if (empty($names)) {
            return;
        }

        try {
            $options = Option::where(['name' => $names])->result();
        } catch (Throwable $e) {
            $options = [];
        }

        // Load the options from the database
        foreach ($options as $option) {
            // Add them to the collection
            $this->options->add($option->name, $option->value);
        }
    }

    /**
     * Load options from the database for a CMS page by ID.
     *
     * @param string $id The ID of the CMS page to load options for.
     * @return void
     */
    public function loadCms(string $id): void
    {
        // Load the cms options from the cache or the database
        try {
            $options = cache('cms')
                ->load(
                    $id,
                    fn() => Option::where("name like 'cms_{$id}_%'")->result(),
                    '15 minutes'
                );
        } catch (Throwable $e) {
            $options = [];
        }

        // Load the cms options from the database
        foreach ($options as $option) {
            // Add them to the collection
            $this->options->add($option->name, $option->value);
        }
    }

    /**
     * Get the options collection.
     * 
     * @return Collect The collection of options.
     */
    public function getCollect(): Collect
    {
        return $this->options;
    }

    /**
     * Get all options.
     *
     * @return array An array of all options.
     */
    public function all()
    {
        return $this->options->all();
    }
}
