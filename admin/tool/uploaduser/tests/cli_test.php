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

namespace tool_uploaduser;

/**
 * Tests for CLI tool_uploaduser.
 *
 * @package    tool_uploaduser
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cli_test extends \advanced_testcase {

    /**
     * Generate cli_helper and mock $_SERVER['argv']
     *
     * @param array $mockargv
     * @return \tool_uploaduser\cli_helper
     */
    protected function construct_helper(array $mockargv = []) {
        if (array_key_exists('argv', $_SERVER)) {
            $oldservervars = $_SERVER['argv'];
        }
        $_SERVER['argv'] = array_merge([''], $mockargv);
        $clihelper = new cli_helper(\tool_uploaduser\local\text_progress_tracker::class);
        if (isset($oldservervars)) {
            $_SERVER['argv'] = $oldservervars;
        } else {
            unset($_SERVER['argv']);
        }
        return $clihelper;
    }

    /**
     * Tests simple upload with course enrolment and group allocation
     */
    public function test_upload_with_course_enrolment(): void {
        global $CFG;
        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Maths', 'shortname' => 'math102']);
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 1', 'idnumber' => 'S1']);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 3', 'idnumber' => 'S3']);

        $filepath = $CFG->dirroot.'/lib/tests/fixtures/upload_users.csv';

        $clihelper = $this->construct_helper(["--file=$filepath"]);
        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        // CLI output suggests that 2 users were created.
        $stats = $clihelper->get_stats();
        $this->assertEquals(2, preg_match_all('/New user/', $output));
        $this->assertEquals('Users created: 2', $stats[0]);

        // Tom Jones and Trent Reznor are enrolled into the course, first one to group $g1 and second to group $g2.
        $enrols = array_values(enrol_get_course_users($course->id));
        $this->assertEqualsCanonicalizing(['reznor', 'jonest'], [$enrols[0]->username, $enrols[1]->username]);
        $g1members = groups_get_groups_members($g1->id);
        $this->assertEquals(1, count($g1members));
        $this->assertEquals('Jones', $g1members[key($g1members)]->lastname);
        $g2members = groups_get_groups_members($g2->id);
        $this->assertEquals(1, count($g2members));
        $this->assertEquals('Reznor', $g2members[key($g2members)]->lastname);
    }

    /**
     * Test applying defaults during the user upload
     */
    public function test_upload_with_applying_defaults(): void {
        global $CFG;
        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Maths', 'shortname' => 'math102']);
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 1', 'idnumber' => 'S1']);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 3', 'idnumber' => 'S3']);

        $filepath = $CFG->dirroot.'/lib/tests/fixtures/upload_users.csv';

        $clihelper = $this->construct_helper(["--file=$filepath", '--city=Brighton', '--department=Purchasing']);
        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        // CLI output suggests that 2 users were created.
        $stats = $clihelper->get_stats();
        $this->assertEquals(2, preg_match_all('/New user/', $output));
        $this->assertEquals('Users created: 2', $stats[0]);

        // Users have default values applied.
        $user1 = \core_user::get_user_by_username('jonest');
        $this->assertEquals('Brighton', $user1->city);
        $this->assertEquals('Purchasing', $user1->department);
    }

    /**
     * User upload with user profile fields
     */
    public function test_upload_with_profile_fields(): void {
        global $CFG;
        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'superfield', 'name' => 'Super field',
            'datatype' => 'text', 'signup' => 1, 'visible' => 1, 'required' => 1, 'sortorder' => 1]);

        $filepath = $CFG->dirroot.'/lib/tests/fixtures/upload_users_profile.csv';

        $clihelper = $this->construct_helper(["--file=$filepath"]);
        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        // CLI output suggests that 2 users were created.
        $stats = $clihelper->get_stats();
        $this->assertEquals(2, preg_match_all('/New user/', $output));
        $this->assertEquals('Users created: 2', $stats[0]);

        // Created users have data in the profile fields.
        $user1 = \core_user::get_user_by_username('reznort');
        $profilefields1 = profile_user_record($user1->id);
        $this->assertObjectHasProperty('superfield', $profilefields1);
        $this->assertEquals('Loves cats', $profilefields1->superfield);
    }

    /**
     * Testing that help for CLI does not throw errors
     */
    public function test_cli_help(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $clihelper = $this->construct_helper(["--help"]);
        ob_start();
        $clihelper->print_help();
        $output = ob_get_contents();
        ob_end_clean();

        // Basically a test that everything can be parsed and displayed without errors. Check that some options are present.
        $this->assertEquals(1, preg_match('/--delimiter_name=VALUE/', $output));
        $this->assertEquals(1, preg_match('/--uutype=VALUE/', $output));
        $this->assertEquals(1, preg_match('/--auth=VALUE/', $output));
    }

    /**
     * Testing skipped user when one exists
     */
    public function test_create_when_user_exists(): void {
        global $CFG;
        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Maths', 'shortname' => 'math102']);
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 1', 'idnumber' => 'S1']);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 3', 'idnumber' => 'S3']);

        // Create a user with username jonest.
        $user1 = $this->getDataGenerator()->create_user(['username' => 'jonest', 'email' => 'jonest@someplace.edu']);

        $filepath = $CFG->dirroot.'/lib/tests/fixtures/upload_users.csv';

        $clihelper = $this->construct_helper(["--file=$filepath"]);
        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        // CLI output suggests that 1 user was created and 1 skipped.
        $stats = $clihelper->get_stats();
        $this->assertEquals(1, preg_match_all('/New user/', $output));
        $this->assertEquals('Users created: 1', $stats[0]);
        $this->assertEquals('Users skipped: 1', $stats[1]);

        // Trent Reznor is enrolled into the course, Tom Jones is not!
        $enrols = array_values(enrol_get_course_users($course->id));
        $this->assertEqualsCanonicalizing(['reznor'], [$enrols[0]->username]);
    }

    /**
     * Testing update mode - do not update user records but allow enrolments
     */
    public function test_enrolments_when_user_exists(): void {
        global $CFG;
        require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/locallib.php');

        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Maths', 'shortname' => 'math102']);
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 1', 'idnumber' => 'S1']);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 3', 'idnumber' => 'S3']);

        // Create a user with username jonest.
        $this->getDataGenerator()->create_user(['username' => 'jonest', 'email' => 'jonest@someplace.edu',
            'firstname' => 'OLDNAME']);

        $filepath = $CFG->dirroot.'/lib/tests/fixtures/upload_users.csv';

        $clihelper = $this->construct_helper(["--file=$filepath", '--uutype='.UU_USER_UPDATE]);
        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        // CLI output suggests that 1 user was created and 1 skipped.
        $stats = $clihelper->get_stats();
        $this->assertEquals(0, preg_match_all('/New user/', $output));
        $this->assertEquals('Users updated: 0', $stats[0]);
        $this->assertEquals('Users skipped: 1', $stats[1]);

        // Tom Jones is enrolled into the course.
        $enrols = array_values(enrol_get_course_users($course->id));
        $this->assertEqualsCanonicalizing(['jonest'], [$enrols[0]->username]);
        // User reznor is not created.
        $this->assertFalse(\core_user::get_user_by_username('reznor'));
        // User jonest is not updated.
        $this->assertEquals('OLDNAME', \core_user::get_user_by_username('jonest')->firstname);
    }

    /**
     * Testing update mode - update user records and perform enrolments.
     */
    public function test_udpate_user(): void {
        global $CFG;
        require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/locallib.php');

        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Maths', 'shortname' => 'math102']);
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 1', 'idnumber' => 'S1']);
        $g2 = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Section 3', 'idnumber' => 'S3']);

        // Create a user with username jonest.
        $this->getDataGenerator()->create_user(['username' => 'jonest',
            'email' => 'jonest@someplace.edu', 'firstname' => 'OLDNAME']);

        $filepath = $CFG->dirroot.'/lib/tests/fixtures/upload_users.csv';

        $clihelper = $this->construct_helper(["--file=$filepath", '--uutype='.UU_USER_UPDATE,
            '--uuupdatetype='.UU_UPDATE_FILEOVERRIDE]);
        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        // CLI output suggests that 1 user was created and 1 skipped.
        $stats = $clihelper->get_stats();
        $this->assertEquals(0, preg_match_all('/New user/', $output));
        $this->assertEquals('Users updated: 1', $stats[0]);
        $this->assertEquals('Users skipped: 1', $stats[1]);

        // Tom Jones is enrolled into the course.
        $enrols = array_values(enrol_get_course_users($course->id));
        $this->assertEqualsCanonicalizing(['jonest'], [$enrols[0]->username]);
        // User reznor is not created.
        $this->assertFalse(\core_user::get_user_by_username('reznor'));
        // User jonest is updated, new first name is Tom.
        $this->assertEquals('Tom', \core_user::get_user_by_username('jonest')->firstname);
    }
}
