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
 * Class containing unit tests for the repository file node class.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class file_node_test extends \repository_googledocs_testcase {
    /**
     * Test create_node_array().
     *
     * @dataProvider create_node_array_provider
     * @param \stdClass $gdfile The Google Drive file object
     * @param array $configsettings The googledoc repository config settings that should be set
     * @param array|null $expected The expected repository file node array
     */
    public function test_create_node_array(\stdClass $gdfile, array $configsettings, ?array $expected): void {
        $this->resetAfterTest();
        // Set the required config settings.
        array_walk($configsettings, function($value, $name) {
            set_config($name, $value, 'googledocs');
        });

        $filenode = new file_node($gdfile);
        $filenodearray = $filenode->create_node_array();
        // Assert that the returned repository file node array by create_node_array() is equal to the expected one.
        $this->assertEquals($expected, $filenodearray);
    }

    /**
     * Data provider for test_create_node_array().
     *
     * @return array
     */
    public static function create_node_array_provider(): array {
        return [
            'Google Drive file with an extension.' =>
                [
                    self::create_google_drive_file_object('d85b21c0f86cb0', 'File.pdf',
                        'application/pdf', 'pdf', '1000', '01/01/21 0:30'),
                    [],
                    self::create_file_content_node_array('d85b21c0f86cb0', 'File.pdf', 'File.pdf', '1000',
                        '1609432200', 'https://googleusercontent.com/type/application/pdf', '',
                        'download'),
                ],
            'Google Drive file that has webContentLink and webViewLink.' =>
                [
                    self::create_google_drive_file_object('d85b21c0f86cb0', 'File.pdf',
                        'application/pdf', 'pdf', null, '',
                        'https://drive.google.com/uc?id=d85b21c0f86cb0&export=download',
                        'https://drive.google.com/file/d/d85b21c0f86cb0/view?usp=drivesdk'),
                    [
                        'documentformat' => 'rtf',
                    ],
                    self::create_file_content_node_array('d85b21c0f86cb0', 'File.pdf', 'File.pdf', null,
                        '', 'https://googleusercontent.com/type/application/pdf',
                        'https://drive.google.com/file/d/d85b21c0f86cb0/view?usp=drivesdk', 'download'),
                ],
            'Google Drive file that has webContentLink and no webViewLink.' =>
                [
                    self::create_google_drive_file_object('d85b21c0f86cb0', 'File.pdf',
                        'application/pdf', 'pdf', null, '',
                        'https://drive.google.com/uc?id=d85b21c0f86cb0&export=download', ''),
                    [],
                    self::create_file_content_node_array('d85b21c0f86cb0', 'File.pdf', 'File.pdf', null,
                        '', 'https://googleusercontent.com/type/application/pdf',
                        'https://drive.google.com/uc?id=d85b21c0f86cb0&export=download', 'download'),
                ],
            'Google Drive file without an extension (Google document file; documentformat config set to rtf).' =>
                [
                    self::create_google_drive_file_object('d85b21c0f86cb0', 'File',
                        'application/vnd.google-apps.document', null),
                    [
                        'documentformat' => 'rtf',
                    ],
                    self::create_file_content_node_array('d85b21c0f86cb0', 'File', 'File.gdoc', '', '',
                        'https://googleusercontent.com/type/application/vnd.google-apps.document', '',
                        'application/rtf', 'document'),
                ],
            'Google Drive file without an extension (Google presentation file; presentationformat config not set).' =>
                [
                    self::create_google_drive_file_object('d85b21c0f86cb0', 'File',
                        'application/vnd.google-apps.presentation', null),
                    [
                        'documentformat' => 'rtf',
                    ],
                    null,
                ],
            'Google Drive file without an extension (File type not supported).' =>
                [
                    self::create_google_drive_file_object('d85b21c0f86cb0', 'File',
                        'application/vnd.google-apps.unknownmimetype', null),
                    [],
                    null,
                ],
        ];
    }
}
