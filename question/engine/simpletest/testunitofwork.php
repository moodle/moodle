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
 * This file contains tests for the question_engine_unit_of_work class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../lib.php');
require_once(dirname(__FILE__) . '/helpers.php');


/**
 * Test subclass to allow access to some protected data so that the correct
 * behaviour can be verified.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_question_engine_unit_of_work extends question_engine_unit_of_work {
    public function get_modified() {
        return $this->modified;
    }

    public function get_attempts_added() {
        return $this->attemptsadded;
    }

    public function get_attempts_modified() {
        return $this->attemptsmodified;
    }

    public function get_steps_added() {
        return $this->stepsadded;
    }

    public function get_steps_modified() {
        return $this->stepsmodified;
    }

    public function get_steps_deleted() {
        return $this->stepsdeleted;
    }
}


/**
 * Unit tests for the {@link question_engine_unit_of_work} class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_unit_of_work_test extends data_loading_method_test_base {
    /** @var question_usage_by_activity the test question usage. */
    protected $quba;

    /** @var int the slot number of the one qa in the test usage.*/
    protected $slot;

    /** @var testable_question_engine_unit_of_work the unit of work we are testing. */
    protected $observer;

    public function setUp() {
        // Create a usage in an initial state, with one shortanswer question added,
        // and attempted in interactive mode submitted responses 'toad' then 'frog'.
        // Then set it to use a new unit of work for any subsequent changes.
        // Create a short answer question.
        $question = test_question_maker::make_question('shortanswer');
        $question->hints = array(
            new question_hint(0, 'This is the first hint.', FORMAT_HTML),
            new question_hint(0, 'This is the second hint.', FORMAT_HTML),
        );
        $question->id = -1;
        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);

        $this->setup_initial_test_state($this->get_test_data());
     }

    public function testDown() {
        question_bank::end_unit_test();
    }

    protected function setup_initial_test_state($testdata) {
        $records = new test_recordset($testdata);

        $this->quba = question_usage_by_activity::load_from_records($records, 1);

        $this->slot = 1;
        $this->observer = new testable_question_engine_unit_of_work($this->quba);
        $this->quba->set_observer($this->observer);
    }

    protected function get_test_data() {
        return array(
        array('qubaid', 'contextid', 'component', 'preferredbehaviour',
                                                'questionattemptid', 'contextid', 'questionusageid', 'slot',
                                                               'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'flagged',
                                                                                                              'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                                                     'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                                     'timecreated', 'userid', 'name', 'value'),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 1, 0, 'todo',             null, 1256233700, 1, '-_triesleft', 3),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 2, 1, 'todo',             null, 1256233720, 1, 'answer',     'toad'),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 2, 1, 'todo',             null, 1256233720, 1, '-submit',     1),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 2, 1, 'todo',             null, 1256233720, 1, '-_triesleft', 1),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 3, 2, 'todo',             null, 1256233740, 1, '-tryagain',   1),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 5, 3, 'gradedright',      null, 1256233790, 1, 'answer',     'frog'),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 1.0000000, 0.0000000, 0, '', '', '', 1256233790, 5, 3, 'gradedright', 1.0000000, 1256233790, 1, '-submit',     1),
        );
    }

    public function test_initial_state() {
        $this->assertFalse($this->observer->get_modified());
        $this->assertEqual(0, count($this->observer->get_attempts_added()));
        $this->assertEqual(0, count($this->observer->get_attempts_modified()));
        $this->assertEqual(0, count($this->observer->get_steps_added()));
        $this->assertEqual(0, count($this->observer->get_steps_modified()));
        $this->assertEqual(0, count($this->observer->get_steps_deleted()));
    }

    public function test_update_usage() {

        $this->quba->set_preferred_behaviour('deferredfeedback');

        $this->assertTrue($this->observer->get_modified());
    }

    public function test_add_question() {

        $slot = $this->quba->add_question(test_question_maker::make_question('truefalse'));

        $newattempts = $this->observer->get_attempts_added();
        $this->assertEqual(1, count($newattempts));
        $this->assertIdentical($this->quba->get_question_attempt($slot), reset($newattempts));
        $this->assertIdentical($slot, key($newattempts));
    }

    public function test_add_and_start_question() {

        $slot = $this->quba->add_question(test_question_maker::make_question('truefalse'));
                $this->quba->start_question($slot);

        // The point here is that, although we have added a step, it is not listed
        // separately becuase it is part of a newly added attempt, and all steps
        // for a newly added attempt are automatically added to the DB, so it does
        // not need to be tracked separately.
        $newattempts = $this->observer->get_attempts_added();
        $this->assertEqual(1, count($newattempts));
        $this->assertIdentical($this->quba->get_question_attempt($slot),
                reset($newattempts));
        $this->assertIdentical($slot, key($newattempts));
        $this->assertEqual(0, count($this->observer->get_steps_added()));
    }

    public function test_process_action() {

        $this->quba->manual_grade($this->slot, 'Acutally, that is not quite right', 0.5);

        // Here, however, were we are adding a step to an existing qa, we do need to track that.
        $this->assertEqual(0, count($this->observer->get_attempts_added()));

        $updatedattempts = $this->observer->get_attempts_modified();
        $this->assertEqual(1, count($updatedattempts));

        $updatedattempt = reset($updatedattempts);
        $this->assertIdentical($this->quba->get_question_attempt($this->slot), $updatedattempt);
        $this->assertIdentical($this->slot, key($updatedattempts));

        $newsteps = $this->observer->get_steps_added();
        $this->assertEqual(1, count($newsteps));

        list($newstep, $qaid, $seq) = reset($newsteps);
        $this->assertIdentical($this->quba->get_question_attempt($this->slot)->get_last_step(), $newstep);
    }

    public function test_regrade_same_steps() {

        // Change the question in a minor way and regrade.
        $this->quba->get_question($this->slot)->answers[14]->fraction = 0.5;
        $this->quba->regrade_all_questions();

        // Here, the qa, and all the steps, should be marked as updated.
        // Here, however, were we are adding a step to an existing qa, we do need to track that.
        $this->assertEqual(0, count($this->observer->get_attempts_added()));
        $this->assertEqual(0, count($this->observer->get_steps_added()));
        $this->assertEqual(0, count($this->observer->get_steps_deleted()));

        $updatedattempts = $this->observer->get_attempts_modified();
        $this->assertEqual(1, count($updatedattempts));

        $updatedattempt = reset($updatedattempts);
        $this->assertIdentical($this->quba->get_question_attempt($this->slot), $updatedattempt);

        $updatedsteps = $this->observer->get_steps_modified();
        $this->assertEqual($updatedattempt->get_num_steps(), count($updatedsteps));

        foreach ($updatedattempt->get_step_iterator() as $seq => $step) {
            $this->assertIdentical(array($step, $updatedattempt->get_database_id(), $seq),
                    $updatedsteps[$seq]);
        }
    }

    public function test_regrade_losing_steps() {

        // Change the question so that 'toad' is also right, and regrade. This
        // will mean that the try again, and second try states are no longer
        // needed, so they should be dropped.
        $this->quba->get_question($this->slot)->answers[14]->fraction = 1;
        $this->quba->regrade_all_questions();

        $this->assertEqual(0, count($this->observer->get_attempts_added()));
        $this->assertEqual(0, count($this->observer->get_steps_added()));

        $updatedattempts = $this->observer->get_attempts_modified();
        $this->assertEqual(1, count($updatedattempts));

        $updatedattempt = reset($updatedattempts);
        $this->assertIdentical($this->quba->get_question_attempt($this->slot), $updatedattempt);

        $updatedsteps = $this->observer->get_steps_modified();
        $this->assertEqual($updatedattempt->get_num_steps(), count($updatedsteps));

        foreach ($updatedattempt->get_step_iterator() as $seq => $step) {
            $this->assertIdentical(array($step, $updatedattempt->get_database_id(), $seq),
                    $updatedsteps[$seq]);
        }

        $deletedsteps = $this->observer->get_steps_deleted();
        $this->assertEqual(2, count($deletedsteps));

        $firstdeletedstep = reset($deletedsteps);
        $this->assertEqual(array('-tryagain' => 1), $firstdeletedstep->get_all_data());

        $seconddeletedstep = end($deletedsteps);
        $this->assertEqual(array('answer' => 'frog', '-submit' => 1),
                $seconddeletedstep->get_all_data());
    }

    public function test_tricky_regrade() {

        // The tricky thing here is that we take a half-complete question-attempt,
        // and then as one transaction, we submit some more responses, and then
        // change the question attempt as in test_regrade_losing_steps, and regrade
        // before the steps are even written to the database the first time.
        $somedata = $this->get_test_data();
        $somedata = array_slice($somedata, 0, 5);
        $this->setup_initial_test_state($somedata);

        $this->quba->process_action($this->slot, array('-tryagain' => 1));
        $this->quba->process_action($this->slot, array('answer' => 'frog', '-submit' => 1));
        $this->quba->finish_all_questions();

        $this->quba->get_question($this->slot)->answers[14]->fraction = 1;
        $this->quba->regrade_all_questions();

        $this->assertEqual(0, count($this->observer->get_attempts_added()));

        $updatedattempts = $this->observer->get_attempts_modified();
        $this->assertEqual(1, count($updatedattempts));

        $updatedattempt = reset($updatedattempts);
        $this->assertIdentical($this->quba->get_question_attempt($this->slot), $updatedattempt);

        $this->assertEqual(0, count($this->observer->get_steps_added()));

        $updatedsteps = $this->observer->get_steps_modified();
        $this->assertEqual($updatedattempt->get_num_steps(), count($updatedsteps));

        foreach ($updatedattempt->get_step_iterator() as $seq => $step) {
            $this->assertIdentical(array($step, $updatedattempt->get_database_id(), $seq),
                    $updatedsteps[$seq]);
        }

        $this->assertEqual(0, count($this->observer->get_steps_deleted()));
    }
}
