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

namespace mod_forum\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');

use core_external\external_api;
use mod_forum\external\set_forum_tracking;
use mod_forum\subscriptions;

/**
 * Tests for the set_forum_tracking external function.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_forum\external\set_forum_tracking
 */
final class set_forum_tracking_test extends \core_external\tests\externallib_testcase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();
        // We must clear the subscription caches.
        // This has to be done both before each test, and after in case of other tests using these functions.
        subscriptions::reset_forum_cache();
    }

    #[\Override]
    public function tearDown(): void {
        // We must clear the subscription caches.
        // This has to be done both before each test, and after in case of other tests using these functions.
        subscriptions::reset_forum_cache();
        parent::tearDown();
    }

    /**
     * Test execute method.
     *
     * @dataProvider execute_provider
     * @covers ::execute
     *
     * @param bool|null $initialstate Initialise tracking state, null means no initial state.
     * @param bool $targetstate Expected target state of the tracking.
     * @param int $forumtype Tracking mode for the forum.
     * @param bool $expectedexception Whether an exception is expected.
     */
    public function test_execute(
        ?bool $initialstate,
        bool $targetstate,
        int $forumtype = FORUM_TRACKING_OPTIONAL,
        bool $expectedexception = false,
    ): void {

        global $CFG;

        $this->resetAfterTest();

        // Allow force.
        $CFG->forum_allowforcedreadtracking = 1;

        $user = self::getDataGenerator()->create_user(['trackforums' => 1]);
        $course = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $forum = self::getDataGenerator()->create_module(
            'forum',
            [
                'course' => $course->id,
                'trackingtype' => $forumtype,
            ],
        );

        $this->setUser($user);

        if ($expectedexception) {
            $this->expectException(\moodle_exception::class);
        } else if ($initialstate !== null) {
            // Set the initial state of the subscription.
            if ($initialstate) {
                forum_tp_start_tracking($forum->id);
            } else {
                forum_tp_stop_tracking($forum->id);
            }
            $this->assertEquals($initialstate, forum_tp_is_tracked($forum));
        }
        $return = external_api::clean_returnvalue(
            set_forum_tracking::execute_returns(),
            set_forum_tracking::execute($forum->id, $targetstate),
        );
        $this->assertEquals($targetstate, $return['userstate']['tracked']);
        $this->assertEquals($targetstate, forum_tp_is_tracked($forum));
    }

    /**
     * Data provider for test_execute.
     *
     * @return array The data provider array.
     */
    public static function execute_provider(): array {
        return [
            'Initially false, set to true' => [
                'initialstate' => false,
                'targetstate' => true,
            ],
            'Initially false, set to false' => [
                'initialstate' => false,
                'targetstate' => false,
            ],
            'Initially true, set to false' => [
                'initialstate' => true,
                'targetstate' => false,
            ],
            'Initially true, set to true' => [
                'initialstate' => true,
                'targetstate' => true,
            ],
            'Forced off' => [
                'initialstate' => null,
                'targetstate' => false,
                'forumtype' => FORUM_TRACKING_OFF,
                'expectedexception' => true,
            ],
            'Forced on' => [
                'initialstate' => null,
                'targetstate' => true,
                'forumtype' => FORUM_TRACKING_FORCED,
            ],
        ];
    }

    /**
     * Test execute method when tracking is not enabled for the user.
     *
     * @covers ::execute
     */
    public function test_execute_no_tracking(): void {

        $this->resetAfterTest();

        $user = self::getDataGenerator()->create_user(['trackforums' => 0]);
        $course = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $forum = self::getDataGenerator()->create_module('forum', [ 'course' => $course->id]);

        $this->setUser($user);

        $this->expectException(\moodle_exception::class);
        external_api::clean_returnvalue(
            set_forum_tracking::execute_returns(),
            set_forum_tracking::execute($forum->id, true),
        );
    }

    /**
     * Test execute method when forum does not exist.
     *
     * @covers ::execute
     */
    public function test_execute_unexisting_forum(): void {

        $this->resetAfterTest();

        $this->setAdminUser();
        $this->expectException(\moodle_exception::class);
        external_api::clean_returnvalue(
            set_forum_tracking::execute_returns(),
            set_forum_tracking::execute(9999, true),
        );
    }

    /**
     * Test execute method when user is not enrolled in the course of the forum.
     *
     * @covers ::execute
     */
    public function test_execute_unenrolled_user(): void {

        $this->resetAfterTest();

        $user = self::getDataGenerator()->create_user();
        $course = self::getDataGenerator()->create_course();
        $this->setUser($user);
        $forum = self::getDataGenerator()->create_module('forum', ['course' => $course->id]);

        $this->expectException(\moodle_exception::class);
        external_api::clean_returnvalue(
            set_forum_tracking::execute_returns(),
            set_forum_tracking::execute($forum->id, true),
        );
    }
}
