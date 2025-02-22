<?php

use Backpack\Lib\PrettyTime;
use Backpack\Lib\Settings;
use Backpack\Lib\TailwindHelper;
use Backpack\MenuManager;
use Backpack\Panel;
use Hyper\Response;
use Hyper\Template;
use Hyper\Translator;

/**
 * Checks if the currently logged in admin user has a given permission.
 *
 * @param string $permission
 *
 * @return bool
 */
function has_permission(string $permission): bool
{
    return collect(user()->role->getPermissions())
        ->find(fn($per) => $per->name === $permission) !== null;
}

/**
 * Checks if the currently logged in admin user has any of the given permissions.
 *
 * @param array $permissions
 *
 * @return bool
 */
function has_any_permission(array $permissions): bool
{
    return collect($permissions)
        ->find(fn($per) => has_permission($per)) !== null;
}

/**
 * Checks if the currently logged in admin user has all of the given permissions.
 *
 * @param array $permissions
 *
 * @return bool
 */
function has_all_permissions(array $permissions): bool
{
    return collect($permissions)
        ->map(fn($per) => has_permission($per))
        ->in(false) !== true;
}


/**
 * Applies the given permissions to the currently logged in admin user.
 * If the user does not have the given permission(s), it will send a 403 response and exit.
 *
 * @param string|array $permissions The permission(s) to check.
 * @param string $type The type of permission check. Can be one of 'one', 'all', or 'any'.
 * @return void
 */
function apply_permissions(string|array $permissions, string $type = 'one'): void
{
    if (
        !match ($type) {
            'one' => has_permission($permissions),
            'all' => has_all_permissions($permissions),
            'any' => has_any_permission($permissions),
        }
    ) {
        $translations = [
            'heading' => __('Permission Denied'),
            'description' => __('__permission_denied_msg'),
            'back_to_admin' => __('Go to Admin')
        ];
        (new Response(
            <<<HTML
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Permission Denied</title>
                </head>
                <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8d7da; color: #721c24; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center;">
                    <div>
                        <h1 style="font-size: 48px; margin: 0;">403</h1>
                        <h2 style="margin: 10px 0; font-size: 24px;">{$translations['heading']}</h2>
                        <p style="font-size: 16px; color: #856404; max-width: 400px; margin: 0 auto;">{$translations['description']}</p>
                        <a href="/admin" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #f5c6cb; color: #721c24; text-decoration: none; border-radius: 5px; border: 1px solid #f5c6cb;">
                            &larr;&nbsp;{$translations['back_to_admin']}
                        </a>
                    </div>
                </body>
                </html>
            HTML,
            403
        ))->send();
        exit;
    }
}

/**
 * Renders an admin template with the given context.
 *
 * This function initializes a template engine, renders the specified template
 * with the provided context, and writes the rendered content to the application's response.
 *
 * @param string $template The path to the template file to render.
 * @param array $context An associative array of variables to pass to the template.
 * @return Response The response object after writing the rendered content.
 */
function backpack_template(string $template, array $context = []): Response
{
    // Check if the request accepts JSON
    if (request()->accept('application/json')) {
        // Get the template engine
        $engine = get(Template::class)
            ->setPath(backpack_templates_dir());

        // Render the template and get the rendered HTML and title
        $html = $engine->render($template, $context);
        $title = $engine->getContext()['title'] ?? null;

        // Return a JSON response with the rendered HTML and title
        return response()->json([
            'html' => $html,
            'title' => $title,
        ]);
    }

    // Otherwise, return a regular HTTP response with the rendered HTML
    return get(Response::class)->write(
        get(Template::class)
            ->setPath(backpack_templates_dir())
            ->render($template, $context)
    );
}

/**
 * Returns the path to the templates folder.
 *
 * The path is the absolute path to the templates folder inside the package.
 *
 * @param string $suffix An optional suffix to append to the path.
 * @return string The path to the templates folder.
 */
function backpack_templates_dir(string $suffix = ''): string
{
    return dir_path(__DIR__ . '/Templates/' . $suffix);
}

