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
 * Privacy tests for core_comment.
 *
 * @package    core_comment
 * @category   test
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/comment/locallib.php');
require_once($CFG->dirroot . '/comment/lib.php');

use \core_privacy\tests\provider_testcase;

/**
 * Unit tests for comment/classes/privacy/policy
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_comment_privacy_testcase extends provider_testcase {

    /**
     * Check the exporting of comments for a user id in a context.
     */
    public function test_export_comments() {
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $comment = $this->get_comment_object($context, $course);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Add comments.
        $comments = [];
        $firstcomment = 'This is the first comment';
        $this->setUser($user1);
        $comment->add($firstcomment);
        $comments[$user1->id] = $firstcomment;

        $secondcomment = 'From the second user';
        $this->setUser($user2);
        $comment->add($secondcomment);
        $comments[$user2->id] = $secondcomment;

        // Retrieve comments only for user1.
        $this->setUser($user1);
        $writer = \core_privacy\local\request\writer::with_context($context);
        \core_comment\privacy\provider::export_comments($context, 'block_comments', 'page_comments', 0, []);

        $data = $writer->get_data([get_string('commentsubcontext', 'core_comment')]);
        $exportedcomments = $data->comments;

        // There is only one comment made by this user.
        $this->assertCount(1, $exportedcomments);
        $comment = reset($exportedcomments);
        $this->assertEquals($comments[$user1->id], format_string($comment->content, FORMAT_PLAIN));

        // Retrieve comments from any user.
        \core_comment\privacy\provider::export_comments($context, 'block_comments', 'page_comments', 0, [], false);

        $data = $writer->get_data([get_string('commentsubcontext', 'core_comment')]);
        $exportedcomments = $data->comments;

        // The whole conversation is two comments.
        $this->assertCount(2, $exportedcomments);
        foreach ($exportedcomments as $comment) {
            $this->assertEquals($comments[$comment->userid], format_string($comment->content, FORMAT_PLAIN));
        }
    }

    /**
     * Tests the deletion of all comments in a context.
     */
    public function test_delete_comments_for_all_users_in_context() {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $comment1 = $this->get_comment_object($coursecontext1, $course1);
        $comment2 = $this->get_comment_object($coursecontext2, $course2);

        $this->setUser($user1);
        $comment1->add('First comment for user 1 on comment 1');
        $comment2->add('First comment for user 1 on comment 2');
        $this->setUser($user2);
        $comment1->add('First comment for user 2 on comment 1');
        $comment2->add('First comment for user 2 on comment 2');

        // Delete only for the first context. All records in the comments table for this context should be removed.
        \core_comment\privacy\provider::delete_comments_for_all_users_in_context($coursecontext1);
        // No records left here.
        $this->assertCount(0, $comment1->get_comments());
        // All of the records are left intact here.
        $this->assertCount(2, $comment2->get_comments());

    }

    /**
     * Tests deletion of comments for a specified user and contexts.
     */
    public function test_delete_comments_for_user() {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);
        $coursecontext3 = context_course::instance($course3->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $comment1 = $this->get_comment_object($coursecontext1, $course1);
        $comment2 = $this->get_comment_object($coursecontext2, $course2);
        $comment3 = $this->get_comment_object($coursecontext3, $course3);

        $this->setUser($user1);
        $comment1->add('First comment for user 1');
        $comment2->add('User 1 comment in second comment');

        $this->setUser($user2);
        $comment2->add('User two replied in comment two');
        $comment3->add('Comment three for user 2.');

        // Delete the comments for user 1.
        $approvedcontextlist = new core_privacy\tests\request\approved_contextlist($user1, 'block_comments',
                [$coursecontext1->id, $coursecontext2->id]);
        \core_comment\privacy\provider::delete_comments_for_user($approvedcontextlist);

        // No comments left in comments 1 as only user 1 commented there.
        $this->assertCount(0, $comment1->get_comments());
        // Only user 2 comments left in comments 2.
        $comment2comments = $comment2->get_comments();
        $this->assertCount(1, $comment2comments);
        $this->assertEquals($user2->id, $comment2comments[0]->userid);
        // Nothing changed here as user 1 did not leave a comment.
        $comment3comments = $comment3->get_comments();
        $this->assertCount(1, $comment3comments);
        $this->assertEquals($user2->id, $comment3comments[0]->userid);
    }

    /**
     * Creates a comment object
     *
     * @param  context $context A context object.
     * @param  stdClass $course A course object.
     * @return comment The comment object.
     */
    protected function get_comment_object($context, $course) {
        // Comment on course page.
        $args = new stdClass;
        $args->context = $context;
        $args->course = $course;
        $args->area = 'page_comments';
        $args->itemid = 0;
        $args->component = 'block_comments';
        $comment = new comment($args);
        $comment->set_post_permission(true);
        return $comment;
    }
}
