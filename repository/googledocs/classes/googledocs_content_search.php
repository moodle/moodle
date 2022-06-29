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

/**
 * Utility class for displaying google drive content that matched a given search criteria.
 *
 * This class is responsible for generating the content that is returned based on a given search query.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class googledocs_content_search extends googledocs_content {

    /**
     * Returns all relevant contents based on the given path and/or search query.
     *
     * The method fetches all content (files) through an API call that matches a given search criteria.
     *
     * @param string $query The search query
     * @return array The array containing the contents
     */
    protected function get_contents(string $query): array {
        $searchterm = str_replace("'", "\'", $query);

        // Define the parameters required by the API call.
        // Query all contents which name contains $searchterm and have not been trashed.
        $q = "fullText contains '{$searchterm}' AND trashed = false";
        // The file fields that should be returned in the response.
        $fields = "files(id,name,mimeType,webContentLink,webViewLink,fileExtension,modifiedTime,size,iconLink)";

        $params = [
            'q' => $q,
            'fields' => $fields,
            'spaces' => 'drive',
        ];

        // If shared drives exist, include the additional required parameters in order to extend the content search
        // into the shared drives area as well.
        $response = helper::request($this->service, 'shared_drives_list', []);
        if (!empty($response->drives)) {
            $params['supportsAllDrives'] = 'true';
            $params['includeItemsFromAllDrives'] = 'true';
            $params['corpora'] = 'allDrives';
        }

        // Request the content through the API call.
        $response = helper::request($this->service, 'list', $params);

        return $response->files ?? [];
    }
}
