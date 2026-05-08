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

use advanced_testcase;
use context_system;
use context_course;
use context_coursecat;
use stdClass;
use tool_uploaduser\cli_helper;
use tool_uploaduser\local\text_progress_tracker;

/**
 * Class upload_users_test
 *
 * @package    tool_uploaduser
 * @copyright  2020 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class upload_users_test extends advanced_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/{$CFG->admin}/tool/uploaduser/locallib.php");
        parent::setUpBeforeClass();
    }

    /**
     * Test upload users, enrol and role assignation
     * @covers \tool_uploadusers::process
     */
    public function test_user_can_upload_with_course_enrolment(): void {

        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        // Create category and course.
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $course = $this->getDataGenerator()->create_course(['shortname' => 'course01', 'category' => $coursecat->id]);
        $coursecontext = context_course::instance($course->id);

        // Create user.
        $user = $this->getDataGenerator()->create_user();

        // Create role with capability to upload CSV files, and assign this role to user.
        $uploadroleid = create_role('upload role', 'uploadrole', '');
        set_role_contextlevels($uploadroleid, [CONTEXT_SYSTEM]);
        $systemcontext = context_system::instance();
        assign_capability('moodle/site:uploadusers', CAP_ALLOW, $uploadroleid, $systemcontext->id);
        $this->getDataGenerator()->role_assign($uploadroleid, $user->id, $systemcontext->id);

        // Create role with some of allowed capabilities to enrol users, and assign this role to user.
        $enrolroleid = create_role('enrol role', 'enrolrole', '');
        set_role_contextlevels($enrolroleid, [CONTEXT_COURSECAT]);
        assign_capability('enrol/manual:enrol', CAP_ALLOW, $enrolroleid, $coursecatcontext->id);
        assign_capability('moodle/course:enrolreview', CAP_ALLOW, $enrolroleid, $coursecatcontext->id);
        assign_capability('moodle/role:assign', CAP_ALLOW, $enrolroleid, $coursecatcontext->id);
        $this->getDataGenerator()->role_assign($enrolroleid, $user->id, $coursecatcontext->id);

        // User makes assignments.
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        core_role_set_assign_allowed($enrolroleid, $studentrole->id);

        // Flush accesslib.
        accesslib_clear_all_caches_for_unit_testing();

        // Process CSV file as user.
        $csv = <<<EOF
username,firstname,lastname,email,course1,role1
student1,Student,One,s1@example.com,{$course->shortname},{$studentrole->shortname}
student2,Student,Two,s2@example.com,{$course->shortname},teacher
EOF;
        $this->setUser($user);
        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_ADDNEW]);

        $this->assertStringContainsString('Enrolled in "course01" as "student"', $output);
        $this->assertStringContainsString('Unknown role "teacher"', $output);

        // Check user creation, enrolment and role assignation.
        $this->assertEquals(1, count_enrolled_users($coursecontext));

        $usersasstudent = get_role_users($studentrole->id, $coursecontext);
        $this->assertCount(1, $usersasstudent);
        $this->assertEquals('student1', reset($usersasstudent)->username);
    }

    /**
     * Test upload users, enrol and assign default role from manual enrol plugin.
     * @covers \tool_uploadusers::process
     */
    public function test_user_can_upload_with_course_enrolment_default_role(): void {

        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        // Create category and courses.
        $coursecat = $this->getDataGenerator()->create_category();
        $coursecatcontext = context_coursecat::instance($coursecat->id);
        $course1 = $this->getDataGenerator()->create_course(['shortname' => 'course01', 'category' => $coursecat->id]);
        $course1context = context_course::instance($course1->id);
        // Change the default role to 'teacher'.
        set_config('roleid', 4, 'enrol_manual');
        $course2 = $this->getDataGenerator()->create_course(['shortname' => 'course02', 'category' => $coursecat->id]);
        $course2context = context_course::instance($course2->id);

        // Create user.
        $user = $this->getDataGenerator()->create_user();

        // Create role with capability to upload CSV files, and assign this role to user.
        $uploadroleid = create_role('upload role', 'uploadrole', '');
        set_role_contextlevels($uploadroleid, [CONTEXT_SYSTEM]);
        $systemcontext = context_system::instance();
        assign_capability('moodle/site:uploadusers', CAP_ALLOW, $uploadroleid, $systemcontext->id);
        $this->getDataGenerator()->role_assign($uploadroleid, $user->id, $systemcontext->id);

        // Create role with some of allowed capabilities to enrol users, and assign this role to user.
        $enrolroleid = create_role('enrol role', 'enrolrole', '');
        set_role_contextlevels($enrolroleid, [CONTEXT_COURSECAT]);
        assign_capability('enrol/manual:enrol', CAP_ALLOW, $enrolroleid, $coursecatcontext->id);
        assign_capability('moodle/course:enrolreview', CAP_ALLOW, $enrolroleid, $coursecatcontext->id);
        assign_capability('moodle/role:assign', CAP_ALLOW, $enrolroleid, $coursecatcontext->id);
        $this->getDataGenerator()->role_assign($enrolroleid, $user->id, $coursecatcontext->id);

        // User makes assignments.
        $studentarch = get_archetype_roles('student');
        $studentrole = array_shift($studentarch);
        core_role_set_assign_allowed($enrolroleid, $studentrole->id);

        // Flush accesslib.
        accesslib_clear_all_caches_for_unit_testing();

        // Process CSV file (no roles specified) as user.
        $csv = <<<EOF
username,firstname,lastname,email,course1,role1
student1,Student,One,s1@example.com,{$course1->shortname},
student2,Student,Two,s2@example.com,{$course2->shortname},
EOF;
        $this->setUser($user);
        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_ADDNEW]);

        $this->assertStringContainsString('Enrolled in "course01" as "student"', $output);
        // This $user cannot assign teacher role.
        $this->assertStringContainsString('Unknown role "teacher"', $output);

        // Check user creation, enrolment and role assignation.
        $this->assertEquals(1, count_enrolled_users($course1context));
        // This $user cannot enrol anyone as teacher.
        $this->assertEquals(0, count_enrolled_users($course2context));

        // Test user is enrolled as default-manual-enrol-plugin role.
        $manualenrolinstance = new stdClass;
        $enrolinstances = enrol_get_instances($course1->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol === 'manual') {
                $manualenrolinstance = $courseenrolinstance;
                break;
            }
        }
        $defaulroleidexpected = $manualenrolinstance->roleid ?? 0;
        // The default role of course01 is student, id 5.
        $this->assertEquals(5, $defaulroleidexpected);

        $usersasdefaultrole = get_role_users($defaulroleidexpected, $course1context);
        $this->assertCount(1, $usersasdefaultrole);
        $this->assertEquals('student1', reset($usersasdefaultrole)->username);
    }

    /**
     * Test that invalid data contained in uploaded CSV triggers appropriate warnings
     */
    public function test_user_upload_user_validate(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $csv = <<<EOF
username,firstname,lastname,email,country
student1,Student,One,s1@example.com,Wales
EOF;

        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_ADDNEW]);

        // We should get the debugging from the user class itself, as well as warning in the output regarding the same.
        $this->assertDebuggingCalled('The property \'country\' has invalid data and has been cleaned.');
        $this->assertStringContainsString('Incorrect data (country) found for user student1. ' .
            'This data has been corrected or deleted.', $output);
    }

    /**
     * Generate cli_helper and mock $_SERVER['argv']
     *
     * @param string $filecontent
     * @param array $mockargv
     * @return string
     */
    protected function process_csv_upload(string $filecontent, array $mockargv = []): string {
        $filepath = make_request_directory() . '/upload.csv';
        file_put_contents($filepath, $filecontent);
        $mockargv[] = "--file={$filepath}";

        if (array_key_exists('argv', $_SERVER)) {
            $oldservervars = $_SERVER['argv'];
        }

        $_SERVER['argv'] = array_merge([''], $mockargv);
        $clihelper = new cli_helper(text_progress_tracker::class);

        if (isset($oldservervars)) {
            $_SERVER['argv'] = $oldservervars;
        } else {
            unset($_SERVER['argv']);
        }

        ob_start();
        $clihelper->process();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * Test that uploading users respects unique custom profile field constraints:
     * - Users with different unique values are all created.
     * - Duplicate values within the same CSV are rejected (only first is created).
     * - Values that already exist in the database are rejected.
     * - Updating a user with their own existing unique value is not rejected as a duplicate.
     *
     * @covers \tool_uploaduser\process::process_line
     */
    public function test_upload_users_unique_profile_field_no_duplicates(): void {
        global $DB;

        $this->resetAfterTest();
        set_config('passwordpolicy', 0);
        $this->setAdminUser();

        // Create a unique custom profile field.
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'uniquecode',
            'name' => 'Unique Code',
            'datatype' => 'text',
            'forceunique' => 1,
        ]);

        // 1. Upload users with different unique values — all should be created.
        $csv = <<<EOF
