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

namespace tool_mfa;

/**
 * Tests for MFA secret manager class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class secret_manager_test extends \advanced_testcase {

    /**
     * Tests create factor's secret
     *
     * @covers ::create_secret
     */
    public function test_create_secret(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->setUser($this->getDataGenerator()->create_user());

        // Test adding secret to DB.
        $secman = new \tool_mfa\local\secret_manager('mock');

        // Mutate the sessionid using reflection.
        $reflectedsessionid = new \ReflectionProperty($secman, 'sessionid');
        $reflectedsessionid->setValue($secman, 'fakesession');

        $sec1 = $secman->create_secret(1800, false);
        $count1 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $record1 = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals(1, $count1);
        $this->assertNotEquals('', $sec1);
        $this->assertTrue(empty($record1->sessionid));
        $sec2 = $secman->create_secret(1800, false);
        $count2 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(1, $count2);
        $this->assertEquals('', $sec2);
        $DB->delete_records('tool_mfa_secrets', []);

        // Now adding secret to session.
        $sec1 = $secman->create_secret(1800, true);
        $count1 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $record1 = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals(1, $count1);
        $this->assertNotEquals('', $sec1);
        $this->assertEquals('fakesession', $record1->sessionid);
        $sec2 = $secman->create_secret(1800, true);
        $this->assertEquals('', $sec2);
        $DB->delete_records('tool_mfa_secrets', []);

        // Now test adding a forced code.
        $sec1 = $secman->create_secret(1800, false);
        $count1 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(1, $count1);
        $this->assertNotEquals('', $sec1);
        $sec2 = $secman->create_secret(1800, false, 'code');
        $count2 = $DB->count_records('tool_mfa_secrets', ['factor' => 'mock']);
        $this->assertEquals(2, $count2);
        $this->assertEquals('code', $sec2);
        $DB->delete_records('tool_mfa_secrets', []);
    }

    /**
     * Tests add factor's secret to database
     *
     * @covers ::get_record
     * @covers ::delete_records
     */
    public function test_add_secret_to_db(): void {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\local\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());
        $sid = 'fakeid';

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);
        $reflectedmethod = $reflectedscanner->getMethod('add_secret_to_db');

        // Now add a secret and confirm it creates the correct record.
        $reflectedmethod->invoke($secman, 'code', 1800);
        $record = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals('code', $record->secret);
        $this->assertEquals($USER->id, $record->userid);
        $this->assertEquals('mock', $record->factor);
        $this->assertGreaterThanOrEqual(time(), (int) $record->expiry);
        $this->assertEquals(0, $record->revoked);
        $DB->delete_records('tool_mfa_secrets', []);

        // Now add a sessionid and confirm it is added correctly.
        $reflectedmethod->invoke($secman, 'code', 1800, $sid);
        $record = $DB->get_record('tool_mfa_secrets', []);
        $this->assertEquals('code', $record->secret);
        $this->assertGreaterThanOrEqual(time(), (int) $record->expiry);
        $this->assertEquals(0, $record->revoked);
        $this->assertEquals($sid, $record->sessionid);
    }

    /**
     * Tests validating factor's secret
     *
     * @covers ::validate_secret
     * @covers ::create_secret
     */
    public function test_validate_secret(): void {
        global $DB;

        // Test adding a code and getting it returned, then validated.
        $this->resetAfterTest(true);
        $this->setUser($this->getDataGenerator()->create_user());
        $secman = new \tool_mfa\local\secret_manager('mock');

        $secret = $secman->create_secret(1800, false);
        $this->assertEquals(\tool_mfa\local\secret_manager::VALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test a manual forced code.
        $secret = $secman->create_secret(1800, false, 'code');
        $this->assertEquals(\tool_mfa\local\secret_manager::VALID, $secman->validate_secret($secret));
        $this->assertEquals('code', $secret);
        $DB->delete_records('tool_mfa_secrets', []);

        // Test bad codes.
        $secret = $secman->create_secret(1800, false);
        $this->assertEquals(\tool_mfa\local\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test validate when no secrets present.
        $this->assertEquals(\tool_mfa\local\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));

        // Test revoked secrets.
        $secret = $secman->create_secret(1800, false);
        $DB->set_field('tool_mfa_secrets', 'revoked', 1, []);
        $this->assertEquals(\tool_mfa\local\secret_manager::REVOKED, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Test expired secrets.
        $secret = $secman->create_secret(-1, false);
        $this->assertEquals(\tool_mfa\local\secret_manager::NONVALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Session locked code from the same session id.
        // Mutate the sessionid using reflection.
        $reflectedsessionid = new \ReflectionProperty($secman, 'sessionid');
        $reflectedsessionid->setValue($secman, 'fakesession');

        $secret = $secman->create_secret(1800, true);
        $this->assertEquals(\tool_mfa\local\secret_manager::VALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Now test a session locked code from a different sessionid.
        $secret = $secman->create_secret(1800, true);
        $reflectedsessionid->setValue($secman, 'diffsession');
        $this->assertEquals(\tool_mfa\local\secret_manager::NONVALID, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);
    }

    /**
     * Tests revoking factor's secret
     *
     * @covers ::validate_secret
     * @covers ::create_secret
     * @covers ::revoke_secret
     */
    public function test_revoke_secret(): void {
        global $DB, $SESSION;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\local\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Session secrets.
        $secret = $secman->create_secret(1800, true);
        $secman->revoke_secret($secret);
        $this->assertEquals(\tool_mfa\local\secret_manager::REVOKED, $secman->validate_secret($secret));
        unset($SESSION->tool_mfa_secrets_mock);

        // DB secrets.
        $secret = $secman->create_secret(1800, false);
        $secman->revoke_secret($secret);
        $this->assertEquals(\tool_mfa\local\secret_manager::REVOKED, $secman->validate_secret($secret));
        $DB->delete_records('tool_mfa_secrets', []);

        // Revoke a non-valid secret.
        $secret = $secman->create_secret(1800, false);
        $secman->revoke_secret('nonvalid');
        $this->assertEquals(\tool_mfa\local\secret_manager::NONVALID, $secman->validate_secret('nonvalid'));
    }

    /**
     * Tests checking if factor has an active secret
     *
     * @covers ::create_secret
     * @covers ::revoke_secret
     */
    public function test_has_active_secret(): void {
        global $DB;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\local\secret_manager('mock');
        $this->setUser($this->getDataGenerator()->create_user());

        // Let's make stuff public using reflection.
        $reflectedscanner = new \ReflectionClass($secman);

        $reflectedmethod = $reflectedscanner->getMethod('has_active_secret');

        // DB secrets.
        $this->assertFalse($reflectedmethod->invoke($secman));
        $secman->create_secret(1800, false);
        $this->assertTrue($reflectedmethod->invoke($secman));
        $DB->delete_records('tool_mfa_secrets', []);
        $secman->create_secret(-1, false);
        $this->assertFalse($reflectedmethod->invoke($secman));
        $DB->delete_records('tool_mfa_secrets', []);
        $secret = $secman->create_secret(1800, false);
        $secman->revoke_secret($secret);
        $this->assertFalse($reflectedmethod->invoke($secman));

        // Now check a secret with session involvement.
        // Mutate the sessionid using reflection.
        $reflectedsessionid = new \ReflectionProperty($secman, 'sessionid');
        $reflectedsessionid->setValue($secman, 'fakesession');

        $this->assertFalse($reflectedmethod->invoke($secman, true));
        $secman->create_secret(1800, true);
        $this->assertTrue($reflectedmethod->invoke($secman, true));
        $DB->delete_records('tool_mfa_secrets', []);
        $secman->create_secret(-1, true);
        $this->assertFalse($reflectedmethod->invoke($secman, true));
        $DB->delete_records('tool_mfa_secrets', []);
        $secret = $secman->create_secret(1800, true);
        $secman->revoke_secret($secret);
        $this->assertFalse($reflectedmethod->invoke($secman, true));
        $DB->delete_records('tool_mfa_secrets', []);
        $secret = $secman->create_secret(1800, true);
         $reflectedsessionid->setValue($secman, 'diffsession');
        $this->assertFalse($reflectedmethod->invoke($secman, true));
    }

    /**
     * Tests with cleanup temporal secrets
     *
     * @covers ::cleanup_temp_secrets
     */
    public function test_cleanup_temp_secrets(): void {
        global $DB;

        $this->resetAfterTest(true);
        $secman = new \tool_mfa\local\secret_manager('mock');
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create secrets.
        $secman->create_secret(1800, true);
        $secman->create_secret(1800, true);

        // Cleanup current user secrets.
        $secman->cleanup_temp_secrets();

        // Check there are no secrets of the current user.
        $records = $DB->get_records('tool_mfa_secrets', ['userid' => $user->id]);
        $this->assertEmpty($records);
    }
}
