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

namespace mod_survey;

use externallib_advanced_testcase;
use mod_survey_external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/survey/lib.php');

/**
 * Survey module external functions tests
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->survey = $this->getDataGenerator()->create_module('survey', array('course' => $this->course->id));
        $this->context = \context_module::instance($this->survey->cmid);
        $this->cm = get_coursemodule_from_instance('survey', $this->survey->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }


    /*
     * Test get surveys by courses
     */
    public function test_mod_survey_get_surveys_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second survey.
        $record = new \stdClass();
        $record->course = $course2->id;
        $survey2 = self::getDataGenerator()->create_module('survey', $record);
        // Force empty intro.
        $DB->set_field('survey', 'intro', '', array('id' => $survey2->id));

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $this->student->id, $this->studentrole->id);

        self::setUser($this->student);

        $returndescription = mod_survey_external::get_surveys_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $expectedfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'template', 'days',
                                'questions', 'surveydone');

        // Add expected coursemodule and data.
        $survey1 = $this->survey;
        $survey1->coursemodule = $survey1->cmid;
        $survey1->introformat = 1;
        $survey1->surveydone = 0;
        $survey1->section = 0;
        $survey1->visible = true;
        $survey1->groupmode = 0;
        $survey1->groupingid = 0;
        $survey1->introfiles = [];

        $survey2->coursemodule = $survey2->cmid;
        $survey2->introformat = 1;
        $survey2->surveydone = 0;
        $survey2->section = 0;
        $survey2->visible = true;
        $survey2->groupmode = 0;
        $survey2->groupingid = 0;
        $tempo = $DB->get_field("survey", "intro", array("id" => $survey2->template));
        $survey2->intro = nl2br(get_string($tempo, "survey"));
        $survey2->introfiles = [];

        foreach ($expectedfields as $field) {
            $expected1[$field] = $survey1->{$field};
            $expected2[$field] = $survey2->{$field};
        }

        $expectedsurveys = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_survey_external::get_surveys_by_courses(array($course2->id, $this->course->id));
        $result = \external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedsurveys, $result['surveys']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_survey_external::get_surveys_by_courses();
        $result = \external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected surveys.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedsurveys);

        // Call the external function without passing course id.
        $result = mod_survey_external::get_surveys_by_courses();
        $result = \external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_survey_external::get_surveys_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        $additionalfields = array('timecreated', 'timemodified', 'section', 'visible', 'groupmode', 'groupingid');

        foreach ($additionalfields as $field) {
            $expectedsurveys[0][$field] = $survey1->{$field};
        }

        $result = mod_survey_external::get_surveys_by_courses();
        $result = \external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_survey_external::get_surveys_by_courses(array($this->course->id));
        $result = \external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedsurveys, $result['surveys']);

        // Now, prohibit capabilities.
        $this->setUser($this->student);
        $contextcourse1 = \context_course::instance($this->course->id);
        // Prohibit capability = mod/survey:participate on Course1 for students.
        assign_capability('mod/survey:participate', CAP_PROHIBIT, $this->studentrole->id, $contextcourse1->id);
        accesslib_clear_all_caches_for_unit_testing();

        $surveys = mod_survey_external::get_surveys_by_courses(array($this->course->id));
        $surveys = \external_api::clean_returnvalue(mod_survey_external::get_surveys_by_courses_returns(), $surveys);
        $this->assertFalse(isset($surveys['surveys'][0]['intro']));
    }

    /**
     * Test view_survey
     */
    public function test_view_survey() {
        global $DB;

        // Test invalid instance id.
        try {
            mod_survey_external::view_survey(0);
            $this->fail('Exception expected due to invalid mod_survey instance id.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_survey_external::view_survey($this->survey->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_survey_external::view_survey($this->survey->id);
        $result = \external_api::clean_returnvalue(mod_survey_external::view_survey_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodlesurvey = new \moodle_url('/mod/survey/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodlesurvey, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/survey:participate', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_survey_external::view_survey($this->survey->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

    }

    /**
     * Test get_questions
     */
    public function test_get_questions() {
        global $DB;

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Build our expectation array.
        $expectedquestions = array();
        $questions = survey_get_questions($this->survey);
        foreach ($questions as $q) {
            if ($q->type >= 0) {
                $expectedquestions[$q->id] = $q;
                if ($q->multi) {
                    $subquestions = survey_get_subquestions($q);
                    foreach ($subquestions as $sq) {
                        $expectedquestions[$sq->id] = $sq;
                    }
                }
            }
        }

        $result = mod_survey_external::get_questions($this->survey->id);
        $result = \external_api::clean_returnvalue(mod_survey_external::get_questions_returns(), $result);

        // Check we receive the same questions.
        $this->assertCount(0, $result['warnings']);
        foreach ($result['questions'] as $q) {
            $this->assertEquals(get_string($expectedquestions[$q['id']]->text, 'survey'), $q['text']);
            $this->assertEquals(get_string($expectedquestions[$q['id']]->shorttext, 'survey'), $q['shorttext']);
            $this->assertEquals($expectedquestions[$q['id']]->multi, $q['multi']);
            $this->assertEquals($expectedquestions[$q['id']]->type, $q['type']);
            // Parent questions must have parent eq to 0.
            if ($q['multi']) {
                $this->assertEquals(0, $q['parent']);
                $this->assertEquals(get_string($expectedquestions[$q['id']]->options, 'survey'), $q['options']);
            }
        }

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/survey:participate', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_survey_external::get_questions($this->survey->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }
    }

    /**
     * Test submit_answers
     */
    public function test_submit_answers() {
        global $DB;

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Build our questions and responses array.
        $realquestions = array();
        $questions = survey_get_questions($this->survey);
        $i = 5;
        foreach ($questions as $q) {
            if ($q->type >= 0) {
                if ($q->multi) {
                    $subquestions = survey_get_subquestions($q);
                    foreach ($subquestions as $sq) {
                        $realquestions[] = array(
                            'key' => 'q' . $sq->id,
                            'value' => $i % 5 + 1   // Values between 1 and 5.
                        );
                        $i++;
                    }
                } else {
                    $realquestions[] = array(
                        'key' => 'q' . $q->id,
                        'value' => $i % 5 + 1
                    );
                    $i++;
                }
            }
        }

        $result = mod_survey_external::submit_answers($this->survey->id, $realquestions);
        $result = \external_api::clean_returnvalue(mod_survey_external::submit_answers_returns(), $result);

        $this->assertTrue($result['status']);
        $this->assertCount(0, $result['warnings']);

        $dbanswers = $DB->get_records_menu('survey_answers', array('survey' => $this->survey->id), '', 'question, answer1');
        foreach ($realquestions as $q) {
            $id = str_replace('q', '', $q['key']);
            $this->assertEquals($q['value'], $dbanswers[$id]);
        }

        // Submit again, we expect an error here.
        try {
            mod_survey_external::submit_answers($this->survey->id, $realquestions);
            $this->fail('Exception expected due to answers already submitted.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('alreadysubmitted', $e->errorcode);
        }

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/survey:participate', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            mod_survey_external::submit_answers($this->survey->id, $realquestions);
            $this->fail('Exception expected due to missing capability.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_survey_external::submit_answers($this->survey->id, $realquestions);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (\moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }
    }

}
