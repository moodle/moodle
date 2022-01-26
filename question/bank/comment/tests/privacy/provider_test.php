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

namespace qbank_comment\privacy;

use comment;
use context;
use context_course;
use core_privacy\local\metadata\collection;
use qbank_comment\privacy\provider;
use core_privacy\local\request\approved_userlist;
use stdClass;

/**
 * Privacy api tests.
 *
 * @package    qbank_comment
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /** @var stdClass A teacher who is only enrolled in course1. */
    protected $teacher1;

    /** @var stdClass A teacher who is only enrolled in course2. */
    protected $teacher2;

    /** @var stdClass A teacher who is enrolled in both course1 and course2. */
    protected $teacher3;

    /** @var stdClass A test course. */
    protected $course1;

    /** @var stdClass A test course. */
    protected $course2;

    /**
     * Set up function for tests in this class.
     */
    protected function setUp(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create courses.
        $generator = $this->getDataGenerator();
        $this->course1 = $generator->create_course();
        $this->course2 = $generator->create_course();

        // Create and enrol teachers.
        $this->teacher1 = $generator->create_user();
        $this->teacher2 = $generator->create_user();
        $this->teacher3 = $generator->create_user();

        $studentrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $generator->enrol_user($this->teacher1->id,  $this->course1->id, $studentrole->id);
        $generator->enrol_user($this->teacher2->id,  $this->course2->id, $studentrole->id);
        $generator->enrol_user($this->teacher3->id,  $this->course1->id, $studentrole->id);
        $generator->enrol_user($this->teacher3->id,  $this->course2->id, $studentrole->id);
    }

    /**
     * Posts a comment on a given context.
     *
     * @param string $text The comment's text.
     * @param context $context The context on which we want to put the comment.
     */
    protected function add_comment($text, context $context) {
        $args = new stdClass;
        $args->context = $context;
        $args->area = 'question';
        $args->itemid = 0;
        $args->component = 'qbank_comment';
        $args->linktext = get_string('commentheader', 'qbank_comment');
        $args->notoggle = true;
        $args->autostart = true;
        $args->displaycancel = false;
        $comment = new comment($args);

        $comment->add($text);
    }

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('qbank_comment');
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
        $this->setUser($this->teacher1);
        $coursecontext1 = context_course::instance($this->course1->id);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->teacher2);
        $contextlist = provider::get_contexts_for_userid($this->teacher2->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->teacher3);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        $contextlist = provider::get_contexts_for_userid($this->teacher3->id);
        $this->assertCount(2, $contextlist);

        $contextids = $contextlist->get_contextids();
        $this->assertEqualsCanonicalizing([$coursecontext1->id, $coursecontext2->id], $contextids);
    }

    /**
     * Test for provider::export_user_data() when the user has not posted any comments.
     */
    public function test_export_for_context_no_comment() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->teacher1);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->teacher2);

        $this->setUser($this->teacher2);
        $this->export_context_data_for_user($this->teacher1->id, $coursecontext2, 'qbank_comment');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext2);
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context() {
        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->teacher3);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        // Export all of the data for the context.
        $this->export_context_data_for_user($this->teacher3->id, $coursecontext1, 'qbank_comment');
        $writer = \core_privacy\local\request\writer::with_context($coursecontext1);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->teacher1);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->teacher2);
        $this->add_comment('New comment', $coursecontext2);

        $this->setUser($this->teacher3);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        // Before deletion, we should have 3 comments in $coursecontext1 and 2 comments in $coursecontext2.
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext2->id])
        );

        // Delete data based on context.
        provider::delete_data_for_all_users_in_context($coursecontext1);

        // After deletion, the comments for $coursecontext1 should have been deleted.
        $this->assertEquals(
                0,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext2->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->teacher1);
        $this->add_comment('New comment', $coursecontext1);

        $this->setUser($this->teacher2);
        $this->add_comment('New comment', $coursecontext2);

        $this->setUser($this->teacher3);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);

        // Before deletion, we should have 3 comments in $coursecontext1 and 2 comments in $coursecontext2,
        // and 3 comments by student12 in $coursecontext1 and $coursecontext2 combined.
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext2->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'userid' => $this->teacher3->id])
        );

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->teacher3, 'qbank_comment',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_data_for_user($contextlist);

        // After deletion, the comments for the student12 should have been deleted.
        $this->assertEquals(
                1,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext1->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'contextid' => $coursecontext2->id])
        );
        $this->assertEquals(
                0,
                $DB->count_records('comments', ['component' => 'qbank_comment', 'userid' => $this->teacher3->id])
        );
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context() {
        $component = 'qbank_comment';

        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        $this->setUser($this->teacher3);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);
        $this->setUser($this->teacher1);
        $this->add_comment('New comment', $coursecontext1);

        // The list of users should contain teacher3 and user1.
        provider::get_users_in_context($userlist1);
        $this->assertCount(2, $userlist1);
        $this->assertTrue(in_array($this->teacher1->id, $userlist1->get_userids()));
        $this->assertTrue(in_array($this->teacher3->id, $userlist1->get_userids()));

        // The list of users should contain teacher3.
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$this->teacher3->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $component = 'qbank_comment';

        $coursecontext1 = context_course::instance($this->course1->id);
        $coursecontext2 = context_course::instance($this->course2->id);

        $this->setUser($this->teacher3);
        $this->add_comment('New comment', $coursecontext1);
        $this->add_comment('New comment', $coursecontext2);
        $this->setUser($this->teacher1);
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
}
