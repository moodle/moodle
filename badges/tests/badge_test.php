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

declare(strict_types=1);

namespace core_badges;

use core_badges_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/badgeslib.php");

/**
 * Unit tests for badge class.
 *
 * @package     core_badges
 * @covers      \core_badges\badge
 * @copyright   2024 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class badge_test extends \advanced_testcase {

    /**
     * Test create_badge.
     *
     * @dataProvider badges_provider
     * @param bool $iscourse Whether the badge is a course badge or not.
     * @param array $data Badge data. It will override the default data.
     */
    public function test_create_badge(bool $iscourse = false, array $data = []): void {
        global $DB;

        $this->resetAfterTest();

        $courseid = null;
        if ($iscourse) {
            $course = $this->getDataGenerator()->create_course();
            $courseid = (int) $course->id;
        }

        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        // Check no badges exist.
        $this->assertEquals(0, $DB->count_records('badge'));

        $data = (object) array_merge($this->get_badge(), $data);

        // Trigger and capture events.
        $sink = $this->redirectEvents();

        $badge = badge::create_badge($data, $courseid);
        // Check the badge was created with the correct data.
        $this->assertEquals(1, $DB->count_records('badge'));
        $this->assertNotEmpty($badge->id);
        if ($iscourse) {
            $this->assertEquals(BADGE_TYPE_COURSE, $badge->type);
            $this->assertEquals($course->id, $badge->courseid);
        } else {
            $this->assertEquals(BADGE_TYPE_SITE, $badge->type);
            $this->assertNull($badge->courseid);
        }
        // Badges are always inactive by default, regardless the given status.
        $this->assertEquals(BADGE_STATUS_INACTIVE, $badge->status);

        if (property_exists($data, 'tags')) {
            $this->assertEquals($data->tags, $badge->get_badge_tags());
        }

        // Check that the event was triggered.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_created', $event);
        $this->assertEquals($badge->usercreated, $event->userid);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();
    }

    /**
     * Test update() in badge class.
     *
     * @dataProvider badges_provider
     * @param bool $iscourse Whether the badge is a course badge or not.
     * @param array $data Badge data to update the badge with. It will override the default data.
     */
    public function test_udpate_badge(bool $iscourse = false, array $data = []): void {
        global $USER, $DB;

        $this->resetAfterTest();

        $record = [];
        if ($iscourse) {
            $course = $this->getDataGenerator()->create_course();
            $record['type'] = BADGE_TYPE_COURSE;
            $record['courseid'] = $course->id;
        }

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        /** @var badge $badge */
        $badge = $generator->create_badge($record);
        $data = (object) array_merge($this->get_badge(), $data);

        // Check the badge has been created.
        $this->assertEquals(1, $DB->count_records('badge'));
        $this->assertNotEquals($data->name, $badge->name);
        $this->assertEmpty($badge->get_badge_tags());

        // Trigger and capture events.
        $sink = $this->redirectEvents();

        $this->setUser($user2);
        $this->assertTrue($badge->update($data));
        // Check the badge was updated with the correct data.
        $this->assertEquals(1, $DB->count_records('badge'));
        $this->assertNotEmpty($badge->id);
        $this->assertEquals($data->name, $badge->name);
        if ($iscourse) {
            $this->assertEquals(BADGE_TYPE_COURSE, $badge->type);
            $this->assertEquals($course->id, $badge->courseid);
        } else {
            $this->assertEquals(BADGE_TYPE_SITE, $badge->type);
            $this->assertNull($badge->courseid);
        }
        $this->assertEquals(BADGE_STATUS_ACTIVE, $badge->status);
        $this->assertEquals($USER->id, $badge->usermodified);

        if (property_exists($data, 'tags')) {
            $this->assertEquals($data->tags, $badge->get_badge_tags());
        }

        // Check that the event was triggered.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_updated', $event);
        $this->assertEquals($badge->usermodified, $event->userid);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();
    }

    /**
     * Test update_message() in badge class.
     *
     * @dataProvider badges_provider
     * @param bool $iscourse Whether the badge is a course badge or not.
     * @param array $data Badge data to update the badge with. It will override the default data.
     */
    public function test_udpate_message_badge(bool $iscourse = false, array $data = []): void {
        global $USER, $DB;

        $this->resetAfterTest();

        $record = [];
        if ($iscourse) {
            $course = $this->getDataGenerator()->create_course();
            $record['type'] = BADGE_TYPE_COURSE;
            $record['courseid'] = $course->id;
        }

        /** @var core_badges_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_badges');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);

        /** @var badge $badge */
        $badge = $generator->create_badge($record);
        $data = (object) array_merge($this->get_badge(), $data);

        // Check the badge has been created.
        $this->assertEquals(1, $DB->count_records('badge'));
        $this->assertNotEquals($data->name, $badge->name);
        $this->assertNotEquals($data->messagesubject, $badge->messagesubject);
        $this->assertNotEquals($data->message_editor['text'], $badge->message);
        $this->assertEmpty($badge->get_badge_tags());

        // Trigger and capture events.
        $sink = $this->redirectEvents();

        $this->setUser($user2);
        $this->assertTrue($badge->update_message($data));
        // Check the badge was updated with the correct data.
        $this->assertEquals(1, $DB->count_records('badge'));
        $this->assertNotEmpty($badge->id);
        $this->assertNotEquals($data->name, $badge->name);
        $this->assertEquals($data->messagesubject, $badge->messagesubject);
        $this->assertEquals($data->message_editor['text'], $badge->message);
        if ($iscourse) {
            $this->assertEquals(BADGE_TYPE_COURSE, $badge->type);
            $this->assertEquals($course->id, $badge->courseid);
        } else {
            $this->assertEquals(BADGE_TYPE_SITE, $badge->type);
            $this->assertNull($badge->courseid);
        }
        $this->assertEquals(BADGE_STATUS_ACTIVE, $badge->status);
        $this->assertEquals($user1->id, $badge->usermodified);

        // Check that the event was triggered.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\badge_updated', $event);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($badge->id, $event->objectid);
        $this->assertDebuggingNotCalled();
        $sink->close();
    }

    /**
     * Data provider for badge tests.
     *
     * @return array
     */
    public static function badges_provider(): array {
        return [
            'Site badge' => [
            ],
            'Site badge with tags' => [
                'iscourse' => false,
                'data' => [
                    'tags' => ['tag1', 'tag2'],
                ],
            ],
            'Course badge' => [
                'iscourse' => true,
            ],
        ];
    }

    /**
     * Get default badge data for testing purpose.
     *
     * @return array Badge data.
     */
    private function get_badge(): array {
        global $USER;

        return [
            'name' => 'My test badge',
            'description' => 'Testing badge description',
            'timecreated' => time(),
            'timemodified' => time(),
            'usercreated' => $USER->id,
            'usermodified' => $USER->id,
            'issuername' => 'Test issuer',
            'issuerurl' => 'http://issuer-url.domain.co.nz',
            'issuercontact' => 'issuer@example.com',
            'expiry' => 0,
            'expiredate' => null,
            'expireperiod' => null,
            'type' => BADGE_TYPE_SITE,
            'courseid' => null,
            'messagesubject' => 'The new test message subject',
            'messageformat' => '1',
            'message_editor' => [
                'text' => 'The new test message body',
            ],
            'attachment' => 1,
            'notification' => 0,
            'status' => BADGE_STATUS_ACTIVE_LOCKED,
            'version' => OPEN_BADGES_V2,
            'language' => 'en',
            'imageauthorname' => 'Image author',
            'imageauthoremail' => 'author@example.com',
            'imageauthorurl' => 'http://image.example.com/',
            'imagecaption' => 'Image caption',
            'tags' => [],
        ];
    }
}
