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
 * Class containing unit tests for the drive browser class.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class googledocs_drive_content_test extends \googledocs_content_testcase {

    /**
     * Test get_content_nodes().
     *
     * @dataProvider get_content_nodes_provider
     * @param string $query The query string
     * @param string $path The path
     * @param bool $sortcontent Whether the contents should be sorted in alphabetical order
     * @param array $filterextensions The array containing file extensions that should be disallowed (filtered)
     * @param array $shareddrives The array containing the existing shared drives
     * @param array $drivecontents The array containing the fetched google drive contents
     * @param array $expected The expected array which contains the generated repository content nodes
     */
    public function test_get_content_nodes(string $query, string $path, bool $sortcontent, array $filterextensions,
            array $shareddrives, array $drivecontents, array $expected): void {

        // Mock the service object.
        $servicemock = $this->createMock(\repository_googledocs\rest::class);

        $listparams = [
            'q' => "'" . str_replace("'", "\'", $query) . "' in parents AND trashed = false",
            'fields' => 'files(id,name,mimeType,webContentLink,webViewLink,fileExtension,modifiedTime,size,iconLink)',
            'spaces' => 'drive',
        ];

        if (!empty($shareddrives)) {
            $listparams['supportsAllDrives'] = 'true';
            $listparams['includeItemsFromAllDrives'] = 'true';
        }

        // Assert that the call() method is being called twice with the given arguments consecutively. In the first
        // instance it is being called to fetch the shared drives (shared_drives_list), while in the second instance
        // to fetch the relevant drive contents (list). Also, define the returned data objects by these calls.
        $callinvocations = $this->exactly(2);
        $servicemock->expects($callinvocations)
            ->method('call')
            ->willReturnCallback(function(string $method, array $params) use (
                $callinvocations,
                $shareddrives,
                $listparams,
                $drivecontents,
            ) {
                switch (self::getInvocationCount($callinvocations)) {
                    case 1:
                        $this->assertEquals('shared_drives_list', $method);
                        $this->assertEquals([], $params);

                        return (object) [
                            'kind' => 'drive#driveList',
                            'nextPageToken' => 'd838181f30b0f5',
                            'drives' => $shareddrives,
                        ];
                    case 2:
                        $this->assertEquals('list', $method);
                        $this->assertEquals($listparams, $params);
                        return (object)[
                            'files' => $drivecontents,
                        ];
                    default:
                        $this->fail('Unexpected call to the call() method.');
                }
            });

        // Set the disallowed file types (extensions).
        $this->disallowedextensions = $filterextensions;
        $drivebrowser = new googledocs_drive_content($servicemock, $path, $sortcontent);
        $contentnodes = $drivebrowser->get_content_nodes($query, [$this, 'filter']);

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

        return [
            'Folders and files exist in the drive; shared drives exist; ordering applied.' =>
                [
                    $mydriveid,
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                    true,
                    [],
                    [
                        self::create_google_drive_shared_drive_object('d85b21c0f86cb5', 'Shared Drive 1'),
                    ],
                    [
                        self::create_google_drive_folder_object('1c4ad262c65333', 'Folder 2'),
                        self::create_google_drive_file_object('d85b21c0f86cb0', 'File 3.pdf',
                            'application/pdf', 'pdf', '1000'),
                        self::create_google_drive_folder_object('0c4ad262c65333', 'Folder 1'),
                        self::create_google_drive_file_object('bed5a0f08d412a', 'File 1.pdf',
                            'application/pdf', 'pdf'),
                    ],
                    [
                        self::create_folder_content_node_array('0c4ad262c65333', 'Folder 1',
                            "{$rootid}|Google+Drive/{$mydriveid}|My+Drive"),
                        self::create_folder_content_node_array('1c4ad262c65333', 'Folder 2',
                            "{$rootid}|Google+Drive/{$mydriveid}|My+Drive"),
                        self::create_file_content_node_array('bed5a0f08d412a', 'File 1.pdf', 'File 1.pdf',
                            null, '', 'https://googleusercontent.com/type/application/pdf', '',
                            'download'),
                        self::create_file_content_node_array('d85b21c0f86cb0', 'File 3.pdf', 'File 3.pdf',
                            '1000', '', 'https://googleusercontent.com/type/application/pdf', '',
                            'download'),
                    ],
                ],
            'Only folders exist in the drive; shared drives do not exist; ordering not applied.' =>
                [
                    $mydriveid,
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                    false,
                    [],
                    [],
                    [
                        self::create_google_drive_folder_object('0c4ad262c65333', 'Folder 3'),
                        self::create_google_drive_folder_object('d85b21c0f86cb0', 'Folder 1'),
                        self::create_google_drive_folder_object('bed5a0f08d412a', 'Folder 2'),
                    ],
                    [
                        self::create_folder_content_node_array('0c4ad262c65333', 'Folder 3',
                            "{$rootid}|Google+Drive/{$mydriveid}|My+Drive"),
                        self::create_folder_content_node_array('d85b21c0f86cb0', 'Folder 1',
                            "{$rootid}|Google+Drive/{$mydriveid}|My+Drive"),
                        self::create_folder_content_node_array('bed5a0f08d412a', 'Folder 2',
                            "{$rootid}|Google+Drive/{$mydriveid}|My+Drive"),
                    ],
                ],
            'Only files exist in the drive; shared drives do not exist; ordering not applied; filter .txt.' =>
                [
                    $mydriveid,
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                    false,
                    ['txt'],
                    [],
                    [
                        self::create_google_drive_file_object('d85b21c0f86cb0', 'File 3.pdf',
                            'application/pdf', 'pdf', '1000'),
                        self::create_google_drive_file_object('a85b21c0f86cb0', 'File 1.txt',
                            'text/plain', 'txt', '3000'),
                        self::create_google_drive_file_object('f85b21c0f86cb0', 'File 2.doc',
                            'application/msword', 'doc', '2000'),
                    ],
                    [
                        self::create_file_content_node_array('d85b21c0f86cb0', 'File 3.pdf', 'File 3.pdf',
                            '1000', '', 'https://googleusercontent.com/type/application/pdf', '',
                            'download'),
                        self::create_file_content_node_array('f85b21c0f86cb0', 'File 2.doc', 'File 2.doc',
                            '2000', '', 'https://googleusercontent.com/type/application/msword', '',
                            'download'),
                    ],
                ],
            'Contents do not exist in the drive; shared drives do not exist.' =>
                [
                    $mydriveid,
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                    false,
                    [],
                    [],
                    [],
                    [],
                ],
        ];
    }

    /**
     * Test get_navigation().
     *
     * @dataProvider get_navigation_provider
     * @param string $nodepath The node path string
     * @param array $expected The expected array containing the repository navigation nodes
     */
    public function test_get_navigation(string $nodepath, array $expected): void {
        // Mock the service object.
        $servicemock = $this->createMock(\repository_googledocs\rest::class);

        $drivebrowser = new googledocs_drive_content($servicemock, $nodepath);
        $navigation = $drivebrowser->get_navigation();

        // Assert that the returned array containing the navigation nodes is equal to the expected one.
        $this->assertEquals($expected, $navigation);
    }

    /**
     * Data provider for test_get_navigation().
     *
     * @return array
     */
    public static function get_navigation_provider(): array {

        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;
        $mydriveid = \repository_googledocs::MY_DRIVE_ROOT_ID;

        return [
            'Return navigation nodes array from path where all nodes have a name.' =>
                [
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive/bed5a0f08d|Test+Folder",
                    [
                        [
                            'name' => 'Google Drive',
                            'path' => "{$rootid}|Google+Drive",
                        ],
                        [
                            'name' => 'My Drive',
                            'path' => "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                        ],
                        [
                            'name' => 'Test Folder',
                            'path' => "{$rootid}|Google+Drive/{$mydriveid}|My+Drive/bed5a0f08d|Test+Folder",
                        ],
                    ],
                ],
            'Return navigation nodes array from path where some nodes do not have a name.' =>
                [
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive/bed5a0f08d/d85b21c0f8|Test+Folder",
                    [
                        [
                            'name' => 'Google Drive',
                            'path' => "{$rootid}|Google+Drive",
                        ],
                        [
                            'name' => 'My Drive',
                            'path' => "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                        ],
                        [
                            'name' => 'bed5a0f08d',
                            'path' => "{$rootid}|Google+Drive/{$mydriveid}|My+Drive/bed5a0f08d|bed5a0f08d",
                        ],
                        [
                            'name' => 'Test Folder',
                            'path' => "{$rootid}|Google+Drive/{$mydriveid}|My+Drive/bed5a0f08d|bed5a0f08d/" .
                                "d85b21c0f8|Test+Folder",
                        ],
                    ],
                ],
        ];
    }
}
