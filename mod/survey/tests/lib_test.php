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
 * Unit tests for mod_survey lib
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for mod_survey lib
 *
 * @package    mod_survey
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_survey_lib_testcase extends advanced_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/survey/lib.php');
    }

    /**
     * Test survey_view
     * @return void
     */
    public function test_survey_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($survey->cmid);
        $cm = get_coursemodule_from_instance('survey', $survey->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        survey_view($survey, $course, $cm, $context, 'form');

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/survey/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEquals('form', $event->other['viewed']);
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Test survey_order_questions
     */
    public function test_survey_order_questions() {
        global $DB;

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        $orderedquestionids = explode(',', $survey->questions);
        $surveyquestions = $DB->get_records_list("survey_questions", "id", $orderedquestionids);

        $questionsordered = survey_order_questions($surveyquestions, $orderedquestionids);

        // Check one by one the correct order.
        for ($i = 0; $i < count($orderedquestionids); $i++) {
            $this->assertEquals($orderedquestionids[$i], $questionsordered[$i]->id);
        }
    }

    /**
     * Test survey_save_answers
     */
    public function test_survey_save_answers() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));
        $context = context_module::instance($survey->cmid);

        // Build our questions and responses array.
        $realquestions = array();
        $questions = survey_get_questions($survey);
        $i = 5;
        foreach ($questions as $q) {
            if ($q->type > 0) {
                if ($q->multi) {
                    $subquestions = survey_get_subquestions($q);
                    foreach ($subquestions as $sq) {
                        $key = 'q' . $sq->id;
                        $realquestions[$key] = $i % 5 + 1;
                        $i++;
                    }
                } else {
                    $key = 'q' . $q->id;
                    $realquestions[$key] = $i % 5 + 1;
                    $i++;
                }
            }
        }

        $sink = $this->redirectEvents();
        survey_save_answers($survey, $realquestions, $course, $context);

        // Check the stored answers, they must match.
        $dbanswers = $DB->get_records_menu('survey_answers', array('survey' => $survey->id), '', 'question, answer1');
        foreach ($realquestions as $key => $value) {
            $id = str_replace('q', '', $key);
            $this->assertEquals($value, $dbanswers[$id]);
        }

        // Check events.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\response_submitted', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($survey->id, $event->other['surveyid']);
    }
}
