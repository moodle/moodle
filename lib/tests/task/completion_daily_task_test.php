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

namespace core\task;

use advanced_testcase;

/**
 * Class containing unit tests for the daily completion cron task.
 *
 * @package core
 * @copyright 2020 Jun Pataleta
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class completion_daily_task_test extends advanced_testcase {

    /**
     * Test calendar cron task with a broken subscription URL.
     */
    public function test_completion_daily_cron(): void {
        global $DB;

        $this->resetAfterTest();

        set_config('enablecompletion', 1);
        set_config('enrol_plugins_enabled', 'self,manual');

        $generator = $this->getDataGenerator();

        $now = time();
        $lastweek = $now - WEEKSECS;
        $yesterday = $now - DAYSECS;
        $tomorrow = $now + DAYSECS;

        // Course with completion enabled and has already started.
        $c1 = $generator->create_course(['enablecompletion' => 1, 'startdate' => $lastweek]);
        // Course with completion enabled but hasn't started yet.
        $c2 = $generator->create_course(['enablecompletion' => 1, 'startdate' => $tomorrow]);
        // Completion not enabled.
        $c3 = $generator->create_course();

        // Create users.
        $t1 = $generator->create_user(['username' => 't1']);
        $t2 = $generator->create_user(['username' => 't2']);
        $s1 = $generator->create_user(['username' => 's1']);
        $s2 = $generator->create_user(['username' => 's2']);

        // Enrol s1 by self and manual methods to c1.
        $generator->enrol_user($s1->id, $c1->id, 'student', 'self', $lastweek);
        $generator->enrol_user($s1->id, $c1->id, 'student', 'manual', $yesterday);

        // Enrol s1 by self and manual methods to c2.
        $generator->enrol_user($s1->id, $c2->id, 'student', 'self');
        $generator->enrol_user($s1->id, $c2->id, 'student', 'manual', $tomorrow);

        // Enrol s1 by self and manual methods to c3.
        $generator->enrol_user($s1->id, $c3->id, 'student', 'self', $lastweek);
        $generator->enrol_user($s1->id, $c3->id, 'student');

        // Enrol the rest.
        foreach ([$c1, $c2, $c3] as $course) {
            // Enrol s2 by manual and self enrol methods.
            $generator->enrol_user($s2->id, $course->id, 'student', 'self');
            $generator->enrol_user($s2->id, $course->id, 'student', 'manual', $course->startdate);

            // Enrol t1 as teacher to these courses.
            $generator->enrol_user($t1->id, $course->id, 'editingteacher', 'manual', $course->startdate);
            $generator->enrol_user($t1->id, $course->id, 'editingteacher', 'manual', $course->startdate);

            // Enrol t2 as a non-editing teacher to these courses.
            $generator->enrol_user($t1->id, $course->id, 'teacher', 'manual', $course->startdate);
            $generator->enrol_user($t1->id, $course->id, 'teacher', 'manual', $course->startdate);
        }

        // The course completion table should be empty prior to running the task.
        $this->assertEquals(0, $DB->count_records('course_completions'));

        // Run the daily completion task.
        ob_start();
        $task = new completion_daily_task();
        $task->execute();
        ob_end_clean();

        // Confirm there are no completion records for teachers nor for courses that haven't started yet or without completion.
        list($tsql, $tparams) = $DB->get_in_or_equal([$t1->id, $t2->id], SQL_PARAMS_NAMED);
        list($csql, $cparams) = $DB->get_in_or_equal([$c2->id, $c3->id], SQL_PARAMS_NAMED);
        $select = "userid $tsql OR course $csql";
        $params = array_merge($tparams, $cparams);
        $this->assertEmpty($DB->get_records_select('course_completions', $select, $params));

        // We should have 2 completion records for both s1 and s2 in course c1.
        $this->assertCount(2, $DB->get_records('course_completions'));

        // Get s1's completion record from c1.
        $s1c1 = $DB->get_record('course_completions', ['userid' => $s1->id, 'course' => $c1->id]);
        $this->assertGreaterThanOrEqual($now, $s1c1->timeenrolled);
        $this->assertLessThanOrEqual(time(), $s1c1->timeenrolled);

        // Get s2's completion record from c1.
        $s2c1 = $DB->get_record('course_completions', ['userid' => $s2->id, 'course' => $c1->id]);
        $this->assertGreaterThanOrEqual($now, $s2c1->timeenrolled);
        $this->assertLessThanOrEqual(time(), $s2c1->timeenrolled);
    }
}
