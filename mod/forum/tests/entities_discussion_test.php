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

/**
 * The discussion entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\entities\post as post_entity;

/**
 * The discussion entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_entities_discussion_testcase extends advanced_testcase {
    /**
     * Test the entity returns expected values.
     */
    public function test_entity() {
        $this->resetAfterTest();

        // In the past to ensure the time started is true.
        $time = time() + 10;
        $discussion = new discussion_entity(
            1,
            2,
            3,
            'test discussion',
            4,
            5,
            6,
            false,
            $time,
            $time,
            0,
            0,
            false,
            0
        );
        $firstpost = new post_entity(
            4,
            1,
            0,
            1,
            time(),
            time(),
            true,
            'post subject',
            'post message',
            1,
            true,
            false,
            0,
            false,
            false,
            false
        );
        $notfirstpost = new post_entity(
            1,
            1,
            0,
            1,
            time(),
            time(),
            true,
            'post subject',
            'post message',
            1,
            true,
            false,
            0,
            false,
            false,
            false
        );

        $this->assertEquals(1, $discussion->get_id());
        $this->assertEquals(2, $discussion->get_course_id());
        $this->assertEquals(3, $discussion->get_forum_id());
        $this->assertEquals('test discussion', $discussion->get_name());
        $this->assertEquals(4, $discussion->get_first_post_id());
        $this->assertEquals(5, $discussion->get_user_id());
        $this->assertEquals(6, $discussion->get_group_id());
        $this->assertEquals(false, $discussion->is_assessed());
        $this->assertEquals($time, $discussion->get_time_modified());
        $this->assertEquals($time, $discussion->get_user_modified());
        $this->assertEquals(0, $discussion->get_time_start());
        $this->assertEquals(0, $discussion->get_time_end());
        $this->assertEquals(false, $discussion->is_pinned());
        $this->assertEquals(true, $discussion->is_first_post($firstpost));
        $this->assertEquals(false, $discussion->is_first_post($notfirstpost));
        $this->assertEquals(true, $discussion->has_started());
        $this->assertEquals(true, $discussion->has_group());
    }

    /**
     * Test the display period settings for discussions.
     * This covers each individual date function as well as the combination of the 2.
     *
     * @dataProvider test_diplay_period_options_provider
     * @param string $testdescription A basic description of the base assertions.
     * @param int $startoffset Start time offset with current time in seconds.
     * @param int $endoffset End time offset with current time in seconds.
     * @param bool $timestartresult Expected result from the has_started function
     * @param bool $timeendresult Expected result from the has_ended function
     * @param bool $isvisible Expected result from the is_timed_discussion_visible function
     */
    public function test_display_period_settings($testdescription, $startoffset, $endoffset,
                                                 $timestartresult, $timeendresult, $isvisible) {
        global $CFG;
        $this->resetAfterTest();

        $basetime = time();
        $starttime = $startoffset != 0 ? $basetime + $startoffset : 0;
        $endtime = $endoffset != 0 ? $basetime + $endoffset : 0;
        $discussion = new discussion_entity(
            1,
            2,
            3,
            'test discussion',
            4,
            5,
            6,
            false,
            $basetime,
            $basetime,
            $starttime,
            $endtime,
            false,
            0
        );
        $CFG->forum_enabletimedposts = true;

        $this->assertEquals($timestartresult, $discussion->has_started(), $testdescription);
        $this->assertEquals($timeendresult, $discussion->has_ended(), $testdescription);
        $this->assertEquals($isvisible, $discussion->is_timed_discussion_visible(), $testdescription);
    }

    public function test_is_favourited() {
        $this->resetAfterTest(true);

        $time = time() + 10;
        // Create a user.
        $user = self::getDataGenerator()->create_user(array('trackforums' => 1));

        // Set to the user.
        self::setUser($user);

        // Create courses to add the modules.
        $course1 = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course1->id);

        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course1->id;
        $record->trackingtype = FORUM_TRACKING_OFF;
        $forum1 = self::getDataGenerator()->create_module('forum', $record);

        $discussion = new discussion_entity(
            1,
            $course1->id,
            $forum1->id,
            'test discussion',
            4,
            5,
            6,
            false,
            $time,
            $time,
            0,
            0,
            false
        );
        $coursemodule = get_coursemodule_from_instance('forum', $forum1->id);
        $contextmodule = context_module::instance($coursemodule->id);

        $this->assertFalse(\mod_forum\local\entities\discussion::is_favourited($discussion, $contextmodule, $user));

        // Toggle the favourite for discussion.
        $usercontext = \context_user::instance($user->id);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $ufservice->create_favourite('mod_forum', 'discussions', $discussion->get_id(), $contextmodule);

        $this->assertTrue(\mod_forum\local\entities\discussion::is_favourited($discussion, $contextmodule, $user));
    }

    /**
     * Data provider for test_display_period_settings().
     *
     * @return array start/end time offsets and the expected results.
     */
    public function test_diplay_period_options_provider() {
        return array(
            ["No dates set", 0, 0, true, false, true],
            ["Only started date in the future", 100, 0, false, false, false],
            ["Only started date in the past", -100, 0, true, false, true],
            ["Only end date in the future", 0, 100, true, false, true],
            ["Only end date in the past", 0, -100, true, true, false],
            ["Start date in the past, end date in the future", -100, 100, true, false, true],
            ["Both dates in the past", -100, -50, true, true, false],
            ["Both dates in the future", 100, 150, false, false, false],
        );
    }
}
