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
 * PHPunit tests for external files API.
 *
 * @package    core_files
 * @category   external
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.6
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/files/externallib.php');

class core_files_externallib_testcase extends advanced_testcase {

    /*
     * Test core_files_external::upload().
     */

    public function test_upload() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_user::instance($USER->id);
        $contextid = $context->id;
        $component = "user";
        $filearea = "draft";
        $itemid = 0;
        $filepath = "/";
        $filename = "Simple.txt";
        $filecontent = base64_encode("Let us create a nice simple file");
        $contextlevel = null;
        $instanceid = null;
        $browser = get_file_browser();

        // Make sure no file exists.
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertEmpty($file);

        // Call the api to create a file.
        $fileinfo = core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);
        // Get the created draft item id.
        $itemid = $fileinfo['itemid'];

        // Make sure the file was created.
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);

        // Make sure no file exists.
        $filename = "Simple2.txt";
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertEmpty($file);

        // Call the api to create a file.
        $fileinfo = core_files_external::upload($contextid, $component, $filearea, $itemid,
                $filepath, $filename, $filecontent, $contextlevel, $instanceid);
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);

        // Let us try creating a file using contextlevel and instance id.
        $filename = "Simple5.txt";
        $contextid = 0;
        $contextlevel = "user";
        $instanceid = $USER->id;
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertEmpty($file);
        $fileinfo = core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);

        // Make sure the same file cannot be created again.
        $this->setExpectedException("moodle_exception");
        core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);
    }

    /*
     * Make sure only user component is allowed in  core_files_external::upload().
     */
    public function test_upload_param_component() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_user::instance($USER->id);
        $contextid = $context->id;
        $component = "backup";
        $filearea = "private";
        $itemid = 0;
        $filepath = "/";
        $filename = "Simple3.txt";
        $filecontent = base64_encode("Let us create a nice simple file");
        $contextlevel = null;
        $instanceid = null;

        // Make sure exception is thrown.
        $this->setExpectedException("coding_exception");
        core_files_external::upload($contextid, $component, $filearea, $itemid,
                $filepath, $filename, $filecontent, $contextlevel, $instanceid);
    }

    /*
     * Make sure only private or draft areas are allowed in  core_files_external::upload().
     */
    public function test_upload_param_area() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_user::instance($USER->id);
        $contextid = $context->id;
        $component = "user";
        $filearea = "draft";
        $itemid = file_get_unused_draft_itemid();
        $filepath = "/";
        $filename = "Simple4.txt";
        $filecontent = base64_encode("Let us create a nice simple file");
        $contextlevel = null;
        $instanceid = null;

        // Make sure the file is created.
        @core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath, $filename, $filecontent);
        $browser = get_file_browser();
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);
    }

    /*
     * Make sure core_files_external::upload() works without new parameters.
     */
    public function test_upload_without_new_param() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_user::instance($USER->id);
        $contextid = $context->id;
        $component = "user";
        $filearea = "private";
        $itemid = 0;
        $filepath = "/";
        $filename = "Simple4.txt";
        $filecontent = base64_encode("Let us create a nice simple file");

        @core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath, $filename, $filecontent);

        // Assert debugging called (deprecation warning).
        $this->assertDebuggingCalled();

        // Make sure the file is created.
        $browser = get_file_browser();
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);
    }

    /**
     * Test getting a list of files with and without a context ID.
     */
    public function test_get_files() {
        global $USER, $DB;

        $this->resetAfterTest();

        // Set the current user to be the administrator.
        $this->setAdminUser();
        $USER->email = 'test@moodle.com';

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->course = $course->id;
        $record->name = "Mod data upload test";
        $record->intro = "Some intro of some sort";

        // Create a database module.
        $module = $this->getDataGenerator()->create_module('data', $record);

        // Create a new field in the database activity.
        $field = data_get_field_new('file', $module);
        // Add more detail about the field.
        $fielddetail = new stdClass();
        $fielddetail->d = $module->id;
        $fielddetail->mode = 'add';
        $fielddetail->type = 'file';
        $fielddetail->sesskey = sesskey();
        $fielddetail->name = 'Upload file';
        $fielddetail->description = 'Some description';
        $fielddetail->param3 = '0';

        $field->define_field($fielddetail);
        $field->insert_field();
        $recordid = data_add_record($module);

        // File information for the database module record.
        $datacontent = array();
        $datacontent['fieldid'] = $field->field->id;
        $datacontent['recordid'] = $recordid;
        $datacontent['content'] = 'Simple4.txt';

        // Insert the information about the file.
        $contentid = $DB->insert_record('data_content', $datacontent);
        // Required information for uploading a file.
        $context = context_module::instance($module->cmid);
        $usercontext = context_user::instance($USER->id);
        $component = 'mod_data';
        $filearea = 'content';
        $itemid = $contentid;
        $filename = $datacontent['content'];
        $filecontent = base64_encode("Let us create a nice simple file.");

        $filerecord = array();
        $filerecord['contextid'] = $context->id;
        $filerecord['component'] = $component;
        $filerecord['filearea'] = $filearea;
        $filerecord['itemid'] = $itemid;
        $filerecord['filepath'] = '/';
        $filerecord['filename'] = $filename;

        // Create an area to upload the file.
        $fs = get_file_storage();
        // Create a file from the string that we made earlier.
        $file = $fs->create_file_from_string($filerecord, $filecontent);
        $timemodified = $file->get_timemodified();

        // Use the web service function to return the information about the file that we just uploaded.
        // The first time is with a valid context ID.
        $filename = '';
        $testfilelisting = core_files_external::get_files($context->id, $component, $filearea, $itemid, '/', $filename);

        // With the information that we have provided we should get an object exactly like the one below.
        $coursecontext = context_course::instance($course->id);
        $testdata = array();
        $testdata['parents'] = array();
        $testdata['parents']['0'] = array('contextid' => 1,
                                          'component' => null,
                                          'filearea' => null,
                                          'itemid' => null,
                                          'filepath' => null,
                                          'filename' => 'System');
        $testdata['parents']['1'] = array('contextid' => 3,
                                          'component' => null,
                                          'filearea' => null,
                                          'itemid' => null,
                                          'filepath' => null,
                                          'filename' => 'Miscellaneous');
        $testdata['parents']['2'] = array('contextid' => $coursecontext->id,
                                          'component' => null,
                                          'filearea' => null,
                                          'itemid' => null,
                                          'filepath' => null,
                                          'filename' => 'Test course 1');
        $testdata['parents']['3'] = array('contextid' => $context->id,
                                          'component' => null,
                                          'filearea' => null,
                                          'itemid' => null,
                                          'filepath' => null,
                                          'filename' => 'Mod data upload test (Database)');
        $testdata['parents']['4'] = array('contextid' => $context->id,
                                          'component' => 'mod_data',
                                          'filearea' => 'content',
                                          'itemid' => null,
                                          'filepath' => null,
                                          'filename' => 'Fields');
        $testdata['files'] = array();
        $testdata['files']['0'] = array('contextid' => $context->id,
                                        'component' => 'mod_data',
                                        'filearea' => 'content',
                                        'itemid' => $itemid,
                                        'filepath' => '/',
                                        'filename' => 'Simple4.txt',
                                        'url' => 'http://www.example.com/moodle/pluginfile.php/'.$context->id.'/mod_data/content/'.$itemid.'/Simple4.txt',
                                        'isdir' => false,
                                        'timemodified' => $timemodified);
        // Make sure that they are the same.
        $this->assertEquals($testdata, $testfilelisting);

        // Try again but without the context. Minus one signals the function to use other variables to obtain the context.
        $nocontext = -1;
        $modified = 0;
        // Context level and instance ID are used to determine what the context is.
        $contextlevel = 'module';
        $instanceid = $module->cmid;
        $testfilelisting = core_files_external::get_files($nocontext, $component, $filearea, $itemid, '/', $filename, $modified, $contextlevel, $instanceid);
        $this->assertEquals($testfilelisting, $testdata);
    }
}
