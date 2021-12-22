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
 * Privacy provider tests.
 *
 * @package    block_comments
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_comments\privacy;

use core_privacy\local\metadata\collection;
use block_comments\privacy\provider;
use core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider test for block_comments.
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /** @var stdClass A student who is only enrolled in course1. */
    protected $student1;

    /** @var stdClass A student who is only enrolled in course2. */
    protected $student2;

    /** @var stdClass A student who is enrolled in both course1 and course2. */
    protected $student12;

    /** @var stdClass A test course. */
    protected $course1;

    /** @var stdClass A test course. */
    protected $course2;

    protected function setUp(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create courses.
        $generator = $this->getDataGenerator();
        $this->course1 = $generator->create_course();
        $this->course2 = $generator->create_course();

        // Create and enrol students.
        $this->student1 = $generator->create_user();
        $this->student2 = $generator->create_user();
        $this->student12 = $generator->create_user();

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $generator->enrol_user($this->student1->id,  $this->course1->id, $studentrole->id);
        $generator->enrol_user($this->student2->id,  $this->course2->id, $studentrole->id);
        $generator->enrol_user($this->student12->id,  $this->course1->id, $studentrole->id);
        $generator->enrol_user($this->student12->id,  $this->course2->id, $studentrole->id);

        // Comment block on course pages.
        $block = $this->add_comments_block_in_context(\context_course::instance($this->course1->id));
        $block = $this->add_comments_block_in_context(\context_course::instance($this->course2->id));
    }

    /**
     * Posts a comment on a given context.
     *
     * @param string $text The comment's text.
     * @param context $context The context on which we want to put the comment.
     */
    protected function add_comment($text, \context $context) {
        $args = new \stdClass;
        $args->context = $context;
        $args->area = 'page_comments';
        $args->itemid = 0;
        $args->component = 'block_comments';
        $args->linktext = get_string('showcomments');
        $args->notoggle = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $comment = new \comment($args);

        $comment->add($text);
    }

    /**
     * Creates a comments block on a context.
     *
     * @param context $context The context on which we want to put the block.
     * @return block_base The created block instance.
     * @throws coding_exception
     */
    protected function add_comments_block_in_context(\context $context) {
        global $DB;

        $course = null;

        $page = new \moodle_page();
        $page->set_context($context);

        switch ($context->contextlevel) {
            case CONTEXT_SYSTEM:
                $page->set_pagelayout('frontpage');
                $page->set_pagetype('site-index');
                break;
            case CONTEXT_COURSE:
                $page->set_pagelayout('standard');
                $page->set_pagetype('course-view');
                $course = $DB->get_record('course', ['id' => $context->instanceid]);
                $page->set_course($course);
                break;
            case CONTEXT_MODULE:
                $page->set_pagelayout('standard');
                $mod = $DB->get_field_sql("SELECT m.name
                                             FROM {modules} m
                                             JOIN {course_modules} cm on cm.module = m.id
                                            WHERE cm.id = ?", [$context->instanceid]);
                $page->set_pagetype("mod-$mod-view");
                break;
            case CONTEXT_USER:
                $page->set_pagelayout('mydashboard');
                $page->set_pagetype('my-index');
                break;
            default:
                throw new coding_exception('Unsupported context for test');
        }

        $page->blocks->load_blocks();

        $page->blocks->add_block_at_end_of_default_region('comments');

        // We need to use another page object as load_blocks() only loads the blocks once.
        $page2 = new \moodle_page();
        $page2->set_context($page->context);
        $page2->set_pagelayout($page->pagelayout);
        $page2->set_pagetype($page->pagetype);
        if ($course) {
            $page2->set_course($course);
        }

        $page->blocks->load_blocks();
        $page2->blocks->load_blocks();
        $blocks = $page2->blocks->get_blocks_for_region($page2->blocks->get_default_region());
        $block = end($blocks);

        $block = block_instance('comments', $block->instance);

        return $block;
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('block_comments');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $link = reset($itemcollection);

        $this->assertEquals('core_comment', $link->get_name());
        $this->assertEmpty($link->get_privacy_fields());
        $this->assertEquals('privacy:metadata:core_comment', $link->get_summary());
    }

    /**
     * Test for provider::get_contexts_for_userid() when user had not posted any comments..
     */
    public function test_get_contexts_for_userid_no_comment() {
        $this->setUser($this->student1);
        $coursecontext1 = \context_course::instance($this->course1->id);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->student2);
        $contextlist = provider::get_contexts_for_userid($this->student2->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $this->setUser($this->student12);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        $contextlist = provider::get_contexts_for_userid($this->student12->id);
        $this->assertCount(2, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEqualsCanonicalizing([$coursecontext1->id, $coursecontext2->id], $contextids);
    }

    /**
     * Test for provider::export_user_data() when the user has not posted any comments.
     */
    public function test_export_for_context_no_comment() {
        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $this->setUser($this->student1);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->student2);

        $this->setUser($this->student2);
        $this->export_context_data_for_user($this->student2->id, $coursecontext2, 'block_comments');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext2);
        $this->assertFalse($writer->has_any_data());

    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $this->setUser($this->student12);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->student12->id, $coursecontext1, 'block_comments');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext1);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $this->setUser($this->student1);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->student2);
        $this->add_comment('New comment', $coursecontext2);

        $this->setUser($this->student12);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        // Before deletion, we should have 3 comments in $coursecontext1 and 2 comments in $coursecontext2.
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext2->id])
        );

        // Delete data based on context.
        provider::delete_data_for_all_users_in_context($coursecontext1);

        // After deletion, the comments for $coursecontext1 should have been deleted.
        $this->assertEquals(
                0,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext2->id])
        );
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context() when there are also comments from other plugins.
     */
    public function test_delete_data_for_all_users_in_context_with_comments_from_other_plugins() {
        global $DB;

        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $this->course1]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $assigncontext = \context_module::instance($cm->id);
        $assign = new \assign($assigncontext, $cm, $this->course1);

        // Add a comments block in the assignment page.
        $this->add_comments_block_in_context($assigncontext);

        $submission = $assign->get_user_submission($this->student1->id, true);

        $options = new \stdClass();
        $options->area = 'submission_comments';
        $options->course = $assign->get_course();
        $options->context = $assigncontext;
        $options->itemid = $submission->id;
        $options->component = 'assignsubmission_comments';
        $options->showcount = true;
        $options->displaycancel = true;

        $comment = new \comment($options);
        $comment->set_post_permission(true);

        $this->setUser($this->student1);
        $comment->add('Comment from student 1');

        $this->add_comment('New comment', $assigncontext);

        $this->setUser($this->student2);
        $this->add_comment('New comment', $assigncontext);

        // Before deletion, we should have 3 comments in $assigncontext.
        // One comment is for the assignment submission and 2 are for the comments block.
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['contextid' => $assigncontext->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $assigncontext->id])
        );

        provider::delete_data_for_all_users_in_context($assigncontext);

        // After deletion, the comments for $assigncontext in the comment block should have been deleted,
        // but the assignment submission comment should be left.
        $this->assertEquals(
                1,
                $DB->count_records('comments', ['contextid' => $assigncontext->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $assigncontext->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $this->setUser($this->student1);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->student2);
        $this->add_comment('New comment', $coursecontext2);

        $this->setUser($this->student12);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        // Before deletion, we should have 3 comments in $coursecontext1 and 2 comments in $coursecontext2,
        // and 3 comments by student12 in $coursecontext1 and $coursecontext2 combined.
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext2->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['component' => 'block_comments', 'userid' => $this->student12->id])
        );

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student12, 'block_comments',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the comments for the student12 should have been deleted.
        $this->assertEquals(
                1,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('comments', ['component' => 'block_comments', 'contextid' => $coursecontext2->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('comments', ['component' => 'block_comments', 'userid' => $this->student12->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user() when there are also comments from other plugins.
     */
    public function test_delete_data_for_user_with_comments_from_other_plugins() {
        global $DB;

        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $this->course1]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $assigncontext = \context_module::instance($cm->id);
        $assign = new \assign($assigncontext, $cm, $this->course1);

        // Add a comments block in the assignment page.
        $this->add_comments_block_in_context($assigncontext);

        $submission = $assign->get_user_submission($this->student1->id, true);

        $options = new \stdClass();
        $options->area = 'submission_comments';
        $options->course = $assign->get_course();
        $options->context = $assigncontext;
        $options->itemid = $submission->id;
        $options->component = 'assignsubmission_comments';
        $options->showcount = true;
        $options->displaycancel = true;

        $comment = new \comment($options);
        $comment->set_post_permission(true);

        $this->setUser($this->student1);
        $comment->add('Comment from student 1');

        $this->add_comment('New comment', $assigncontext);
        $this->add_comment('New comment', $assigncontext);

        // Before deletion, we should have 3 comments in $assigncontext.
        // one comment is for the assignment submission and 2 are for the comments block.
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['contextid' => $assigncontext->id])
        );

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->student1, 'block_comments',
                [$assigncontext->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the comments for the student1 in the comment block should have been deleted,
        // but the assignment submission comment should be left.
        $this->assertEquals(
                1,
                $DB->count_records('comments', ['contextid' => $assigncontext->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('comments', ['component' => 'block_comments', 'userid' => $this->student1->id])
        );
    }

    /**
     * Test that only users within a course context are fetched.
     * @group qtesttt
     */
    public function test_get_users_in_context() {
        $component = 'block_comments';

        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        $this->setUser($this->student12);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);
        $this->setUser($this->student1);
        $this->add_comment('New comment', $coursecontext1);

        // The list of users should contain user12 and user1.
        provider::get_users_in_context($userlist1);
        $this->assertCount(2, $userlist1);
        $this->assertTrue(in_array($this->student1->id, $userlist1->get_userids()));
        $this->assertTrue(in_array($this->student12->id, $userlist1->get_userids()));

        // The list of users should contain user12.
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$this->student12->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $component = 'block_comments';

        $coursecontext1 = \context_course::instance($this->course1->id);
        $coursecontext2 = \context_course::instance($this->course2->id);

        $this->setUser($this->student12);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);
        $this->setUser($this->student1);
        $this->add_comment('New comment', $coursecontext1);

        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(2, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($coursecontext1, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in coursecontext1.
        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        // The user data in coursecontext1 should be deleted.
        $this->assertCount(0, $userlist1);

        // Re-fetch users in coursecontext2.
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in coursecontext2 should be still present.
        $this->assertCount(1, $userlist2);
    }

    /**
     * Test for provider::delete_data_for_user() when there are also comments from other plugins.
     */
    public function test_delete_data_for_users_with_comments_from_other_plugins() {
        $component = 'block_comments';

        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $this->course1]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $assigncontext = \context_module::instance($cm->id);
        $assign = new \assign($assigncontext, $cm, $this->course1);

        // Add a comments block in the assignment page.
        $this->add_comments_block_in_context($assigncontext);

        $submission = $assign->get_user_submission($this->student1->id, true);

        $options = new \stdClass();
        $options->area = 'submission_comments';
        $options->course = $assign->get_course();
        $options->context = $assigncontext;
        $options->itemid = $submission->id;
        $options->component = 'assignsubmission_comments';
        $options->showcount = true;
        $options->displaycancel = true;

        $comment = new \comment($options);
        $comment->set_post_permission(true);

        $this->setUser($this->student1);
        $comment->add('Comment from student 1');

        $this->add_comment('New comment', $assigncontext);
        $this->add_comment('New comment', $assigncontext);

        $userlist1 = new \core_privacy\local\request\userlist($assigncontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist = new approved_userlist($assigncontext, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in assigncontext.
        $userlist1 = new \core_privacy\local\request\userlist($assigncontext, $component);
        provider::get_users_in_context($userlist1);
        // The user data in assigncontext should be deleted.
        $this->assertCount(0, $userlist1);
    }
}