/**
 * Converts a string into a URL-friendly "slug" by normalizing,
 * transliterating, and formatting it.
 *
 * The function normalizes the string to NFKD form, transliterates
 * it to ASCII, converts it to lowercase, and replaces non-alphanumeric
 * characters with a specified separator. It also removes leading and
 * trailing separators.
 *
 * @param string $string The input string to be slugified.
 * @param string $separator The separator to use for non-alphanumeric characters.
 *
 * @return string The slugified version of the input string.
 */
function slugify(string $string, string $separator = '-'): string
{
    // Normalize the string (NFKD normalization)
    $string = normalizer_normalize($string, Normalizer::FORM_KD);

    // Transliterate the string to ASCII
    $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);

    // Convert the string to lowercase
    $string = mb_strtolower($string, 'UTF-8');

    // Replace any non-alphanumeric characters with the separator
    $string = preg_replace('/[^a-z0-9]+/u', $separator, $string);

    // Remove any leading or trailing separators
    $string = trim($string, $separator);

    return $string;
}

/**
 * Converts a given string into a prettier text by replacing underscores, dashes, and dots
 * with spaces and capitalizing the first letter of each word.
 *
 * @param string $text The input string to be prettified.
 *
 * @return string The prettified version of the input string.
 */
function pretty_text(string $text): string
{
    return trim(ucwords(str_replace(['_', '-', '.'], ' ', $text)));
}

/**
 * Trim a string to a specified number of words.
 *
 * @param string $string The input string.
 * @param int $wordLimit The maximum number of words to keep.
 * @param string $suffix A suffix to append if the string is trimmed (default: '...').
 * @param bool $trimFromMiddle Whether to trim from the middle of the string (default: false).
 * @return string The trimmed string.
 */
function trim_words(string $string, int $wordLimit = 10, string $suffix = '...', bool $trimFromMiddle = false): string
{
    $words = explode(' ', $string);

    if (count($words) <= $wordLimit) {
        return $string; // No trimming needed.
    }

    if ($trimFromMiddle) {
        // Calculate words to keep from the start and the end.
        $halfLimit = (int) floor($wordLimit / 2);
        $startWords = array_slice($words, 0, $halfLimit);
        $endWords = array_slice($words, -$halfLimit);

        // Adjust for odd word limits.
        if ($wordLimit % 2 !== 0) {
            $startWords[] = $words[$halfLimit];
        }

        return implode(' ', $startWords) . " $suffix " . implode(' ', $endWords);
    }

    // Default: Trim from the start.
    return implode(' ', array_slice($words, 0, $wordLimit)) . $suffix;
}

/**
 * Trim a string to a specified number of characters.
 *
 * @param string $string The input string.
 * @param int $charLimit The maximum number of characters to keep.
 * @param string $suffix A suffix to append if the string is trimmed (default: '...').
 * @param bool $trimFromMiddle Whether to trim from the middle of the string (default: false).
 * @return string The trimmed string.
 */
function trim_characters(string $string, int $charLimit = 100, string $suffix = '...', bool $trimFromMiddle = false): string
{
    if (strlen($string) <= $charLimit) {
        return $string; // No trimming needed.
    }

    $suffixLength = strlen($suffix);
    $trimmedLimit = $charLimit - $suffixLength;

    if ($trimFromMiddle) {
        // Split the limit into two halves for middle trimming.
        $halfLimit = (int) floor($trimmedLimit / 2);
        $start = substr($string, 0, $halfLimit);
        $end = substr($string, -$halfLimit);

        // Adjust for odd limits.
        if ($trimmedLimit % 2 !== 0) {
            $start .= $string[$halfLimit];
        }

        return $start . $suffix . $end;
    }

    // Default: Trim from the start.
    return substr($string, 0, $trimmedLimit) . $suffix;
}

/**
 * Transform a flat array into a nested array using a parent-child relationship.
 *
 * @param array $array The input array with a parent-child relationship.
 * @param string $parentKey The key for the parent id (default: 'parent').
 * @param string $idKey The key for the item id (default: 'id').
 * @param string $childrenKey The key for the children array (default: 'children').
 * @return array The nested array.
 */
