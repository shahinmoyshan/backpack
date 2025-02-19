<?php

namespace Backpack;

use Exception;

/**
 * Class that represents the Backpack panel.
 *
 * This class is a central place to store data about the Backpack panel.
 *
 * @package backpack
 */
class Panel
{
    /**
     * The configuration array for the Backpack panel.
     *
     * @var array
     */
    private array $config;

    /**
     * The menu array for the Backpack panel.
     *
     * @var array
     */
    private array $menu;

    /**
     * The state array for the Backpack panel.
     *
     * @var array
     */
    private array $state;

    /**
     * Constructor.
     *
     * Initializes the Panel instance with the given configuration paths.
     *
     * @param array $setup Optional array of configuration paths to use.
     */
    public function __construct(private array $setup = [])
    {
    }

    /**
     * Get a path from the panel config paths array.
     *
     * @param string $key The key of the path to retrieve.
     *
     * @return string The path from the config paths array.
     *
     * @throws Exception If the path is not set in the config paths array.
     */
    public function getPath(string $key): string
    {
        if (!isset($this->setup['path'][$key])) {
            throw new Exception("Panel config path ({$key}), is not set");
        }

        return $this->setup['path'][$key];
    }

    /**
     * Get the menu array for the Backpack panel.
     *
     * @return array The menu array.
     */
    public function getMenu(): array
    {
        return $this->menu ??= require $this->getPath('menu');
    }

    /**
     * Set the menu array for the Backpack panel.
     *
     * @param array $menu The menu array.
     *
     * @return void
     */
    public function setMenu(array $menu): void
    {
        $this->menu = $menu;
    }

    /**
     * Add an item to the top level menu.
     *
     * @param string $route The route to use for the menu item.
     * @param array $item The menu item to add.
     *
     * @return $this
     */
    public function addMenuItem(string $route, array $item): self
    {
        $this->menu[$route] = $item;

        return $this;
    }

    /**
     * Set the submenu of a given top level menu item.
     *
     * @param string $parentRoute The route of the parent menu item.
     * @param array $submenu The submenu to set.
     *
     * @return $this
     */
    public function setSubmenuItem(string $parentRoute, array $submenu): self
    {
        $this->menu[$parentRoute]['submenu'] = $submenu;

        return $this;
    }

    /**
     * Add an item to the submenu of a given top level menu item.
     *
     * @param string $parentRoute The route of the parent menu item.
     * @param string $route The route to use for the menu item.
     * @param array $item The menu item to add.
     *
     * @return $this
     */
    public function addSubmenuItem(string $parentRoute, string $route, array $item): self
    {
        $this->menu[$parentRoute]['submenu'][$route] = $item;

        return $this;
    }

    /**
     * Set the configuration array for the Backpack panel.
     *
     * @param array $config The configuration array to use.
     *
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Check if a value is set in the panel state.
     *
     * @param string $key The key of the value to check.
     *
     * @return bool True if the value is set, false otherwise.
     */
    public function hasData(string $key): bool
    {
        return isset($this->state[$key]) && !empty($this->state[$key]);
    }

    /**
     * Set a value in the panel state.
     *
     * @param string $key The key under which the value will be stored.
     * @param mixed $value The value to store in the panel state.
     *
     * @return void
     */
    public function setData(string $key, mixed $value): void
    {
        $this->state[$key] = $value;
    }

    /**
     * Add a value to the panel state, merging it with the existing value if it already exists.
     *
     * @param string $key The key of the state value to add to.
     * @param array $value The value to add to the panel state.
     *
     * @return void
     */
    public function addData(string $key, mixed $value): void
    {
        $this->state[$key] = array_merge($this->state[$key] ?? [], [$value]);
    }

    /**
     * Retrieve a value from the panel state by key.
     *
     * @param string $key The key of the state value to retrieve.
     * @param array $default The default value to return if the key does not exist.
     *
     * @return array The state value associated with the key, or the default value if not found.
     */

    public function getData(string $key, $default = []): array
    {
        return $this->state[$key] ?? $default;
    }

    /**
     * Get a configuration value from the configuration array.
     *
     * @param string $key The key of the configuration value to retrieve.
     * @param mixed $default The default value to return if the key is not found.
     *
     * @return mixed The configuration value.
     */
    public function getConfig(string $key, $default = null): mixed
    {
        if (!isset($this->config)) {
            $this->config = require $this->getPath('config');
        }

        // If the key is '*', return the entire config array
        if ($key === '*') {
            return $this->config;
        }

        return $this->config[$key] ?? $default;
    }
}
