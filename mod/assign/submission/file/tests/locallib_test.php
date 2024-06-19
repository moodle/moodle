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

namespace assignsubmission_file;

use mod_assign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Unit tests for mod/assign/submission/file/locallib.php
 *
 * @package    assignsubmission_file
 * @copyright  2016 Cameron Ball
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class locallib_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Test submission_is_empty
     *
     * @dataProvider submission_is_empty_testcases
     * @param string $data The file submission data
     * @param bool $expected The expected return value
     */
    public function test_submission_is_empty($data, $expected): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
                'assignsubmission_file_enabled' => 1,
                'assignsubmission_file_maxfiles' => 12,
                'assignsubmission_file_maxsizebytes' => 10,
            ]);

        $this->setUser($student->id);

        $itemid = file_get_unused_draft_itemid();
        $submission = (object)['files_filemanager' => $itemid];
        $plugin = $assign->get_submission_plugin_by_type('file');

        if ($data) {
            $data += ['contextid' => \context_user::instance($student->id)->id, 'itemid' => $itemid];
            $fs = get_file_storage();
            $fs->create_file_from_string((object)$data, 'Content of ' . $data['filename']);
        }

        $result = $plugin->submission_is_empty($submission);
        $this->assertTrue($result === $expected);
    }

    /**
     * Test that an empty directory is is not detected as a valid submission by submission_is_empty.
     */
    public function test_submission_is_empty_directory_only(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
                'assignsubmission_file_enabled' => 1,
                'assignsubmission_file_maxfiles' => 12,
                'assignsubmission_file_maxsizebytes' => 10,
            ]);
        $this->setUser($student->id);
        $itemid = file_get_unused_draft_itemid();
        $submission = (object)['files_filemanager' => $itemid];
        $plugin = $assign->get_submission_plugin_by_type('file');
        $fs = get_file_storage();
        $fs->create_directory(
                \context_user::instance($student->id)->id,
                'user',
                'draft',
                $itemid,
                '/subdirectory/'
        );

        $this->assertTrue($plugin->submission_is_empty($submission));
    }

    /**
     * Test new_submission_empty
     *
     * @dataProvider submission_is_empty_testcases
     * @param string $data The file submission data
     * @param bool $expected The expected return value
     */
    public function test_new_submission_empty($data, $expected): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
                'assignsubmission_file_enabled' => 1,
                'assignsubmission_file_maxfiles' => 12,
                'assignsubmission_file_maxsizebytes' => 10,
            ]);

        $this->setUser($student);

        $itemid = file_get_unused_draft_itemid();
        $submission = (object) ['files_filemanager' => $itemid];

        if ($data) {
            $data += ['contextid' => \context_user::instance($student->id)->id, 'itemid' => $itemid];
            $fs = get_file_storage();
            $fs->create_file_from_string((object)$data, 'Content of ' . $data['filename']);
        }

        $result = $assign->new_submission_empty($submission);
        $this->assertTrue($result === $expected);
    }

    /**
     * Test that an empty directory is is not detected as a valid submission by new_submission_is_empty.
     */
    public function test_new_submission_empty_directory_only(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
                'assignsubmission_file_enabled' => 1,
                'assignsubmission_file_maxfiles' => 12,
                'assignsubmission_file_maxsizebytes' => 10,
            ]);
        $this->setUser($student->id);
        $itemid = file_get_unused_draft_itemid();
        $submission = (object)['files_filemanager' => $itemid];
        $plugin = $assign->get_submission_plugin_by_type('file');
        $fs = get_file_storage();
        $fs->create_directory(
                \context_user::instance($student->id)->id,
                'user',
                'draft',
                $itemid,
                '/subdirectory/'
        );

        $this->assertTrue($assign->new_submission_empty($submission));
    }

    /**
     * Dataprovider for the test_submission_is_empty testcase
     *
     * @return array of testcases
     */
    public static function submission_is_empty_testcases(): array {
        return [
            'With file' => [
                [
                    'component' => 'user',
                    'filearea' => 'draft',
                    'filepath' => '/',
                    'filename' => 'not_a_virus.exe'
                ],
                false
            ],
            'With file in directory' => [
                [
                    'component' => 'user',
                    'filearea' => 'draft',
                    'filepath' => '/subdir/',
                    'filename' => 'not_a_virus.exe'
                ],
                false
            ],
            'Without file' => [null, true]
        ];
    }

    /**
     * Test getting files from plugin submission
     */
    public function test_get_files(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course, [
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 2,
            'assignsubmission_file_maxsizebytes' => 512,
        ]);

        // Switch to student, create some dummy files, and submit data to plugin.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);

        $filerecord = [
            'contextid' => $assign->get_context()->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
        ];

        get_file_storage()->create_file_from_string($filerecord + ['filename' => 'File 1.txt'], 'File One');
        get_file_storage()->create_file_from_string($filerecord + ['filename' => 'File 2.txt'], 'File Two');

        /** @var \assign_submission_file $plugin */
        $plugin = $assign->get_submission_plugin_by_type('file');
        $plugin->save($submission, (object) []);

        // Ensure we retrieve back list of file submissions, deterministically ordered.
        $files = $plugin->get_files($submission, $student);
        $this->assertSame([
            '/File 1.txt' => 'File 1.txt',
            '/File 2.txt' => 'File 2.txt',
        ], array_map(fn(\stored_file $f) => $f->get_filename(), $files));
    }
}
