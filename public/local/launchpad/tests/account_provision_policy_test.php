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
 * Pure policy tests for account provisioning (no DB).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\account_provision
 * @covers     \local_launchpad\account_provision_exception
 */
final class account_provision_policy_test extends \basic_testcase {

    public function test_allowed_grants_include_fellowship_mentor_employee(): void {
        $this->assertSame(
            [account_persona::FELLOWSHIP, account_persona::MENTOR, account_persona::EMPLOYEE],
            account_provision::allowed_grants()
        );
        $this->assertTrue(account_provision::is_allowed_grant('fellowship'));
        $this->assertTrue(account_provision::is_allowed_grant('mentor'));
        $this->assertTrue(account_provision::is_allowed_grant('employee'));
        $this->assertFalse(account_provision::is_allowed_grant('admin'));
    }

    public function test_assert_grant_rejects_admin(): void {
        $this->expectException(account_provision_exception::class);
        $this->expectExceptionMessage('forbidden_grant_admin');
        account_provision::assert_grant_allowed(account_persona::ADMIN);
    }

    public function test_assert_grant_allows_employee_policy(): void {
        account_provision::assert_grant_allowed(account_persona::EMPLOYEE);
        $this->assertTrue(true);
    }

    public function test_grant_admin_always_throws(): void {
        $actor = (object) ['id' => 1];
        try {
            account_provision::grant_admin($actor, 2);
            $this->fail('expected exception');
        } catch (account_provision_exception $e) {
            $this->assertSame('forbidden_grant_admin', $e->errorcode);
        }
    }

    public function test_launchpad_idnumber_and_mentor_roles(): void {
        $this->assertTrue(account_provision::is_launchpad_idnumber('lp-ai-pilot'));
        $this->assertTrue(account_provision::is_launchpad_idnumber('LP-PM-PILOT'));
        $this->assertFalse(account_provision::is_launchpad_idnumber('legacy-course'));
        $this->assertFalse(account_provision::is_launchpad_idnumber(''));

        $this->assertTrue(account_provision::is_allowed_mentor_role('editingteacher'));
        $this->assertTrue(account_provision::is_allowed_mentor_role('teacher'));
        $this->assertFalse(account_provision::is_allowed_mentor_role('manager'));
        $this->assertFalse(account_provision::is_allowed_mentor_role('student'));
    }
}
