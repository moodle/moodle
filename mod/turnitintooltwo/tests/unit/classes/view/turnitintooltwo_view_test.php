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
 * Unit tests for (some of) mod/turnitintooltwo/view.php.
 *
 * @package    mod_turnitintooltwo
 * @copyright  2017 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/turnitintooltwo/tests/unit/generator/lib.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_view.class.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_assignment.class.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_user.class.php');
require_once($CFG->dirroot . '/mod/turnitintooltwo/turnitintooltwo_comms.class.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Tests for inbox
 *
 * @package turnitintooltwo
 */
class mod_turnitintooltwo_view_testcase extends test_lib {

    /**
     * Test that the page layout is set to standard so that the header displays.
     */

    public function test_output_header() {
        global $PAGE;
        $turnitintooltwoview = new turnitintooltwo_view();

        $pageurl = '/fake/url/';
        $pagetitle = 'Fake Title';
        $pageheading = 'Fake Heading';
        $turnitintooltwoview->output_header($pageurl, $pagetitle, $pageheading, true);

        $this->assertStringContainsString($pageurl, (string)$PAGE->url);
        $this->assertStringContainsString($pagetitle, $PAGE->title);
        $this->assertEquals($pageheading, $PAGE->heading);
    }

    /**
     * Test that the v1 migration tab is present in the settings tabs if v1 is installed
     * AND the tool has been activated.
     */
    public function test_draw_settings_menu_v1_installed() {
        global $DB;
        $this->resetAfterTest();
        $turnitintooltwoview = new turnitintooltwo_view();

        // If v1 is not installed then create a fake row to trick Moodle into thinking it's installed.
        $module = $DB->get_record('config_plugins', array('plugin' => 'mod_turnitintool'));
        if (!boolval($module)) {
            $module = new stdClass();
            $module->plugin = 'mod_turnitintool';
            $module->name = 'version';
            $module->value = 1001;
            $DB->insert_record('config_plugins', $module);
        }

        // Test that tab is present.
        $tabs = $turnitintooltwoview->draw_settings_menu('v1migration');
        $this->assertStringContainsString(get_string('v1migrationtitle', 'turnitintooltwo'), $tabs);
    }

    /**
     * Test that the v1 migration tab is not present in the settings tabs if v1 is not installed,
     */
    public function test_draw_settings_menu_v1_not_installed() {
        global $DB;
        $this->resetAfterTest();
        $turnitintooltwoview = new turnitintooltwo_view();

        // If v1 is installed then temporarily modify the plugin record to trick Moodle into thinking it's not installed.
        $module = $DB->get_record('config_plugins', array('plugin' => 'mod_turnitintool'));
        if (boolval($module)) {
            $tmpmodule = new stdClass();
            $tmpmodule->id = $module->id;
            $tmpmodule->plugin = 'mod_turnitintool_tmp';
            $DB->update_record('config_plugins', $tmpmodule);
        }

        $tabs = $turnitintooltwoview->draw_settings_menu('v1migration');
        $this->assertStringNotContainsString(get_string('v1migrationtitle', 'turnitintooltwo'), $tabs);

        if (boolval($module)) {
            $tmpmodule->plugin = 'mod_turnitintool';
            $DB->update_record('config_plugins', $tmpmodule);
        }
    }

	/**
	 * Test that the submissions table layout conforms to expectations when the user is an instructor.
	 *
	 * @return void
	 */
	public function test_inbox_table_structure_instructor() {
		global $DB;
		$this->resetAfterTest();
		$course = $this->getDataGenerator()->create_course();

		$turnitintooltwoassignment = $this->make_test_tii_assignment();

		$cmid = $this->make_test_module($turnitintooltwoassignment->turnitintooltwo->course,'turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->id);
		$cm = $DB->get_record("course_modules", array('id' => $cmid));

		$roles = array("Instructor");
		$testuser = $this->make_test_users(1, $roles);
		$turnitintooltwouser = $testuser['turnitintooltwo_users'][0];

		$partdetails = $this->make_test_parts('turnitintooltwo',$turnitintooltwoassignment->turnitintooltwo->id, 1);

		$turnitintooltwoview = new turnitintooltwo_view();
		$table = $turnitintooltwoview->init_submission_inbox($cm, $turnitintooltwoassignment, $partdetails, $turnitintooltwouser);

		$this->assertStringContainsString(get_string('studentlastname', 'turnitintooltwo'), $table, 'submission table did not contain expected text "'.get_string('studentlastname','turnitintooltwo').'"');
		$this->assertStringContainsString("<tbody class=\"empty\"><tr><td colspan=\"17\"></td></tr></tbody>", $table, 'datatable did not contain the expected empty tbody');
	}

	public function test_inbox_table_structure_student() {

		global $DB, $USER;
		$this->resetAfterTest();
		$_SESSION["unit_test"] = true;

		$USER->firstname = 'unit_test_first_654984';
		$USER->lastname = 'unit_test_last_654984';
		$USER->language = "en_US";
		$USER->firstnamephonetic = "";
		$USER->lastnamephonetic = "";
		$USER->middlename = "";
		$USER->alternatename = "";

		// Set Turnitin account values in config as they are used in comms.
		set_config('apiurl', 'http://invalid', 'turnitintooltwo');
		set_config('accountid', '1001', 'turnitintooltwo');
		set_config('secretkey', 'ABCDEFGH', 'turnitintooltwo');

		$course = $this->getDataGenerator()->create_course();

		$turnitintooltwoassignment = $this->make_test_tii_assignment();

		$cmid = $this->make_test_module($turnitintooltwoassignment->turnitintooltwo->course,'turnitintooltwo', $turnitintooltwoassignment->turnitintooltwo->id);
		$cm = $DB->get_record("course_modules", array('id' => $cmid));

		$roles = array("Learner");
		$testuser = $this->make_test_users(1, $roles);
		$turnitintooltwouser = $testuser['turnitintooltwo_users'][0];
		$moodleuser = $DB->get_record("turnitintooltwo_users", array("id" => $testuser['joins'][0]));

		$this->enrol_test_user($moodleuser->userid, $course->id, "Learner");

		$partdetails = $this->make_test_parts('turnitintooltwo',$turnitintooltwoassignment->turnitintooltwo->id, 1);

		$turnitintooltwoview = new turnitintooltwo_view();
		$table = $turnitintooltwoview->init_submission_inbox($cm, $turnitintooltwoassignment, $partdetails, $turnitintooltwouser);

		reset($partdetails);
		$partid = key($partdetails);

		$this->assertStringNotContainsString(get_string('studentlastname', 'turnitintooltwo'), $table, 'submission table contained unexpected text "'.get_string('studentlastname','turnitintooltwo').'"');
		$this->assertStringContainsString("<table class=\"mod_turnitintooltwo_submissions_data_table\" id=\"$partid\">", $table, 'Return did not include the expected table.');
		$this->assertStringContainsString("<td class=\"centered_cell cell c0\" style=\"\">$partid</td>", $table, 'Return did not contain the expected student row.');
	}

    /**
     * Test the method used to determine whether the delete link is shown to users.
     * The delete link should only be shown to instructors if there has been a submission made to Moodle or
     * to students if they have a submission and the due date hasn't passed or late submissions are allowed.
     *
     * @return void
     */
    public function test_show_delete_link() {
        $turnitintooltwoview = new turnitintooltwo_view();

        // Show delete link to instructor if a submission has been made.
        $submission = new stdClass();
        $submission->id = 1;
        $showdeletelink = $turnitintooltwoview->show_delete_link(true, $submission, time(), 1);
        $this->assertEquals(true, $showdeletelink);

        // Do not show delete link to instructor if no submission has been made.
        $submission = new stdClass();
        $showdeletelink = $turnitintooltwoview->show_delete_link(true, $submission, time(), 1);
        $this->assertEquals(false, $showdeletelink);

        // Show delete link to student if a submission has only been made to moodle and the due date hasn't passed.
        $submission = new stdClass();
        $submission->id = 1;
        $showdeletelink = $turnitintooltwoview->show_delete_link(false, $submission, time() + 1000, 1);
        $this->assertEquals(true, $showdeletelink);

        // Show delete link to student if a submission has only been made to moodle,
        // the due date has passed and late submissions are allowed.
        $showdeletelink = $turnitintooltwoview->show_delete_link(false, $submission, time() - 1, 1);
        $this->assertEquals(true, $showdeletelink);

        // Do not show delete link to student if a submission has only been made to moodle,
        // the due date has passed and late submissions are not allowed.
        $showdeletelink = $turnitintooltwoview->show_delete_link(false, $submission, time() - 1, 0);
        $this->assertEquals(false, $showdeletelink);

        // Do not show delete link to student if a submission has been sent to Turnitin.
        $submission->submission_objectid = 1;
        $showdeletelink = $turnitintooltwoview->show_delete_link(false, $submission, time(), 1);
        $this->assertEquals(false, $showdeletelink);
    }
}
