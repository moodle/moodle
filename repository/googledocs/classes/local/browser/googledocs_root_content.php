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
 * Utility class for browsing the content within the googledocs repository root.
 *
 * This class is responsible for generating the content that would be displayed in the googledocs repository root.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class googledocs_root_content extends googledocs_content {

    /**
     * Returns all relevant contents based on the given path or search query.
     *
     * The method predefines the content which will be displayed in the repository root level. Currently,
     * only the folders representing 'My drives' and 'Shared drives' will be displayed in the root level.
     *
     * @param string $query The search query
     * @return array The array containing the contents
     */
    protected function get_contents(string $query): array {
        // Add 'My drive' folder into the displayed contents.
        $contents = [
            (object)[
                'id' => \repository_googledocs::MY_DRIVE_ROOT_ID,
                'name' => get_string('mydrive', 'repository_googledocs'),
                'mimeType' => 'application/vnd.google-apps.folder',
                'modifiedTime' => '',
            ],
        ];

        // If shared drives exists, include 'Shared drives' folder to the displayed contents.
        $response = helper::request($this->service, 'shared_drives_list', []);

        if (!empty($response->drives)) {
            $contents[] = (object)[
                'id' => \repository_googledocs::SHARED_DRIVES_ROOT_ID,
                'name' => get_string('shareddrives', 'repository_googledocs'),
                'mimeType' => 'application/vnd.google-apps.folder',
                'modifiedTime' => '',
            ];
        }

        return $contents;
    }
}
