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
 * Database enrolment tests.
 *
 * @package    enrol_database
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_database;

use course_enrolment_manager;

defined('MOODLE_INTERNAL') || die();


/**
 * Database enrolment tests.
 *
 * @package    enrol_database
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    public static function tearDownAfterClass(): void {
        global $DB;
        // Apply sqlsrv native driver error and logging default
        // settings while finishing the AdoDB tests.
        if ($DB->get_dbfamily() === 'mssql') {
            sqlsrv_configure("WarningsReturnAsErrors", false);
            sqlsrv_configure("LogSubsystems", SQLSRV_LOG_SYSTEM_OFF);
            sqlsrv_configure("LogSeverity", SQLSRV_LOG_SEVERITY_ERROR);
        }
        parent::tearDownAfterClass();
    }

    /**
     * Test for getting user enrolment actions.
     */
    public function test_get_user_enrolment_actions(): void {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        // Set page URL to prevent debugging messages.
        $PAGE->set_url('/enrol/editinstance.php');

        $pluginname = 'database';

        // Only enable the database enrol plugin.
        $CFG->enrol_plugins_enabled = $pluginname;

        $generator = $this->getDataGenerator();

        // Get the enrol plugin.
        $plugin = enrol_get_plugin($pluginname);

        // Create a course.
        $course = $generator->create_course();
        // Enable this enrol plugin for the course.
        $plugin->add_instance($course);

        // Create a student.
        $student = $generator->create_user();
        // Enrol the student to the course.
        $generator->enrol_user($student->id, $course->id, 'student', $pluginname);

        // Teachers don't have enrol/database:unenrol capability by default. Login as admin for simplicity.
        $this->setAdminUser();
        require_once($CFG->dirroot . '/enrol/locallib.php');
        $manager = new course_enrolment_manager($PAGE, $course);
        $userenrolments = $manager->get_user_enrolments($student->id);
        $this->assertCount(1, $userenrolments);

        $ue = reset($userenrolments);
        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Database enrol has 0 enrol actions for active users.
        $this->assertCount(0, $actions);

        // Enrol actions for a suspended student.
        // Suspend the student.
        $ue->status = ENROL_USER_SUSPENDED;

        $actions = $plugin->get_user_enrolment_actions($manager, $ue);
        // Database enrol has enrol actions for suspended students -- unenrol.
        $this->assertCount(1, $actions);
    }
}
