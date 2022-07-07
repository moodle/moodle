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
 * Quiz attempt walk through tests.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Quiz attempt walk through.
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_attempt_walkthrough_testcase extends advanced_testcase {

    /**
     * Create a quiz with questions and walk through a quiz attempt.
     */
    public function test_quiz_attempt_walkthrough() {
        global $SITE;

        $this->resetAfterTest(true);

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(array('course'=>$SITE->id, 'questionsperpage' => 0, 'grade' => 100.0,
                                                      'sumgrades' => 2));

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        // Make a user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();

        $quizobj = quiz::create($quiz->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user1->id);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        $this->assertEquals('1,2,0', $attempt->layout);

        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertFalse($attemptobj->has_response_to_at_least_one_graded_question());

        $prefix1 = $quba->get_field_prefix(1);
        $prefix2 = $quba->get_field_prefix(2);

        $tosubmit = array(1 => array('answer' => 'frog'),
                          2 => array('answer' => '3.14'));

        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);

        // Re-load quiz attempt data.
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check that results are stored as expected.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(2, $attemptobj->get_sum_marks());
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());

        // Check quiz grades.
        $grades = quiz_get_user_grades($quiz, $user1->id);
        $grade = array_shift($grades);
        $this->assertEquals(100.0, $grade->rawgrade);

        // Check grade book.
        $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $quiz->id, $user1->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $gradebookgrade = array_shift($gradebookitem->grades);
        $this->assertEquals(100, $gradebookgrade->grade);
    }

    /**
     * Create a quiz with a random as well as other questions and walk through quiz attempts.
     */
    public function test_quiz_with_random_question_attempt_walkthrough() {
        global $SITE;

        $this->resetAfterTest(true);
        question_bank::get_qtype('random')->clear_caches_before_testing();

        $this->setAdminUser();

        // Make a quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

        $quiz = $quizgenerator->create_instance(array('course' => $SITE->id, 'questionsperpage' => 2, 'grade' => 100.0,
                                                      'sumgrades' => 4));

        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        // Add two questions to question category.
        $cat = $questiongenerator->create_question_category();
        $saq = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add random question to the quiz.
        quiz_add_random_questions($quiz, 0, $cat->id, 1, false);

        // Make another category.
        $cat2 = $questiongenerator->create_question_category();
        $match = $questiongenerator->create_question('match', null, array('category' => $cat->id));

        quiz_add_quiz_question($match->id, $quiz, 0);

        $multichoicemulti = $questiongenerator->create_question('multichoice', 'two_of_four', array('category' => $cat->id));

        quiz_add_quiz_question($multichoicemulti->id, $quiz, 0);

        $multichoicesingle = $questiongenerator->create_question('multichoice', 'one_of_four', array('category' => $cat->id));

        quiz_add_quiz_question($multichoicesingle->id, $quiz, 0);

        foreach (array($saq->id => 'frog', $numq->id => '3.14') as $randomqidtoselect => $randqanswer) {
            // Make a new user to do the quiz each loop.
            $user1 = $this->getDataGenerator()->create_user();
            $this->setUser($user1);

            $quizobj = quiz::create($quiz->id, $user1->id);

            // Start the attempt.
            $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
            $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

            $timenow = time();
            $attempt = quiz_create_attempt($quizobj, 1, false, $timenow);

            quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow, array(1 => $randomqidtoselect));
            $this->assertEquals('1,2,0,3,4,0', $attempt->layout);

            quiz_attempt_save_started($quizobj, $quba, $attempt);

            // Process some responses from the student.
            $attemptobj = quiz_attempt::create($attempt->id);
            $this->assertFalse($attemptobj->has_response_to_at_least_one_graded_question());

            $tosubmit = array();
            $selectedquestionid = $quba->get_question_attempt(1)->get_question_id();
            $tosubmit[1] = array('answer' => $randqanswer);
            $tosubmit[2] = array(
                'frog' => 'amphibian',
                'cat'  => 'mammal',
                'newt' => 'amphibian');
            $tosubmit[3] = array('One' => '1', 'Two' => '0', 'Three' => '1', 'Four' => '0'); // First and third choice.
            $tosubmit[4] = array('answer' => 'One'); // The first choice.

            $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

            // Finish the attempt.
            $attemptobj = quiz_attempt::create($attempt->id);
            $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
            $attemptobj->process_finish($timenow, false);

            // Re-load quiz attempt data.
            $attemptobj = quiz_attempt::create($attempt->id);

            // Check that results are stored as expected.
            $this->assertEquals(1, $attemptobj->get_attempt_number());
            $this->assertEquals(4, $attemptobj->get_sum_marks());
            $this->assertEquals(true, $attemptobj->is_finished());
            $this->assertEquals($timenow, $attemptobj->get_submitted_date());
            $this->assertEquals($user1->id, $attemptobj->get_userid());
            $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());

            // Check quiz grades.
            $grades = quiz_get_user_grades($quiz, $user1->id);
            $grade = array_shift($grades);
            $this->assertEquals(100.0, $grade->rawgrade);

            // Check grade book.
            $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $quiz->id, $user1->id);
            $gradebookitem = array_shift($gradebookgrades->items);
            $gradebookgrade = array_shift($gradebookitem->grades);
            $this->assertEquals(100, $gradebookgrade->grade);
        }
    }


    public function get_correct_response_for_variants() {
        return array(array(1, 9.9), array(2, 8.5), array(5, 14.2), array(10, 6.8, true));
    }

    protected $quizwithvariants = null;

    /**
     * Create a quiz with a single question with variants and walk through quiz attempts.
     *
     * @dataProvider get_correct_response_for_variants
     */
    public function test_quiz_with_question_with_variants_attempt_walkthrough($variantno, $correctresponse, $done = false) {
        global $SITE;

        $this->resetAfterTest($done);

        $this->setAdminUser();

        if ($this->quizwithvariants === null) {
            // Make a quiz.
            $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');

            $this->quizwithvariants = $quizgenerator->create_instance(array('course'=>$SITE->id,
                                                                            'questionsperpage' => 0,
                                                                            'grade' => 100.0,
                                                                            'sumgrades' => 1));

            $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

            $cat = $questiongenerator->create_question_category();
            $calc = $questiongenerator->create_question('calculatedsimple', 'sumwithvariants', array('category' => $cat->id));
            quiz_add_quiz_question($calc->id, $this->quizwithvariants, 0);
        }


        // Make a new user to do the quiz.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);
        $quizobj = quiz::create($this->quizwithvariants->id, $user1->id);

        // Start the attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow);

        // Select variant.
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow, array(), array(1 => $variantno));
        $this->assertEquals('1,0', $attempt->layout);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertFalse($attemptobj->has_response_to_at_least_one_graded_question());

        $tosubmit = array(1 => array('answer' => $correctresponse));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());

        $attemptobj->process_finish($timenow, false);

        // Re-load quiz attempt data.
        $attemptobj = quiz_attempt::create($attempt->id);

        // Check that results are stored as expected.
        $this->assertEquals(1, $attemptobj->get_attempt_number());
        $this->assertEquals(1, $attemptobj->get_sum_marks());
        $this->assertEquals(true, $attemptobj->is_finished());
        $this->assertEquals($timenow, $attemptobj->get_submitted_date());
        $this->assertEquals($user1->id, $attemptobj->get_userid());
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());

        // Check quiz grades.
        $grades = quiz_get_user_grades($this->quizwithvariants, $user1->id);
        $grade = array_shift($grades);
        $this->assertEquals(100.0, $grade->rawgrade);

        // Check grade book.
        $gradebookgrades = grade_get_grades($SITE->id, 'mod', 'quiz', $this->quizwithvariants->id, $user1->id);
        $gradebookitem = array_shift($gradebookgrades->items);
        $gradebookgrade = array_shift($gradebookitem->grades);
        $this->assertEquals(100, $gradebookgrade->grade);
    }
}
