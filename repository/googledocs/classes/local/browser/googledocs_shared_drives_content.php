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
 * Utility class for browsing the content within the googledocs repository shared drives root.
 *
 * This class is responsible for generating the content that would be displayed in the googledocs repository
 * shared drives root.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class googledocs_shared_drives_content extends googledocs_content {

    /**
     * Returns all relevant contents based on the given path or search query.
     *
     * The method generates the content which will be displayed in the repository shared drives root level.
     * All existing shared drives will be fetched through an API call and presented as folders.
     *
     * @param string $query The search query
     * @return array The array containing the contents
     */
    protected function get_contents(string $query): array {
        // Make an API request to get all existing shared drives.
        $response = helper::request($this->service, 'shared_drives_list', []);
        // If shared drives exist, create folder for each shared drive.
        if ($shareddrives = $response->drives) {
            return array_map(function($shareddrive) {
                return (object)[
                    'id' => $shareddrive->id,
                    'name' => $shareddrive->name,
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'modifiedTime' => '',
                ];
            }, $shareddrives);
        }

        return [];
    }
}
