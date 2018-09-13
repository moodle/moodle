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
 * PHPUnit questionnaire generator tests
 *
 * @package    mod_questionnaire
 * @copyright  2015 Mike Churchward (mike@churchward.ca)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_questionnaire\question\base;

global $CFG;
require_once($CFG->dirroot.'/mod/questionnaire/lib.php');
require_once($CFG->dirroot.'/mod/questionnaire/classes/question/base.php');

/**
 * Unit tests for {@link questionnaire_lib_testcase}.
 * @group mod_questionnaire
 */
class mod_questionnaire_lib_testcase extends advanced_testcase {
    public function test_questionnaire_supports() {
        $this->assertTrue(questionnaire_supports(FEATURE_BACKUP_MOODLE2));
        $this->assertFalse(questionnaire_supports(FEATURE_COMPLETION_TRACKS_VIEWS));
        $this->assertTrue(questionnaire_supports(FEATURE_COMPLETION_HAS_RULES));
        $this->assertFalse(questionnaire_supports(FEATURE_GRADE_HAS_GRADE));
        $this->assertFalse(questionnaire_supports(FEATURE_GRADE_OUTCOMES));
        $this->assertTrue(questionnaire_supports(FEATURE_GROUPINGS));
        $this->assertTrue(questionnaire_supports(FEATURE_GROUPS));
        $this->assertTrue(questionnaire_supports(FEATURE_MOD_INTRO));
        $this->assertTrue(questionnaire_supports(FEATURE_SHOW_DESCRIPTION));
        $this->assertNull(questionnaire_supports('unknown option'));
    }

    public function test_questionnaire_get_extra_capabilities() {
        $caps = questionnaire_get_extra_capabilities();
        $this->assertInternalType('array', $caps);
        $this->assertEquals(1, count($caps));
        $this->assertEquals('moodle/site:accessallgroups', reset($caps));
    }

    public function test_add_instance() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        // Create test data as a record.
        $questdata = new stdClass();
        $questdata->course = $course->id;
        $questdata->coursemodule = '';
        $questdata->name = 'Test questionnaire';
        $questdata->intro = 'Intro to test questionnaire.';
        $questdata->introformat = FORMAT_HTML;
        $questdata->qtype = 1;
        $questdata->respondenttype = 'anonymous';
        $questdata->resp_eligible = 'none';
        $questdata->resp_view = 2;
        $questdata->opendate = 99;
        $questdata->closedate = 50;
        $questdata->resume = 1;
        $questdata->navigate = 1;
        $questdata->grade = 100;
        $questdata->sid = 1;
        $questdata->timemodified = 3;
        $questdata->completionsubmit = 1;
        $questdata->autonum = 1;

