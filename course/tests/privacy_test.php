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
 * Privacy tests for core_course.
 *
 * @package    core_course
 * @category   test
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/completion/tests/fixtures/completion_creation.php');

/**
 * Unit tests for course/classes/privacy/policy
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_privacy_testcase extends \core_privacy\tests\provider_testcase {

    use completion_creation;

    /**
     * Test getting the appropriate context for the userid. This should only ever
     * return the user context for the user id supplied.
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $contextlist = \core_course\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEquals($this->coursecontext->id, $contextlist->current()->id);
    }

    /**
     * Test that user data is exported.
     */
    public function test_export_user_data() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'core_course',
                [$this->coursecontext->id]);
        $writer = \core_privacy\local\request\writer::with_context($this->coursecontext);
        \core_course\privacy\provider::export_user_data($approvedlist);
        $completiondata = $writer->get_data([get_string('privacy:completionpath', 'course')]);
        $this->assertEquals('In progress', $completiondata->status);
        $this->assertCount(2, $completiondata->criteria);
    }

    public function test_export_complete_context_data() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course(['fullname' => 'Course 1', 'shortname' => 'C1']);
        $context1 = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course(['fullname' => 'Course 2', 'shortname' => 'C2']);
        $context2 = context_course::instance($course2->id);
        $course3 = $this->getDataGenerator()->create_course(['fullname' => 'Course 3', 'shortname' => 'C3']);

        $this->setUser($user);
        $modforum = $this->getDataGenerator()->create_module('forum', ['course' => $course1->id]);
        $modresource = $this->getDataGenerator()->create_module('resource', ['course' => $course2->id]);
        $modpage = $this->getDataGenerator()->create_module('page', ['course' => $course3->id]);
        $forumcontext = context_module::instance($modforum->cmid);
        $resourcecontext = context_module::instance($modresource->cmid);

        $collection = new \core_privacy\local\request\contextlist_collection($user->id);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'mod_forum', [$forumcontext->id]);
        $collection->add_contextlist($approvedlist);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'mod_resource', [$resourcecontext->id]);
        $collection->add_contextlist($approvedlist);

        $writer = \core_privacy\local\request\writer::with_context(context_system::instance());
        \core_course\privacy\provider::export_complete_context_data($collection);
        $courses = $writer->get_data();
        print_object($courses);
        // print_object($writer);
    }

    /**
     * Test deleting all user data for one context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(2, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(2, $records);
        \core_course\privacy\provider::delete_data_for_all_users_in_context($this->coursecontext);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(0, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(0, $records);
    }

    /**
     * Test deleting data for only one user.
     */
    public function test_delete_data_for_user() {
        global $DB;
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(2, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(2, $records);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_course',
                [$this->coursecontext->id]);
        \core_course\privacy\provider::delete_data_for_user($approvedlist);
        $records = $DB->get_records('course_modules_completion');
        $this->assertCount(1, $records);
        $records = $DB->get_records('course_completion_crit_compl');
        $this->assertCount(1, $records);
    }
}
