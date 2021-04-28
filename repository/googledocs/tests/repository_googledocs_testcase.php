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

/**
 * Base class for the googledoc repository unit tests.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class repository_googledocs_testcase extends \advanced_testcase {

    /**
     * Create an array that would replicate the structure of a repository folder node array.
     *
     * @param string $id The ID of the folder
     * @param string $name The name of the folder
     * @param string $path The root path
     * @param string $modified The date of the last modification
     * @return array The repository folder node array
     */
    protected function create_folder_content_node_array(string $id, string $name, string $path,
            string $modified = ''): array {

        global $OUTPUT;

        return [
            'id' => $id,
            'title' => $name,
            'path' => repository_googledocs\helper::build_node_path($id, $name, $path),
            'date' => $modified,
            'thumbnail' => $OUTPUT->image_url(file_folder_icon(64))->out(false),
            'thumbnail_height' => 64,
            'thumbnail_width' => 64,
            'children' => [],
        ];
    }

    /**
     * Create an array that would replicate the structure of a repository file node array.
     *
     * @param string $id The ID of the file
     * @param string $name The name of the file
     * @param string $title The title of the file node
     * @param string|null $size The size of the file
     * @param string $modified The date of the last modification
     * @param string $thumbnail The thumbnail of the file
     * @param string|null $link The external link to the file
     * @param string|null $exportformat The export format of the file
     * @return array The repository file node array
     */
    protected function create_file_content_node_array(string $id, string $name, string $title, ?string $size = null,
            string $modified = '', string $thumbnail = '' , string $link = '', string $exportformat = ''): array {

        return [
            'id' => $id,
            'title' => $title,
            'source' => json_encode([
                'id' => $id,
                'name' => $name,
                'link' => $link,
                'exportformat' => $exportformat
            ]),
            'date' => $modified,
            'size' => $size,
            'thumbnail' => $thumbnail,
            'thumbnail_height' => 64,
            'thumbnail_width' => 64,
        ];
    }

    /**
     * Create an object that would replicate the metadata for a shared drive returned by the Google Drive API call.
     *
     * @param string $id The ID of the shared drive
     * @param string $name The name of the shared drive
     * @return \stdClass The shared drive object
     */
    protected function create_google_drive_shared_drive_object(string $id, string $name): \stdClass {
        return (object)[
            'kind' => 'drive#drive',
            'id' => $id,
            'name' => $name,
        ];
    }

    /**
     * Create an object that would replicate the metadata for a folder returned by the Google Drive API call.
     *
     * @param string $id The ID of the folder
     * @param string $name The name of the folder
     * @param string|null $modified The date of the last modification
     * @return \stdClass The folder object
     */
    protected function create_google_drive_folder_object(string $id, string $name, ?string $modified = null): \stdClass {
        return (object)[
            'id' => $id,
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'webViewLink' => "https://drive.google.com/drive/folders/{$id}",
            'iconLink' => 'https://googleusercontent.com/16/type/application/vnd.google-apps.folder+shared',
            'modifiedTime' => $modified ?? '',
        ];
    }

    /**
     * Create an object that would replicate the metadata for a file returned by the Google Drive API call.
     *
     * @param string $id The ID of the file
     * @param string $name The name of the file
     * @param string $mimetype The mimetype of the file
     * @param string|null $extension The extension of the file
     * @param string|null $size The size of the file
     * @param string|null $modified The date of the last modification
     * @param string|null $webcontentlink The link for downloading the content of the file
     * @param string|null $webviewlink The link for opening the file in a browser
     * @return \stdClass The file object
     */
    protected function create_google_drive_file_object(string $id, string $name, string $mimetype,
            ?string $extension = null, ?string $size = null, ?string $modified = null, ?string $webcontentlink = null,
            ?string $webviewlink = null): \stdClass {

        $googledrivefile = [
            'id' => $id,
            'name' => $name,
            'mimeType' => $mimetype,
            'size' => $size ?? '',
            'webContentLink' => $webcontentlink ?? '',
            'webViewLink' => $webviewlink ?? '',
            'iconLink' => "https://googleusercontent.com/type/{$mimetype}",
            'modifiedTime' => $modified ?? '',
        ];
        // The returned metadata might not always have the 'fileExtension' property.
        if ($extension) {
            $googledrivefile['fileExtension'] = $extension;
        }

        return (object)$googledrivefile;
    }
}
