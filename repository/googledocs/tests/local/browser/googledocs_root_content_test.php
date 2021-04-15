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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/googledocs/tests/googledocs_content_testcase.php');
require_once($CFG->dirroot . '/repository/googledocs/lib.php');

/**
 * Class containing unit tests for the repository root browser class.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_googledocs_root_browser_testcase extends \googledocs_content_testcase {

    /**
     * Test get_content_nodes().
     *
     * @dataProvider get_content_nodes_provider
     * @param array $shareddrives The array containing the existing shared drives
     * @param array $expected The expected array which contains the generated repository content nodes
     */
    public function test_get_content_nodes(array $shareddrives, array $expected) {
        // Mock the service object.
        $servicemock = $this->createMock(\repository_googledocs\rest::class);

        // Assert that the call() method is being called only once with the given arguments to fetch the existing
        // shared drives. Define the returned data object by this call.
        $servicemock->expects($this->once())
            ->method('call')
            ->with('shared_drives_list', [])
            ->willReturn((object)[
                'kind' => 'drive#driveList',
                'nextPageToken' => 'd838181f30b0f5',
                'drives' => $shareddrives,
            ]);

        $rootbrowser = new googledocs_root_content($servicemock,
            \repository_googledocs::REPOSITORY_ROOT_ID . '|Google+Drive', false);
        $contentnodes = $rootbrowser->get_content_nodes('', [$this, 'filter']);

        // Assert that the returned array of repository content nodes is equal to the expected one.
        $this->assertEquals($expected, $contentnodes);
    }

    /**
     * Data provider for test_get_content_nodes().
     *
     * @return array
     */
    public function get_content_nodes_provider(): array {

        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;
        $mydriveid = \repository_googledocs::MY_DRIVE_ROOT_ID;
        $shareddrivesid = \repository_googledocs::SHARED_DRIVES_ROOT_ID;

        return [
            'Shared drives exist.' =>
                [
                    [
                        $this->create_google_drive_shared_drive_object('d85b21c0f86cb5', 'Shared Drive 1'),
                        $this->create_google_drive_shared_drive_object('d85b21c0f86cb0', 'Shared Drive 3'),
                    ],
                    [
                        $this->create_folder_content_node_array($mydriveid,
                            get_string('mydrive', 'repository_googledocs'),
                            "{$rootid}|Google+Drive"),
                        $this->create_folder_content_node_array($shareddrivesid,
                            get_string('shareddrives', 'repository_googledocs'),
                            "{$rootid}|Google+Drive"),
                    ],
                ],
            'Shared drives do not exist.' =>
                [
                    [],
                    [
                        $this->create_folder_content_node_array($mydriveid,
                            get_string('mydrive', 'repository_googledocs'),
                            "{$rootid}|Google+Drive"),
                    ],
                ],
        ];
    }
}
