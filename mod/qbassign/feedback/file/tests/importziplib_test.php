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

/**
 * Unit tests for importziplib.
 *
 * @package    qbassignfeedback_file
 * @copyright  2020 Eric Merrill <merrill@oakland.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbassignfeedback_file;

use mod_qbassign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/tests/generator.php');
require_once($CFG->dirroot . '/mod/qbassign/feedback/file/importziplib.php');

/**
 * Unit tests for importziplib.
 *
 * @package    qbassignfeedback_file
 * @copyright  2020 Eric Merrill <merrill@oakland.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class importziplib_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_qbassign_test_generator;

    /**
     * Test the qbassignfeedback_file_zip_importer->is_valid_filename_for_import() method.
     */
    public function test_is_valid_filename_for_import() {
        // Do the initial qbassign setup.
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $qbassign = $this->create_instance($course, [
                'qbassignsubmission_onlinetex_enabled' => 1,
                'qbassignfeedback_file_enabled' => 1,
            ]);

        // Create an online text submission.
        $this->add_submission($student, $qbassign);

        // Now onto the file work.
        $fs = get_file_storage();

        // Setup a basic file we will work with. We will keep renaming and repathing it.
        $record = new \stdClass;
        $record->contextid = $qbassign->get_context()->id;
        $record->component = 'qbassignfeedback_file';
        $record->filearea  = qbassignFEEDBACK_FILE_FILEAREA;
        $record->itemid    = $qbassign->get_user_grade($student->id, true)->id;
        $record->filepath  = '/';
        $record->filename  = '1.txt';
        $record->source    = 'test';
        $file = $fs->create_file_from_string($record, 'file content');

        // The importer we will use.
        $importer = new \qbassignfeedback_file_zip_importer();

        // Setup some variable we use.
        $user = null;
        $plugin = null;
        $filename = '';

        $allusers = $qbassign->list_participants(0, false);
        $participants = array();
        foreach ($allusers as $user) {
            $participants[$qbassign->get_uniqueid_for_user($user->id)] = $user;
        }

        $file->rename('/import/', '.hiddenfile');
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        $file->rename('/import/', '~hiddenfile');
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        $file->rename('/import/some_path_here/', 'RandomFile.txt');
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        $file->rename('/import/', '~hiddenfile');
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        // Get the students qbassign id.
        $studentid = $qbassign->get_uniqueid_for_user($student->id);

        // Submissions are identified with the format:
        // StudentName_StudentID_PluginType_Plugin_FilePathAndName.

        // Test a string student id.
        $badname = 'Student Name_StringID_qbassignsubmission_file_My_cool_filename.txt';
        $file->rename('/import/', $badname);
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        // Test an invalid student id.
        $badname = 'Student Name_' . ($studentid + 100) . '_qbassignsubmission_file_My_cool_filename.txt';
        $file->rename('/import/', $badname);
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        // Test an invalid submission plugin.
        $badname = 'Student Name_' . $studentid . '_qbassignsubmission_noplugin_My_cool_filename.txt';
        $file->rename('/import/', $badname);
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertFalse($result);

        // Test a basic, good file.
        $goodbase = 'Student Name_' . $studentid . '_qbassignsubmission_file_';
        $file->rename('/import/', $goodbase . "My_cool_filename.txt");
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertTrue($result);
        $this->assertEquals($participants[$studentid], $user);
        $this->assertEquals('My_cool_filename.txt', $filename);
        $this->assertInstanceOf(\qbassign_submission_file::class, $plugin);

        // Test another good file, with some additional path and underscores.
        $user = null;
        $plugin = null;
        $filename = '';
        $file->rename('/import/some_path_here/' . $goodbase . '/some_path/', 'My File.txt');
        $result = $importer->is_valid_filename_for_import($qbassign, $file, $participants, $user, $plugin, $filename);
        $this->assertTrue($result);
        $this->assertEquals($participants[$studentid], $user);
        $this->assertEquals('/some_path/My File.txt', $filename);
        $this->assertInstanceOf(\qbassign_submission_file::class, $plugin);
    }
}
