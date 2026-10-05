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

namespace core_badges;

use core_badges\tests\badges_testcase;
use moodle_exception;
use core_badges\award_manager;
use context_course;
use context_system;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Unit tests for award_manager class.
 *
 * @package     core_badges
 * @copyright   2025 Dai Nguyen Trong <ngtrdai@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_badges\award_manager::class)]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_badges\award_manager::class, 'process_manual_award')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_badges\award_manager::class, 'process_manual_revoke')]
#[\PHPUnit\Framework\Attributes\CoversMethod(\core_badges\classes\award_manager::class, 'process_manual_revoke')]
final class award_manager_test extends badges_testcase {
    /**
     * Create a simple test environment.
     *
     * @param int $badgetype Type of badge to create.
     * @param mixed $courseid Course ID or configuration.
     * @return object Test environment object.
     */
    private function create_test_environment(int $badgetype = BADGE_TYPE_COURSE, $courseid = null): object {
        $env = new \stdClass();

        // Create course if needed.
        if ($badgetype === BADGE_TYPE_COURSE) {
            if ($courseid === 'create_course' || $courseid === null) {
                $env->course = $this->getDataGenerator()->create_course();
                $courseid = $env->course->id;
            }
            $env->context = context_course::instance($courseid);
        } else {
            $env->course = null;
            $env->context = context_system::instance();
        }

        // Create badge.
        $badgedata = ['type' => $badgetype, 'status' => BADGE_STATUS_ACTIVE];
        if ($badgetype === BADGE_TYPE_COURSE) {
            $badgedata['courseid'] = $courseid;
        }
        $env->badge = $this->getDataGenerator()->get_plugin_generator('core_badges')->create_badge($badgedata);

        // Get role ID.
        $env->roleid = $this->get_student_role()->id;

        return $env;
    }

    /**
     * Get student role.
     */
    private function get_student_role(): object {
        global $DB;
        return $DB->get_record('role', ['shortname' => 'student']);
    }

    /**
     * Data provider for badge types.
     */
    public static function badge_types_provider(): array {
        return [
            'Course badge' => [BADGE_TYPE_COURSE, 'create_course'],
        ];
    }

