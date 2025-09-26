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

use Google_Service_Drive;

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
final class googledocs_root_content_test extends \googledocs_content_testcase {
    /**
     * Test get_content_nodes().
     *
     * @dataProvider get_content_nodes_provider
     * @param array $shareddrives The array containing the existing shared drives
     * @param array $expected The expected array which contains the generated repository content nodes
     * @param bool $expectshared Whether shared drives should be tested
     * @covers \repository_googledocs
     */
    public function test_get_content_nodes(array $shareddrives, array $expected, bool $expectshared): void {
        $scopessupportshared = $this->shared_drives_supported();

        if ($expectshared && !$scopessupportshared) {
            $this->markTestSkipped('Shared drives not supported in current OAuth scope.');
        }

        // Mock the service object.
        $servicemock = $this->createMock(\repository_googledocs\rest::class);

        if ($expectshared && $scopessupportshared) {
            // Expect shared drives API to be called.
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
        } else {
            // If shared drives are not expected or not supported, call() should not be invoked.
            $servicemock->expects($this->never())
                ->method('call');
        }

        $showshared = $expectshared && $scopessupportshared;
        $rootbrowser = new googledocs_root_content(
            $servicemock,
            \repository_googledocs::REPOSITORY_ROOT_ID . '|Google+Drive',
            $showshared
        );
        $contentnodes = $rootbrowser->get_content_nodes('', [$this, 'filter']);

        // Assert that the returned array of repository content nodes is equal to the expected one.
        $this->assertEquals($expected, $contentnodes);
    }

    /**
     * Data provider for test_get_content_nodes().
     *
     * @return array
     */
    public static function get_content_nodes_provider(): array {
        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;
        $mydriveid = \repository_googledocs::MY_DRIVE_ROOT_ID;
        $shareddrivesid = \repository_googledocs::SHARED_DRIVES_ROOT_ID;

        return [
            'Shared drives exist.' => [
                [
                    self::create_google_drive_shared_drive_object('d85b21c0f86cb5', 'Shared Drive 1'),
                    self::create_google_drive_shared_drive_object('d85b21c0f86cb0', 'Shared Drive 3'),
                ],
                [
                    self::create_folder_content_node_array($mydriveid,
                        get_string('mydrive', 'repository_googledocs'),
                        "{$rootid}|Google+Drive"),
                    self::create_folder_content_node_array($shareddrivesid,
                        get_string('shareddrives', 'repository_googledocs'),
                        "{$rootid}|Google+Drive"),
                ],
                true, // Expect shared drives.
            ],
            'Shared drives do not exist.' => [
                [],
                [
                    self::create_folder_content_node_array($mydriveid,
                        get_string('mydrive', 'repository_googledocs'),
                        "{$rootid}|Google+Drive"),
                ],
                false, // Do not expect shared drives.
            ],
        ];
    }

    /**
     * Determines whether shared drives are supported under the current Google Drive scope.
     *
     * @return bool
     */
    private function shared_drives_supported(): bool {
        $scopes = Google_Service_Drive::DRIVE_FILE;

        // Full access is needed for shared drives (not just drive.file).
        return str_contains($scopes, 'auth/drive') && !str_contains($scopes, 'auth/drive.file');
    }
}
