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
 * Unit Tests for the request helper.
 *
 * @package     core_completion
 * @category    test
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_completion\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/completion/tests/fixtures/completion_creation.php');

/**
 * Tests for the \core_completion API's provider functionality.
 *
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    use \completion_creation;

    /**
     * Test joining course completion data to an sql statement.
     */
    public function test_get_course_completion_join_sql(): void {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user, false);

        list($join, $where, $params) = \core_completion\privacy\provider::get_course_completion_join_sql($user->id, 'comp', 'c.id');
        $sql = "SELECT DISTINCT c.id
                FROM {course} c
                {$join}
                WHERE {$where}";
        $records = $DB->get_records_sql($sql, $params);
        $data = array_shift($records);
        $this->assertEquals($this->course->id, $data->id);
    }

    /**
     * Test fetching users' course completion by context and adding to a userlist.
     */
    public function test_add_course_completion_users_to_userlist(): void {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // User1 and user2 complete course.
        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);

        // User3 is enrolled but has not completed course.
        $this->getDataGenerator()->enrol_user($user3->id, $this->course->id, 'student');

        $userlist = new \core_privacy\local\request\userlist($this->coursecontext, 'test');
        \core_completion\privacy\provider::add_course_completion_users_to_userlist($userlist);

        // Ensure only users that have course completion are returned.
        $expected = [$user1->id, $user2->id];
        $actual = $userlist->get_userids();
        sort($expected);
        sort($actual);
        $this->assertCount(2, $actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test getting course completion information.
     */
    public function test_get_course_completion_info(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $coursecompletion = \core_completion\privacy\provider::get_course_completion_info($user, $this->course);
        $this->assertEquals('Complete', $coursecompletion['status']);
        $this->assertCount(2, $coursecompletion['criteria']);
    }

    /**
     * Test getting activity completion information.
     */
    public function test_get_activity_completion_info(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $activitycompletion = \core_completion\privacy\provider::get_activity_completion_info($user, $this->course,
                $this->cm);
        $this->assertEquals($user->id, $activitycompletion->userid);
        $this->assertEquals($this->cm->id, $activitycompletion->coursemoduleid);
        $this->assertEquals(1, $activitycompletion->completionstate);
    }

    /**
     * Test deleting activity completion information for a user.
     */
    public function test_delete_completion_activity_user(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        \core_completion\privacy\provider::delete_completion($user, null, $this->cm->id);
        $activitycompletion = \core_completion\privacy\provider::get_activity_completion_info($user, $this->course,
                $this->cm);
        $this->assertEquals(0, $activitycompletion->completionstate);
    }

    /**
     * Test deleting course completion information.
     */
    public function test_delete_completion_course(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        \core_completion\privacy\provider::delete_completion(null, $this->course->id);
        $coursecompletion = \core_completion\privacy\provider::get_course_completion_info($user, $this->course);
        foreach ($coursecompletion['criteria'] as $criterion) {
            $this->assertEquals('No', $criterion['completed']);
        }
    }

    /**
     * Test deleting course completion information by approved userlist.
     */
    public function test_delete_completion_by_approved_userlist(): void {
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->create_course_completion();
        $this->complete_course($user1);
        $this->complete_course($user2);
        $this->complete_course($user3);
        $this->complete_course($user4);

        // Prepare approved userlist (context/component are irrelevant for this test).
        $approveduserids = [$user1->id, $user3->id];
        $userlist = new \core_privacy\local\request\approved_userlist($this->coursecontext, 'completion', $approveduserids);

        // Test deleting activity completion information only affects approved userlist.
        \core_completion\privacy\provider::delete_completion_by_approved_userlist(
                $userlist, null, $this->cm->id);
        $activitycompletion1 = \core_completion\privacy\provider::get_activity_completion_info($user1, $this->course,
                $this->cm);
        $this->assertEquals(0, $activitycompletion1->completionstate);
        $activitycompletion2 = \core_completion\privacy\provider::get_activity_completion_info($user2, $this->course,
                $this->cm);
        $this->assertNotEquals(0, $activitycompletion2->completionstate);
        $activitycompletion3 = \core_completion\privacy\provider::get_activity_completion_info($user3, $this->course,
                $this->cm);
        $this->assertEquals(0, $activitycompletion3->completionstate);
        $activitycompletion4 = \core_completion\privacy\provider::get_activity_completion_info($user4, $this->course,
                $this->cm);
        $this->assertNotEquals(0, $activitycompletion4->completionstate);

        // Prepare different approved userlist (context/component are irrelevant for this test).
        $approveduserids = [$user2->id, $user4->id];
        $userlist = new \core_privacy\local\request\approved_userlist($this->coursecontext, 'completion', $approveduserids);

        // Test deleting course completion information only affects approved userlist.
        \core_completion\privacy\provider::delete_completion_by_approved_userlist($userlist, $this->course->id);

        $coursecompletion1 = \core_completion\privacy\provider::get_course_completion_info($user1, $this->course);
        $hasno = array_search('No', $coursecompletion1['criteria'], true);
        $this->assertFalse($hasno);
        $coursecompletion2 = \core_completion\privacy\provider::get_course_completion_info($user2, $this->course);
        $hasyes = array_search('Yes', $coursecompletion2['criteria'], true);
        $this->assertFalse($hasyes);
        $coursecompletion3 = \core_completion\privacy\provider::get_course_completion_info($user3, $this->course);
        $hasno = array_search('No', $coursecompletion3['criteria'], true);
        $this->assertFalse($hasno);
        $coursecompletion4 = \core_completion\privacy\provider::get_course_completion_info($user4, $this->course);
        $hasyes = array_search('Yes', $coursecompletion4['criteria'], true);
        $this->assertFalse($hasyes);
    }

    /**
     * Test getting course completion information with completion disabled.
     */
    public function test_get_course_completion_info_completion_disabled(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 0]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $coursecompletion = \core_completion\privacy\provider::get_course_completion_info($user, $course);

        $this->assertTrue(is_array($coursecompletion));
        $this->assertEmpty($coursecompletion);
    }
}