username,firstname,lastname,email,profile_field_uniquecode
user1,First,User,user1@example.com,CODE001
user2,Second,User,user2@example.com,CODE002
EOF;

        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_ADDNEW]);

        $user1 = $DB->get_record('user', ['username' => 'user1']);
        $this->assertNotEmpty($user1, 'User1 should be created');
        $this->assertEquals('CODE001', profile_user_record($user1->id)->uniquecode);

        $user2 = $DB->get_record('user', ['username' => 'user2']);
        $this->assertNotEmpty($user2, 'User2 should be created');
        $this->assertEquals('CODE002', profile_user_record($user2->id)->uniquecode);

        // 2. Upload users where two rows share the same unique value — only the first should be created.
        $csv = <<<EOF
username,firstname,lastname,email,profile_field_uniquecode
user3,Third,User,user3@example.com,CODE003
user4,Fourth,User,user4@example.com,CODE003
EOF;

        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_ADDNEW]);

        $user3 = $DB->get_record('user', ['username' => 'user3']);
        $this->assertNotEmpty($user3, 'User3 should be created (first with CODE003)');
        $this->assertEquals('CODE003', profile_user_record($user3->id)->uniquecode);

        $user4 = $DB->get_record('user', ['username' => 'user4']);
        $this->assertEmpty($user4, 'User4 should not be created (duplicate CODE003 in CSV)');
        $this->assertStringContainsString('This value has already been used in the uploaded users file.', $output);
        $this->assertStringContainsString('This value has already been used.', $output);

        // 3. Upload a new user with a value that already exists in the database — should be rejected.
        $csv = <<<EOF
username,firstname,lastname,email,profile_field_uniquecode
user5,Fifth,User,user5@example.com,CODE001
EOF;

        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_ADDNEW]);

        $user5 = $DB->get_record('user', ['username' => 'user5']);
        $this->assertEmpty($user5, 'User5 should not be created (CODE001 already exists in DB)');
        $this->assertStringContainsString('This value has already been used.', $output);

        // 4. Update an existing user re-supplying their own unique value — must not be rejected as a duplicate.
        $csv = <<<EOF
username,firstname,lastname,email,profile_field_uniquecode
user1,First,Updated,user1@example.com,CODE001
EOF;

        $output = $this->process_csv_upload($csv, ['--uutype=' . UU_USER_UPDATE, '--uuupdatetype=' . UU_UPDATE_FILEOVERRIDE]);

        $user1 = $DB->get_record('user', ['username' => 'user1']);
        $this->assertEquals('Updated', $user1->lastname, 'User1 lastname should be updated');
        $this->assertEquals('CODE001', profile_user_record($user1->id)->uniquecode);
        $this->assertStringNotContainsString(
            'This value has already been used.',
            $output,
            'Updating a user with their own unique value must not produce a duplicate error'
        );
    }
}
