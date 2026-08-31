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

namespace report_aiusage\reportbuilder\local\systemreports;

use context_course;
use core_reportbuilder\system_report_factory;
use core_reportbuilder\exception\report_access_exception;

/**
 * Tests for the course_usage system report.
 *
 * @package    report_aiusage
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \report_aiusage\reportbuilder\local\systemreports\course_usage
 */
final class course_usage_test extends \advanced_testcase {
    /** @var int Counter used to keep actionid unique across ai_action_register test rows. */
    private static int $nextactionid = 0;

    /**
     * Insert an ai_action_register row for the given user, context and course.
     *
     * @param int $userid
     * @param int $contextid
     * @param int $courseid
     */
    private function insert_ai_action(int $userid, int $contextid, int $courseid): void {
        global $DB;

        $DB->insert_record('ai_action_register', (object) [
            'actionname' => 'generate_text',
            'actionid' => ++self::$nextactionid,
            'success' => 1,
            'userid' => $userid,
            'contextid' => $contextid,
            'provider' => 'aiprovider_openai',
            'timecreated' => time(),
            'timecompleted' => time(),
            'courseid' => $courseid,
        ]);
    }

    /**
     * Set $PAGE->url, matching what index.php does before rendering the report.
     *
     * @param int $courseid
     */
    private function set_page_url(int $courseid): void {
        global $PAGE;

        $PAGE->set_url('/report/aiusage/index.php', ['id' => $courseid]);
    }

    /**
     * Create a course with an editing teacher and two students, and log one AI action per student.
     *
     * @return array [course, teacher, student1, student2]
     */
    private function create_course_with_ai_usage(): array {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $coursecontext = context_course::instance($course->id);

        $teacher = $generator->create_and_enrol($course, 'editingteacher');
        $student1 = $generator->create_and_enrol($course, 'student');
        $student2 = $generator->create_and_enrol($course, 'student');

        foreach ([$student1, $student2] as $student) {
            $this->insert_ai_action($student->id, $coursecontext->id, $course->id);
        }

        return [$course, $teacher, $student1, $student2];
    }

    /**
     * Test a user with the "view all" capability sees every student's usage in the course.
     */
    public function test_teacher_sees_all_students(): void {
        $this->resetAfterTest();

        [$course, $teacher, $student1, $student2] = $this->create_course_with_ai_usage();
        $context = context_course::instance($course->id);

        $this->setUser($teacher);
        $this->set_page_url($course->id);
        $report = system_report_factory::create(course_usage::class, $context, 'report_aiusage', '', 0, [
            'courseid' => $course->id,
        ]);
        $content = $report->output();

        $this->assertStringContainsString(fullname($student1), $content);
        $this->assertStringContainsString(fullname($student2), $content);
    }

    /**
     * Test a user with only the "view own" capability sees only their own usage in the course.
     */
    public function test_student_sees_only_own_usage(): void {
        $this->resetAfterTest();

        [$course, , $student1, $student2] = $this->create_course_with_ai_usage();
        $context = context_course::instance($course->id);

        $this->setUser($student1);
        $this->set_page_url($course->id);
        $report = system_report_factory::create(course_usage::class, $context, 'report_aiusage', '', 0, [
            'courseid' => $course->id,
            'restricttouserid' => $student1->id,
        ]);
        $content = $report->output();

        $this->assertStringContainsString(fullname($student1), $content);
        $this->assertStringNotContainsString(fullname($student2), $content);
    }

    /**
     * Test the report is unavailable to a user with neither capability.
     */
    public function test_unauthorised_user_cannot_view_report(): void {
        $this->resetAfterTest();

        [$course] = $this->create_course_with_ai_usage();
        $context = context_course::instance($course->id);

        // A user with no role at all in the course has neither capability.
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $this->expectException(report_access_exception::class);
        system_report_factory::create(course_usage::class, $context, 'report_aiusage', '', 0, [
            'courseid' => $course->id,
        ]);
    }

    /**
     * Test a user with only the "view own" capability cannot request the report without the
     * restriction to their own actions, as the parameters round-trip through the client.
     */
    public function test_student_cannot_drop_own_user_restriction(): void {
        $this->resetAfterTest();

        [$course, , $student1] = $this->create_course_with_ai_usage();
        $context = context_course::instance($course->id);

        $this->setUser($student1);
        $this->set_page_url($course->id);

        $this->expectException(report_access_exception::class);
        system_report_factory::create(course_usage::class, $context, 'report_aiusage', '', 0, [
            'courseid' => $course->id,
        ]);
    }

    /**
     * Test a user cannot point the report at a course where they hold neither capability.
     */
    public function test_student_cannot_view_other_course(): void {
        $this->resetAfterTest();

        [$course, , $student1] = $this->create_course_with_ai_usage();
        $context = context_course::instance($course->id);

        // A course the student is not enrolled in, so they hold neither capability there.
        $othercourse = $this->getDataGenerator()->create_course();

        $this->setUser($student1);
        $this->set_page_url($course->id);

        $this->expectException(report_access_exception::class);
        system_report_factory::create(course_usage::class, $context, 'report_aiusage', '', 0, [
            'courseid' => $othercourse->id,
            'restricttouserid' => $student1->id,
        ]);
    }

    /**
     * Test a viewer without the "access all groups" capability in a separate groups course only
     * sees actions from members of their own groups.
     */
    public function test_separate_groups_viewer_sees_only_own_groups(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['groupmode' => SEPARATEGROUPS]);
        $coursecontext = context_course::instance($course->id);

        // Non-editing teachers hold report/aiusage:view but not moodle/site:accessallgroups.
        $teacher = $generator->create_and_enrol($course, 'teacher');
        $student1 = $generator->create_and_enrol($course, 'student');
        $student2 = $generator->create_and_enrol($course, 'student');

        foreach ([$student1, $student2] as $student) {
            $this->insert_ai_action($student->id, $coursecontext->id, $course->id);
        }

        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);
        groups_add_member($group1, $teacher);
        groups_add_member($group1, $student1);
        groups_add_member($group2, $student2);

        $this->setUser($teacher);
        $this->set_page_url($course->id);
        $report = system_report_factory::create(course_usage::class, $coursecontext, 'report_aiusage', '', 0, [
            'courseid' => $course->id,
        ]);
        $content = $report->output();

        $this->assertStringContainsString(fullname($student1), $content);
        $this->assertStringNotContainsString(fullname($student2), $content);
    }

    /**
     * Test the report only includes actions for the given course, not actions from other courses.
     */
    public function test_report_scoped_to_course(): void {
        $this->resetAfterTest();

        [$course, $teacher, $student1] = $this->create_course_with_ai_usage();
        $context = context_course::instance($course->id);

        // A student in a different course, whose AI action must not leak into this course's report.
        $othercourse = $this->getDataGenerator()->create_course();
        $otherstudent = $this->getDataGenerator()->create_and_enrol($othercourse, 'student');
        $this->insert_ai_action($otherstudent->id, context_course::instance($othercourse->id)->id, $othercourse->id);

        $this->setUser($teacher);
        $this->set_page_url($course->id);
        $report = system_report_factory::create(course_usage::class, $context, 'report_aiusage', '', 0, [
            'courseid' => $course->id,
        ]);
        $content = $report->output();

        $this->assertStringContainsString(fullname($student1), $content);
        $this->assertStringNotContainsString(fullname($otherstudent), $content);
    }
}
