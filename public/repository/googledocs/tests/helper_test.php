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
require_once($CFG->dirroot . '/repository/googledocs/tests/repository_googledocs_testcase.php');
require_once($CFG->dirroot . '/repository/googledocs/lib.php');

/**
 * Class containing unit tests for the helper class.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class helper_test extends \repository_googledocs_testcase {

    /**
     * Test build_node_path().
     *
     * @dataProvider build_node_path_provider
     * @param string $id The ID of the node
     * @param string $name The name of the node
     * @param string $rootpath The path to append the node on
     * @param string $expected The expected node path
     */
    public function test_build_node_path(string $id, string $name, string $rootpath, string $expected): void {
        // Assert that the returned node path is equal to the expected one.
        $this->assertEquals($expected, helper::build_node_path($id, $name, $rootpath));
    }

    /**
     * Data provider for test_build_node_path().
     *
     * @return array
     */
    public static function build_node_path_provider(): array {

        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;
        $mydriveid = \repository_googledocs::MY_DRIVE_ROOT_ID;
        $shareddrivesid = \repository_googledocs::SHARED_DRIVES_ROOT_ID;

        return [
            'Generate the path for a node without a root path.' =>
                [
                    $rootid,
                    'Google Drive',
                    '',
                    "{$rootid}|Google+Drive",
                ],
            'Generate the path for a node without a name and root path.' =>
                [
                    $rootid,
                    '',
                    '',
                    $rootid,
                ],
            'Generate the path for a node without a name.' =>
                [
                    $mydriveid,
                    '',
                    "{$rootid}|Google+Drive",
                    "{$rootid}|Google+Drive/{$mydriveid}",
                ],
            'Generate the path for a node which has a name and root path.' =>
                [
                    '092cdf4732b9d5',
                    'Shared Drive 5',
                    "{$rootid}|Google+Drive/{$shareddrivesid}|Shared+Drives",
                    "{$rootid}|Google+Drive/{$shareddrivesid}|Shared+Drives/092cdf4732b9d5|Shared+Drive+5",
                ],
        ];
    }

    /**
     * Test explode_node_path().
     *
     * @dataProvider explode_node_path_provider
     * @param string $node The node string to extract information from
     * @param array $expected The expected array containing the information about the node
     */
    public function test_explode_node_path(string $node, array $expected): void {
        // Assert that the returned array is equal to the expected one.
        $this->assertEquals($expected, helper::explode_node_path($node));
    }

    /**
     * Data provider for test_explode_node_path().
     *
     * @return array
     */
    public static function explode_node_path_provider(): array {

        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;

        return [
            'Return the information for a path node that has a name.' =>
                [
                    "{$rootid}|Google+Drive",
                    [
                        0 => $rootid,
                        1 => 'Google Drive',
                        'id' => $rootid,
                        'name' => 'Google Drive',
                    ],
                ],
            'Return the information for a path node that does not have a name.' =>
                [
                    $rootid,
                    [
                        0 => $rootid,
                        1 => '',
                        'id' => $rootid,
                        'name' => '',
                    ],
                ],
        ];
    }

    /**
     * Test get_browser().
     *
     * @dataProvider get_browser_provider
     * @param string $nodepath The node path string
     * @param string $expected The expected browser class
     */
    public function test_get_browser(string $nodepath, string $expected): void {
        // The service (rest API) object is required by get_browser(), but not being used to determine which browser
        // object should be returned. Therefore, we can simply mock this object in this test.
        $servicemock = $this->createMock(rest::class);
        $browser = helper::get_browser($servicemock, $nodepath);

        // Assert that the returned browser class by get_browser() is equal to the expected one.
        $this->assertEquals($expected, get_class($browser));
    }

    /**
     * Data provider for test_get_browser().
     *
     * @return array
     */
    public static function get_browser_provider(): array {

        $rootid = \repository_googledocs::REPOSITORY_ROOT_ID;
        $mydriveid = \repository_googledocs::MY_DRIVE_ROOT_ID;
        $shareddrivesid = \repository_googledocs::SHARED_DRIVES_ROOT_ID;

        return [
            'Repository root level path.' =>
                [
                    "{$rootid}|Google+Drive",
                    \repository_googledocs\local\browser\googledocs_root_content::class,
                ],
            'My drive path.' =>
                [
                    "{$rootid}|Google+Drive/{$mydriveid}|My+Drive",
                    \repository_googledocs\local\browser\googledocs_drive_content::class,
                ],
            'Shared drives root path.' =>
                [
                    "{$rootid}|Google+Drive/{$shareddrivesid}|Shared+Drives",
                    \repository_googledocs\local\browser\googledocs_shared_drives_content::class,
                ],
            'Path within a shared drive.' =>
                [
                    "{$rootid}|Google+Drive/{$shareddrivesid}|Shared+Drives/092cdf4732b9d5|Shared+Drive+5",
                    \repository_googledocs\local\browser\googledocs_drive_content::class,
                ],
        ];
    }

    /**
     * Test get_node().
     *
     * @dataProvider get_node_provider
     * @param \stdClass $gdcontent The Google Drive content (file/folder) object
     * @param string $expected The expected content node class
     */
    public function test_get_node(\stdClass $gdcontent, string $expected): void {
        // The path is required by get_content_node(), but not being used to determine which content node
        // object should be returned. Therefore, we can just generate a dummy path.
        $path = \repository_googledocs::REPOSITORY_ROOT_ID . '|Google+Drive|' .
            \repository_googledocs::MY_DRIVE_ROOT_ID . '|My+Drive';
        $node = helper::get_node($gdcontent, $path);

        // Assert that the returned content node class by get_node() is equal to the expected one.
        $this->assertEquals($expected, get_class($node));
    }

    /**
     * Data provider for test_get_node().
     *
     * @return array
     */
    public static function get_node_provider(): array {
        return [
            'The content object represents a Google Drive folder.' =>
                [
                    self::create_google_drive_folder_object('e3b0c44298fc1c149', 'Folder', ''),
                    \repository_googledocs\local\node\folder_node::class,
                ],
            'The content object represents a Google Drive file.' =>
                [
                    self::create_google_drive_file_object('de04d58dc5ccc', 'File.pdf',
                        'application/pdf'),
                    \repository_googledocs\local\node\file_node::class,
                ],
        ];
    }

    /**
     * Test request() when an exception is thrown by the API call.
     *
     * @dataProvider request_exception_provider
     * @param \Exception $exception The exception thrown by the API call
     * @param \Exception $expected The expected exception thrown by request()
     */
    public function test_request_exception(\Exception $exception, \Exception $expected): void {
        // Mock the service object.
        $servicemock = $this->createMock(rest::class);

        // Assert that the call() method is being called only once with the given arguments.
        // Define the thrown exception by this call.
        $servicemock->expects($this->once())
            ->method('call')
            ->with('list', [])
            ->willThrowException($exception);

        $this->expectExceptionObject($expected);

        helper::request($servicemock, 'list', []);
    }

    /**
     * Data provider for test_request_exception().
     *
     * @return array
     */
    public static function request_exception_provider(): array {

        return [
            'The API call throws exception (status: 403; message: Access Not Configured).' =>
                [
                    new \Exception('Access Not Configured', 403),
                    new \repository_exception('servicenotenabled', 'repository_googledocs'),
                ],
            'The API call throws exception (status: 405; message: Access Not Configured).' =>
                [
                    new \Exception('Access Not Configured', 405),
                    new \Exception('Access Not Configured', 405),
                ],
            'The API call throws exception (status: 403; message: Access Forbidden).' =>
                [
                    new \Exception('Access Forbidden', 403),
                    new \Exception('Access Forbidden', 403),
                ],
            'The API call throws exception (status: 404; message: Not Found).' =>
                [
                    new \Exception('Not Found', 404),
                    new \Exception('Not Found', 404),
                ],
        ];
    }
}
