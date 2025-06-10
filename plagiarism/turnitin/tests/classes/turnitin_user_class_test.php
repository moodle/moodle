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
 * Unit tests for (some of) plagiarism/turnitin/classes/modules/turnitin_user.class.php.
 *
 * @package    plagiarism_turnitin
 * @copyright  2018 Turnitin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/tests/generator/lib.php');
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');
require_once($CFG->dirroot . '/plagiarism/turnitin/classes/turnitin_user.class.php');
require_once($CFG->dirroot . '/mod/assign/externallib.php');

/**
 * Tests for Turnitin user class
 *
 * @package turnitin
 */
class plagiarism_turnitin_user_class_testcase extends plagiarism_turnitin_test_lib {

    public $faketiicomms;

    /**
     * Set Overwrite mtrace to avoid output during the tests.
     */
    public function setUp(): void {
        // Stub a fake tii comms.
        $this->faketiicomms = $this->getMockBuilder(turnitin_comms::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_get_moodle_user() {
        $this->resetAfterTest();

        $student = $this->getDataGenerator()->create_user();

        $turnitinuser = new turnitin_user(0, null, null, null, null);
        $response = $turnitinuser->get_moodle_user($student->id);

        // Check that we have an object back with user details. No need to check all params.
        $this->assertEquals($student->id, $response->id);
        $this->assertEquals('username1', $response->username);
    }

    public function test_get_pseudo_domain() {
        $this->resetAfterTest();

        $response = turnitin_user::get_pseudo_domain();
        $this->assertEquals(PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_DOMAIN, $response);
    }

    public function test_get_pseudo_firstname() {
        $this->resetAfterTest();

        $turnitinuser = new turnitin_user(0, null, null, null, null);
        $response = $turnitinuser->get_pseudo_firstname();
        $this->assertEquals(PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_FIRSTNAME, $response);
    }

    public function test_get_pseudo_lastname() {
        global $DB;
        $this->resetAfterTest();

        $student = $this->getDataGenerator()->create_user();
        $DB->insert_record('user_info_data', array('userid' => $student->id, 'fieldid' => 1, 'data' => 'Student', 'dataformat' => 0));

        set_config('plagiarism_turnitin_pseudolastname', 1, 'plagiarism_turnitin');
        set_config('plagiarism_turnitin_lastnamegen', 1, 'plagiarism_turnitin');

        $turnitinuser = new turnitin_user($student->id, null, null, null, null);
        $response = $turnitinuser->get_pseudo_lastname();
        $this->assertEquals(PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_FIRSTNAME, $response);
    }

    public function test_unlink_user() {
        global $DB;

        $this->resetAfterTest();

        $roles = array("Learner");
        $testuser = $this->make_test_users(1, $roles, 1);

        // Check that we have a user.
        $count = $DB->count_records('plagiarism_turnitin_users');
        $this->assertEquals(1, $count);

        // Unlink the user.
        $turnitinuser = new turnitin_user(0, null, null, null, null);
        $turnitinuser->unlink_user($testuser["joins"][0]);

        // We should have a Turnitin user ID of 0.
        $user = $DB->get_record('plagiarism_turnitin_users', array('id' => $testuser["joins"][0]));
        $this->assertEquals(0, $user->turnitin_uid);
    }
}
