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
 * This file contains tests that walks a question through a whole attempt.
 *
 * @package core_question
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/..//lib.php');
require_once(__DIR__ . '/helpers.php');


/**
 * End-to-end tests of attempting a question.
 *
 * @copyright  2017 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_question_walkthrough_testcase extends qbehaviour_walkthrough_test_base {

    public function test_regrade_does_not_lose_flag() {

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
    public function test_action_author_with_display_options_testcase() {
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

        // The step just show the user profile link if the step's userid is different with student id.
        preg_match_all("/<a ?.*>(.*)<\/a>/", $result, $matches);
        $this->assertEquals(1, count($matches[0]));
    }
}
