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
 * Integration tests for staff account provisioning (isolated phpunit DB).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\account_provision
 */
final class account_provision_test extends \advanced_testcase {

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    private function create_lp_course(string $idnumber = 'lp-prov-ai'): \stdClass {
        $course = $this->getDataGenerator()->create_course([
            'shortname' => strtoupper($idnumber),
            'idnumber' => $idnumber,
            'visible' => 1,
        ]);
        // Ensure manual enrol instance exists and is enabled (generator usually does).
        $enrol = enrol_get_plugin('manual');
        $this->assertNotNull($enrol);
        $instances = enrol_get_instances($course->id, true);
        $hasmanual = false;
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                $hasmanual = true;
                break;
            }
        }
        $this->assertTrue($hasmanual, 'manual enrol required');
        return $course;
    }

    public function test_authorized_staff_creates_user_and_enrols_fellow(): void {
        $this->setAdminUser();
        global $USER;
        $actor = $USER;

        $beforeauth = account_provision::auth_config_snapshot();

        $created = account_provision::create_manual_user($actor, [
            'username' => 'lp_fellow_prov',
            'email' => 'lp_fellow_prov@example.local',
            'firstname' => 'Fellow',
            'lastname' => 'Prov',
            'password' => 'TestPass1!',
        ]);
        $this->assertSame('created', $created['status']);

        $again = account_provision::create_manual_user($actor, [
            'username' => 'lp_fellow_prov',
            'email' => 'lp_fellow_prov@example.local',
            'firstname' => 'Fellow',
            'lastname' => 'Prov',
        ]);
        $this->assertSame('exists', $again['status']);
        $this->assertSame($created['userid'], $again['userid']);

        $course = $this->create_lp_course();
        $enrol = account_provision::enrol_fellow($actor, $created['userid'], (int) $course->id);
        $this->assertSame('enrolled', $enrol['status']);
        $this->assertTrue(account_persona::is_fellowship($created['userid'], (int) $course->id));

        $dup = account_provision::enrol_fellow($actor, $created['userid'], (int) $course->id);
        $this->assertSame('already_enrolled', $dup['status']);

        $this->assertSame($beforeauth, account_provision::auth_config_snapshot());
    }

    public function test_authorized_staff_assigns_course_scoped_mentor(): void {
        $this->setAdminUser();
        global $USER;

        $course = $this->create_lp_course('lp-prov-mentor');
        $other = $this->create_lp_course('lp-prov-other');
        $mentor = $this->getDataGenerator()->create_user(['username' => 'lp_mentor_prov']);

        $result = account_provision::assign_mentor($USER, (int) $mentor->id, (int) $course->id);
        $this->assertSame('enrolled', $result['status']);
        $this->assertSame('editingteacher', $result['role']);
        $this->assertTrue(account_persona::is_mentor((int) $mentor->id, (int) $course->id));
        $this->assertFalse(account_persona::is_mentor((int) $mentor->id, (int) $other->id));
    }

    public function test_unauthorized_user_cannot_create_or_enrol(): void {
        $student = $this->getDataGenerator()->create_user();
        $course = $this->create_lp_course('lp-prov-deny');
        $target = $this->getDataGenerator()->create_user();

        $this->expectException(account_provision_exception::class);
        account_provision::create_manual_user($student, [
            'username' => 'should_fail',
            'email' => 'should_fail@example.local',
            'firstname' => 'No',
            'lastname' => 'Auth',
        ]);
    }

    public function test_unauthorized_user_cannot_enrol_fellow(): void {
        $student = $this->getDataGenerator()->create_user();
        $course = $this->create_lp_course('lp-prov-deny2');
        $target = $this->getDataGenerator()->create_user();

        try {
            account_provision::enrol_fellow($student, (int) $target->id, (int) $course->id);
            $this->fail('expected forbidden');
        } catch (account_provision_exception $e) {
            $this->assertSame('forbidden', $e->errorcode);
        }
    }

    public function test_rejects_admin_grant_but_allows_employee_cohort(): void {
        $this->setAdminUser();
        global $USER;
        $target = $this->getDataGenerator()->create_user();

        try {
            account_provision::grant_admin($USER, (int) $target->id);
            $this->fail('expected');
        } catch (account_provision_exception $e) {
            $this->assertSame('forbidden_grant_admin', $e->errorcode);
        }
        $this->assertFalse(is_siteadmin((int) $target->id));

        $result = account_provision::grant_employee($USER, (int) $target->id);
        $this->assertSame('added', $result['status']);
        $this->assertTrue(account_persona::is_employee((int) $target->id));
        $this->assertFalse(account_persona::is_fellowship((int) $target->id));
        $this->assertFalse(account_persona::is_mentor((int) $target->id));

        $dup = account_provision::grant_employee($USER, (int) $target->id);
        $this->assertSame('already_member', $dup['status']);
    }

    public function test_unauthorized_cannot_grant_employee(): void {
        $student = $this->getDataGenerator()->create_user();
        $target = $this->getDataGenerator()->create_user();
        try {
            account_provision::grant_employee($student, (int) $target->id);
            $this->fail('expected');
        } catch (account_provision_exception $e) {
            $this->assertSame('forbidden', $e->errorcode);
        }
        $this->assertFalse(account_persona::is_employee((int) $target->id));
    }

    public function test_rejects_invalid_user_course_and_non_launchpad(): void {
        $this->setAdminUser();
        global $USER;

        try {
            account_provision::require_user(0);
            $this->fail('expected');
        } catch (account_provision_exception $e) {
            $this->assertSame('invalid_user', $e->errorcode);
        }

        $legacy = $this->getDataGenerator()->create_course(['idnumber' => 'not-lp']);
        $user = $this->getDataGenerator()->create_user();
        try {
            account_provision::enrol_fellow($USER, (int) $user->id, (int) $legacy->id);
            $this->fail('expected');
        } catch (account_provision_exception $e) {
            $this->assertSame('invalid_course', $e->errorcode);
        }

        try {
            account_provision::assign_mentor($USER, (int) $user->id, (int) $legacy->id, 'manager');
            $this->fail('expected');
        } catch (account_provision_exception $e) {
            // Non-lp fails before role check.
            $this->assertSame('invalid_course', $e->errorcode);
        }

        $lp = $this->create_lp_course('lp-prov-badrole');
        try {
            account_provision::assign_mentor($USER, (int) $user->id, (int) $lp->id, 'manager');
            $this->fail('expected');
        } catch (account_provision_exception $e) {
            $this->assertSame('invalid_role', $e->errorcode);
        }
    }

    public function test_auth_snapshot_unchanged_by_provisioning(): void {
        $this->setAdminUser();
        global $USER, $CFG;

        $before = account_provision::auth_config_snapshot();
        $this->assertSame((string) ($CFG->registerauth ?? ''), $before['registerauth']);

        account_provision::create_manual_user($USER, [
            'username' => 'lp_auth_snap',
            'email' => 'lp_auth_snap@example.local',
            'firstname' => 'Auth',
            'lastname' => 'Snap',
            'password' => 'TestPass1!',
        ]);

        $this->assertSame($before, account_provision::auth_config_snapshot());
        $this->assertSame('', (string) ($CFG->registerauth ?? ''));
    }
}
