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

namespace repository_googledocs\local\browser;

use repository_googledocs\googledocs_content;
use repository_googledocs\helper;

/**
 * Utility class for browsing content from or within a specified google drive.
 *
 * This class is responsible for generating the content that would be displayed for a specified drive such as
 * 'my drive' or any existing shared drive. It also supports generating data for paths which are located
 * within a given drive.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class googledocs_drive_content extends googledocs_content {

    /**
     * Returns all relevant contents based on the given path or search query.
     *
     * The method fetches all existing content (files and folders) located in a specific folder under a given drive
     * through an API call.
     *
     * @param string $query The search query
     * @return array The array containing the contents
     */
    protected function get_contents(string $query): array {

        $id = str_replace("'", "\'", $query);
        // Define the parameters required by the API call.
        // Query all files and folders which have not been trashed and located directly in the folder whose ID is $id.
        $q = "'{$id}' in parents AND trashed = false";
        // The file fields that should be returned in the response.
        $fields = 'files(id,name,mimeType,webContentLink,webViewLink,fileExtension,modifiedTime,size,iconLink)';

        $params = [
            'q' => $q,
            'fields' => $fields,
            'spaces' => 'drive',
        ];

        // Check whether there are any shared drives.
        $response = helper::request($this->service, 'shared_drives_list', []);
        if (!empty($response->drives)) {
            // To be able to include content from shared drives, we need to enable 'supportsAllDrives' and
            // 'includeItemsFromAllDrives'. The Google Drive API requires explicit request for inclusion of content from
            // shared drives and also a confirmation that the application is designed to handle files on shared drives.
            $params['supportsAllDrives'] = 'true';
            $params['includeItemsFromAllDrives'] = 'true';
        }

        // Request the content through the API call.
        $response = helper::request($this->service, 'list', $params);

        return $response->files ?? [];
    }
}