    /**
     * Test successful manual badge award.
     *
     * @param int $badgetype The type of badge to test.
     * @param mixed $courseid Course ID configuration.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('badge_types_provider')]
    public function test_process_manual_award_success(int $badgetype, $courseid): void {
        global $DB;

        $this->resetAfterTest();

        // Create test environment.
        $env = $this->create_test_environment($badgetype, $courseid, 0);

        // Create test users.
        $recipient = $this->getDataGenerator()->create_user();
        $issuer = $this->getDataGenerator()->create_user();

        // Verify no manual award exists initially.
        $this->assertEquals(0, $DB->count_records('badge_manual_award'));

        // Process manual award.
        $result = award_manager::process_manual_award(
            $recipient->id,
            $issuer->id,
            $env->roleid,
            $env->badge->id
        );

        // Verify the award was processed successfully.
        $this->assertTrue($result);
        $this->assertEquals(1, $DB->count_records('badge_manual_award'));

        // Verify the record was created with correct data.
        $record = $DB->get_record('badge_manual_award', [
            'badgeid' => $env->badge->id,
            'recipientid' => $recipient->id,
            'issuerid' => $issuer->id,
            'issuerrole' => $env->roleid,
        ]);

        $this->assertNotFalse($record);
        $this->assertEquals($env->badge->id, $record->badgeid);
        $this->assertEquals($recipient->id, $record->recipientid);
        $this->assertEquals($issuer->id, $record->issuerid);
        $this->assertEquals($env->roleid, $record->issuerrole);
        $this->assertNotEmpty($record->datemet);
    }

    /**
     * Test duplicate manual badge award prevention.
     *
     * @param int $badgetype The type of badge to test.
     * @param mixed $courseid Course ID configuration.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('badge_types_provider')]
    public function test_process_manual_award_duplicate_prevention(int $badgetype, $courseid): void {
        global $DB;

        $this->resetAfterTest();

        // Create test environment.
        $env = $this->create_test_environment($badgetype, $courseid, 0);

        // Create test users.
        $recipient = $this->getDataGenerator()->create_user();
        $issuer = $this->getDataGenerator()->create_user();

        // Process first manual award.
        $result1 = award_manager::process_manual_award(
            $recipient->id,
            $issuer->id,
            $env->roleid,
            $env->badge->id
        );

        $this->assertTrue($result1);
        $this->assertEquals(1, $DB->count_records('badge_manual_award'));

        // Attempt to process duplicate award.
        $result2 = award_manager::process_manual_award(
            $recipient->id,
            $issuer->id,
            $env->roleid,
            $env->badge->id
        );

        // Verify duplicate was prevented.
        $this->assertFalse($result2);
        $this->assertEquals(1, $DB->count_records('badge_manual_award'));
    }

    /**
     * Test successful manual badge revocation.
     *
     * @param int $badgetype The type of badge to test.
     * @param mixed $courseid Course ID configuration.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('badge_types_provider')]
    public function test_process_manual_revoke_success(int $badgetype, $courseid): void {
        global $DB;

        $this->resetAfterTest();

        // Create test environment.
        $env = $this->create_test_environment($badgetype, $courseid, 0);

        // Create test users.
        $recipient = $this->getDataGenerator()->create_user();
        $issuer = $this->getDataGenerator()->create_user();

        // Create manual award record.
        $DB->insert_record('badge_manual_award', [
            'badgeid' => $env->badge->id,
            'recipientid' => $recipient->id,
            'issuerid' => $issuer->id,
            'issuerrole' => $env->roleid,
            'datemet' => time(),
        ]);

        // Create badge issued record.
        $DB->insert_record('badge_issued', [
            'badgeid' => $env->badge->id,
            'userid' => $recipient->id,
            'uniquehash' => sha1($env->badge->id . $recipient->id . time()),
            'dateissued' => time(),
            'dateexpire' => null,
            'visible' => 1,
        ]);

        $this->assertEquals(1, $DB->count_records('badge_manual_award'));
        $this->assertEquals(1, $DB->count_records('badge_issued'));

        // Capture events.
        $sink = $this->redirectEvents();

        // Process manual revocation.
        $result = award_manager::process_manual_revoke(
            $recipient->id,
            $issuer->id,
            $env->roleid,
            $env->badge->id
        );

        // Verify the revocation was processed successfully.
        $this->assertTrue($result);
        $this->assertEquals(0, $DB->count_records('badge_manual_award'));
        $this->assertEquals(0, $DB->count_records('badge_issued'));

        // Verify badge_revoked event was triggered.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\core\event\badge_revoked', $event);
        $this->assertEquals($env->badge->id, $event->objectid);
        $this->assertEquals($recipient->id, $event->relateduserid);

        $sink->close();
    }

    /**
     * Test manual badge revocation when award record doesn't exist.
     *
     * @param int $badgetype The type of badge to test.
     * @param mixed $courseid Course ID configuration.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('badge_types_provider')]
    public function test_process_manual_revoke_nonexistent_award(int $badgetype, $courseid): void {
        $this->resetAfterTest();

        // Create test environment.
        $env = $this->create_test_environment($badgetype, $courseid, 0);

        // Create test users.
        $recipient = $this->getDataGenerator()->create_user();
        $issuer = $this->getDataGenerator()->create_user();

        // Attempt to revoke non-existent award.
        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage(get_string('error:badgenotfound', 'badges'));

        award_manager::process_manual_revoke(
            $recipient->id,
            $issuer->id,
            $env->roleid,
            $env->badge->id
        );
    }

    /**
     * Test manual badge revocation with partial database failure.
     *
     * @param int $badgetype The type of badge to test.
     * @param mixed $courseid Course ID configuration.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('badge_types_provider')]
    public function test_process_manual_revoke_partial_failure(int $badgetype, $courseid): void {
        global $DB;

        $this->resetAfterTest();

        // Create test environment.
        $env = $this->create_test_environment($badgetype, $courseid, 0);

        // Create test users.
        $recipient = $this->getDataGenerator()->create_user();
        $issuer = $this->getDataGenerator()->create_user();

        // Create manual award record.
        $DB->insert_record('badge_manual_award', [
            'badgeid' => $env->badge->id,
            'recipientid' => $recipient->id,
            'issuerid' => $issuer->id,
            'issuerrole' => $env->roleid,
            'datemet' => time(),
        ]);

        // Note: Not creating badge_issued record to simulate partial failure.
        $this->assertEquals(1, $DB->count_records('badge_manual_award'));
        $this->assertEquals(0, $DB->count_records('badge_issued'));

        // Process manual revocation.
        $result = award_manager::process_manual_revoke(
            $recipient->id,
            $issuer->id,
            $env->roleid,
            $env->badge->id
        );

        // Should still return true even if badge_issued record doesn't exist.
        $this->assertTrue($result);
        $this->assertEquals(0, $DB->count_records('badge_manual_award'));
    }
}
