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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/completion/tests/fixtures/completion_creation.php');

/**
 * Tests for the \core_completion API's provider functionality.
 *
 * @copyright   2018 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_completion_privacy_test extends \core_privacy\tests\provider_testcase {

    use completion_creation;

    /**
     * Test joining course completion data to an sql statement.
     */
    public function test_get_course_completion_join_sql() {
        global $DB;
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);

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
     * Test getting course completion information.
     */
    public function test_get_course_completion_info() {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->create_course_completion();
        $this->complete_course($user);
        $coursecompletion = \core_completion\privacy\provider::get_course_completion_info($user, $this->course);
        $this->assertEquals('In progress', $coursecompletion['status']);
        $this->assertCount(2, $coursecompletion['criteria']);
    }

    /**
     * Test getting activity completion information.
     */
    public function test_get_activity_completion_info() {
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
    public function test_delete_completion_activity_user() {
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
    public function test_delete_completion_course() {
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
}
