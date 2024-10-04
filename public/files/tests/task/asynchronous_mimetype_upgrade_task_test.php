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

namespace core_files\task;

/**
 * Tests for the asynchronous mimetype upgrade task.
 *
 * @package    core_files
 * @category   test
 * @copyright  2025 Daniel Ziegenberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \core_files\task\asynchronous_mimetype_upgrade_task
 */
final class asynchronous_mimetype_upgrade_task_test extends \advanced_testcase {

    /**
     * Data provider for test_upgrade_mimetype().
     *
     * @return array
     */
    public static function upgrade_mimetype_provider(): array {
        return [
            'Single file, one extension' => [
                'files' => [
                    'filename.extension1' => 'type/subtype',
                ],
                'mimetype' => 'type/subtype',
                'extensions' => ['extension1'],
            ],
            'Single file, one extension, desired extension substring of actual extension' => [
                'files' => [
                    'filename.extension1andsomemore' => 'text/plain',
                ],
                'mimetype' => 'type/subtype',
                'extensions' => ['extension1'],
            ],
            'Single file, one extension, filename same as desired extension' => [
                'files' => [
                    'extension1.bogus' => 'text/plain',
                ],
                'mimetype' => 'type/subtype',
                'extensions' => ['extension1'],
            ],
            'Multiple files, one extension' => [
                'files' => [
                    'filename_a.extension1' => 'type/subtype',
                    'filename_b.extension1' => 'type/subtype',
                ],
                'mimetype' => 'type/subtype',
                'extensions' => ['extension1'],
            ],
            'Multiple files, multiple extensions' => [
                'files' => [
                    'filename_a.extension1' => 'type/subtype',
                    'filename_b.extension1' => 'type/subtype',
                    'filename_a.extension2' => 'type/subtype',
                    'filename_b.extension2' => 'type/subtype',
                ],
                'mimetype' => 'type/subtype',
                'extensions' => [
                    'extension1',
                    'extension2',
                ],
            ],
            'Multiple files, multiple extensions, some unrelated' => [
                'files' => [
                    'filename_a.extension1' => 'type/subtype',
                    'filename_b.extension1' => 'type/subtype',
                    'filename_a.extension2' => 'type/subtype',
                    'filename_b.extension2' => 'type/subtype',
                    'filename_c.bogus' => 'text/plain',
                    'filename_c.bogus2' => 'text/plain',
                ],
                'mimetype' => 'type/subtype',
                'extensions' => [
                    'extension1',
                    'extension2',
                    'extension3',
                ],
            ],
        ];
    }

    /**
     * Test upgrading the mimetype of files.
     *
     * @dataProvider upgrade_mimetype_provider
     *
     * @param array $files
     * @param string $mimetype
     * @param array $extensions
     */
    public function test_execute(
        array $files,
        string $mimetype,
        array $extensions,
    ): void {

        global $DB;

        $this->resetAfterTest();

        // Create files with different extensions.
        $fs = get_file_storage();
        foreach (array_keys($files) as $filename) {
            $filerecord = [
                'contextid' => \core\context\system::instance()->id,
                'component' => 'core',
                'filearea'  => 'unittest',
                'itemid'    => 0,
                'filepath'  => '/',
                'filename'  => $filename,
            ];

            $fs->create_file_from_string($filerecord, 'content');
        }

        // Create and run the upgrade task.
        $task = new asynchronous_mimetype_upgrade_task();
        $task->set_custom_data([
            'mimetype' => $mimetype,
            'extensions' => $extensions,
        ]);

        ob_start();
        $task->execute();
        $output = ob_get_clean();

        // Check that the task output is correct.
        foreach ($extensions as $extension) {
            $this->assertStringContainsString(
                "Updating mime type for files with extension *.{$extension} to {$mimetype}",
                $output,
            );
            $countfiles = count(array_filter(
                array_keys($files),
                fn($filename) => str_ends_with($filename, $extension),
            ));
            $this->assertStringContainsString(
                "Updated {$countfiles} files with extension *.{$extension} to {$mimetype}",
                $output,
            );
        }

        // Check that the mimetype was updated and unrelated files remain untouched.
        foreach ($files as $filename => $exptectedmimetype) {
            $mimetypedb = $DB->get_field(
                table: 'files',
                return: 'mimetype',
                conditions: ['filename' => $filename],
            );
            $this->assertEquals(expected: $exptectedmimetype, actual: $mimetypedb);
        }
    }
}