function nested_array(array $array, string $parentKey = 'parent', string $idKey = 'id', string $childrenKey = 'children'): array
{
    // Step 1: Index the array by id
    $indexedItems = [];
    foreach ($array as $item) {
        $item[$childrenKey] = [];
        $indexedItems[$item[$idKey]] = $item;
    }

    // Step 2: Build the nested structure
    $nestedArray = [];
    foreach ($indexedItems as $id => $item) {
        if ($item[$parentKey] === null) {
            // Top-level item
            $nestedArray[] = &$indexedItems[$id];
        } else {
            // Child item
            $indexedItems[$item[$parentKey]][$childrenKey][] = &$indexedItems[$id];
        }
    }

    return $nestedArray;
}

/**
 * Process a nested array and transform it into a flat array with a callback.
 *
 * @param array $items The input array with a nested structure.
 * @param callable $callback A callback that transforms each item in the array.
 * @param string $idKey The key for the item id (default: 'id').
 * @param string $childrenKey The key for the children array (default: 'children').
 * @param int $depth The current depth of the nested array (default: 0).
 * @return array The processed flat array.
 */
function nested_array_walker(array $items, callable $callback, string $idKey = 'id', string $childrenKey = 'children', int $depth = 0): array
{
    $list = [];

    foreach ($items as $item) {
        // Add the current category with indentation based on depth
        $list[$item[$idKey]] = call_user_func($callback, $item, $depth);

        // If the category has children, process them recursively
        if (!empty($item[$childrenKey])) {
            $list += nested_array_walker($item[$childrenKey], $callback, $idKey, $childrenKey, $depth + 1);
        }
    }

    return $list;
}

/**
 * Get the Backpack\Panel instance.
 *
 * @return Panel
 */
function panel(): Panel
{
    return get(Panel::class);
}

/**
 * Returns the path to the admin templates directory for Backpack\CRUD.
 *
 * @param string $path The subdirectory or file path relative to the admin templates directory for Backpack\CRUD.
 * @return string The absolute path to the admin templates directory for Backpack\CRUD with the given path appended.
 */
function admin_bread_dir(string $path): string
{
    return admin_template_dir("/bread/$path");
}

/**
 * Returns the path to the admin templates directory.
 *
 * @param string $path The subdirectory or file path relative to the admin templates directory.
 * @return string The absolute path to the admin templates directory with the given path appended.
 */
function admin_template_dir(string $path): string
{
    return root_dir('app/Templates/admin/' . trim($path, '/'));
}

/**
 * Retrieve a setting value by its name.
 * 
 * This function fetches the value of a specified setting from the settings manager.
 * If the setting does not exist, the default value is returned.
 *
 * @param string $name The name of the setting to retrieve.
 * @param mixed $default The default value to return if the setting is not found.
 * 
 * @return mixed The value of the setting, or the default value if the setting does not exist.
 */
function setting(string $name, $default = null)
{
    return getSettings()
        ->get($name, $default);
}

/**
 * Retrieves the Backpack settings manager instance.
 *
 * @return Settings The settings manager instance.
 */
function getSettings(): Settings
{
    return get(Settings::class);
}

/**
 * Retrieve a CMS option value by its ID and key.
 * 
 * This function fetches the value of a specified CMS option from the settings manager.
 * If the option does not exist, the default value is returned.
 *
 * @param string $id The ID of the CMS page to retrieve the option for.
 * @param string $key The key of the option to retrieve.
 * @param mixed $default The default value to return if the option is not found.
 * 
 * @return mixed The value of the option, or the default value if the option does not exist.
 */
function cms(string $id, string $key, $default = null)
{
    return getSettings()
        ->getCms($id, $key, $default);
}

/**
 * Converts a given time into a human-readable "pretty" time format relative to a reference time.
 * 
 * This function takes a time and an optional reference time, both of which can be provided 
 * as either a timestamp or a date-time string. It returns a string representing the time 
 * difference in a more readable format, such as "moments ago", "yesterday", or "2 weeks ago".
 *
 * @param int|string $time The time to be formatted, as a timestamp or a date-time string.
 * @param int|string|null $reference The reference time to compare against, as a timestamp 
 *                                    or a date-time string. Defaults to the current time if null.
 * 
 * @return string The "pretty" string representation of the time difference from the reference time.
 */
