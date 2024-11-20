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

namespace core_question;

use question_bank;
use question_display_options;
use question_state;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');


/**
 * End-to-end tests of attempting a question.
 *
 * @package    core_question
 * @copyright  2017 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {

    public function test_regrade_does_not_lose_flag(): void {

        // Create a true-false question with correct answer true.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $this->start_attempt_at_question($tf, 'deferredfeedback', 2);

        // Process a true answer.
        $this->process_submission(array('answer' => 1));

        // Finish the attempt.
        $this->quba->finish_all_questions();

        // Flag the question.
        $this->get_question_attempt()->set_flagged(true);

        // Now change the correct answer to the question, and regrade.
        $tf->rightanswer = false;
        $this->quba->regrade_all_questions();

        // Verify the flag has not been lost.
        $this->assertTrue($this->get_question_attempt()->is_flagged());
    }

    /**
     * Test action_author function.
     */
    public function test_action_author_with_display_options_testcase(): void {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $teacher = $this->getDataGenerator()->create_user();
        $student = $this->getDataGenerator()->create_user();

        // Create an essay question in the DB.
        $cat = $generator->create_question_category();
        $essay = $generator->create_question('essay', 'editorfilepicker', ['category' => $cat->id]);

        // Start attempt at the question.
        $q = question_bank::load_question($essay->id);

        // Student attempt the question.
        $this->setUser($student);
        $this->start_attempt_at_question($q, 'deferredfeedback', 10, 1);

        // Simulate some data submitted by the student.
        $this->process_submission(['answer' => 'This is my wonderful essay!', 'answerformat' => FORMAT_HTML]);
        $this->finish();

        // Process a manual comment.
        $this->setUser($teacher);
        $this->manual_grade('Not good enough!', 10, FORMAT_HTML);
        $this->render();
        $this->save_quba();

        // Set display option userinfoinhistory to HIDDEN.
        $displayoptions = new question_display_options();
        $displayoptions->history = question_display_options::VISIBLE;
        $displayoptions->userinfoinhistory = question_display_options::HIDDEN;

        $this->load_quba();
        $result = $this->quba->render_question($this->slot, $displayoptions);

        // The profile user link should not display.
        preg_match("/<a ?.*>(.*)<\/a>/", $result, $matches);
        $this->assertEquals(false, isset($matches[0]));

        // Set display option userinfoinhistory to SHOW_ALL.
        $displayoptions = new question_display_options();
        $displayoptions->history = question_display_options::VISIBLE;
        $displayoptions->userinfoinhistory = question_display_options::SHOW_ALL;

        $this->load_quba();
        $this->quba->preload_all_step_users();
        $result = $this->quba->render_question($this->slot, $displayoptions);
        $numsteps = $this->quba->get_question_attempt($this->slot)->get_num_steps();

        // All steps in the result should contain user profile link.
        preg_match_all("/<a ?.*>(.*)<\/a>/", $result, $matches);
        $this->assertEquals($numsteps, count($matches[0]));

        // Set the userinfoinhistory to student id.
        $displayoptions = new question_display_options();
        $displayoptions->history = question_display_options::VISIBLE;
        $displayoptions->userinfoinhistory = $student->id;

        $this->load_quba();
        $result = $this->quba->render_question($this->slot, $displayoptions);
        $message = 'Attempt to access the step user before it was initialised.';
        $message .= ' Did you forget to call question_usage_by_activity::preload_all_step_users() or similar?';
        $this->assertDebuggingCalled($message, DEBUG_DEVELOPER);
        $this->resetDebugging();
        $this->quba->preload_all_step_users();
        $result = $this->quba->render_question($this->slot, $displayoptions);
        $this->assertDebuggingNotCalled();

        // The step just show the user profile link if the step's userid is different with student id.
        preg_match_all("/<a ?.*>(.*)<\/a>/", $result, $matches);
        $this->assertEquals(1, count($matches[0]));
    }

    /**
     * @covers \question_usage_by_activity::regrade_question
     * @covers \question_attempt::regrade
     * @covers \question_attempt::get_attempt_state_data_to_regrade_with_version
     */
    public function test_regrading_an_interactive_attempt_while_in_progress(): void {

        // Start an attempt at a matching question.
        $q = test_question_maker::make_question('match');
        $this->start_attempt_at_question($q, 'interactive', 1);
        $this->save_quba();

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->check_current_output($this->get_tries_remaining_expectation(1));

        // Regrade the attempt.
        // Duplicating the question here essential to triggering the bug we are trying to reproduce.
        $reloadedquestion = clone($q);
        $this->quba->regrade_question($this->slot, false, null, $reloadedquestion);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->check_current_output($this->get_tries_remaining_expectation(1));
    }

    /**
     * @covers \question_usage_by_activity::regrade_question
     * @covers \question_attempt::regrade
     * @covers \question_attempt::get_attempt_state_data_to_regrade_with_version
     */
    public function test_regrading_does_not_lose_metadata(): void {

        // Start an attempt at a matching question.
        $q = test_question_maker::make_question('match');
        $this->start_attempt_at_question($q, 'interactive', 1);
        // Like in process_redo_question in mod_quiz.
        $this->quba->set_question_attempt_metadata($this->slot, 'originalslot', 42);
        $this->save_quba();

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->check_current_output($this->get_tries_remaining_expectation(1));

        // Regrade the attempt.
        $reloadedquestion = clone($q);
        $this->quba->regrade_question($this->slot, false, null, $reloadedquestion);

        // Verify.
        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->check_current_output($this->get_tries_remaining_expectation(1));
        $this->assertEquals(42, $this->quba->get_question_attempt_metadata($this->slot, 'originalslot'));
    }
}
