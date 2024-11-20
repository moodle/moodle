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

namespace core_files;

use core_external\external_api;
use core_files\external\delete\draft;
use core_files\external\get\unused_draft;
use core_files_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/files/externallib.php');

/**
 * PHPunit tests for external files API.
 *
 * @package    core_files
 * @category   external
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.6
 */
class externallib_test extends \advanced_testcase {

    /*
     * Test core_files_external::upload().
     */

    public function test_upload(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = \context_user::instance($USER->id);
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
        $fileinfo = external_api::clean_returnvalue(core_files_external::upload_returns(), $fileinfo);
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
        $fileinfo = external_api::clean_returnvalue(core_files_external::upload_returns(), $fileinfo);
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
        $fileinfo = external_api::clean_returnvalue(core_files_external::upload_returns(), $fileinfo);
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);

        // Make sure the same file cannot be created again.
        $this->expectException("moodle_exception");
        core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);
    }

    /*
     * Make sure only user component is allowed in  core_files_external::upload().
     */
    public function test_upload_param_component(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = \context_user::instance($USER->id);
        $contextid = $context->id;
        $component = "backup";
        $filearea = "draft";
        $itemid = 0;
        $filepath = "/";
        $filename = "Simple3.txt";
        $filecontent = base64_encode("Let us create a nice simple file");
        $contextlevel = null;
        $instanceid = null;

        // Make sure exception is thrown.
        $this->expectException("coding_exception");
        core_files_external::upload($contextid, $component, $filearea, $itemid,
                $filepath, $filename, $filecontent, $contextlevel, $instanceid);
    }

    /*
     * Make sure only draft areas are allowed in  core_files_external::upload().
     */
    public function test_upload_param_area(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = \context_user::instance($USER->id);
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
        $fileinfo = core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath, $filename, $filecontent,
            'user', $USER->id);
        $fileinfo = external_api::clean_returnvalue(core_files_external::upload_returns(), $fileinfo);
        $browser = get_file_browser();
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);
    }

    /**
     * Test getting a list of files with and without a context ID.
     */
    public function test_get_files(): void {
        global $USER, $DB;

        $this->resetAfterTest();

        // Set the current user to be the administrator.
        $this->setAdminUser();
        $USER->email = 'test@example.com';

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $record = new \stdClass();
        $record->course = $course->id;
        $record->name = "Mod data upload test";
        $record->intro = "Some intro of some sort";

        // Create a database module.
        $module = $this->getDataGenerator()->create_module('data', $record);

        // Create a new field in the database activity.
        $field = data_get_field_new('file', $module);
        // Add more detail about the field.
        $fielddetail = new \stdClass();
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
        $context = \context_module::instance($module->cmid);
        $usercontext = \context_user::instance($USER->id);
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
        $timecreated = $file->get_timemodified();
        $filesize = $file->get_filesize();

        // Use the web service function to return the information about the file that we just uploaded.
        // The first time is with a valid context ID.
        $filename = '';
        $testfilelisting = core_files_external::get_files($context->id, $component, $filearea, $itemid, '/', $filename);
        $testfilelisting = external_api::clean_returnvalue(core_files_external::get_files_returns(), $testfilelisting);

        // With the information that we have provided we should get an object exactly like the one below.
        $coursecontext = \context_course::instance($course->id);
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
                                          'filename' => get_string('defaultcategoryname'));
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
                                        'url' => 'https://www.example.com/moodle/pluginfile.php/'.$context->id.'/mod_data/content/'.$itemid.'/Simple4.txt',
                                        'isdir' => false,
                                        'timemodified' => $timemodified,
                                        'timecreated' => $timecreated,
                                        'filesize' => $filesize,
                                        'author' => null,
                                        'license' => null
                                        );
        // Make sure that they are the same.
        $this->assertEquals($testdata, $testfilelisting);

        // Try again but without the context. Minus one signals the function to use other variables to obtain the context.
        $nocontext = -1;
        $modified = 0;
        // Context level and instance ID are used to determine what the context is.
        $contextlevel = 'module';
        $instanceid = $module->cmid;
        $testfilelisting = core_files_external::get_files($nocontext, $component, $filearea, $itemid, '/', $filename, $modified, $contextlevel, $instanceid);
        $testfilelisting = external_api::clean_returnvalue(core_files_external::get_files_returns(), $testfilelisting);

        $this->assertEquals($testfilelisting, $testdata);
    }

    /**
     * Test delete draft files
     */
    public function test_delete_draft_files(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Add files to user draft area.
        $draftitemid = file_get_unused_draft_itemid();
        $context = \context_user::instance($USER->id);
        $filerecordinline = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftitemid,
            'filepath'  => '/',
            'filename'  => 'faketxt.txt',
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'fake txt contents 1.');

        // Now create a folder with a file inside.
        $fs->create_directory($context->id, 'user', 'draft', $draftitemid, '/fakefolder/');
        $filerecordinline['filepath'] = '/fakefolder/';
        $filerecordinline['filename'] = 'fakeimage.png';
        $fs->create_file_from_string($filerecordinline, 'img...');

        // Check two files were created (one file and one directory).
        $files = core_files_external::get_files($context->id, 'user', 'draft', $draftitemid, '/', '');
        $files = external_api::clean_returnvalue(core_files_external::get_files_returns(), $files);
        $this->assertCount(2, $files['files']);

        // Check the folder has one file.
        $files = core_files_external::get_files($context->id, 'user', 'draft', $draftitemid, '/fakefolder/', '');
        $files = external_api::clean_returnvalue(core_files_external::get_files_returns(), $files);
        $this->assertCount(1, $files['files']);

        // Delete a file and a folder.
        $filestodelete = [
            ['filepath' => '/', 'filename' => 'faketxt.txt'],
            ['filepath' => '/fakefolder/', 'filename' => ''],
        ];
        $paths = draft::execute($draftitemid, $filestodelete);
        $paths = external_api::clean_returnvalue(draft::execute_returns(), $paths);

        // Check everything was deleted.
        $files = core_files_external::get_files($context->id, 'user', 'draft', $draftitemid, '/', '');
        $files = external_api::clean_returnvalue(core_files_external::get_files_returns(), $files);
        $this->assertCount(0, $files['files']);
    }

    /**
     * Test get_unused_draft_itemid.
     */
    public function test_get_unused_draft_itemid(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Add files to user draft area.
        $result = unused_draft::execute();
        $result = external_api::clean_returnvalue(unused_draft::execute_returns(), $result);

        $filerecordinline = [
            'contextid' => $result['contextid'],
            'component' => $result['component'],
            'filearea'  => $result['filearea'],
            'itemid'    => $result['itemid'],
            'filepath'  => '/',
            'filename'  => 'faketxt.txt',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'fake txt contents 1.');

        // Now create a folder with a file inside.
        $fs->create_directory($result['contextid'], $result['component'], $result['filearea'], $result['itemid'], '/fakefolder/');
        $filerecordinline['filepath'] = '/fakefolder/';
        $filerecordinline['filename'] = 'fakeimage.png';
        $fs->create_file_from_string($filerecordinline, 'img...');

        $context = \context_user::instance($USER->id);
        // Check two files were created (one file and one directory).
        $files = core_files_external::get_files($context->id, 'user', 'draft', $result['itemid'], '/', '');
        $files = external_api::clean_returnvalue(core_files_external::get_files_returns(), $files);
        $this->assertCount(2, $files['files']);
    }
}
