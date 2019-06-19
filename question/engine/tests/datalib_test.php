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
 * Unit tests for parts of {@link question_engine_data_mapper}.
 *
 * @package   core_question
 * @category  test
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');


/**
 * Unit tests for parts of {@link question_engine_data_mapper}.
 *
 * Note that many of the methods used when attempting questions, like
 * load_questions_usage_by_activity, insert_question_*, delete_steps are
 * tested elsewhere, e.g. by {@link question_usage_autosave_test}. We do not
 * re-test them here.
 *
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_data_mapper_testcase extends qbehaviour_walkthrough_test_base {

    /**
     * We create two usages, each with two questions, a short-answer marked
     * out of 5, and and essay marked out of 10. We just start these attempts.
     *
     * Then we change the max mark for the short-answer question in one of the
     * usages to 20, using a qubaid_list, and verify.
     *
     * Then we change the max mark for the essay question in the other
     * usage to 2, using a qubaid_join, and verify.
     */
    public function test_set_max_mark_in_attempts() {

        // Set up some things the tests will need.
        $this->resetAfterTest();
        $dm = new question_engine_data_mapper();

        // Create the questions.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $sa = $generator->create_question('shortanswer', null,
                array('category' => $cat->id));
        $essay = $generator->create_question('essay', null,
                array('category' => $cat->id));

        // Create the first usage.
        $q = question_bank::load_question($sa->id);
        $this->start_attempt_at_question($q, 'interactive', 5);

        $q = question_bank::load_question($essay->id);
        $this->start_attempt_at_question($q, 'interactive', 10);

        $this->finish();
        $this->save_quba();
        $usage1id = $this->quba->get_id();

        // Create the second usage.
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
                context_system::instance());

        $q = question_bank::load_question($sa->id);
        $this->start_attempt_at_question($q, 'interactive', 5);
        $this->process_submission(array('answer' => 'fish'));

        $q = question_bank::load_question($essay->id);
        $this->start_attempt_at_question($q, 'interactive', 10);

        $this->finish();
        $this->save_quba();
        $usage2id = $this->quba->get_id();

        // Test set_max_mark_in_attempts with a qubaid_list.
        $usagestoupdate = new qubaid_list(array($usage1id));
        $dm->set_max_mark_in_attempts($usagestoupdate, 1, 20.0);
        $quba1 = question_engine::load_questions_usage_by_activity($usage1id);
        $quba2 = question_engine::load_questions_usage_by_activity($usage2id);
        $this->assertEquals(20, $quba1->get_question_max_mark(1));
        $this->assertEquals(10, $quba1->get_question_max_mark(2));
        $this->assertEquals( 5, $quba2->get_question_max_mark(1));
        $this->assertEquals(10, $quba2->get_question_max_mark(2));

        // Test set_max_mark_in_attempts with a qubaid_join.
        $usagestoupdate = new qubaid_join('{question_usages} qu', 'qu.id',
                'qu.id = :usageid', array('usageid' => $usage2id));
        $dm->set_max_mark_in_attempts($usagestoupdate, 2, 2.0);
        $quba1 = question_engine::load_questions_usage_by_activity($usage1id);
        $quba2 = question_engine::load_questions_usage_by_activity($usage2id);
        $this->assertEquals(20, $quba1->get_question_max_mark(1));
        $this->assertEquals(10, $quba1->get_question_max_mark(2));
        $this->assertEquals( 5, $quba2->get_question_max_mark(1));
        $this->assertEquals( 2, $quba2->get_question_max_mark(2));

        // Test the nothing to do case.
        $usagestoupdate = new qubaid_join('{question_usages} qu', 'qu.id',
                'qu.id = :usageid', array('usageid' => -1));
        $dm->set_max_mark_in_attempts($usagestoupdate, 2, 2.0);
        $quba1 = question_engine::load_questions_usage_by_activity($usage1id);
        $quba2 = question_engine::load_questions_usage_by_activity($usage2id);
        $this->assertEquals(20, $quba1->get_question_max_mark(1));
        $this->assertEquals(10, $quba1->get_question_max_mark(2));
        $this->assertEquals( 5, $quba2->get_question_max_mark(1));
        $this->assertEquals( 2, $quba2->get_question_max_mark(2));
    }

    public function test_load_used_variants() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $generator->create_question_category();
        $questiondata1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $questiondata2 = $generator->create_question('shortanswer', null, array('category' => $cat->id));
        $questiondata3 = $generator->create_question('shortanswer', null, array('category' => $cat->id));

        $quba = question_engine::make_questions_usage_by_activity('test', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $question1 = question_bank::load_question($questiondata1->id);
        $question3 = question_bank::load_question($questiondata3->id);
        $quba->add_question($question1);
        $quba->add_question($question1);
        $quba->add_question($question3);
        $quba->start_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        $this->assertEquals(array(
                    $questiondata1->id => array(1 => 2),
                    $questiondata2->id => array(),
                    $questiondata3->id => array(1 => 1),
                ), question_engine::load_used_variants(
                    array($questiondata1->id, $questiondata2->id, $questiondata3->id),
                    new qubaid_list(array($quba->get_id()))));
    }

    public function test_repeated_usage_saving_new_usage() {
        global $DB;

        $this->resetAfterTest();

        $initialqurows = $DB->count_records('question_usages');
        $initialqarows = $DB->count_records('question_attempts');
        $initialqasrows = $DB->count_records('question_attempt_steps');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $questiondata1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));

        $quba = question_engine::make_questions_usage_by_activity('test', context_system::instance());
        $quba->set_preferred_behaviour('deferredfeedback');
        $quba->add_question(question_bank::load_question($questiondata1->id));
        $quba->start_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        // Check one usage, question_attempts and step added.
        $firstid = $quba->get_id();
        $this->assertEquals(1, $DB->count_records('question_usages') - $initialqurows);
        $this->assertEquals(1, $DB->count_records('question_attempts') - $initialqarows);
        $this->assertEquals(1, $DB->count_records('question_attempt_steps') - $initialqasrows);

        $quba->finish_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        // Check usage id not changed.
        $this->assertEquals($firstid, $quba->get_id());

        // Check still one usage, question_attempts, but now two steps.
        $this->assertEquals(1, $DB->count_records('question_usages') - $initialqurows);
        $this->assertEquals(1, $DB->count_records('question_attempts') - $initialqarows);
        $this->assertEquals(2, $DB->count_records('question_attempt_steps') - $initialqasrows);
    }

    public function test_repeated_usage_saving_existing_usage() {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $questiondata1 = $generator->create_question('shortanswer', null, array('category' => $cat->id));

        $initquba = question_engine::make_questions_usage_by_activity('test', context_system::instance());
        $initquba->set_preferred_behaviour('deferredfeedback');
        $slot = $initquba->add_question(question_bank::load_question($questiondata1->id));
        $initquba->start_all_questions();
        question_engine::save_questions_usage_by_activity($initquba);

        $quba = question_engine::load_questions_usage_by_activity($initquba->get_id());

        $initialqurows = $DB->count_records('question_usages');
        $initialqarows = $DB->count_records('question_attempts');
        $initialqasrows = $DB->count_records('question_attempt_steps');

        $quba->process_all_actions(time(), $quba->prepare_simulated_post_data(
                [$slot => ['answer' => 'Frog']]));
        question_engine::save_questions_usage_by_activity($quba);

        // Check one usage, question_attempts and step added.
        $this->assertEquals(0, $DB->count_records('question_usages') - $initialqurows);
        $this->assertEquals(0, $DB->count_records('question_attempts') - $initialqarows);
        $this->assertEquals(1, $DB->count_records('question_attempt_steps') - $initialqasrows);

        $quba->finish_all_questions();
        question_engine::save_questions_usage_by_activity($quba);

        // Check still one usage, question_attempts, but now two steps.
        $this->assertEquals(0, $DB->count_records('question_usages') - $initialqurows);
        $this->assertEquals(0, $DB->count_records('question_attempts') - $initialqarows);
        $this->assertEquals(2, $DB->count_records('question_attempt_steps') - $initialqasrows);
    }
}
