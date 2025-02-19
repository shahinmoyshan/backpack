<?php

namespace Backpack;

use Backpack\Models\Option;
use Hyper\Request;
use Hyper\Response;

/**
 * Class MenuManager
 *
 * This class provides methods for managing menus in the site.
 *
 * @package Backpack
 */
class MenuManager
{
    /**
     * Configuration array for the menu manager.
     *
     * @var array
     */
    private array $config = [];

    /**
     * List of menu item providers.
     *
     * @var array
     */
    private array $menuItemProviders = [];

    /**
     * Cache of the menu items provided by the menu item providers.
     *
     * @var array
     */
    private array $cachedProviderItems = [];

    /**
     * List of URL providers for the menu.
     *
     * @var array
     */
    private array $menuUrlProviders = [];

    /**
     * Configure the menu manager with the given settings.
     *
     * @param array $config
     */
    public function configure(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Returns the current location.
     *
     * @return string
     */
    public function getLocation(): string
    {
        return $this->config['location'] ?? 'primary';
    }

    /**
     * Returns the list of available menu locations.
     *
     * @return array
     */
    public function getLocations(): array
    {
        return $this->config['locations'] ?? [];
    }

    /**
     * Retrieve a configuration value by its key.
     *
     * @param string $key The key for the configuration value.
     * @param mixed|null $default The default value to return if the key is not found.
     * @return mixed The configuration value, or the default value if the key doesn't exist.
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Handles the rendering of the menu index page.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return Response
     */
    public function renderIndex(Request $request, Response $response): Response
    {
        // If the request method is POST, save the menu items
        if ($request->getMethod() === 'POST') {
            $location = $this->getLocations()[$this->getLocation()] ?? $this->getLocation();

            // Save the menu items
            if (
                $this->saveMenuItems(
                    $this->getLocation(),
                    (array) json_decode(
                        $request->post('__menuItems', ''),
                        true
                    )
                )
            ) {
                // If the menu items were saved successfully, flush the cache and set a success session message
                cache('cms')->erase("menu:{$this->getLocation()}");
                session()->set('success_message', __('menu (%s) saved successfully', $location));
            } else {
                // If the menu items were not modified, set a warning session message
                session()->set('warning_message', __('nothing to save %s', $location));
            }

            // Redirect the user to the same page, using the route defined in the manager config
            $response->redirect(route_url(
                $this->getConfig('route'),
                ['location' => $this->getLocation()]
            ));
        }

        // If the request method is not POST, render the menu index template
        return backpack_template('cms/menu', ['manager' => $this]);
    }

    /**
     * Retrieves the menu items for the given location.
     *
     * @param  string  $location The location of the menu.
     * @return array The menu items for the given location.
     */
    public function getMenuItems(string $location): array
    {
        // Retrieve the menu items for the given location from the database
        // as a JSON-decoded array.
        return (array) json_decode(
            // Select the 'value' column from the 'options' table where the 'name'
            // column matches the pattern "cms_menu_<location>".
            Option::select('value')
                ->where(['name' => "cms_menu_{$location}"])
                // Fetch the result as a scalar value.
                ->fetch(\PDO::FETCH_COLUMN)
                // Return the first element of the result (there should only be one).
                ->first(),
            // Decode the JSON string to an associative array.
            true
        );
    }

    /**
     * Adds a custom menu URL provider.
     *
     * The callback function receives a single argument, an associative array
     * containing the menu item properties, and should return a URL string.
     *
     * @param string $id The unique identifier for the provider.
     * @param callable $callback The callback function to generate the URL.
     * @return void
     */
    public function addMenuUrlProvider(string $id, callable $callback): void
    {
        $this->menuUrlProviders[$id] = $callback;
    }

    /**
     * Adds a custom menu item provider.
     *
     * The callback function receives no arguments and should return an array of
     * associative arrays containing the menu item properties.
     *
     * @param string $id The unique identifier for the provider.
     * @param string $label The label for the provider.
     * @param callable $provider The callback function to generate the menu items.
     * @return void
     */
    public function addMenuItemProvider(string $id, string $label, callable $provider): void
    {
        $this->menuItemProviders[$id] = compact(['id', 'label', 'provider']);
    }

    /**
     * Retrieves all registered menu item providers.
     *
     * @return array An array of menu item providers, each being an associative array
     *               containing 'id', 'label', and 'provider'.
     */
    public function getMenuItemProviders(): array
    {
        return $this->menuItemProviders ?? [];
    }

    /**
     * Retrieves the menu items for the given provider.
     *
     * @param string $id The unique identifier for the provider.
     * @return array An array of associative arrays containing the menu item properties.
     */
    public function getProviderItems(string $id): array
    {
        return $this->cachedProviderItems[$id] ??= call_user_func($this->menuItemProviders[$id]['provider']);
    }

    /**
     * Retrieves menu items for a specific location, syncing titles with providers if applicable.
     *
     * This method fetches menu items for the specified location and attempts to sync the titles
     * with those provided by registered menu item providers. If a menu item does not have a matching
     * item in its provider, it is removed from the list.
     *
     * @param string $location The location identifier for which menu items are retrieved.
     * @return array The array of synchronized menu items, with titles updated if applicable.
     */
    public function getSyncedMenuItems(string $location): array
    {
        // Retrieve menu items and providers for the given location
        $menuItems = $this->getMenuItems($location);
        $providers = $this->getMenuItemProviders();

        // If there are providers and menu items, attempt to sync titles
        if (!empty($providers) && !empty($menuItems)) {
            foreach ($menuItems as &$menuItem) {
                foreach ($providers as $provider) {
                    // Check if the provider ID matches the menu item's type
                    if ($provider['id'] === $menuItem['type']) {
                        // Find the item in the provider's list that matches the menu item's value
                        $item = collect($this->getProviderItems($provider['id']))
                            ->find(fn($i) => $i['id'] == $menuItem['value']);

                        if ($item) {
                            // Update the menu item's title with the provider item's title
                            $menuItem['title'] = $item['title'];
                        } else {
                            // Remove menu item if no matching provider item is found
                            $menuItem = null;
                        }
                        break;
                    }
                }
            }

            // Remove any null entries from the menu items
            $menuItems = array_filter($menuItems);
        }

        // Return the array of synchronized menu items
        return array_values($menuItems);
    }

    /**
     * Saves the menu items for the given location in the database.
     *
     * @param string $location The location identifier for which menu items are saved.
     * @param array $items The array of menu items to be saved.
     * @return bool True if the menu items were saved successfully, false otherwise.
     */
    public function saveMenuItems(string $location, array $items): bool
    {
        // Insert or update the menu items in the options table
        return Option::insert(
            [
                'name' => "cms_menu_{$location}", // Use a unique name for each location
                'value' => collect($items)
                    ->multiSort('order') // Sort the items by their order
                    ->toJson(), // Convert the items to a JSON string
                'preload' => false, // Don't preload the menu items
            ],
            ['conflict' => ['name' => 'name'], 'update' => ['value' => 'value']] // Update the existing entry if it exists
        );
    }

    /**
     * Retrieves the menu items for the given menu identifier.
     *
     * This method uses caching to speed up the retrieval of menu items.
     * If the menu items are not found in the cache, it will fetch them from the database
     * and store them in the cache for 30 minutes.
     *
     * @param string $id The menu identifier.
     * @return array The menu items for the given menu identifier.
     */
    public function getMenu(string $id): array
    {
        return cache('cms')
            ->load(
                "menu:$id",
                function () use ($id) {
                    // Fetch the menu items from the database
                    $menuCollection = collect(
                        $this->getSyncedMenuItems($id)
                    );

                    // If the menu items are empty, return an empty array
                    if ($menuCollection->count() === 0) {
                        return [];
                    }

                    // Parse the menu items to convert the URLs to absolute URLs
                    $parsedMenuItems = $menuCollection->where('type', 'custom')
                        ->map(
                            function ($item) {
                        // Convert the URL to an absolute URL if it's not already absolute
                        $item['url'] = strpos($item['value'], '://') === false ? url($item['value']) : $item['value'];
                        return $item;
                    }
                        );

                    // Loop through the menu item providers and add their items to the parsed menu items
                    foreach (array_keys($this->getMenuItemProviders()) as $provider) {
                        if (isset($this->menuUrlProviders[$provider])) {
                            $providerItems = $menuCollection->where('type', $provider);
                            if ($providerItems->count() > 0) {
                                // Call the provider callback to generate the URLs
                                $parsedMenuItems->merge(
                                    call_user_func($this->menuUrlProviders[$provider], $providerItems->all())
                                );
                            }
                        }
                    }

                    // Return the parsed menu items in a nested array structure
                    return nested_array($parsedMenuItems->multiSort('order')->all(), 'parent');
                },
                '30 minutes'
            );
    }
}