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
 * Course completion cron unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/completion/tests/lib.php");

class completioncron_testcase extends completion_testcase {

    /**
     * Things this test needs to be checking for:
     * - No users are missed!
     * - The function handles already started users correctly
     *     e.g. does not alter the record in any way
     * - The handling of users with multiple enrolments in the same course
     *     e.g. the lowest non-zero timestart from an active enrolment is used as timeenrolled
     * - The correct times are used for users enrolled in multiple courses
     * - Users who have no current enrolments are not handled
     * - Ignore courses with completion disabled
     */
    public function test_completion_cron_mark_started() {
        global $CFG, $DB;

        $this->_create_complex_testdata();

        $course1 = $this->_testcourses[1];
        $course2 = $this->_testcourses[2];
        $course3 = $this->_testcourses[3];

        $user1 = $this->_testusers[1];
        $user2 = $this->_testusers[2];
        $user3 = $this->_testusers[3];
        $user4 = $this->_testusers[4];

        $now =    $this->_testdates['now'];
        $past =   $this->_testdates['past'];
        $future = $this->_testdates['future'];

        // Run cron function to test
        require_once("{$CFG->dirroot}/completion/cron.php");
        completion_cron_mark_started();

        // Load all records for these courses in course_completions
        // Return results indexed by userid (which will not hide duplicates due to their being a unique index on that and the course columns)
        $cc1 = $DB->get_records('course_completions', array('course' => $course1->id), '', 'userid, *');
        $cc2 = $DB->get_records('course_completions', array('course' => $course2->id), '', 'userid, *');
        $cc3 = $DB->get_records('course_completions', array('course' => $course3->id), '', 'userid, *');

        // Test results
        // Check correct number of records
        $this->assertEquals(4, $DB->count_records('course_completions', array('course' => $course1->id)));
        $this->assertEquals(3, $DB->count_records('course_completions', array('course' => $course2->id)));

        // All users should be mark started in course1
        $this->assertEquals($past-2,    $cc1[$user2->id]->timeenrolled);
        $this->assertGreaterThanOrEqual($now, $cc1[$user4->id]->timeenrolled);
        $this->assertLessThan($now + 60, $cc1[$user4->id]->timeenrolled);

        // User1 should have a timeenrolled in course1 of $past-5 (due to multiple enrolments)
        $this->assertEquals($past-5,    $cc1[$user1->id]->timeenrolled);

        // User3 should have a timeenrolled in course1 of $past-2 (due to multiple enrolments)
        $this->assertEquals($past-2,    $cc1[$user3->id]->timeenrolled);

        // User 2 should not be mark as started in course2 at all (nothing current)
        $this->assertEquals(false,      isset($cc2[$user2->id]));

        // Add some enrolment to course2 with different times to check for bugs
        $this->assertEquals($past-10,   $cc2[$user1->id]->timeenrolled);
        $this->assertEquals($past-15,   $cc2[$user3->id]->timeenrolled);

        // Add enrolment in course2 for user4 (who will be already started)
        $this->assertEquals($past-50,   $cc2[$user4->id]->timeenrolled);

        // Check no records in course with completion disabled
        $this->assertEquals(0, $DB->count_records('course_completions', array('course' => $course3->id)));
    }
}