function pretty_time(int|string $time, int|string $reference = null): string
{
    // Convert $time to DateTime
    $time = is_numeric($time)
        ? new DateTime("@$time") // If numeric, assume it's a timestamp
        : new DateTime($time);  // Otherwise, parse as a date-time string

    // Convert $reference to DateTime or use current time if null
    $reference = $reference !== null
        ? (is_numeric($reference)
            ? new DateTime("@$reference") // If numeric, assume it's a timestamp
            : new DateTime($reference))  // Otherwise, parse as a date-time string
        : new DateTime(); // Default to now if no reference is provided

    // Return the pretty time string using the PrettyTime singleton
    return get(PrettyTime::class)->parse($time, $reference);
}

/**
 * Formats a large number into a more readable format, such as 1K, 2.5M, 3B, etc.
 *
 * @param ?float $num The number to format.
 * @return string The formatted number.
 */
function short_number(?float $num): string
{
    $num ??= 0; // Default to 0 if $num is null

    // Define the units for the number, such as K, M, B, and T
    $units = ['', 'K', 'M', 'B', 'T'];
    // Loop until the number is less than 1000
    for ($i = 0; $num >= 1000; $i++) {
        // Divide the number by 1000 to move to the next unit
        $num /= 1000;
    }
    // Return the formatted number, rounded to one decimal place
    return round($num, 1) . $units[$i];
}

/**
 * Retrieve the menu items for a given location.
 * 
 * This function fetches the menu items associated with a specified location
 * using the menu manager.
 *
 * @param string $location The identifier of the menu location.
 * 
 * @return array The array of menu items for the specified location.
 */
function menu(string $location): array
{
    return get(MenuManager::class)
        ->getMenu($location);
}

/**
 * Extracts image sizes from an array of image paths.
 *
 * This function takes an array of image paths and extracts the sizes from the
 * file names. It uses a regular expression to match the size pattern in the
 * file name, and returns an associative array where the keys are the sizes
 * and the values are the corresponding image paths.
 *
 * @param string|array $images The array of image paths.
 *
 * @return array The associative array of image sizes and paths.
 */
function extract_image_sizes(string|array $images): array
{
    $_images = [];

    // check if the images is json encoded
    if (is_string($images)) {
        $encoded = json_decode($images, true);
        $images = json_last_error() === JSON_ERROR_NONE ? $encoded : [$images];
    }

    foreach ($images as $key => $image) {
        // The pattern matches any string that ends with ".ext" and
        // contains "XxY" where X and Y are digits.
        if (preg_match('/(\d+x\d+)(?=\.\w{3,4}$)/', $image, $matches)) {
            $key = $matches[1]; // Return the matched size
        } else {
            // If the image does not have a specific size, then
            // treat it as "original" size of images. 
            $key = 'original';
        }

        // If the key is not an array, convert it to an array
        $_images[$key] = !isset($_images[$key]) ? $image : array_merge((array) $_images[$key], [$image]);
    }

    return $_images;
}

/**
 * Finds an image size from an array of images.
 *
 * This function takes an array of images and a list of desired image sizes.
 * It returns the first image that matches one of the sizes. If no image is
 * found, it returns the first image in the array.
 *
 * @param string|array $images The array of image paths.
 * @param array|string $sizes The list of desired image sizes.
 * @param bool $single Whether to return only the first matching image.
 * @param bool $strick Whether to return null if no matching image is found.
 *
 * @return mixed The path to the matching image, or null if no image is found.
 */
function find_a_image_size(string|array $images, array|string $sizes, bool $single = true, bool $strick = false): mixed
{
    $images = extract_image_sizes($images);
    $image = null;
    // Iterate through the list of desired image sizes
    foreach ((array) $sizes as $size) {
        // If the image matches the size, return it
        $image = $images[$size] ?? null;
        if ($image) {
            break;
        }
    }

    // If no image is found, return the first image in the array
    if ($strick === false) {
        $image ??= array_values($images)[0] ?? null;
    }

    // If $single is true, return only the first image
    if ($single && is_array($image)) {
        do {
            $image = array_values($image)[0] ?? null;
        } while (is_array($image));
    }

    return $image;
}

