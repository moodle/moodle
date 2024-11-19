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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/googledocs/tests/googledocs_content_testcase.php');
require_once($CFG->dirroot . '/repository/googledocs/lib.php');

/**
 * Class containing unit tests for the search content class.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class googledocs_search_content_test extends \googledocs_content_testcase {

    /**
     * Test get_content_nodes().
     *
     * @dataProvider get_content_nodes_provider
     * @param string $query The query string
     * @param bool $sortcontent Whether the contents should be sorted in alphabetical order
     * @param array $filterextensions The array containing file extensions that should be disallowed (filtered)
     * @param array $shareddrives The array containing the existing shared drives
     * @param array $searccontents The array containing the fetched google drive contents that match the search criteria
     * @param array $expected The expected array which contains the generated repository content nodes
     */
    public function test_get_content_nodes(string $query, bool $sortcontent, array $filterextensions,
            array $shareddrives, array $searccontents, array $expected): void {

        // Mock the service object.
        $servicemock = $this->createMock(rest::class);

        $searchparams = [
            'q' => "fullText contains '" . str_replace("'", "\'", $query) . "' AND trashed = false",
            'fields' => 'files(id,name,mimeType,webContentLink,webViewLink,fileExtension,modifiedTime,size,iconLink)',
            'spaces' => 'drive',
        ];

        if (!empty($shareddrives)) {
            $searchparams['supportsAllDrives'] = 'true';
            $searchparams['includeItemsFromAllDrives'] = 'true';
            $searchparams['corpora'] = 'allDrives';
        }

        // Assert that the call() method is being called twice with the given arguments consecutively. In the first
        // instance it is being called to fetch the shared drives (shared_drives_list), while in the second instance
        // to fetch the relevant drive contents (list) that match the search criteria. Also, define the returned
        // data objects by these calls.
        $callinvocations = $this->exactly(2);
        $servicemock->expects($callinvocations)
            ->method('call')
            ->willReturnCallback(function(string $method, array $params) use (
                $callinvocations,
                $shareddrives,
                $searccontents,
                $searchparams,
            ) {
                switch (self::getInvocationCount($callinvocations)) {
                    case 1:
                        $this->assertEquals('shared_drives_list', $method);

                        $this->assertEmpty($params);
                        return (object) [
                            'kind' => 'drive#driveList',
                            'nextPageToken' => 'd838181f30b0f5',
                            'drives' => $shareddrives,
                        ];
                    case 2:
                        $this->assertEquals('list', $method);
                        $this->assertEquals($searchparams, $params);

                        return (object) [
                            'files' => $searccontents,
                        ];
                }
            });

        // Construct the node path.
        $path = \repository_googledocs::REPOSITORY_ROOT_ID . '|' . urlencode('Google Drive') . '/' .
            \repository_googledocs::SEARCH_ROOT_ID . '|' . urlencode(
            get_string('searchfor', 'repository_googledocs') . " '{$query}'");

        $searchcontentobj = new googledocs_content_search($servicemock, $path, $sortcontent);
        $this->disallowedextensions = $filterextensions;
        $contentnodes = $searchcontentobj->get_content_nodes($query, [$this, 'filter']);

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
        $searchnodeid = \repository_googledocs::SEARCH_ROOT_ID;
        $searchforstring = get_string('searchfor', 'repository_googledocs');

        return [
            'Folders and files match the search criteria; shared drives exist; ordering applied.' =>
                [
                    'test',
                    true,
                    [],
                    [
                        self::create_google_drive_shared_drive_object('d85b21c0f86cb5', 'Shared Drive 1'),
                    ],
                    [
                        self::create_google_drive_file_object('d85b21c0f86cb0', 'Test file 3.pdf',
                            'application/pdf', 'pdf', '1000', '',
                            'https://drive.google.com/uc?id=d85b21c0f86cb0&export=download'),
                        self::create_google_drive_folder_object('0c4ad262c65333', 'Test folder 1'),
                        self::create_google_drive_file_object('bed5a0f08d412a', 'Test file 1.pdf',
                            'application/pdf', 'pdf'),
                        self::create_google_drive_folder_object('9c4ad262c65333', 'Test folder 2'),
                    ],
                    [
                        self::create_folder_content_node_array('0c4ad262c65333', 'Test folder 1',
                            "{$rootid}|Google+Drive/{$searchnodeid}|" . urlencode("{$searchforstring} 'test'")),
                        self::create_folder_content_node_array('9c4ad262c65333', 'Test folder 2',
                            "{$rootid}|Google+Drive/{$searchnodeid}|" . urlencode("{$searchforstring} 'test'")),
                        self::create_file_content_node_array('bed5a0f08d412a', 'Test file 1.pdf',
                            'Test file 1.pdf', null, '', 'https://googleusercontent.com/type/application/pdf',
                            '', 'download'),
                        self::create_file_content_node_array('d85b21c0f86cb0', 'Test file 3.pdf',
                            'Test file 3.pdf', '1000', '', 'https://googleusercontent.com/type/application/pdf',
                            'https://drive.google.com/uc?id=d85b21c0f86cb0&export=download', 'download'),
                    ],
                ],
            'Only folders match the search criteria; shared drives do not exist; ordering not applied.' =>
                [
                    'testing',
                    false,
                    [],
                    [],
                    [
                        self::create_google_drive_folder_object('0c4ad262c65333', 'Testing folder 3'),
                        self::create_google_drive_folder_object('d85b21c0f86cb0', 'Testing folder 1'),
                        self::create_google_drive_folder_object('bed5a0f08d412a', 'Testing folder 2'),
                    ],
                    [
                        self::create_folder_content_node_array('0c4ad262c65333', 'Testing folder 3',
                            "{$rootid}|Google+Drive/{$searchnodeid}|" . urlencode("{$searchforstring} 'testing'")),
                        self::create_folder_content_node_array('d85b21c0f86cb0', 'Testing folder 1',
                            "{$rootid}|Google+Drive/{$searchnodeid}|" . urlencode("{$searchforstring} 'testing'")),
                        self::create_folder_content_node_array('bed5a0f08d412a', 'Testing folder 2',
                            "{$rootid}|Google+Drive/{$searchnodeid}|" . urlencode("{$searchforstring} 'testing'")),
                    ],
                ],
            'Only files match the search criteria; shared drives exist; ordering not applied; filter .doc and .txt.' =>
                [
                    'root',
                    false,
                    ['doc', 'txt'],
                    [
                        self::create_google_drive_shared_drive_object('d85b21c0f86cb5', 'Shared Drive 1'),
                    ],
                    [
                        self::create_google_drive_file_object('d85b21c0f86cb0', 'Testing file 3.pdf',
                            'application/pdf', 'pdf', '1000'),
                        self::create_google_drive_file_object('a85b21c0f86cb0', 'Testing file 1.txt',
                            'text/plain', 'txt', '3000'),
                        self::create_google_drive_file_object('f85b21c0f86cb0', 'Testing file 2.doc',
                            'application/msword', 'doc', '2000'),
                    ],
                    [
                        self::create_file_content_node_array('d85b21c0f86cb0', 'Testing file 3.pdf',
                            'Testing file 3.pdf', '1000', '',
                            'https://googleusercontent.com/type/application/pdf', '', 'download'),
                    ],
                ],
            'No content that matches the search criteria; shared drives do not exist.' =>
                [
                    'root',
                    false,
                    [],
                    [],
                    [],
                    [],
                ],
        ];
    }
}
