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
use mod_forum\external\set_read_state;

/**
 * Tests for the set_read_state external function.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2026 Daniel Urena <daniel.urena@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(external\set_read_state::class)]
final class set_read_state_test extends \core_external\tests\externallib_testcase {
    /**
     * Test setting the read/unread state for a post when manual control is allowed.
     */
    public function test_mod_forum_set_read_state_success(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Enable tracking and manual marking.
        $CFG->forum_trackreadposts = true;
        $CFG->forum_usermarksread = true;

        $user = self::getDataGenerator()->create_user(['trackforums' => 1]);
        self::setUser($user);

        // Create course and enrol user.
        $course = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Create a forum with forced tracking enabled so the user is tracked.
        $forum = self::getDataGenerator()->create_module('forum', (object) [
            'course' => $course->id,
            'trackingtype' => FORUM_TRACKING_FORCED,
        ]);

        $generator = self::getDataGenerator()->get_plugin_generator('mod_forum');
        $discussion = $generator->create_discussion((object) [
            'course' => $course->id,
            'userid' => $user->id,
            'forum' => $forum->id,
        ]);

        // Create a reply in the discussion which will be manipulated via the web service.
        $reply = $generator->create_post((object) [
            'discussion' => $discussion->id,
            'parent' => $discussion->firstpost,
            'userid' => $user->id,
        ]);

        $post = $DB->get_record('forum_posts', ['id' => $reply->id], '*', MUST_EXIST);
        $this->assertFalse(\forum_tp_is_post_read($user->id, $post));

        // Mark the post as read.
        $result = set_read_state::execute($reply->id, true);
        $result = external_api::clean_returnvalue(set_read_state::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        $post = $DB->get_record('forum_posts', ['id' => $reply->id], '*', MUST_EXIST);
        $this->assertTrue(\forum_tp_is_post_read($user->id, $post));

        // Mark the post as unread.
        $result = set_read_state::execute($reply->id, false);
        $result = external_api::clean_returnvalue(set_read_state::execute_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertEmpty($result['warnings']);

        $post = $DB->get_record('forum_posts', ['id' => $reply->id], '*', MUST_EXIST);
        $this->assertFalse(\forum_tp_is_post_read($user->id, $post));
    }

    /**
     * Test setting the read/unread state when the user cannot manually control it.
     */
    public function test_mod_forum_set_read_state_without_manual_control(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Enable tracking but disable manual marking.
        $CFG->forum_trackreadposts = true;
        $CFG->forum_usermarksread = false;

        $user = self::getDataGenerator()->create_user(['trackforums' => 1]);
        self::setUser($user);

        // Create course and enrol user.
        $course = self::getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Create a forum with forced tracking enabled.
        $forum = self::getDataGenerator()->create_module('forum', (object) [
            'course' => $course->id,
            'trackingtype' => FORUM_TRACKING_FORCED,
        ]);

        $generator = self::getDataGenerator()->get_plugin_generator('mod_forum');
        $discussion = $generator->create_discussion((object) [
            'course' => $course->id,
            'userid' => $user->id,
            'forum' => $forum->id,
        ]);

        $reply = $generator->create_post((object) [
            'discussion' => $discussion->id,
            'parent' => $discussion->firstpost,
            'userid' => $user->id,
        ]);

        $post = $DB->get_record('forum_posts', ['id' => $reply->id], '*', MUST_EXIST);
        $this->assertFalse(\forum_tp_is_post_read($user->id, $post));

        // Attempt to change the read state. Manual control is disabled, so this should return a warning and status false.
        $result = set_read_state::execute($reply->id, true);
        $result = external_api::clean_returnvalue(set_read_state::execute_returns(), $result);

        $this->assertFalse($result['status']);
        $this->assertNotEmpty($result['warnings']);
        $this->assertEquals('cannotcontrolreadstatus', $result['warnings'][0]['warningcode']);
        $this->assertEquals('post', $result['warnings'][0]['item']);
        $this->assertEquals($reply->id, $result['warnings'][0]['itemid']);

        // Ensure the post has not been marked as read.
        $post = $DB->get_record('forum_posts', ['id' => $reply->id], '*', MUST_EXIST);
        $this->assertFalse(\forum_tp_is_post_read($user->id, $post));
    }
}
