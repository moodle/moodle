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

namespace mod_forum;

use mod_forum_tests_generator_trait;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once($CFG->dirroot . '/mod/forum/locallib.php');
require_once(__DIR__ . '/generator_trait.php');

/**
 * Tests for private reply functionality.
 *
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class private_replies_test extends \advanced_testcase {

    use mod_forum_tests_generator_trait;

    /**
     * Setup before tests.
     */
    public function setUp(): void {
        parent::setUp();
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
    }

    /**
     * Tear down after tests.
     */
    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        parent::tearDown();
    }

    /**
     * Ensure that the forum_post_is_visible_privately function reports that a post is visible to a user when another
     * user wrote the post, and it is not private.
     */
    public function test_forum_post_is_visible_privately_not_private(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student] = $this->helper_create_users($course, 1, 'student');
        [$teacher] = $this->helper_create_users($course, 1, 'teacher');
        [$discussion] = $this->helper_post_to_forum($forum, $teacher);
        $post = $this->helper_post_to_discussion($forum, $discussion, $teacher);

        $this->setUser($student);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $this->assertTrue(forum_post_is_visible_privately($post, $cm));
    }

    /**
     * Ensure that the forum_post_is_visible_privately function reports that a post is visible to a user when another
     * user wrote the post, and the user under test is the intended recipient.
     */
    public function test_forum_post_is_visible_privately_private_to_user(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student] = $this->helper_create_users($course, 1, 'student');
        [$teacher] = $this->helper_create_users($course, 1, 'teacher');
        [$discussion] = $this->helper_post_to_forum($forum, $teacher);
        $post = $this->helper_post_to_discussion($forum, $discussion, $teacher, [
                'privatereplyto' => $student->id,
            ]);

        $this->setUser($student);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $this->assertTrue(forum_post_is_visible_privately($post, $cm));
    }

    /**
     * Ensure that the forum_post_is_visible_privately function reports that a post is visible to a user when another
     * user wrote the post, and the user under test is a role with the view capability.
     */
    public function test_forum_post_is_visible_privately_private_to_user_view_as_teacher(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student] = $this->helper_create_users($course, 1, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');
        [$discussion] = $this->helper_post_to_forum($forum, $teacher);
        $post = $this->helper_post_to_discussion($forum, $discussion, $teacher, [
                'privatereplyto' => $student->id,
            ]);

        $this->setUser($otherteacher);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $this->assertTrue(forum_post_is_visible_privately($post, $cm));
    }

    /**
     * Ensure that the forum_post_is_visible_privately function reports that a post is not visible to a user when
     * another user wrote the post, and the user under test is a role without the view capability.
     */
    public function test_forum_post_is_visible_privately_private_to_user_view_as_other_student(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher] = $this->helper_create_users($course, 1, 'teacher');
        [$discussion] = $this->helper_post_to_forum($forum, $teacher);
        $post = $this->helper_post_to_discussion($forum, $discussion, $teacher, [
                'privatereplyto' => $student->id,
            ]);

        $this->setUser($otherstudent);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $this->assertFalse(forum_post_is_visible_privately($post, $cm));
    }

    /**
     * Ensure that the forum_post_is_visible_privately function reports that a post is visible to a user who wrote a
     * private reply, but not longer holds the view capability.
     */
    public function test_forum_post_is_visible_privately_private_to_user_view_as_author(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student] = $this->helper_create_users($course, 1, 'student');
        [$teacher] = $this->helper_create_users($course, 1, 'teacher');
        [$discussion] = $this->helper_post_to_forum($forum, $teacher);
        $post = $this->helper_post_to_discussion($forum, $discussion, $teacher, [
                'privatereplyto' => $student->id,
            ]);

        unassign_capability('mod/forum:readprivatereplies', $this->get_role_id('teacher'));

        $this->setUser($teacher);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $this->assertTrue(forum_post_is_visible_privately($post, $cm));
    }

    /**
     * Ensure that the forum_user_can_reply_privately returns true for a teacher replying to a forum post.
     */
    public function test_forum_user_can_reply_privately_as_teacher(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student] = $this->helper_create_users($course, 1, 'student');
        [$teacher] = $this->helper_create_users($course, 1, 'teacher');
        [, $post] = $this->helper_post_to_forum($forum, $student);

        $this->setUser($teacher);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($cm->id);
        $this->assertTrue(forum_user_can_reply_privately($context, $post));
    }

    /**
     * Ensure that the forum_user_can_reply_privately returns true for a teacher replying to a forum post.
     */
    public function test_forum_user_can_reply_privately_as_student(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [, $post] = $this->helper_post_to_forum($forum, $student);

        $this->setUser($otherstudent);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($cm->id);
        $this->assertFalse(forum_user_can_reply_privately($context, $post));
    }

    /**
     * Ensure that the forum_user_can_reply_privately returns false where the parent post is already a private reply.
     */
    public function test_forum_user_can_reply_privately_parent_is_already_private(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
        ]);

        [$student] = $this->helper_create_users($course, 1, 'student');
        [$teacher] = $this->helper_create_users($course, 1, 'teacher');
        [$discussion] = $this->helper_post_to_forum($forum, $student);
        $post = $this->helper_post_to_discussion($forum, $discussion, $teacher, ['privatereplyto' => $student->id]);

        $this->setUser($teacher);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($cm->id);
        $this->assertFalse(forum_user_can_reply_privately($context, $post));
    }
}
