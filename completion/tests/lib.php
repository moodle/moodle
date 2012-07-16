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
 * Course completion unit test helper
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class completion_testcase extends advanced_testcase {

    /**
     * Test data
     */
    protected $_testcourses = array();
    protected $_testusers = array();
    protected $_testdates = array();

    protected function _create_complex_testdata() {
        global $DB;

        $this->resetAfterTest(true);

        $gen = $this->getDataGenerator();

        // Setup a couple of courses
        $courseparams = array('enablecompletion' => 1, 'completionstartonenrol' => 1);
        $course1 = $gen->create_course($courseparams);
        $course2 = $gen->create_course($courseparams);
        $course3 = $gen->create_course();

        // Make all enrolment plugins enabled
        $DB->execute(
            "
            UPDATE
                {enrol}
            SET
                status = ?
            WHERE
                courseid IN (?, ?, ?)
            ",
            array(
                ENROL_INSTANCE_ENABLED,
                $course1->id, $course2->id, $course3->id
            )
        );

        // Setup some users
        $user1 = $gen->create_user();
        $user2 = $gen->create_user();
        $user3 = $gen->create_user();
        $user4 = $gen->create_user();

        // Enrol the users
        $now    = time();
        $past   = $now - (60*60*24);
        $future = $now + (60*60*24);

        $enrolments = array(
            // All users should be mark started in course1
            array($course1->id, $user1->id, $past,      0,              null),
            array($course1->id, $user2->id, $past-2,    0,              null),
            array($course1->id, $user3->id, 0,          0,              null),
            array($course1->id, $user4->id, 0,          0,              null),
            // User1 should have a timeenrolled in course1 of $past-5 (due to multiple enrolments)
            array($course1->id, $user1->id, $past-5,    0,              'self'),
            // User3 should have a timeenrolled in course1 of $past-2 (due to multiple enrolments)
            array($course1->id, $user3->id, $past-2,    0,              'self'),
            array($course1->id, $user3->id, $past-100,  $past,          null), // in the past
            array($course1->id, $user3->id, $future,    $future+100,    null), // in the future
            // User 2 should not be mark as started in course2 at all (nothing current)
            array($course2->id, $user2->id, $future,    0,              null),
            array($course2->id, $user2->id, 0,          $past,          null),
            // Add some enrolment to course2 with different times to check for bugs
            array($course2->id, $user1->id, $past-10,   0,              null),
            array($course2->id, $user3->id, $past-15,   0,              null),
            // Add enrolment in course2 for user4 (who will be already started)
            array($course2->id, $user4->id, $past-13,   0,              null),
            // Add enrolment in course3 even though completion is not enabled
            array($course3->id, $user1->id, 0,          0,              null),
            // Add multiple enrolments of same type!
        );

        foreach ($enrolments as $enrol) {
            $enrol = array(
                'courseid'  => $enrol[0],
                'userid'    => $enrol[1],
                'timestart' => $enrol[2],
                'timeend'   => $enrol[3],
                'plugin'    => $enrol[4]
            );
            if (!$gen->create_enrolment($enrol)) {
                throw new coding_exception('error creating enrolments in test_completion_cron_mark_started()');
            }
        }

        // Delete all old records in case they were missed
        $DB->delete_records('course_completions', array('course' => $course1->id));
        $DB->delete_records('course_completions', array('course' => $course2->id));
        $DB->delete_records('course_completions', array('course' => $course3->id));

        // Create course_completions record for user4 in course2
        $params = array(
            'course'        => $course2->id,
            'userid'        => $user4->id,
            'timeenrolled'  => $past-50,
            'reaggregate'   => 0
        );
        $DB->insert_record('course_completions', $params);


        $this->_testcourses[1] = $course1;
        $this->_testcourses[2] = $course2;
        $this->_testcourses[3] = $course3;

        $this->_testusers[1] = $user1;
        $this->_testusers[2] = $user2;
        $this->_testusers[3] = $user3;
        $this->_testusers[4] = $user4;

        $this->_testdates['now'] = $now;
        $this->_testdates['past'] = $past;
        $this->_testdates['future'] = $future;
    }
}
