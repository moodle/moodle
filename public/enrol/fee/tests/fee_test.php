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

namespace enrol_fee;

/**
 * enrol_fee tests.
 *
 * @package    enrol_fee
 * @copyright  2026 Andi Permana <andi.permana@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \enrol_fee_plugin
 */
final class fee_test extends \advanced_testcase {
    /**
     * Enable the enrol_fee plugin.
     */
    protected function enable_plugin(): void {
        $enabled = enrol_get_plugins(true);
        $enabled['fee'] = true;
        set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));
    }

    /**
     * Disable the enrol_fee plugin.
     */
    protected function disable_plugin(): void {
        $enabled = enrol_get_plugins(true);
        unset($enabled['fee']);
        set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));
    }

    /**
     * Test basic plugin state and default config.
     */
    public function test_basics(): void {
        $this->assertFalse(enrol_is_enabled('fee'));
        $plugin = enrol_get_plugin('fee');
        $this->assertInstanceOf('enrol_fee_plugin', $plugin);
        $this->assertEquals(ENROL_EXT_REMOVED_SUSPENDNOROLES, get_config('enrol_fee', 'expiredaction'));
    }

    /**
     * Test that sync does not error when there is nothing to do.
     */
    public function test_sync_nothing(): void {
        $this->resetAfterTest();
        $this->enable_plugin();

        $feeplugin = enrol_get_plugin('fee');
        $feeplugin->sync(new \null_progress_trace());
    }

    /**
     * Test that process_expirations() correctly handles all expiry actions:
     * - ENROL_EXT_REMOVED_KEEP: no changes to expired enrolments.
     * - ENROL_EXT_REMOVED_SUSPENDNOROLES: expired active enrolments are suspended and roles removed.
     * - ENROL_EXT_REMOVED_UNENROL: expired enrolments are fully removed from user_enrolments.
     *
     * @covers \enrol_fee_plugin::sync
     */
    public function test_expired(): void {
        global $DB;
        $this->resetAfterTest();
        $this->enable_plugin();

        /** @var \enrol_fee_plugin $feeplugin */
        $feeplugin = enrol_get_plugin('fee');
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $now = time();
        $trace = new \null_progress_trace();

        // Prepare roles.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->assertNotEmpty($studentrole);
        $teacherrole = $DB->get_record('role', ['shortname' => 'teacher']);
        $this->assertNotEmpty($teacherrole);
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        $this->assertNotEmpty($managerrole);

        // Prepare users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Prepare courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);

        // Add fee enrolment instances.
        $instanceid1 = $feeplugin->add_instance($course1, [
            'status'   => ENROL_INSTANCE_ENABLED,
            'roleid'   => $studentrole->id,
            'cost'     => 10,
            'currency' => 'USD',
        ]);
        $instanceid2 = $feeplugin->add_instance($course2, [
            'status'   => ENROL_INSTANCE_ENABLED,
            'roleid'   => $studentrole->id,
            'cost'     => 10,
            'currency' => 'USD',
        ]);
        $instanceid2b = $feeplugin->add_instance($course2, [
            'status'   => ENROL_INSTANCE_ENABLED,
            'roleid'   => $teacherrole->id,
            'cost'     => 10,
            'currency' => 'USD',
        ]);

        $instance1 = $DB->get_record('enrol', ['id' => $instanceid1], '*', MUST_EXIST);
        $instance2 = $DB->get_record('enrol', ['id' => $instanceid2], '*', MUST_EXIST);
        $instance2b = $DB->get_record('enrol', ['id' => $instanceid2b], '*', MUST_EXIST);

        // Enrol a user via manual in course2 to verify it is not affected by fee expiry sync.
        $maninstance2 = $DB->get_record('enrol', ['courseid' => $course2->id, 'enrol' => 'manual'], '*', MUST_EXIST);
        $manualplugin->enrol_user($maninstance2, $user1->id, $teacherrole->id);

        $this->assertEquals(1, $DB->count_records('user_enrolments'));
        $this->assertEquals(1, $DB->count_records('role_assignments'));

        // Enrol users via fee:
        // course1: user1 active (no end), user2 active (no end), user3 expired 60s ago.
        $feeplugin->enrol_user($instance1, $user1->id, $studentrole->id);
        $feeplugin->enrol_user($instance1, $user2->id, $studentrole->id);
        $feeplugin->enrol_user($instance1, $user3->id, $studentrole->id, 0, $now - 60);

        // Course2: user1 no end, user2 expired 1h ago, user3 ends in 1h, user1 as teacher expired 60s ago.
        $feeplugin->enrol_user($instance2, $user1->id, $studentrole->id, 0, 0);
        $feeplugin->enrol_user($instance2, $user2->id, $studentrole->id, 0, $now - 60 * 60);
        $feeplugin->enrol_user($instance2, $user3->id, $studentrole->id, 0, $now + 60 * 60);
        $feeplugin->enrol_user($instance2b, $user1->id, $teacherrole->id, $now - 60 * 60 * 24 * 7, $now - 60);
        $feeplugin->enrol_user($instance2b, $user4->id, $teacherrole->id);

        // Manually assign manager role to user3 in course1.
        role_assign($managerrole->id, $user3->id, $context1->id);

        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(9, $DB->count_records('role_assignments'));
        $this->assertEquals(6, $DB->count_records('role_assignments', ['roleid' => $studentrole->id]));
        $this->assertEquals(2, $DB->count_records('role_assignments', ['roleid' => $teacherrole->id]));
        $this->assertEquals(1, $DB->count_records('role_assignments', ['roleid' => $managerrole->id]));

        // Test 1: ENROL_EXT_REMOVED_KEEP — nothing should change.
        $feeplugin->set_config('expiredaction', ENROL_EXT_REMOVED_KEEP);

        $this->assertSame(0, $feeplugin->sync($trace));
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(9, $DB->count_records('role_assignments'));

        // Test 2: ENROL_EXT_REMOVED_SUSPENDNOROLES — suspend expired + remove roles.
        $feeplugin->set_config('expiredaction', ENROL_EXT_REMOVED_SUSPENDNOROLES);

        $feeplugin->sync($trace);
        // User_enrolments count stays the same (suspend, not unenrol).
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        // Roles removed for the 3 expired enrolments (user3/course1, user2/course2, user1-teacher/course2).
        $this->assertEquals(6, $DB->count_records('role_assignments'));
        $this->assertEquals(4, $DB->count_records('role_assignments', ['roleid' => $studentrole->id]));
        $this->assertEquals(1, $DB->count_records('role_assignments', ['roleid' => $teacherrole->id]));
        // Expired users should no longer have roles in their respective expired enrolment contexts.
        $this->assertFalse($DB->record_exists('role_assignments', [
            'contextid' => $context1->id, 'userid' => $user3->id, 'roleid' => $studentrole->id,
        ]));
        $this->assertFalse($DB->record_exists('role_assignments', [
            'contextid' => $context2->id, 'userid' => $user2->id, 'roleid' => $studentrole->id,
        ]));
        $this->assertFalse($DB->record_exists('role_assignments', [
            'contextid' => $context2->id, 'userid' => $user1->id, 'roleid' => $teacherrole->id,
        ]));
        // User1's student role in course2 (non-expired) must still be there.
        $this->assertTrue($DB->record_exists('role_assignments', [
            'contextid' => $context2->id, 'userid' => $user1->id, 'roleid' => $studentrole->id,
        ]));

        // Test 3: ENROL_EXT_REMOVED_UNENROL — fully remove expired enrolments.
        $feeplugin->set_config('expiredaction', ENROL_EXT_REMOVED_UNENROL);

        // Re-assign the roles that were stripped in test 2 so the count baseline is accurate.
        role_assign($studentrole->id, $user3->id, $context1->id);
        role_assign($studentrole->id, $user2->id, $context2->id);
        role_assign($teacherrole->id, $user1->id, $context2->id);
        $this->assertEquals(9, $DB->count_records('user_enrolments'));
        $this->assertEquals(9, $DB->count_records('role_assignments'));

        $feeplugin->sync($trace);
        // 3 expired enrolments removed: user3/instance1, user2/instance2, user1/instance2b.
        $this->assertEquals(6, $DB->count_records('user_enrolments'));
        $this->assertFalse($DB->record_exists('user_enrolments', ['enrolid' => $instance1->id, 'userid' => $user3->id]));
        $this->assertFalse($DB->record_exists('user_enrolments', ['enrolid' => $instance2->id, 'userid' => $user2->id]));
        $this->assertFalse($DB->record_exists('user_enrolments', ['enrolid' => $instance2b->id, 'userid' => $user1->id]));
        // Roles cleaned up too.
        $this->assertEquals(5, $DB->count_records('role_assignments'));
        $this->assertEquals(4, $DB->count_records('role_assignments', ['roleid' => $studentrole->id]));
        $this->assertEquals(1, $DB->count_records('role_assignments', ['roleid' => $teacherrole->id]));
    }
}
