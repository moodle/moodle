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
 * Data provider tests.
 *
 * @package    core_blog
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_blog\privacy\provider;

require_once($CFG->dirroot . '/blog/locallib.php');
require_once($CFG->dirroot . '/comment/lib.php');

/**
 * Data provider testcase class.
 *
 * @package    core_blog
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_blog_privacy_testcase extends provider_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    public function test_get_contexts_for_userid() {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $c3 = $dg->create_course();
        $cm1a = $dg->create_module('page', ['course' => $c1]);
        $cm1b = $dg->create_module('page', ['course' => $c1]);
        $cm2a = $dg->create_module('page', ['course' => $c2]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);

        // Blog share a table with notes, so throw data in there and make sure it doesn't get reported.
        $dg->get_plugin_generator('core_notes')->create_instance(['userid' => $u1->id, 'courseid' => $c3->id]);

        $this->assertEmpty(provider::get_contexts_for_userid($u1->id)->get_contextids());
        $this->assertEmpty(provider::get_contexts_for_userid($u2->id)->get_contextids());

        // Gradually create blog posts for user 1. First system one.
        $this->create_post(['userid' => $u1->id]);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertEquals($u1ctx->id, $contextids[0]);
        $this->assertEmpty(provider::get_contexts_for_userid($u2->id)->get_contextids());

        // Create a blog post associated with c1.
        $post = $this->create_post(['userid' => $u1->id, 'courseid' => $c1->id]);
        $entry = new blog_entry($post->id);
        $entry->add_association(context_course::instance($c1->id)->id);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(2, $contextids);
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $this->assertTrue(in_array(context_course::instance($c1->id)->id, $contextids));
        $this->assertEmpty(provider::get_contexts_for_userid($u2->id)->get_contextids());

        // Create a blog post associated with cm2a.
        $post = $this->create_post(['userid' => $u1->id, 'courseid' => $c2->id]);
        $entry = new blog_entry($post->id);
        $entry->add_association(context_module::instance($cm2a->cmid)->id);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $this->assertTrue(in_array(context_course::instance($c1->id)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($cm2a->cmid)->id, $contextids));
        $this->assertEmpty(provider::get_contexts_for_userid($u2->id)->get_contextids());

        // User 2 comments on u1's post.
        $comment = $this->get_comment_object($u1ctx, $post->id);
        $this->setUser($u2);
        $comment->add('Hello, it\'s me!');
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $this->assertTrue(in_array(context_course::instance($c1->id)->id, $contextids));
        $this->assertTrue(in_array(context_module::instance($cm2a->cmid)->id, $contextids));
        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($u1ctx->id, $contextids));
    }

    public function test_get_contexts_for_userid_with_one_associated_post_only() {
        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);

        $this->assertEmpty(provider::get_contexts_for_userid($u1->id)->get_contextids());

        // Create a blog post associated with c1. It should always return both the course and user context.
        $post = $this->create_post(['userid' => $u1->id, 'courseid' => $c1->id]);
        $entry = new blog_entry($post->id);
        $entry->add_association(context_course::instance($c1->id)->id);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(2, $contextids);
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $this->assertTrue(in_array(context_course::instance($c1->id)->id, $contextids));
    }

    /**
     * Test that user IDs are returned for a specificed course or module context.
     */
    public function test_get_users_in_context_course_and_module() {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $c1ctx = context_course::instance($course->id);

        $post = $this->create_post(['userid' => $user1->id, 'courseid' => $course->id]);
        $entry = new blog_entry($post->id);
        $entry->add_association($c1ctx->id);

        // Add a comment from user 2.
        $comment = $this->get_comment_object(context_user::instance($user1->id), $entry->id);
        $this->setUser($user2);
        $comment->add('Nice blog post');

        $userlist = new \core_privacy\local\request\userlist($c1ctx, 'core_blog');
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();
        $this->assertCount(2, $userids);

        // Add an association for a module.
        $cm1a = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm1ctx = context_module::instance($cm1a->cmid);

        $post2 = $this->create_post(['userid' => $user2->id, 'courseid' => $course->id]);
        $entry2 = new blog_entry($post2->id);
        $entry2->add_association($cm1ctx->id);

        $userlist = new \core_privacy\local\request\userlist($cm1ctx, 'core_blog');
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();
        $this->assertCount(1, $userids);
    }

    /**
     * Test that user IDs are returned for a specificed user context.
     */
    public function test_get_users_in_context_user_context() {
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $u1ctx = context_user::instance($user1->id);

        $post = $this->create_post(['userid' => $user1->id]);
        $entry = new blog_entry($post->id);

        // Add a comment from user 2.
        $comment = $this->get_comment_object($u1ctx, $entry->id);
        $this->setUser($user2);
        $comment->add('Another nice blog post');

        $userlist = new \core_privacy\local\request\userlist($u1ctx, 'core_blog');
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();
        $this->assertCount(2, $userids);
    }

    /**
     * Test that user IDs are returned for a specificed user context for an external blog.
     */
    public function test_get_users_in_context_external_blog() {
        $user1 = $this->getDataGenerator()->create_user();
        $u1ctx = context_user::instance($user1->id);
        $extu1 = $this->create_external_blog(['userid' => $user1->id]);

        $userlist = new \core_privacy\local\request\userlist($u1ctx, 'core_blog');
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();
        $this->assertCount(1, $userids);
    }

    public function test_delete_data_for_user() {
        global $DB;

        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $cm1a = $dg->create_module('page', ['course' => $c1]);
        $cm1b = $dg->create_module('page', ['course' => $c1]);
        $cm2a = $dg->create_module('page', ['course' => $c2]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();

        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);
        $cm1actx = context_module::instance($cm1a->cmid);
        $cm1bctx = context_module::instance($cm1b->cmid);
        $cm2actx = context_module::instance($cm2a->cmid);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        // Blog share a table with notes, so throw data in there and make sure it doesn't get deleted.
        $this->assertFalse($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));
        $dg->get_plugin_generator('core_notes')->create_instance(['userid' => $u1->id, 'courseid' => $c1->id]);
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));

        // Create two external blogs.
        $extu1 = $this->create_external_blog(['userid' => $u1->id]);
        $extu2 = $this->create_external_blog(['userid' => $u2->id]);

        // Create a set of posts.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id])->id);
        $commentedon = $entry;
        $entry = new blog_entry($this->create_post(['userid' => $u2->id])->id);

        // Two course associations for u1.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($c1ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($c1ctx->id);

        // Two module associations with cm1a, and 1 with cm1b for u1.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1actx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1actx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1bctx->id);

        // One association for u2 in c1, cm1a and cm2a.
        $entry = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c1->id])->id);
        $entry->add_association($c1ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1actx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c2->id])->id);
        $entry->add_association($cm2actx->id);

        // One association for u1 in c2 and cm2a.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c2->id])->id);
        $entry->add_association($c2ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c2->id])->id);
        $entry->add_association($cm2actx->id);

        // Add comments.
        $comment = $this->get_comment_object($u1ctx, $commentedon->id);
        $this->setUser($u1);
        $comment->add('Hello, it\'s me!');
        $comment->add('I was wondering...');
        $this->setUser($u2);
        $comment->add('If after all these years');
        $this->setUser($u3);
        $comment->add('You\'d like to meet');

        // Assert current setup.
        $this->assertCount(6, provider::get_contexts_for_userid($u1->id)->get_contextids());
        $this->assertCount(9, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(5, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertCount(2, $DB->get_records('comments', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('comments', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('comments', ['userid' => $u3->id]));

        // Delete for u1 in cm1a.
        $appctxs = new approved_contextlist($u1, 'core_blog', [$cm1actx->id]);
        provider::delete_data_for_user($appctxs);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(5, $contextids);
        $this->assertFalse(in_array($cm1actx->id, $contextids));
        $this->assertCount(9, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(5, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));

        // Delete for u1 in c1.
        $appctxs = new approved_contextlist($u1, 'core_blog', [$c1ctx->id]);
        provider::delete_data_for_user($appctxs);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(4, $contextids);
        $this->assertFalse(in_array($c1ctx->id, $contextids));
        $this->assertCount(9, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(5, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));

        // Delete for u1 in c2.
        $appctxs = new approved_contextlist($u1, 'core_blog', [$c2ctx->id]);
        provider::delete_data_for_user($appctxs);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertFalse(in_array($c2ctx->id, $contextids));
        $this->assertCount(9, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(5, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));

        // Delete for u1 in another user's context, shouldn't do anything.
        provider::delete_data_for_user(new approved_contextlist($u1, 'core_blog', [$u2ctx->id]));
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertFalse(in_array($c2ctx->id, $contextids));
        $this->assertCount(9, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(5, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));
        $this->assertCount(2, $DB->get_records('comments', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('comments', ['userid' => $u2->id]));

        // Delete for u2 in u1 context.
        provider::delete_data_for_user(new approved_contextlist($u2, 'core_blog', [$u1ctx->id]));
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(3, $contextids);
        $this->assertFalse(in_array($c2ctx->id, $contextids));
        $this->assertCount(9, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(4, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));
        $this->assertCount(2, $DB->get_records('comments', ['userid' => $u1->id]));
        $this->assertCount(0, $DB->get_records('comments', ['userid' => $u2->id]));
        $this->assertCount(1, $DB->get_records('comments', ['userid' => $u3->id]));

        // Delete for u1 in their context.
        $appctxs = new approved_contextlist($u1, 'core_blog', [$u1ctx->id]);
        provider::delete_data_for_user($appctxs);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(0, $contextids);
        $this->assertCount(1, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(4, provider::get_contexts_for_userid($u2->id)->get_contextids());
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(0, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
        $this->assertCount(0, $DB->get_records('comments', ['userid' => $u1->id]));
        $this->assertCount(0, $DB->get_records('comments', ['userid' => $u2->id]));
        $this->assertCount(0, $DB->get_records('comments', ['userid' => $u3->id]));
        $this->assertTrue($DB->record_exists('post', ['courseid' => $c1->id, 'userid' => $u1->id, 'module' => 'notes']));
    }

    /**
     * Test provider delete_data_for_user with a context that contains no entries
     *
     * @return void
     */
    public function test_delete_data_for_user_empty_context() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        // Create a blog entry for user, associated with course.
        $entry = new blog_entry($this->create_post(['userid' => $user->id, 'courseid' => $course->id])->id);
        $entry->add_association($context->id);

        // Generate list of contexts for user.
        $contexts = provider::get_contexts_for_userid($user->id);
        $this->assertContains($context->id, $contexts->get_contextids());

        // Now delete the blog entry.
        $entry->delete();

        // Try to delete user data using contexts obtained prior to entry deletion.
        $contextlist = new approved_contextlist($user, 'core_blog', $contexts->get_contextids());
        provider::delete_data_for_user($contextlist);

        // Sanity check to ensure blog_associations is really empty.
        $this->assertEmpty($DB->get_records('blog_association', ['contextid' => $context->id]));
    }

    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $dg = $this->getDataGenerator();
        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $cm1a = $dg->create_module('page', ['course' => $c1]);
        $cm1b = $dg->create_module('page', ['course' => $c1]);
        $cm2a = $dg->create_module('page', ['course' => $c2]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);
        $cm1actx = context_module::instance($cm1a->cmid);
        $cm1bctx = context_module::instance($cm1b->cmid);
        $cm2actx = context_module::instance($cm2a->cmid);
        $u1ctx = context_user::instance($u1->id);

        // Create two external blogs.
        $extu1 = $this->create_external_blog(['userid' => $u1->id]);
        $extu2 = $this->create_external_blog(['userid' => $u2->id]);

        // Create a set of posts.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id])->id);
        $entry = new blog_entry($this->create_post(['userid' => $u2->id])->id);

        // Course associations for u1 and u2.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($c1ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($c1ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c1->id])->id);
        $entry->add_association($c1ctx->id);

        // Module associations for u1 and u2.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1actx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1actx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1bctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c1->id])->id);
        $entry->add_association($cm1actx->id);

        // Foreign associations for u1, u2.
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c2->id])->id);
        $entry->add_association($c2ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c2->id])->id);
        $entry->add_association($c2ctx->id);
        $entry = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $cm2a->id])->id);
        $entry->add_association($cm2actx->id);

        // Validate what we've got.
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(8, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(6, $contextids);
        $this->assertTrue(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertTrue(in_array($cm1actx->id, $contextids));
        $this->assertTrue(in_array($cm1bctx->id, $contextids));
        $this->assertTrue(in_array($cm2actx->id, $contextids));
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(4, $contextids);
        $this->assertTrue(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertTrue(in_array($cm1actx->id, $contextids));

        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));

        // Delete cm1a context.
        provider::delete_data_for_all_users_in_context($cm1actx);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(8, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(5, $contextids);
        $this->assertTrue(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertFalse(in_array($cm1actx->id, $contextids));
        $this->assertTrue(in_array($cm1bctx->id, $contextids));
        $this->assertTrue(in_array($cm2actx->id, $contextids));
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(3, $contextids);
        $this->assertTrue(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertFalse(in_array($cm1actx->id, $contextids));

        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));

        // Delete c1 context.
        provider::delete_data_for_all_users_in_context($c1ctx);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(8, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(4, $contextids);
        $this->assertFalse(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertFalse(in_array($cm1actx->id, $contextids));
        $this->assertTrue(in_array($cm1bctx->id, $contextids));
        $this->assertTrue(in_array($cm2actx->id, $contextids));
        $this->assertTrue(in_array($u1ctx->id, $contextids));
        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(2, $contextids);
        $this->assertFalse(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertFalse(in_array($cm1actx->id, $contextids));

        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));

        // Delete u1 context.
        provider::delete_data_for_all_users_in_context($u1ctx);
        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(0, $DB->get_records('post', ['userid' => $u1->id]));
        $this->assertCount(0, $contextids);
        $this->assertFalse(in_array($c1ctx->id, $contextids));
        $this->assertFalse(in_array($c2ctx->id, $contextids));
        $this->assertFalse(in_array($cm1actx->id, $contextids));
        $this->assertFalse(in_array($cm1bctx->id, $contextids));
        $this->assertFalse(in_array($cm2actx->id, $contextids));
        $this->assertFalse(in_array($u1ctx->id, $contextids));
        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(4, $DB->get_records('post', ['userid' => $u2->id]));
        $this->assertCount(2, $contextids);
        $this->assertFalse(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));
        $this->assertFalse(in_array($cm1actx->id, $contextids));

        $this->assertCount(0, $DB->get_records('blog_external', ['userid' => $u1->id]));
        $this->assertCount(1, $DB->get_records('blog_external', ['userid' => $u2->id]));
    }

    public function test_export_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();

        $c1 = $dg->create_course();
        $cm1a = $dg->create_module('page', ['course' => $c1]);
        $cm1b = $dg->create_module('page', ['course' => $c1]);
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = context_course::instance($c1->id);
        $cm1actx = context_module::instance($cm1a->cmid);
        $cm1bctx = context_module::instance($cm1b->cmid);
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        // System entries.
        $e1 = new blog_entry($this->create_post(['userid' => $u1->id, 'subject' => 'Hello world!',
            'publishstate' => 'public'])->id);
        $e2 = new blog_entry($this->create_post(['userid' => $u1->id, 'subject' => 'Hi planet!',
            'publishstate' => 'draft'])->id);
        $e3 = new blog_entry($this->create_post(['userid' => $u2->id, 'subject' => 'Ignore me'])->id);

        // Create a blog entry associated with contexts.
        $e4 = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id, 'subject' => 'Course assoc'])->id);
        $e4->add_association($c1ctx->id);
        $e4b = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id, 'subject' => 'Course assoc 2'])->id);
        $e4b->add_association($c1ctx->id);
        $e5 = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id, 'subject' => 'Module assoc',
            'publishstate' => 'public'])->id);
        $e5->add_association($cm1actx->id);
        $e5b = new blog_entry($this->create_post(['userid' => $u1->id, 'courseid' => $c1->id, 'subject' => 'C/CM assoc'])->id);
        $e5b->add_association($c1ctx->id);
        $e5b->add_association($cm1actx->id);
        $e6 = new blog_entry($this->create_post(['userid' => $u2->id, 'courseid' => $c1->id, 'subject' => 'Module assoc'])->id);
        $e6->add_association($cm1actx->id);

        // External blogs.
        $ex1 = $this->create_external_blog(['userid' => $u1->id, 'url' => 'https://moodle.org', 'name' => 'Moodle RSS']);
        $ex2 = $this->create_external_blog(['userid' => $u1->id, 'url' => 'https://example.com', 'name' => 'Example']);
        $ex3 = $this->create_external_blog(['userid' => $u2->id, 'url' => 'https://example.com', 'name' => 'Ignore me']);

        // Attach tags.
        core_tag_tag::set_item_tags('core', 'post', $e1->id, $u1ctx, ['Beer', 'Golf']);
        core_tag_tag::set_item_tags('core', 'blog_external', $ex1->id, $u1ctx, ['Car', 'Golf']);
        core_tag_tag::set_item_tags('core', 'post', $e3->id, $u2ctx, ['ITG']);
        core_tag_tag::set_item_tags('core', 'blog_external', $ex3->id, $u2ctx, ['DDR']);
        core_tag_tag::set_item_tags('core', 'dontfindme', $e1->id, $u1ctx, ['Lone tag']);

        // Attach comments.
        $comment = $this->get_comment_object($u1ctx, $e1->id);
        $this->setUser($u1);
        $comment->add('Hello, it\'s me!');
        $this->setUser($u2);
        $comment->add('I was wondering if after');
        $this->setUser($u1);
        $comment = $this->get_comment_object($u2ctx, $e3->id);
        $comment->add('All these years');

        // Blog share a table with notes, so throw some data in there, it should not be exported.
        $note = $dg->get_plugin_generator('core_notes')->create_instance(['userid' => $u1->id, 'courseid' => $c1->id,
            'subject' => 'ABC']);

        // Validate module associations.
        $contextlist = new approved_contextlist($u1, 'core_blog', [$cm1actx->id]);
        provider::export_user_data($contextlist);
        $writer = writer::with_context($cm1actx);
        $assocs = $writer->get_data([get_string('privacy:path:blogassociations', 'core_blog')]);
        $this->assertCount(2, $assocs->associations);
        $this->assertTrue(in_array('Module assoc', $assocs->associations));
        $this->assertTrue(in_array('C/CM assoc', $assocs->associations));

        // Validate course associations.
        $contextlist = new approved_contextlist($u1, 'core_blog', [$c1ctx->id]);
        provider::export_user_data($contextlist);
        $writer = writer::with_context($c1ctx);
        $assocs = $writer->get_data([get_string('privacy:path:blogassociations', 'core_blog')]);
        $this->assertCount(3, $assocs->associations);
        $this->assertTrue(in_array('Course assoc', $assocs->associations));
        $this->assertTrue(in_array('Course assoc 2', $assocs->associations));
        $this->assertTrue(in_array('C/CM assoc', $assocs->associations));

        // Confirm we're not exporting for another user.
        $contextlist = new approved_contextlist($u2, 'core_blog', [$u2ctx->id]);
        $writer = writer::with_context($u1ctx);
        $this->assertFalse($writer->has_any_data());

        // Now export user context for u2.
        $this->setUser($u2);
        $contextlist = new approved_contextlist($u2, 'core_blog', [$u1ctx->id]);
        provider::export_user_data($contextlist);
        $writer = writer::with_context($u1ctx);
        $data = $writer->get_data([get_string('blog', 'core_blog'), get_string('externalblogs', 'core_blog'),
            $ex1->name . " ({$ex1->id})"]);
        $this->assertEmpty($data);
        $data = $writer->get_data([get_string('blog', 'core_blog'), get_string('blogentries', 'core_blog'),
            $e2->subject . " ({$e2->id})"]);
        $this->assertEmpty($data);
        $data = $writer->get_data([get_string('blog', 'core_blog'), get_string('blogentries', 'core_blog'),
            $e1->subject . " ({$e1->id})"]);
        $this->assertEmpty($data);
        $data = $writer->get_data([get_string('blog', 'core_blog'), get_string('blogentries', 'core_blog'),
            $e1->subject . " ({$e1->id})", get_string('commentsubcontext', 'core_comment')]);
        $this->assertNotEmpty($data);
        $this->assertCount(1, $data->comments);
        $comment = array_shift($data->comments);
        $this->assertEquals('I was wondering if after', strip_tags($comment->content));

        // Now export user context data.
        $this->setUser($u1);
        $contextlist = new approved_contextlist($u1, 'core_blog', [$u1ctx->id]);
        writer::reset();
        provider::export_user_data($contextlist);
        $writer = writer::with_context($u1ctx);

        // Check external blogs.
        $externals = [$ex1, $ex2];
        foreach ($externals as $ex) {
            $data = $writer->get_data([get_string('blog', 'core_blog'), get_string('externalblogs', 'core_blog'),
                $ex->name . " ({$ex->id})"]);
            $this->assertNotEmpty($data);
            $this->assertEquals($data->name, $ex->name);
            $this->assertEquals($data->description, $ex->description);
            $this->assertEquals($data->url, $ex->url);
            $this->assertEquals($data->filtertags, $ex->filtertags);
            $this->assertEquals($data->modified, transform::datetime($ex->timemodified));
            $this->assertEquals($data->lastfetched, transform::datetime($ex->timefetched));
        }

        // Check entries.
        $entries = [$e1, $e2, $e4, $e4b, $e5, $e5b];
        $associations = [
            $e1->id => null,
            $e2->id => null,
            $e4->id => $c1ctx->get_context_name(),
            $e4b->id => $c1ctx->get_context_name(),
            $e5->id => $cm1actx->get_context_name(),
            $e5b->id => [$c1ctx->get_context_name(), $cm1actx->get_context_name()],
        ];
        foreach ($entries as $e) {
            $path = [get_string('blog', 'core_blog'), get_string('blogentries', 'core_blog'), $e->subject . " ({$e->id})"];
            $data = $writer->get_data($path);
            $this->assertNotEmpty($data);
            $this->assertEquals($data->subject, $e->subject);
            $this->assertEquals($data->summary, $e->summary);
            $this->assertEquals($data->publishstate, provider::transform_publishstate($e->publishstate));
            $this->assertEquals($data->created, transform::datetime($e->created));
            $this->assertEquals($data->lastmodified, transform::datetime($e->lastmodified));

            // We attached comments and tags to this entry.
            $commentpath = array_merge($path, [get_string('commentsubcontext', 'core_comment')]);
            if ($e->id == $e1->id) {
                $tagdata = $writer->get_related_data($path, 'tags');
                $this->assertEquals(['Beer', 'Golf'], $tagdata, '', 0, 10, true);

                $comments = $writer->get_data($commentpath);
                $this->assertCount(2, $comments->comments);

                $c0 = strip_tags($comments->comments[0]->content);
                $c1 = strip_tags($comments->comments[1]->content);
                $expectedcomments = [
                    'Hello, it\'s me!',
                    'I was wondering if after',
                ];

                $this->assertNotFalse(array_search($c0, $expectedcomments));
                $this->assertNotFalse(array_search($c1, $expectedcomments));
                $this->assertNotEquals($c0, $c1);

            } else {
                $tagdata = $writer->get_related_data($path, 'tags');
                $this->assertEmpty($tagdata);
                $comments = $writer->get_data($commentpath);
                $this->assertEmpty($comments);
            }

            if (isset($associations[$e->id])) {
                $assocs = $associations[$e->id];
                if (is_array($assocs)) {
                    $this->assertCount(count($assocs), $data->associations);
                    foreach ($assocs as $v) {
                        $this->assertTrue(in_array($v, $data->associations));
                    }
                } else {
                    $this->assertCount(1, $data->associations);
                    $this->assertTrue(in_array($assocs, $data->associations));
                }
            }
        }

        // The note was not exported.
        $path = [get_string('blog', 'core_blog'), get_string('blogentries', 'core_blog'), "ABC ($note->id)"];
        $this->assertEmpty($writer->get_data($path));

    }

    /**
     * Test that deleting of blog information in a user context works as desired.
     */
    public function test_delete_data_for_users_user_context() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();
        $u5 = $this->getDataGenerator()->create_user();

        $u1ctx = context_user::instance($u1->id);

        $post = $this->create_post(['userid' => $u1->id]);
        $entry = new blog_entry($post->id);

        $comment = $this->get_comment_object($u1ctx, $entry->id);
        $this->setUser($u1);
        $comment->add('Hello, I created the blog');
        $this->setUser($u2);
        $comment->add('User 2 making a comment.');
        $this->setUser($u3);
        $comment->add('User 3 here.');
        $this->setUser($u4);
        $comment->add('User 4 is nice.');
        $this->setUser($u5);
        $comment->add('User 5 for the win.');

        // This will only delete the comments made by user 4 and 5.
        $this->assertCount(5, $DB->get_records('comments', ['contextid' => $u1ctx->id]));
        $userlist = new \core_privacy\local\request\approved_userlist($u1ctx, 'core_blog', [$u4->id, $u5->id]);
        provider::delete_data_for_users($userlist);
        $this->assertCount(3, $DB->get_records('comments', ['contextid' => $u1ctx->id]));
        $this->assertCount(1, $DB->get_records('post', ['userid' => $u1->id]));

        // As the owner of the post is here everything will be deleted.
        $userlist = new \core_privacy\local\request\approved_userlist($u1ctx, 'core_blog', [$u1->id, $u2->id]);
        provider::delete_data_for_users($userlist);
        $this->assertEmpty($DB->get_records('comments', ['contextid' => $u1ctx->id]));
        $this->assertEmpty($DB->get_records('post', ['userid' => $u1->id]));
    }

    /**
     * Test that deleting of an external blog in a user context works as desired.
     */
    public function test_delete_data_for_users_external_blog() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();

        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $post = $this->create_external_blog(['userid' => $u1->id, 'url' => 'https://moodle.org', 'name' => 'Moodle RSS']);
        $post2 = $this->create_external_blog(['userid' => $u2->id, 'url' => 'https://moodle.com', 'name' => 'Some other thing']);

        // Check that we have two external blogs created.
        $this->assertCount(2, $DB->get_records('blog_external'));
        // This will only delete the external blog for user 1.
        $userlist = new \core_privacy\local\request\approved_userlist($u1ctx, 'core_blog', [$u1->id, $u2->id]);
        provider::delete_data_for_users($userlist);
        $this->assertCount(1, $DB->get_records('blog_external'));
    }

    public function test_delete_data_for_users_course_and_module_context() {
        global $DB;

        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();
        $u5 = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('page', ['course' => $course]);

        $u1ctx = context_user::instance($u1->id);
        $u3ctx = context_user::instance($u3->id);
        $c1ctx = context_course::instance($course->id);
        $cm1ctx = context_module::instance($module->cmid);

        // Blog with course association.
        $post1 = $this->create_post(['userid' => $u1->id, 'courseid' => $course->id]);
        $entry1 = new blog_entry($post1->id);
        $entry1->add_association($c1ctx->id);

        // Blog with module association.
        $post2 = $this->create_post(['userid' => $u3->id, 'courseid' => $course->id]);
        $entry2 = new blog_entry($post2->id);
        $entry2->add_association($cm1ctx->id);

        $comment = $this->get_comment_object($u1ctx, $entry1->id);
        $this->setUser($u1);
        $comment->add('Hello, I created the blog');
        $this->setUser($u2);
        $comment->add('comment on first course blog');
        $this->setUser($u4);
        $comment->add('user 4 on course blog');

        $comment = $this->get_comment_object($u3ctx, $entry2->id);
        $this->setUser($u3);
        $comment->add('Hello, I created the module blog');
        $this->setUser($u2);
        $comment->add('I am commenting on both');
        $this->setUser($u5);
        $comment->add('User 5 for modules');

        $this->assertCount(6, $DB->get_records('comments', ['component' => 'blog']));
        $this->assertCount(2, $DB->get_records('post', ['courseid' => $course->id]));
        $this->assertCount(2, $DB->get_records('blog_association'));

        // When using the course or module context we are only removing the blog associations and the comments.
        $userlist = new \core_privacy\local\request\approved_userlist($c1ctx, 'core_blog', [$u2->id, $u1->id, $u5->id]);
        provider::delete_data_for_users($userlist);
        // Only one of the blog_associations should be removed. Everything else should be as before.
        $this->assertCount(6, $DB->get_records('comments', ['component' => 'blog']));
        $this->assertCount(2, $DB->get_records('post', ['courseid' => $course->id]));
        $this->assertCount(1, $DB->get_records('blog_association'));

        $userlist = new \core_privacy\local\request\approved_userlist($cm1ctx, 'core_blog', [$u2->id, $u1->id, $u3->id]);
        provider::delete_data_for_users($userlist);
        // Now we've removed the other association.
        $this->assertCount(6, $DB->get_records('comments', ['component' => 'blog']));
        $this->assertCount(2, $DB->get_records('post', ['courseid' => $course->id]));
        $this->assertEmpty($DB->get_records('blog_association'));
    }

    /**
     * Create a blog post.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_post(array $params) {
        global $DB;
        $post = new stdClass();
        $post->module = 'blog';
        $post->courseid = 0;
        $post->subject = 'the test post';
        $post->summary = 'test post summary text';
        $post->summaryformat = FORMAT_PLAIN;
        $post->publishstate = 'site';
        $post->created = time() - HOURSECS;
        $post->lastmodified = time();
        foreach ($params as $key => $value) {
            $post->{$key} = $value;
        }

        $post->id = $DB->insert_record('post', $post);
        return $post;
    }

    /**
     * Create an extenral blog.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_external_blog(array $params) {
        global $DB;
        $post = new stdClass();
        $post->name = 'test external';
        $post->description = 'the description';
        $post->url = 'http://example.com';
        $post->filtertags = 'a, c, b';
        $post->timefetched = time() - HOURSECS;
        $post->timemodified = time();
        foreach ($params as $key => $value) {
            $post->{$key} = $value;
        }
        $post->id = $DB->insert_record('blog_external', $post);
        return $post;
    }

    /**
     * Get the comment area.
     *
     * @param context $context The context.
     * @param int $itemid The item ID.
     * @param string $component The component.
     * @param string $area The area.
     * @return comment
     */
    protected function get_comment_object(context $context, $itemid) {
        $args = new stdClass();
        $args->context = $context;
        $args->course = get_course(SITEID);
        $args->area = 'format_blog';
        $args->itemid = $itemid;
        $args->component = 'blog';
        $comment = new comment($args);
        $comment->set_post_permission(true);
        return $comment;
    }
}
