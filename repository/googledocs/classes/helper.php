<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace repository_googledocs;

use repository_googledocs\local\browser\googledocs_root_content;
use repository_googledocs\local\browser\googledocs_shared_drives_content;
use repository_googledocs\local\browser\googledocs_drive_content;
use repository_googledocs\local\node\node;
use repository_googledocs\local\node\file_node;
use repository_googledocs\local\node\folder_node;

/**
 * Helper class for the googledocs repository.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Generates a safe path to a node.
     *
     * Typically, a node will be id|name of the node.
     *
     * @param string $id The ID of the node
     * @param string $name The name of the node, will be URL encoded
     * @param string $root The path to append the node on (must be a result of this function)
     * @return string The path to the node
     */
    public static function build_node_path(string $id, string $name = '', string $root = ''): string {
        $path = $id;
        if (!empty($name)) {
            $path .= '|' . urlencode($name);
        }
        if (!empty($root)) {
            $path = trim($root, '/') . '/' . $path;
        }
        return $path;
    }

    /**
     * Returns information about a node in a path.
     *
     * @param string $node The node string to extract information from
     * @return array The array containing the information about the node
     * @see self::build_node_path()
     */
    public static function explode_node_path(string $node): array {
        if (strpos($node, '|') !== false) {
            list($id, $name) = explode('|', $node, 2);
            $name = urldecode($name);
        } else {
            $id = $node;
            $name = '';
        }
        $id = urldecode($id);

        return [
            0 => $id,
            1 => $name,
            'id' => $id,
            'name' => $name,
        ];
    }

    /**
     * Returns the relevant googledocs content browser class based on the given path.
     *
     * @param rest $service The rest API object
     * @param string $path The current path
     * @return googledocs_content The googledocs repository content browser
     */
    public static function get_browser(rest $service, string $path): googledocs_content {
        $pathnodes = explode('/', $path);
        $currentnode = self::explode_node_path(array_pop($pathnodes));

        // Return the relevant content browser class based on the ID of the current path node.
        switch ($currentnode['id']) {
            case \repository_googledocs::REPOSITORY_ROOT_ID:
                return new googledocs_root_content($service, $path, false);
            case \repository_googledocs::SHARED_DRIVES_ROOT_ID:
                return new googledocs_shared_drives_content($service, $path);
            default:
                return new googledocs_drive_content($service, $path);
        }
    }

    /**
     * Returns the relevant repository content node class based on the Google Drive file's mimetype.
     *
     * @param \stdClass $gdcontent The Google Drive content (file/folder) object
     * @param string $path The current path
     * @return node The content node object
     */
    public static function get_node(\stdClass $gdcontent, string $path): node {
        // Return the relevant content browser class based on the ID of the current path node.
        switch ($gdcontent->mimeType) {
            case 'application/vnd.google-apps.folder':
                return new folder_node($gdcontent, $path);
            default:
                return new file_node($gdcontent);
        }
    }

    /**
     * Wrapper function to perform an API call and also catch and handle potential exceptions.
     *
     * @param rest $service The rest API object
     * @param string $api The name of the API call
     * @param array $params The parameters required by the API call
     * @return \stdClass The response object
     * @throws \repository_exception
     */
    public static function request(rest $service, string $api, array $params): ?\stdClass {
        try {
            // Retrieving files and folders.
            $response = $service->call($api, $params);
        } catch (\Exception $e) {
            if ($e->getCode() == 403 && strpos($e->getMessage(), 'Access Not Configured') !== false) {
                // This is raised when the service Drive API has not been enabled on Google APIs control panel.
                throw new \repository_exception('servicenotenabled', 'repository_googledocs');
            }
            throw $e;
        }

        return $response;
    }
}
