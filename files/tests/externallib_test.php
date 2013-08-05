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
        $filearea = "private";
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
        core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);

        // Make sure the file was created.
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);

        // Make sure no file exists.
        $itemid = 2;
        $filename = "Simple2.txt";
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertEmpty($file);

        // Call the api to create a file.
        $fileinfo = core_files_external::upload($contextid, $component, $filearea, $itemid,
                $filepath, $filename, $filecontent, $contextlevel, $instanceid);

        // Make sure itemid is always set to 0.
        $this->assertEquals(0, $fileinfo['itemid']);

        // Let us try creating a file using contextlevel and instance id.
        $itemid = 0;
        $filename = "Simple5.txt";
        $contextid = 0;
        $contextlevel = "user";
        $instanceid = $USER->id;
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertEmpty($file);
        $fileinfo = core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);
        $this->assertEmpty($file);

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
     * Make sure only private area is allowed in  core_files_external::upload().
     */
    public function test_upload_param_area() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $context = context_user::instance($USER->id);
        $contextid = $context->id;
        $component = "user";
        $filearea = "draft";
        $itemid = 0;
        $filepath = "/";
        $filename = "Simple4.txt";
        $filecontent = base64_encode("Let us create a nice simple file");
        $contextlevel = null;
        $instanceid = null;

        // Make sure exception is thrown.
        $this->setExpectedException("coding_exception");
        core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath,
                $filename, $filecontent, $contextlevel, $instanceid);
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

        // Make sure the file is created.
        @core_files_external::upload($contextid, $component, $filearea, $itemid, $filepath, $filename, $filecontent);
        $browser = get_file_browser();
        $file = $browser->get_file_info($context, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEmpty($file);
    }
}