/**
 * Save the given data to the specified JSON configuration file.
 *
 * This function reads the current configuration data from the file, merges it
 * with the given data, and then writes the result back to the file.
 *
 * @param string $config_path The path to the JSON configuration file.
 * @param array $data The data to write to the file.
 * @return bool True if the operation was successful, false otherwise.
 */
function save_json_config(string $config_path, array $data): bool
{
    // Read the current configuration data from the file
    $config = get_json_config($config_path);

    // Merge the current configuration with the given data
    $config = array_merge($config, $data);

    // Check if the file is writable
    if (
        is_file($config_path) &&
        !is_writeable($config_path) && !chmod($config_path, 0777)
    ) {
        return false;
    }

    // Write the result back to the file
    return file_put_contents(
        $config_path,
        json_encode(
            $config,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        )
    );
}

/**
 * Retrieve the JSON configuration from a file.
 *
 * This function reads a JSON configuration file from the specified path and 
 * returns its contents as an associative array. If the file does not exist 
 * or is not readable, it returns an empty array.
 *
 * @param string $config_path The path to the JSON configuration file.
 * @return array The configuration data as an associative array.
 */
function get_json_config(string $config_path): array
{
    // Check if the file exists and is readable
    if (
        is_file($config_path) &&
        (is_readable($config_path) || chmod($config_path, 0777))
    ) {
        // Read the contents of the file
        $json = file_get_contents($config_path);
    } else {
        // Use an empty JSON object if the file is not accessible
        $json = '{}';
    }

    // Decode the JSON data into an associative array
    return (array) json_decode($json, true);
}

/**
 * Returns the path to the root directory of the Backpack package.
 *
 * This function appends the given path to the root directory of the Backpack
 * package and returns the result. The given path is trimmed of any leading or
 * trailing forward slashes before being appended.
 *
 * @param string $path The path to append to the root directory.
 * @return string The absolute path to the given directory inside the Backpack
 * package.
 */
function backpack_root_dir(string $path = ''): string
{
    return dir_path(__DIR__ . '/' . trim($path, '/'));
}

/**
 * Retrieve the TailwindHelper instance from the IoC container.
 *
 * This function returns the instance of the TailwindHelper class, which is used
 * to generate Tailwind CSS classes from a given color and shade.
 *
 * @return TailwindHelper The instance of the TailwindHelper class.
 */
function tailwind(): TailwindHelper
{
    return get(TailwindHelper::class);
}

/**
 * Loads the language file for the panel.
 *
 * The language file is loaded from the given directory. If the language file
 * does not exist in the given directory, the English language file is loaded
 * instead.
 *
 * @param string $land_dir The directory containing the language files.
 * @param string $lang The language code.
 * @param bool $prepend Whether to prepend the language file.
 */
function add_language_file(string $land_dir, string $lang, bool $prepend = false): void
{
    $lang_file = "{$land_dir}/{$lang}.php";

    // Load the language file for the panel
    get(Translator::class)
        ->addLanguageFile($lang_file, $prepend);
}

/**
 * Adds the language file for Backpack.
 *
 * The language file is loaded from the "i18n" directory inside the Backpack
 * package. If the language file does not exist in the given directory, the
 * English language file is loaded instead.
 *
 * @param string $lang The language code.
 * @param bool $prepend Whether to prepend the language file.
 */
function add_backpack_language_file(string $lang, bool $prepend = false): void
{
    add_language_file(backpack_root_dir('i18n'), $lang, $prepend);
}

/**
 * Checks if the current route is an admin route.
 *
 * This function checks if the current route is an admin route by checking if
 * the path of the route starts with '/admin'. If the path starts with '/admin',
 * the function returns true. Otherwise, it returns false.
 *
 * @return bool True if the current route is an admin route, false otherwise.
 */
function is_admin_route(): bool
{
    return strpos(request()->getPath(), '/admin') === 0;
}