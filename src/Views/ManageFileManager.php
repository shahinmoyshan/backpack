<?php
namespace Backpack\Views;

use Exception;
use Hyper\Request;
use Hyper\Response;
use Throwable;

/**
 * A class for managing the file manager page.
 *
 * @package Backpack/Views
 */
class ManageFileManager
{
    /**
     * The directory where files will be uploaded.
     *
     * @var string
     */
    private string $uploadDir;

    /**
     * The current folder path in the file manager.
     *
     * @var string
     */
    private string $currentFolder;

    /**
     * Handles the GET and POST requests for the file manager.
     * If the request is a POST, it will handle the file uploads, folder creation, and file/folder deletion.
     * If the request is a GET, it will list the files and folders in the current folder.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        // Set the current folder to the folder specified in the route or the root folder
        $this->setCurrentFolder('/filemanager' . $request->getRouteParam(0, '/'));

        // Set the upload directory from the environment variable
        $this->setUploadDir(upload_dir());

        // If the request is a POST, handle the request
        if ($request->getMethod() === 'POST') {
            $this->handleRequest($request, $response);
        }

        // List the files and folders in the current folder
        return backpack_template('filemanager/index', [
            'files' => $this->getFiles(),
            'folders' => explode(
                '/',
                str_replace('/filemanager', '', $this->getCurrentFolder())
            ),
        ]);
    }

    /**
     * Creates a new instance of the ManageFileManager class.
     *
     * @return ManageFileManager
     */
    public static function make(): ManageFileManager
    {
        return new self();
    }

    /**
     * Retrieves the directory where files are uploaded.
     *
     * @return string The upload directory path.
     */
    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    /**
     * Retrieves the current folder path in the file manager.
     *
     * @return string The current folder path.
     */
    public function getCurrentFolder(): string
    {
        return $this->currentFolder;
    }

    /**
     * Retrieves the full path of the current folder in the file manager.
     *
     * @return string The full path of the current folder.
     */
    public function getPath(): string
    {
        return "{$this->getUploadDir()}/{$this->getCurrentFolder()}";
    }

    /**
     * Sets the current folder in the file manager.
     *
     * @param string $currentFolder The current folder path relative to the root folder.
     *
     * @return void
     */
    public function setCurrentFolder(string $currentFolder): void
    {
        $this->currentFolder = $currentFolder;
    }

    /**
     * Sets the root directory where files are uploaded.
     *
     * The given $uploadDir should be a path relative to the storage root.
     *
     * @param string $uploadDir The root directory path relative to the storage root.
     *
     * @return void
     */
    public function setUploadDir(string $uploadDir): void
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * Handles file manager requests such as deleting a file or folder, creating a new folder, or uploading files.
     *
     * @param Request $request The request object.
     * @param Response $response The response object.
     *
     * @return void
     */
    public function handleRequest(Request $request, Response $response): void
    {
        $redirect_url = $request->post('__redirect', route_url('admin.cms.filemanager'));
        try {
            $delete_file = trim($request->post('delete_file', ''));
            $new_folder = trim($request->post('new_folder', ''));
            $upload_files = $request->file('upload_files', []);

            // Delete file or folder
            if (!empty($delete_file)) {
                apply_permissions('delete_file_manager');

                $filepath = $this->getPath() . '/' . $delete_file;
                if (is_dir($filepath) && rmdir($filepath) === false) {
                    throw new Exception('Unable to delete directory');
                } elseif (is_file($filepath) && unlink($filepath) === false) {
                    throw new Exception('Unable to delete file');
                }
            }

            // Create new folder
            elseif (!empty($new_folder)) {
                apply_permissions('create_file_manager');

                $filepath = $this->getPath() . '/' . slugify($new_folder);
                if (is_dir($filepath)) {
                    throw new Exception('Folder already exists');
                } elseif (mkdir($filepath) === false) {
                    throw new Exception('Unable to create folder');
                }
            }

            // Upload files
            elseif (!empty($upload_files)) {
                apply_permissions('create_file_manager');
                $errors = [];

                // upload each files uploaded if not exists
                for ($i = 0; $i < count($upload_files['name'] ?? []); $i++) {
                    $file = [
                        'name' => $upload_files['name'][$i],
                        'tmp_name' => $upload_files['tmp_name'][$i],
                    ];
                    $filepath = $this->getPath() . '/' . $file['name'];
                    if (file_exists($filepath)) {
                        $errors[] = __('File (%s) already exists', $file['name']);
                    } elseif (move_uploaded_file($file['tmp_name'], $filepath) === false) {
                        $errors[] = __('Unable to upload file (%s)', $file['name']);
                    }
                }

                if (!empty($errors)) {
                    throw new Exception(implode(', ', $errors));
                }
            }

        } catch (Throwable $e) {
            session()->set('error_message', $e->getMessage());
        }

        $response->redirect($redirect_url);
    }

    /**
     * Get files and folders in the current folder.
     *
     * @return array Array of files and folders, each with the following keys:
     *     - type: dir or file
     *     - name: name of the file or folder
     *     - path: full path of the file or folder
     *     - url: URL of the file or folder (only for files)
     *     - size: size of the file (only for files)
     *     - date: last modified date of the file or folder
     *     - extension: file extension (only for files)
     *     - is_empty: whether the folder is empty (only for folders)
     */
    public function getFiles(): array
    {
        $files = [];
        $path = $this->getPath();

        if (is_dir($path) && $handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, ['.', '..'])) {
                    $files[] = is_dir("$path/$file") ? [
                        'type' => 'dir',
                        'name' => $file,
                        'path' => "$path/$file",
                        'date' => date('Y-m-d H:i:s', filemtime("$path/$file")),
                        'is_empty' => count(glob("$path/$file/*")) === 0
                    ] : [
                        'type' => 'file',
                        'name' => $file,
                        'path' => "$path/$file",
                        'url' => media_url("{$this->currentFolder}/$file"),
                        'size' => filesize("$path/$file"),
                        'date' => date('Y-m-d H:i:s', filemtime("$path/$file")),
                        'extension' => pathinfo("$path/$file", PATHINFO_EXTENSION),
                    ];
                }
            }

            closedir($handle);
        }

        return $files;
    }
}