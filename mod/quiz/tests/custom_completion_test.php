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

declare(strict_types=1);

namespace mod_quiz;

use advanced_testcase;
use cm_info;
use core_completion\cm_completion_details;
use grade_item;
use mod_quiz\completion\custom_completion;
use question_engine;
use quiz;
use quiz_attempt;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * Class for unit testing mod_quiz/custom_completion.
 *
 * @package   mod_quiz
 * @copyright 2021 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_quiz\completion\custom_completion
 */
class custom_completion_test extends advanced_testcase {

    /**
     * Setup function for all tests.
     *
     * @param array $completionoptions ['nbstudents'] => int, ['qtype'] => string, ['quizoptions'] => array
     * @return array [$students, $quiz, $cm, $litecm]
     */
    private function setup_quiz_for_testing_completion(array $completionoptions): array {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Enable completion before creating modules, otherwise the completion data is not written in DB.
        $CFG->enablecompletion = true;

        // Create a course and students.
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => true]);
        $students = [];
        $sumgrades = $completionoptions['sumgrades'] ?? 1;
        $nbquestions = $completionoptions['nbquestions'] ?? 1;
        for ($i = 0; $i < $completionoptions['nbstudents']; $i++) {
            $students[$i] = $this->getDataGenerator()->create_user();
            $this->assertTrue($this->getDataGenerator()->enrol_user($students[$i]->id, $course->id, $studentrole->id));
        }

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array_merge([
            'course' => $course->id,
            'grade' => 100.0,
            'questionsperpage' => 0,
            'sumgrades' => $sumgrades,
            'completion' => COMPLETION_TRACKING_AUTOMATIC
        ], $completionoptions['quizoptions']);
        $quiz = $quizgenerator->create_instance($data);
        $litecm = get_coursemodule_from_id('quiz', $quiz->cmid);
        $cm = cm_info::create($litecm);

        // Create a question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        for ($i = 0; $i < $nbquestions; $i++) {
            $overrideparams = ['category' => $cat->id];
            if (isset($completionoptions['questiondefaultmarks'][$i])) {
                $overrideparams['defaultmark'] = $completionoptions['questiondefaultmarks'][$i];
            }
            $question = $questiongenerator->create_question($completionoptions['qtype'], null, $overrideparams);
            quiz_add_quiz_question($question->id, $quiz);
        }

        // Set grade to pass.
        $item = grade_item::fetch(['courseid' => $course->id, 'itemtype' => 'mod', 'itemmodule' => 'quiz',
            'iteminstance' => $quiz->id, 'outcomeid' => null]);
        $item->gradepass = 80;
        $item->update();
        return [
            $students,
            $quiz,
            $cm,
            $litecm
        ];
    }

    /**
     * Helper function for tests.
     * Starts an attempt, processes responses and finishes the attempt.
     *
     * @param array $attemptoptions ['quiz'] => object, ['student'] => object, ['tosubmit'] => array, ['attemptnumber'] => int
     */
    private function do_attempt_quiz(array $attemptoptions) {
        $quizobj = quiz::create($attemptoptions['quiz']->id);

        // Start the passing attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, $attemptoptions['attemptnumber'], false, $timenow, false,
            $attemptoptions['student']->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, $attemptoptions['attemptnumber'], $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, $attemptoptions['tosubmit']);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);
    }

    /**
     * Test checking the completion state of a quiz base on core's completionpassgrade criteria.
     * The quiz requires a passing grade to be completed.
     */
    public function test_completionpass() {
        list($students, $quiz, $cm) = $this->setup_quiz_for_testing_completion([
            'nbstudents' => 2,
            'qtype' => 'numerical',
            'quizoptions' => [
                'completionusegrade' => 1,
                'completionpassgrade' => 1
            ]
        ]);

        list($passstudent, $failstudent) = $students;

        // Do a passing attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $passstudent,
            'attemptnumber' => 1,
            'tosubmit' => [1 => ['answer' => '3.14']]
        ]);

        $completioninfo = new \completion_info($cm->get_course());
        $completiondetails = new cm_completion_details($completioninfo, $cm, (int) $passstudent->id);

        // Check the results.
        $this->assertEquals(COMPLETION_COMPLETE_PASS, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );

        // Do a failing attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $failstudent,
            'attemptnumber' => 1,
            'tosubmit' => [1 => ['answer' => '0']]
        ]);

        $completiondetails = new cm_completion_details($completioninfo, $cm, (int) $failstudent->id);

        // Check the results.
        $this->assertEquals(COMPLETION_COMPLETE_FAIL, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );
    }

    /**
     * Test checking the completion state of a quiz.
     * To be completed, this quiz requires either a passing grade or for all attempts to be used up.
     *
     * @covers ::get_state
     * @covers ::get_custom_rule_descriptions
     */
    public function test_completionexhausted() {
        list($students, $quiz, $cm) = $this->setup_quiz_for_testing_completion([
            'nbstudents' => 2,
            'qtype' => 'numerical',
            'quizoptions' => [
                'attempts' => 2,
                'completionusegrade' => 1,
                'completionpassgrade' => 1,
                'completionattemptsexhausted' => 1
            ]
        ]);

        list($passstudent, $exhauststudent) = $students;

        // Start a passing attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $passstudent,
            'attemptnumber' => 1,
            'tosubmit' => [1 => ['answer' => '3.14']]
        ]);

        $completioninfo = new \completion_info($cm->get_course());

        // Check the results. Quiz is completed by $passstudent because of passing grade.
        $studentid = (int) $passstudent->id;
        $customcompletion = new custom_completion($cm, $studentid, $completioninfo->get_core_completion_state($cm, $studentid));
        $this->assertArrayHasKey('completionpassorattemptsexhausted', $cm->customdata['customcompletionrules']);
        $this->assertEquals(COMPLETION_COMPLETE, $customcompletion->get_state('completionpassorattemptsexhausted'));
        $this->assertEquals(
            'Receive a pass grade or complete all available attempts',
            $customcompletion->get_custom_rule_descriptions()['completionpassorattemptsexhausted']
        );

        // Do a failing attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $exhauststudent,
            'attemptnumber' => 1,
            'tosubmit' => [1 => ['answer' => '0']]
        ]);

        // Check the results. Quiz is not completed by $exhauststudent yet because of failing grade and of remaining attempts.
        $studentid = (int) $exhauststudent->id;
        $customcompletion = new custom_completion($cm, $studentid, $completioninfo->get_core_completion_state($cm, $studentid));
        $this->assertArrayHasKey('completionpassorattemptsexhausted', $cm->customdata['customcompletionrules']);
        $this->assertEquals(COMPLETION_INCOMPLETE, $customcompletion->get_state('completionpassorattemptsexhausted'));
        $this->assertEquals(
            'Receive a pass grade or complete all available attempts',
            $customcompletion->get_custom_rule_descriptions()['completionpassorattemptsexhausted']
        );

        // Do a second failing attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $exhauststudent,
            'attemptnumber' => 2,
            'tosubmit' => [1 => ['answer' => '0']]
        ]);

        // Check the results. Quiz is completed by $exhauststudent because there are no remaining attempts.
        $customcompletion = new custom_completion($cm, $studentid, $completioninfo->get_core_completion_state($cm, $studentid));
        $this->assertArrayHasKey('completionpassorattemptsexhausted', $cm->customdata['customcompletionrules']);
        $this->assertEquals(COMPLETION_COMPLETE, $customcompletion->get_state('completionpassorattemptsexhausted'));
        $this->assertEquals(
            'Receive a pass grade or complete all available attempts',
            $customcompletion->get_custom_rule_descriptions()['completionpassorattemptsexhausted']
        );

    }

    /**
     * Test checking the completion state of a quiz.
     * To be completed, this quiz requires a minimum number of attempts.
     *
     * @covers ::get_state
     * @covers ::get_custom_rule_descriptions
     */
    public function test_completionminattempts() {
        list($students, $quiz, $cm) = $this->setup_quiz_for_testing_completion([
            'nbstudents' => 1,
            'qtype' => 'essay',
            'quizoptions' => [
                'completionminattemptsenabled' => 1,
                'completionminattempts' => 2
            ]
        ]);

        list($student) = $students;

        // Do a first attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $student,
            'attemptnumber' => 1,
            'tosubmit' => [1 => ['answer' => 'Lorem ipsum.', 'answerformat' => '1']]
        ]);

        // Check the results. Quiz is not completed yet because only one attempt was done.
        $customcompletion = new custom_completion($cm, (int) $student->id);
        $this->assertArrayHasKey('completionminattempts', $cm->customdata['customcompletionrules']);
        $this->assertEquals(COMPLETION_INCOMPLETE, $customcompletion->get_state('completionminattempts'));
        $this->assertEquals(
            'Make attempts: 2',
            $customcompletion->get_custom_rule_descriptions()['completionminattempts']
        );

        // Do a second attempt.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $student,
            'attemptnumber' => 2,
            'tosubmit' => [1 => ['answer' => 'Lorem ipsum.', 'answerformat' => '1']]
        ]);

        // Check the results. Quiz is completed by $student because two attempts were done.
        $customcompletion = new custom_completion($cm, (int) $student->id);
        $this->assertArrayHasKey('completionminattempts', $cm->customdata['customcompletionrules']);
        $this->assertEquals(COMPLETION_COMPLETE, $customcompletion->get_state('completionminattempts'));
        $this->assertEquals(
            'Make attempts: 2',
            $customcompletion->get_custom_rule_descriptions()['completionminattempts']
        );
    }

    /**
     * Test for get_defined_custom_rules().
     *
     * @covers ::get_defined_custom_rules
     */
    public function test_get_defined_custom_rules() {
        $rules = custom_completion::get_defined_custom_rules();
        $this->assertCount(2, $rules);
        $this->assertEquals(
            ['completionpassorattemptsexhausted', 'completionminattempts'],
            $rules
        );
    }

    /**
     * Test update moduleinfo.
     *
     * @covers \update_moduleinfo
     */
    public function test_update_moduleinfo() {
        $this->setAdminUser();
        // We need lite cm object not a full cm because update_moduleinfo is not allow some properties to be updated.
        list($students, $quiz, $cm, $litecm) = $this->setup_quiz_for_testing_completion([
            'nbstudents' => 1,
            'qtype' => 'numerical',
            'nbquestions' => 2,
            'sumgrades' => 100,
            'questiondefaultmarks' => [20, 80],
            'quizoptions' => [
                'completionusegrade' => 1,
                'completionpassgrade' => 1,
                'completionview' => 0,
            ]
        ]);
        $course = $cm->get_course();

        list($student) = $students;
        // Do a first attempt with a pass marks = 20.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $student,
            'attemptnumber' => 1,
            'tosubmit' => [1 => ['answer' => '3.14']]
        ]);
        $completioninfo = new \completion_info($course);
        $cminfo = \cm_info::create($cm);
        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is fail because gradepass = 80.
        $this->assertEquals(COMPLETION_COMPLETE_FAIL, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );

        // Update quiz with passgrade = 20 and use highest grade to calculate.
        $moduleinfo = $this->prepare_module_info($cm, $quiz, $course, 20, QUIZ_GRADEHIGHEST);
        update_moduleinfo($litecm, $moduleinfo, $course, null);

        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is pass.
        $this->assertEquals(COMPLETION_COMPLETE_PASS, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );

        // Do a second attempt with pass marks = 80.
        $this->do_attempt_quiz([
            'quiz' => $quiz,
            'student' => $student,
            'attemptnumber' => 2,
            'tosubmit' => [2 => ['answer' => '3.14']]
        ]);

        // Update quiz with gradepass = 80 and use highest grade to calculate completion.
        $moduleinfo = $this->prepare_module_info($cm, $quiz, $course, 80, QUIZ_GRADEHIGHEST);
        update_moduleinfo($litecm, $moduleinfo, $course, null);

        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is pass.
        $this->assertEquals(COMPLETION_COMPLETE_PASS, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );

        // Update quiz with gradepass = 80 and use average grade to calculate completion.
        $moduleinfo = $this->prepare_module_info($cm, $quiz, $course, 80, QUIZ_GRADEAVERAGE);
        update_moduleinfo($litecm, $moduleinfo, $course, null);

        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is fail because student grade = 50.
        $this->assertEquals(COMPLETION_COMPLETE_FAIL, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );

        // Update quiz with gradepass = 50 and use average grade to calculate completion.
        $moduleinfo = $this->prepare_module_info($cm, $quiz, $course, 50, QUIZ_GRADEAVERAGE);
        update_moduleinfo($litecm, $moduleinfo, $course, null);

        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is pass.
        $this->assertEquals(COMPLETION_COMPLETE_PASS, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );

        // Update quiz with gradepass = 50 and use first attempt grade to calculate completion.
        $moduleinfo = $this->prepare_module_info($cm, $quiz, $course, 50, QUIZ_ATTEMPTFIRST);
        update_moduleinfo($litecm, $moduleinfo, $course, null);

        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is fail.
        $this->assertEquals(COMPLETION_COMPLETE_FAIL, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );
        // Update quiz with gradepass = 50 and use last attempt grade to calculate completion.
        $moduleinfo = $this->prepare_module_info($cm, $quiz, $course, 50, QUIZ_ATTEMPTLAST);
        update_moduleinfo($litecm, $moduleinfo, $course, null);

        $completiondetails = new cm_completion_details($completioninfo, $cminfo, (int) $student->id);

        // Check the results. Completion is fail.
        $this->assertEquals(COMPLETION_COMPLETE_PASS, $completiondetails->get_details()['completionpassgrade']->status);
        $this->assertEquals(
            'Receive a passing grade',
            $completiondetails->get_details()['completionpassgrade']->description
        );
    }

    /**
     * Set up moduleinfo object sample data for quiz instance.
     *
     * @param object $cm course-module instance
     * @param object $quiz quiz instance data.
     * @param object $course Course related data.
     * @param int $gradepass Grade to pass and completed completion.
     * @param string $grademethod grade attempt method.
     * @return \stdClass
     */
    private function prepare_module_info(object $cm, object $quiz, object $course, int $gradepass, string $grademethod): \stdClass {
        $grouping = $this->getDataGenerator()->create_grouping(['courseid' => $course->id]);
        // Module test values.
        $moduleinfo = new \stdClass();
        $moduleinfo->coursemodule = $cm->id;
        $moduleinfo->section = 1;
        $moduleinfo->course = $course->id;
        $moduleinfo->groupingid = $grouping->id;
        $draftideditor = 0;
        file_prepare_draft_area($draftideditor, null, null, null, null);
        $moduleinfo->introeditor = ['text' => 'This is a module', 'format' => FORMAT_HTML, 'itemid' => $draftideditor];
        $moduleinfo->modulename = 'quiz';
        $moduleinfo->quizpassword = '';
        $moduleinfo->cmidnumber = '';
        $moduleinfo->marksopen = 1;
        $moduleinfo->visible = 1;
        $moduleinfo->visibleoncoursepage = 1;
        $moduleinfo->completion = COMPLETION_TRACKING_AUTOMATIC;
        $moduleinfo->completionview = COMPLETION_VIEW_NOT_REQUIRED;
        $moduleinfo->name = $quiz->name;
        $moduleinfo->timeopen = $quiz->timeopen;
        $moduleinfo->timeclose = $quiz->timeclose;
        $moduleinfo->timelimit = $quiz->timelimit;
        $moduleinfo->graceperiod = $quiz->graceperiod;
        $moduleinfo->decimalpoints = $quiz->decimalpoints;
        $moduleinfo->questiondecimalpoints = $quiz->questiondecimalpoints;
        $moduleinfo->gradepass = $gradepass;
        $moduleinfo->grademethod = $grademethod;

        return $moduleinfo;
    }
}
