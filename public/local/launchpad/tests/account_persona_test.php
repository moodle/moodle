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

namespace local_launchpad;

/**
 * Account persona classifier tests (read-only detection; uses isolated phpunit DB).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\account_persona
 */
final class account_persona_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Create a LaunchPad-shaped course (idnumber lp-*).
     */
    private function create_launchpad_course(string $idnumber = 'lp-ai-persona'): \stdClass {
        return $this->getDataGenerator()->create_course([
            'shortname' => strtoupper($idnumber),
            'idnumber' => $idnumber,
            'visible' => 1,
        ]);
    }

    public function test_enrolled_fellow_has_fellowship_only(): void {
        $course = $this->create_launchpad_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $this->assertTrue(account_persona::is_fellowship((int) $user->id));
        $this->assertFalse(account_persona::is_mentor((int) $user->id));
        $this->assertFalse(account_persona::is_admin((int) $user->id));
        $this->assertSame([account_persona::FELLOWSHIP], account_persona::for_user((int) $user->id));
        $this->assertTrue(account_persona::has((int) $user->id, account_persona::FELLOWSHIP));
        $this->assertTrue(account_persona::has((int) $user->id, account_persona::FELLOWSHIP, (int) $course->id));
    }

    public function test_mentor_on_track_has_mentor_persona(): void {
        $course = $this->create_launchpad_course('lp-pm-persona');
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');

        $this->assertTrue(account_persona::is_mentor((int) $user->id));
        $this->assertFalse(account_persona::is_fellowship((int) $user->id));
        $this->assertSame([account_persona::MENTOR], account_persona::for_user((int) $user->id));

        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'teacher');
        $this->assertTrue(account_persona::is_mentor((int) $teacher->id));
    }

    public function test_site_administrator_has_admin_persona(): void {
        $this->setAdminUser();
        global $USER;

        $this->assertTrue(account_persona::is_admin((int) $USER->id));
        $this->assertContains(account_persona::ADMIN, account_persona::for_user((int) $USER->id));
        $this->assertTrue(account_persona::has((int) $USER->id, account_persona::ADMIN));
    }

    public function test_user_with_no_qualifying_assignment_has_empty_personas(): void {
        $other = $this->getDataGenerator()->create_course([
            'idnumber' => 'not-launchpad',
            'shortname' => 'OTHER',
        ]);
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $other->id, 'student');

        $this->assertSame([], account_persona::for_user((int) $user->id));
        $this->assertFalse(account_persona::is_fellowship((int) $user->id));
        $this->assertFalse(account_persona::is_mentor((int) $user->id));
        $this->assertFalse(account_persona::is_admin((int) $user->id));
    }

    public function test_user_may_hold_multiple_personas(): void {
        $course = $this->create_launchpad_course('lp-multi-persona');
        $this->setAdminUser();
        global $USER;
        $adminid = (int) $USER->id;

        $this->getDataGenerator()->enrol_user($adminid, $course->id, 'student');

        $personas = account_persona::for_user($adminid);
        $this->assertContains(account_persona::FELLOWSHIP, $personas);
        $this->assertContains(account_persona::ADMIN, $personas);
        $this->assertNotContains(account_persona::EMPLOYEE, $personas);

        // Course-scoped: fellowship on this course; admin still site-wide.
        $scoped = account_persona::for_user($adminid, (int) $course->id);
        $this->assertContains(account_persona::FELLOWSHIP, $scoped);
        $this->assertContains(account_persona::ADMIN, $scoped);

        $otherlp = $this->create_launchpad_course('lp-other-persona');
        $scopedother = account_persona::for_user($adminid, (int) $otherlp->id);
        $this->assertNotContains(account_persona::FELLOWSHIP, $scopedother);
        $this->assertContains(account_persona::ADMIN, $scopedother);
    }

    public function test_request_supplied_persona_values_are_ignored(): void {
        $user = $this->getDataGenerator()->create_user();

        $_GET['persona'] = 'admin';
        $_POST['role'] = 'mentor';
        $_REQUEST['persona'] = account_persona::ADMIN;
        $_REQUEST['role'] = account_persona::MENTOR;
        $_GET['account_persona'] = account_persona::FELLOWSHIP;

        $this->assertSame([], account_persona::for_user((int) $user->id));
        $this->assertFalse(account_persona::has((int) $user->id, account_persona::ADMIN));
        $this->assertFalse(account_persona::is_admin((int) $user->id));
        $this->assertFalse(account_persona::is_mentor((int) $user->id));
        $this->assertFalse(account_persona::is_fellowship((int) $user->id));

        unset($_GET['persona'], $_POST['role'], $_REQUEST['persona'], $_REQUEST['role'], $_GET['account_persona']);
    }

    public function test_employee_is_cohort_based_and_not_automatic(): void {
        $this->assertTrue(account_persona::employee_implemented());
        $this->assertStringContainsString(account_persona::EMPLOYEE_COHORT_IDNUMBER, account_persona::employee_status());
        $this->assertContains(account_persona::EMPLOYEE, account_persona::implemented_personas());

        $user = $this->getDataGenerator()->create_user();
        $this->assertFalse(account_persona::is_employee((int) $user->id));
        $this->assertNotContains(account_persona::EMPLOYEE, account_persona::for_user((int) $user->id));
    }

    public function test_classifier_does_not_mutate_roles_or_enrolments(): void {
        global $DB;

        $course = $this->create_launchpad_course('lp-readonly-persona');
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $enrolbefore = $DB->count_records('user_enrolments', ['userid' => $user->id]);
        $rolesbefore = $DB->count_records('role_assignments', ['userid' => $user->id]);
        $authbefore = $DB->get_field('user', 'auth', ['id' => $user->id]);

        account_persona::for_user((int) $user->id);
        account_persona::is_admin((int) $user->id);
        account_persona::has((int) $user->id, account_persona::FELLOWSHIP);

        $this->assertSame($enrolbefore, $DB->count_records('user_enrolments', ['userid' => $user->id]));
        $this->assertSame($rolesbefore, $DB->count_records('role_assignments', ['userid' => $user->id]));
        $this->assertSame($authbefore, $DB->get_field('user', 'auth', ['id' => $user->id]));
    }

    public function test_non_launchpad_teacher_is_not_mentor(): void {
        $course = $this->getDataGenerator()->create_course(['idnumber' => 'legacy-course']);
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');

        $this->assertFalse(account_persona::is_mentor((int) $user->id));
        $this->assertSame([], account_persona::for_user((int) $user->id));
    }
}
