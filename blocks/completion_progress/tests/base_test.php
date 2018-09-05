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
 * Base class for unit tests for mod_assign.
 *
 * @package    blocks_completion_progress
 * @category   phpunit
 * @copyright  2017 onwards Nelson Moller  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot.'/blocks/completion_progress/lib.php');

class block_completion_progress_base_testcase extends advanced_testcase {

    /** @const Default number of students to create */
    const DEFAULT_STUDENT_COUNT = 3;
    /** @const Default number of teachers to create */
    const DEFAULT_TEACHER_COUNT = 1;

    /**
     * Setup function - we will create a course and add an assign instance to it.
     */
    protected function setUp() {
        global $DB;

        $this->resetAfterTest(true);

        set_config('enablecompletion', 1);

        $this->course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $this->teachers = array();
        for ($i = 0; $i < self::DEFAULT_TEACHER_COUNT; $i++) {
            array_push($this->teachers, $this->getDataGenerator()->create_user());
        }

        $this->students = array();
        for ($i = 0; $i < self::DEFAULT_STUDENT_COUNT; $i++) {
            array_push($this->students, $this->getDataGenerator()->create_user());
        }

        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        foreach ($this->teachers as $i => $teacher) {
            $this->getDataGenerator()->enrol_user($teacher->id,
              $this->course->id,
              $teacherrole->id);
        }

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        foreach ($this->students as $i => $student) {
            $this->getDataGenerator()->enrol_user($student->id,
              $this->course->id,
              $studentrole->id);
        }
    }

    /**
     * Convenience function to create a testable instance of an assignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return assign Assign class.
     */
    protected function create_instance($params=array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        return new assign($context, $cm, $this->course);
    }

    public function test_assign_get_completion_state() {
        global $DB;

        // Add a block.
        $context = CONTEXT_COURSE::instance($this->course->id);
        $blockinfo = [
          'blockname' => 'completion_progress',
          'parentcontextid' => $context->id,
          'pagetypepattern' => 'course-view-*',
          'showinsubcontexts' => 0,
          'defaultweight' => 5,
          'defaultregion' => 'side-post',
          'configdata' => 'Tzo4OiJzdGRDbGFzcyI6Njp7czo3OiJvcmRlcmJ5IjtzOjExOiJvcmRlcmJ5dGltZSI7czo4OiJsb25nYmFycyI7czo3OiJzcXVlZXp'.
                          'lIjtzOjE2OiJwcm9ncmVzc0Jhckljb25zIjtzOjE6IjEiO3M6MTQ6InNob3dwZXJjZW50YWdlIjtzOjE6IjAiO3M6MTM6InByb2dyZX'.
                          'NzVGl0bGUiO3M6MDoiIjtzOjE4OiJhY3Rpdml0aWVzaW5jbHVkZWQiO3M6MTg6ImFjdGl2aXR5Y29tcGxldGlvbiI7fQ=='
        ];

        $blockinstanceid = $DB->insert_record('block_instances', (object) $blockinfo);

        $assign = $this->create_instance([
          'submissiondrafts' => 0,
          'completionsubmit' => 1,
          'completion' => COMPLETION_TRACKING_AUTOMATIC
        ]);

        $this->setUser($this->students[0]);

        $result = assign_get_completion_state(
          $this->course,
          $assign->get_course_module(),
          $this->students[0]->id,
          false
        );
        $this->assertFalse($result);

        $submissions = block_completion_progress_student_submissions($this->course->id, $this->students[0]->id);
        $config = unserialize(base64_decode($blockinfo['configdata']));
        $activities = block_completion_progress_get_activities($this->course->id, $config);

        $completions = block_completion_progress_completions($activities, $this->students[0]->id, $this->course,
          $submissions);

        $text = block_completion_progress_bar(
          $activities,
          $completions,
          $config,
          $this->students[0]->id,
          $this->course,
          $blockinstanceid
        );

        $this->assertContains('assign', $text, '', true);
        $this->assertNotContains('quiz', $text, '', true);

        // The status is futureNotCompleted.
        $color1 = get_string('futureNotCompleted_colour', 'block_completion_progress');
        $this->assertContains('background-color:' . $color1, $text, '');

        $submission = $assign->get_user_submission($this->students[0]->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $DB->update_record('assign_submission', $submission);

        $result = assign_get_completion_state(
          $this->course,
          $assign->get_course_module(),
          $this->students[0]->id,
          false
        );
        $this->assertTrue($result);

        $submissions = block_completion_progress_student_submissions($this->course->id, $this->students[0]->id);
        $completions = block_completion_progress_completions($activities, $this->students[0]->id, $this->course,
          $submissions);

        $text = block_completion_progress_bar(
          $activities,
          $completions,
          $config,
          $this->students[0]->id,
          $this->course,
          $blockinstanceid
        );

        // The status is send but not finished.
        $color2 = get_string('submittednotcomplete_colour', 'block_completion_progress');
        $this->assertContains('background-color:' . $color2, $text, '');
    }
}