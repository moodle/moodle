<?php
// This file is part of Moodle - https://moodle.org/
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

namespace assignfeedback_editpdf;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

/**
 * Unit tests for document services.
 *
 * @package    assignfeedback_editpdf
 * @category   test
 * @copyright  2022 Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \assignfeedback_editpdf\document_services
 */
final class document_services_test extends \advanced_testcase {
    use \mod_assign_test_generator;

    /**
     * Test that the save file method saves the file.
     */
    public function test_save_file_saves_the_file(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $method = new \ReflectionMethod('\assignfeedback_editpdf\document_services', 'save_file');

        $filearea = document_services::TMP_ROTATED_JPG_FILEAREA;
        $content = 'some random content';
        $tempfile = make_request_directory() . DIRECTORY_SEPARATOR . 'mock.file';
        file_put_contents($tempfile, $content);

        // Invoke the method and confirm, that the file is saved.
        $file1 = $method->invoke(null, $assign, $user->id, 1, $filearea, $tempfile);
        $this->assertInstanceOf('stored_file', $file1);
        $this->assertEquals(1, $DB->count_records('files', ['id' => $file1->get_id()]));

        // Invoke the method again and confirm, that exising file is returned.
        $file2 = $method->invoke(null, $assign, $user->id, 1, $filearea, $tempfile);
        $this->assertEquals($file1->get_id(), $file2->get_id());
    }

    /**
     * Test that save_rotated_image_file() method saves the file.
     */
    public function test_save_rotated_image_file_saves_the_file(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assign = $this->create_instance($course);
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $method = new \ReflectionMethod('\assignfeedback_editpdf\document_services', 'save_rotated_image_file');

        $imagecontent = file_get_contents($CFG->dirroot . '/lib/filestorage/tests/fixtures/testimage.png');
        $imageresource = imagecreatefromstring($imagecontent);

        // Invoke the method and confirm, that the file is saved.
        $file1 = $method->invoke(null, $assign, $user->id, 1, $imageresource, 'testimage.png');
        $this->assertInstanceOf('stored_file', $file1);
        $this->assertEquals(1, $DB->count_records('files', ['id' => $file1->get_id()]));

        // Invoke the method again and confirm, that exising file is returned.
        $file2 = $method->invoke(null, $assign, $user->id, 1, $imageresource, 'testimage.png');
        $this->assertEquals($file1->get_id(), $file2->get_id());
    }

    /**
     * Test that save_jpg_to_pdf() method safely rejects a non-JPEG file with a JPEG extension.
     */
    public function test_save_jpg_to_pdf_rejects_non_jpeg(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $user = $this->getDataGenerator()->create_and_enrol($course);

        $assign = $this->create_instance($course, [
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 1,
            'assignsubmission_file_maxsizebytes' => 1024 * 1024,
        ]);
        $fileplugin = $assign->get_plugin_by_type('assignsubmission', 'file');
        $generator->create_submission([
            'userid' => $user->id,
            'cmid' => $assign->get_course_module()->id,
            'file' => 'mod/assign/feedback/editpdf/tests/fixtures/heic.jpg',
        ]);
        $submission = $assign->get_user_submission($user->id, false);
        $files = $fileplugin->get_files($submission, $user);
        $this->assertEquals('image/jpeg', $files['/heic.jpg']->get_mimetype());

        // Invoke the save_jpg_to_pdf method expecting there to be no exceptions.
        $method = new \ReflectionMethod('\assignfeedback_editpdf\document_services', 'save_jpg_to_pdf');
        $retfile = $method->invoke(null, $assign, $user->id, 1, $files['/heic.jpg']);
        $this->assertNull($retfile);
        $this->assertDebuggingCalled("Could not convert {$files['/heic.jpg']->get_contenthash()} jpg to pdf: " .
            "TCPDF ERROR: [Image] Unable to get the size of the image: ", DEBUG_ALL);
    }

    /**
     * Test that get_combined_document_for_attempt() method rotates the image only once.
     */
    public function test_get_combined_document_for_attempt_rotates_image(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $assignparams = [
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 1,
            'assignsubmission_file_maxsizebytes' => 1024 * 1024,
        ];
        $assign = $this->create_instance($course, $assignparams);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);

        $notices = [];
        $submission = $assign->get_user_submission($student->id, true, 1);
        $data = (object) ['files_filemanager' => $submission->id];
        $assign->save_submission($data, $notices);

        // This image was manually rotated to be upside down. Also, Orientation, ExifImageWidth
        // and ExifImageLength EXIF tags were written into its metadata.
        // This is needed to make sure that this image will be rotated by stored_file::rotate_image()
        // and stored as a new rotated file.
        $filename = 'testimage_rotated.jpg';
        $filepath = $CFG->dirroot . '/lib/filestorage/tests/fixtures/' . $filename;
        $filerecord = [
            'contextid' => $assign->get_context()->id,
            'component' => 'assignsubmission_file',
            'filearea'  => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid'    => $submission->id,
            'filepath'  => '/',
            'filename'  => $filename,
        ];
        $fs = get_file_storage();
        $fs->create_file_from_pathname($filerecord, $filepath);

        $params = [
            'filearea' => document_services::TMP_ROTATED_JPG_FILEAREA,
            'component' => document_services::COMPONENT,
            'filename' => $filename,
        ];

        // Combine the document and get the rotated file.
        document_services::get_combined_document_for_attempt($assign, $student->id, 1);
        $records = $DB->get_records('files', $params);
        $this->assertCount(1, $records);
        $record1 = reset($records);

        // Polling file converters do this twice: one call to start a conversion and another one
        // to poll the converted file. So we combine the document again here.
        document_services::get_combined_document_for_attempt($assign, $student->id, 1);
        $records = $DB->get_records('files', $params);
        $this->assertCount(1, $records);
        $record2 = reset($records);

        // Confirm, that the second get_combined_document_for_attempt() call doesn't create new
        // rotated file and re-uses the one that was created as part of the first
        // get_combined_document_for_attempt() call.
        $this->assertEquals($record1->id, $record2->id);
    }
}
