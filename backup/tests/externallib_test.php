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

namespace core_backup;

use backup;
use core_backup_external;
use core_external\external_api;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/backup/externallib.php');

/**
 * Backup webservice tests.
 *
 * @package    core_backup
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Set up tasks for all tests.
     */
    protected function setUp(): void {
        global $CFG;

        $this->resetAfterTest(true);

        // Disable all loggers.
        $CFG->backup_error_log_logger_level = backup::LOG_NONE;
        $CFG->backup_output_indented_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $CFG->backup_database_logger_level = backup::LOG_NONE;
        $CFG->backup_file_logger_level_extra = backup::LOG_NONE;
    }

    /**
     * Test getting course copy progress.
     */
    public function test_get_copy_progress() {
        global $USER;

        $this->setAdminUser();

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $courseid = $course->id;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->courseid = $courseid;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = 1582376400;
        $formdata->enddate = 0;
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        $copydata = \copy_helper::process_formdata($formdata);
        $copydetails = \copy_helper::create_copy($copydata);
        $copydetails['operation'] = \backup::OPERATION_BACKUP;

        $params = array('copies' => $copydetails);
        $returnvalue = core_backup_external::get_copy_progress($params);

        // We need to execute the return values cleaning process to simulate the web service server.
        $returnvalue = external_api::clean_returnvalue(core_backup_external::get_copy_progress_returns(), $returnvalue);

        $this->assertEquals(\backup::STATUS_AWAITING, $returnvalue[0]['status']);
        $this->assertEquals(0, $returnvalue[0]['progress']);
        $this->assertEquals($copydetails['backupid'], $returnvalue[0]['backupid']);
        $this->assertEquals(\backup::OPERATION_BACKUP, $returnvalue[0]['operation']);

        // We are expecting trace output during this test.
        $this->expectOutputRegex("/$courseid/");

        // Execute adhoc task and create the copy.
        $now = time();
        $task = \core\task\manager::get_next_adhoc_task($now);
        $this->assertInstanceOf('\\core\\task\\asynchronous_copy_task', $task);
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);

        // Check the copy progress now.
        $params = array('copies' => $copydetails);
        $returnvalue = core_backup_external::get_copy_progress($params);

        $returnvalue = external_api::clean_returnvalue(core_backup_external::get_copy_progress_returns(), $returnvalue);

        $this->assertEquals(\backup::STATUS_FINISHED_OK, $returnvalue[0]['status']);
        $this->assertEquals(1, $returnvalue[0]['progress']);
        $this->assertEquals($copydetails['restoreid'], $returnvalue[0]['backupid']);
        $this->assertEquals(\backup::OPERATION_RESTORE, $returnvalue[0]['operation']);

    }

    /**
     * Test ajax submission of course copy process.
     */
    public function test_submit_copy_form() {
        global $DB;

        $this->setAdminUser();

        // Create a course with some availability data set.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $courseid = $course->id;

        // Moodle form requires this for validation.
        $sesskey = sesskey();
        $_POST['sesskey'] = $sesskey;

        // Mock up the form data for use in tests.
        $formdata = new \stdClass;
        $formdata->courseid = $courseid;
        $formdata->returnto = '';
        $formdata->returnurl = '';
        $formdata->sesskey = $sesskey;
        $formdata->_qf__core_backup_output_copy_form = 1;
        $formdata->fullname = 'foo';
        $formdata->shortname = 'bar';
        $formdata->category = 1;
        $formdata->visible = 1;
        $formdata->startdate = array('day' => 5, 'month' => 5, 'year' => 2020, 'hour' => 0, 'minute' => 0);
        $formdata->idnumber = 123;
        $formdata->userdata = 1;
        $formdata->role_1 = 1;
        $formdata->role_3 = 3;
        $formdata->role_5 = 5;

        $urlform = http_build_query($formdata, '', '&'); // Take the form data and url encode it.
        $jsonformdata = json_encode($urlform); // Take form string and JSON encode.

        $returnvalue = core_backup_external::submit_copy_form($jsonformdata);

        $returnjson = external_api::clean_returnvalue(core_backup_external::submit_copy_form_returns(), $returnvalue);
        $copyids = json_decode($returnjson, true);

        $backuprec = $DB->get_record('backup_controllers', array('backupid' => $copyids['backupid']));
        $restorerec = $DB->get_record('backup_controllers', array('backupid' => $copyids['restoreid']));

        // Check backup was completed successfully.
        $this->assertEquals(backup::STATUS_AWAITING, $backuprec->status);
        $this->assertEquals(0, $backuprec->progress);
        $this->assertEquals('backup', $backuprec->operation);

        // Check restore was completed successfully.
        $this->assertEquals(backup::STATUS_REQUIRE_CONV, $restorerec->status);
        $this->assertEquals(0, $restorerec->progress);
        $this->assertEquals('restore', $restorerec->operation);
    }
}