        // Call add_instance with the data.
        $this->assertTrue(questionnaire_add_instance($questdata) > 0);
    }

    public function test_update_instance() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        /** @var mod_questionnaire_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        /** @var questionnaire $questionnaire */
        $questionnaire = $generator->create_instance(array('course' => $course->id, 'sid' => 1));

        $qid = $questionnaire->id;
        $this->assertTrue($qid > 0);

        // Change all the default values.
        // Note, we need to get the actual db row to do an update to it.
        $qrow = $DB->get_record('questionnaire', ['id' => $qid]);
        $qrow->qtype = 1;
        $qrow->respondenttype = 'anonymous';
        $qrow->resp_eligible = 'none';
        $qrow->resp_view = 2;
        $qrow->useopendate = true;
        $qrow->opendate = 99;
        $qrow->useclosedate = true;
        $qrow->closedate = 50;
        $qrow->resume = 1;
        $qrow->navigate = 1;
        $qrow->grade = 100;
        $qrow->timemodified = 3;
        $qrow->completionsubmit = 1;
        $qrow->autonum = 1;
        $qrow->coursemodule = $questionnaire->cm->id;

        // Moodle update form passes "instance" instead of "id" to [mod]_update_instance.
        $qrow->instance = $qid;
        // Grade function needs the "cm" "idnumber" field.
        $qrow->cmidnumber = '';

        $this->assertTrue(questionnaire_update_instance($qrow));

        $questrecord = $DB->get_record('questionnaire', array('id' => $qid));
        $this->assertNotEmpty($questrecord);
        $this->assertEquals($qrow->qtype, $questrecord->qtype);
        $this->assertEquals($qrow->respondenttype, $questrecord->respondenttype);
        $this->assertEquals($qrow->resp_eligible, $questrecord->resp_eligible);
        $this->assertEquals($qrow->resp_view, $questrecord->resp_view);
        $this->assertEquals($qrow->opendate, $questrecord->opendate);
        $this->assertEquals($qrow->closedate, $questrecord->closedate);
        $this->assertEquals($qrow->resume, $questrecord->resume);
        $this->assertEquals($qrow->navigate, $questrecord->navigate);
        $this->assertEquals($qrow->grade, $questrecord->grade);
        $this->assertEquals($qrow->sid, $questrecord->sid);
        $this->assertEquals($qrow->timemodified, $questrecord->timemodified);
        $this->assertEquals($qrow->completionsubmit, $questrecord->completionsubmit);
        $this->assertEquals($qrow->autonum, $questrecord->autonum);
    }

    /*
     * Need to verify that delete_instance deletes all data associated with a questionnaire.
     *
     */
    public function test_delete_instance() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Set up a new questionnaire.
        $questiondata = array();
        $questiondata['content'] = 'Enter yes or no';
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $questionnaire = $generator->create_test_questionnaire($course, QUESYESNO, $questiondata);

        $question = reset($questionnaire->questions);

        // Add a response for the question.
        $response = $generator->create_question_response($questionnaire, $question, 'y');

        // Get records for database deletion confirmation.
        $survey = $DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid));

        // Now delete it all.
        $this->assertTrue(questionnaire_delete_instance($questionnaire->id));
        $this->assertEmpty($DB->get_record('questionnaire', array('id' => $questionnaire->id)));
        $this->assertEmpty($DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid)));
        $this->assertEmpty($DB->get_records('questionnaire_question', array('survey_id' => $survey->id)));
        $this->assertEmpty($DB->get_records('questionnaire_response', array('survey_id' => $survey->id)));
        $this->assertEmpty($DB->get_records('questionnaire_attempts', array('qid' => $questionnaire->id)));
        $this->assertEmpty($DB->get_records('questionnaire_response_bool', array('response_id' => $response->id)));
        $this->assertEmpty($DB->get_records('event', array("modulename" => 'questionnaire', "instance" => $questionnaire->id)));
    }

    public function test_questionnaire_user_outline() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $questiondata = array();
        $questiondata['content'] = 'Enter yes or no';
        $questionnaire = $generator->create_test_questionnaire($course, QUESYESNO, $questiondata);

        // Test for correct "no response" values.
        $outline = questionnaire_user_outline($course, $user, null, $questionnaire);
        $this->assertEquals(get_string("noresponses", "questionnaire"), $outline->info);

        // Test for a user with one response.
        $response = $generator->create_question_response($questionnaire, reset($questionnaire->questions), 'y', $user->id);
        $outline = questionnaire_user_outline($course, $user, null, $questionnaire);
        $this->assertEquals('1 '.get_string("response", "questionnaire"), $outline->info);
    }

    public function test_questionnaire_user_complete() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $questionnaire = $generator->create_test_questionnaire($course, QUESYESNO);

        $this->assertTrue(questionnaire_user_complete($course, $user, null, $questionnaire));
        $this->expectOutputString(get_string('noresponses', 'questionnaire'));
    }

    public function test_questionnaire_print_recent_activity() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->assertFalse(questionnaire_print_recent_activity(null, null, null));
    }

    public function test_questionnaire_grades() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->assertNull(questionnaire_grades(null));
    }

    public function test_questionnaire_get_user_grades() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $questionnaire = $generator->create_test_questionnaire($course);

        // Test for an array when user specified.
        $grades = questionnaire_get_user_grades($questionnaire, $user->id);
        $this->assertInternalType('array', $grades);

        // Test for an array when no user specified.
        $grades = questionnaire_get_user_grades($questionnaire);
        $this->assertInternalType('array', $grades);
    }

    public function test_questionnaire_update_grades() {
        // Don't know how to test this yet! It doesn't return anything.
        $this->assertNull(questionnaire_update_grades());
    }

    public function test_questionnaire_grade_item_update() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_questionnaire');
        $questionnaire = $generator->create_test_questionnaire($course);
        $questionnaire->cmidnumber = $questionnaire->cm->idnumber;
        $questionnaire->courseid = $questionnaire->course->id;
        $this->assertEquals(GRADE_UPDATE_OK, questionnaire_grade_item_update($questionnaire));
    }
}
