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
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');

/**
 * Unit tests for the autosave parts of the {@link question_usage} class.
 *
 * @package   core_question
 * @category  test
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class questionusage_autosave_test extends \qbehaviour_walkthrough_test_base {

    public function test_autosave_then_display(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $this->delete_quba();
    }

    public function test_autosave_then_autosave_different_data(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process a second autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'third response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'third response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $this->delete_quba();
    }

    public function test_autosave_then_autosave_same_data(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $stepid = $this->quba->get_question_attempt($this->slot)->get_last_step()->get_id();

        // Process a second autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Try to check it is really the same step
        $newstepid = $this->quba->get_question_attempt($this->slot)->get_last_step()->get_id();
        $this->assertEquals($stepid, $newstepid);

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $this->delete_quba();
    }

    public function test_autosave_then_autosave_original_data(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process a second autosave saving the original response.
        // This should remove the autosave step.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'first response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $this->delete_quba();
    }

    public function test_autosave_then_real_save(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Now save for real a third response.
        $this->process_submission(array('answer' => 'third response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'third response');
        $this->check_output_contains_hidden_input(':sequencecheck', 3);
    }

    public function test_autosave_then_real_save_same(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Now save for real of the same response.
        $this->process_submission(array('answer' => 'second response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 3);
    }

    public function test_autosave_then_submit(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Now submit a third response.
        $this->process_submission(array('answer' => 'third response'));
        $this->quba->finish_all_questions();

        $this->check_current_state(question_state::$gradedwrong);
        $this->check_current_mark(0);
        $this->check_step_count(4);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'third response', false);
        $this->check_output_contains_hidden_input(':sequencecheck', 4);
    }

    public function test_autosave_and_save_concurrently(): void {
        // This test simulates the following scenario:
        // 1. Student looking at a page of the quiz, and edits a field then waits.
        // 2. Autosave starts.
        // 3. Student immediately clicks Next, which submits the current page.
        // In this situation, the real submit should beat the autosave, even
        // thought they happen concurrently. We simulate this by opening a
        // second db connections.
        global $DB;

        // Open second connection
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        $DB2 = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $DB2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        // Since we need to commit our transactions in a given order, close the
        // standard unit test transaction.
        $this->preventResetByRollback();

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);
        $this->save_quba();

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Start to process an autosave on $DB.
        $transaction = $DB->start_delegated_transaction();
        $this->load_quba($DB);
        $this->process_autosave(array('answer' => 'autosaved response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba($DB); // Don't commit the transaction yet.

        // Now process a real submit on $DB2 (using a different response).
        $transaction2 = $DB2->start_delegated_transaction();
        $this->load_quba($DB2);
        $this->process_submission(array('answer' => 'real response'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);

        // Now commit the first transaction.
        $transaction->allow_commit();

        // Now commit the other transaction.
        $this->save_quba($DB2);
        $transaction2->allow_commit();

        // Now re-load and check how that is re-displayed.
        $this->load_quba();
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_output_contains_text_input('answer', 'real response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $DB2->dispose();
    }

    public function test_concurrent_autosaves(): void {
        // This test simulates the following scenario:
        // 1. Student opens  a page of the quiz in two separate browser.
        // 2. Autosave starts in both at the same time.
        // In this situation, one autosave will work, and the other one will
        // get a unique key violation error. This is OK.
        global $DB;

        // Open second connection
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        $DB2 = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $DB2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        // Since we need to commit our transactions in a given order, close the
        // standard unit test transaction.
        $this->preventResetByRollback();

        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);
        $this->save_quba();

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Start to process an autosave on $DB.
        $transaction = $DB->start_delegated_transaction();
        $this->load_quba($DB);
        $this->process_autosave(array('answer' => 'autosaved response 1'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba($DB); // Don't commit the transaction yet.

        // Now process a real submit on $DB2 (using a different response).
        $transaction2 = $DB2->start_delegated_transaction();
        $this->load_quba($DB2);
        $this->process_autosave(array('answer' => 'autosaved response 2'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);

        // Now commit the first transaction.
        $transaction->allow_commit();

        // Now commit the other transaction.
        $this->expectException('dml_write_exception');
        $this->save_quba($DB2);
        $transaction2->allow_commit();

        // Now re-load and check how that is re-displayed.
        $this->load_quba();
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->render();
        $this->check_output_contains_text_input('answer', 'autosaved response 1');
        $this->check_output_contains_hidden_input(':sequencecheck', 1);

        $DB2->dispose();
    }

    public function test_autosave_with_wrong_seq_number_ignored(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'first response'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave with a sequence number 1 too small (so from the past).
        $this->load_quba();
        $postdata = $this->response_data_to_post(array('answer' => 'obsolete response'));
        $postdata[$this->quba->get_field_prefix($this->slot) . ':sequencecheck'] = $this->get_question_attempt()->get_sequence_check_count() - 1;
        $this->quba->process_all_autosaves(null, $postdata);
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        $this->delete_quba();
    }

    public function test_finish_with_unhandled_autosave_data(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Process a response and check the expected result.
        $this->process_submission(array('answer' => 'cat'));

        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(2);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->render();
        $this->check_output_contains_text_input('answer', 'cat');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Process an autosave.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'frog'));
        $this->check_current_state(question_state::$complete);
        $this->check_current_mark(null);
        $this->check_step_count(3);
        $this->save_quba();

        // Now check how that is re-displayed.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'frog');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);

        // Now finishe the attempt, without having done anything since the autosave.
        $this->finish();
        $this->save_quba();

        // Now check how that has been graded and is re-displayed.
        $this->load_quba();
        $this->check_current_state(question_state::$gradedright);
        $this->check_current_mark(1);
        $this->render();
        $this->check_output_contains_text_input('answer', 'frog', false);
        $this->check_output_contains_hidden_input(':sequencecheck', 4);

        $this->delete_quba();
    }

    /**
     * Test that regrading doesn't convert autosave steps to finished steps.
     * This can result in students loosing data (due to question_out_of_sequence_exception) if a teacher
     * regrades an attempt while it is in progress.
     */
    public function test_autosave_and_regrade_then_display(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));

        // Start attempt at a shortanswer question.
        $q = question_bank::load_question($question->id);
        $this->start_attempt_at_question($q, 'deferredfeedback', 1);

        $this->check_current_state(question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // First see if the starting sequence is right.
        $this->render();
        $this->check_output_contains_hidden_input(':sequencecheck', 1);

        // Add a submission.
        $this->process_submission(array('answer' => 'first response'));
        $this->save_quba();

        // Check the submission and that the sequence went up.
        $this->render();
        $this->check_output_contains_text_input('answer', 'first response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);
        $this->assertFalse($this->get_question_attempt()->has_autosaved_step());

        // Add a autosave response.
        $this->load_quba();
        $this->process_autosave(array('answer' => 'second response'));
        $this->save_quba();

        // Confirm that the autosave value shows up, but that the sequence hasn't increased.
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);
        $this->assertTrue($this->get_question_attempt()->has_autosaved_step());

        // Call regrade.
        $this->load_quba();
        $this->quba->regrade_all_questions();
        $this->save_quba();

        // Check and see if the autosave response is still there, that the sequence didn't increase,
        // and that there is an autosave step.
        $this->load_quba();
        $this->render();
        $this->check_output_contains_text_input('answer', 'second response');
        $this->check_output_contains_hidden_input(':sequencecheck', 2);
        $this->assertTrue($this->get_question_attempt()->has_autosaved_step());

        $this->delete_quba();
    }

    protected function tearDown(): void {
        // This test relies on the destructor for the second DB connection being called before running the next test.
        // Without this change - there will be unit test failures on "some" DBs (MySQL).
        gc_collect_cycles();
        parent::tearDown();
    }
}
