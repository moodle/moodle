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

namespace repository_googledocs\local\node;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/googledocs/tests/repository_googledocs_testcase.php');

/**
 * Class containing unit tests for the repository folder node class.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class folder_node_test extends \repository_googledocs_testcase {

    /**
     * Test create_node_array().
     *
     * @dataProvider create_node_array_provider
     * @param \stdClass $gdfolder The Google Drive folder object
     * @param string $path The current path
     * @param array $expected The expected repository folder node array
     */
    public function test_create_node_array(\stdClass $gdfolder, string $path, array $expected): void {
        $foldernode = new folder_node($gdfolder, $path);
        $foldernodearray = $foldernode->create_node_array();
        // Assert that the returned repository folder node array by create_node_array() is equal to the expected one.
        $this->assertEquals($expected, $foldernodearray);
    }

    /**
     * Data provider for test_create_node_array().
     *
     * @return array
     */
    public static function create_node_array_provider(): array {
        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;

        return [
            'Google Drive folder with modified date.' =>
                [
                    self::create_google_drive_folder_object('d85b21c0f86cb0', 'Folder', '01/01/21 0:30'),
                    "{$rootid}|Google+Drive",
                    self::create_folder_content_node_array(
                        'd85b21c0f86cb0',
                        'Folder',
                        "{$rootid}|Google+Drive",
                        '1609432200',
                    ),
                ],
            'Google Drive folder without modified date.' =>
                [
                    self::create_google_drive_folder_object('d85b21c0f86cb0', 'Folder', ''),
                    '',
                    self::create_folder_content_node_array('d85b21c0f86cb0', 'Folder', '', ''),
                ],
        ];
    }
}
