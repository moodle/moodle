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
 * This file contains tests for the question_usage_by_activity class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../lib.php');
require_once(dirname(__FILE__) . '/helpers.php');


/**
 * Unit tests for the question_usage_by_activity class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_by_activity_test extends UnitTestCase {

    public function test_set_get_preferred_model() {
        // Set up
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));

        // Exercise SUT and verify.
        $quba->set_preferred_behaviour('deferredfeedback');
        $this->assertEqual('deferredfeedback', $quba->get_preferred_behaviour());
    }

    public function test_set_get_id() {
        // Set up
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));

        // Exercise SUT and verify
        $quba->set_id_from_database(123);
        $this->assertEqual(123, $quba->get_id());
    }

    public function test_fake_id() {
        // Set up
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));

        // Exercise SUT and verify
        $this->assertTrue($quba->get_id());
    }

    public function test_create_usage_and_add_question() {
        // Exercise SUT
        $context = get_context_instance(CONTEXT_SYSTEM);
        $quba = question_engine::make_questions_usage_by_activity('unit_test', $context);
        $quba->set_preferred_behaviour('deferredfeedback');
        $tf = test_question_maker::make_question('truefalse', 'true');
        $slot = $quba->add_question($tf);

        // Verify.
        $this->assertEqual($slot, 1);
        $this->assertEqual('unit_test', $quba->get_owning_component());
        $this->assertIdentical($context, $quba->get_owning_context());
        $this->assertEqual($quba->question_count(), 1);
        $this->assertEqual($quba->get_question_state($slot), question_state::$notstarted);
    }

    public function test_get_question() {
        // Set up.
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
        $quba->set_preferred_behaviour('deferredfeedback');
        $tf = test_question_maker::make_question('truefalse', 'true');
        $slot = $quba->add_question($tf);

        // Exercise SUT and verify.
        $this->assertIdentical($tf, $quba->get_question($slot));

        $this->expectException();
        $quba->get_question($slot + 1);
    }

    public function test_extract_responses() {
        // Start a deferred feedback attempt with CBM and add the question to it.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
        $quba->set_preferred_behaviour('deferredcbm');
        $slot = $quba->add_question($tf);
        $quba->start_all_questions();

        // Prepare data to be submitted
        $prefix = $quba->get_field_prefix($slot);
        $answername = $prefix . 'answer';
        $certaintyname = $prefix . '-certainty';
        $getdata = array(
            $answername => 1,
            $certaintyname => 3,
            'irrelevant' => 'should be ignored',
        );

        // Exercise SUT
        $submitteddata = $quba->extract_responses($slot, $getdata);

        // Verify.
        $this->assertEqual(array('answer' => 1, '-certainty' => 3), $submitteddata);
    }

    public function test_access_out_of_sequence_throws_exception() {
        // Start a deferred feedback attempt with CBM and add the question to it.
        $tf = test_question_maker::make_question('truefalse', 'true');
        $quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
        $quba->set_preferred_behaviour('deferredcbm');
        $slot = $quba->add_question($tf);
        $quba->start_all_questions();

        // Prepare data to be submitted
        $prefix = $quba->get_field_prefix($slot);
        $answername = $prefix . 'answer';
        $certaintyname = $prefix . '-certainty';
        $postdata = array(
            $answername => 1,
            $certaintyname => 3,
            $prefix . ':sequencecheck' => 1,
            'irrelevant' => 'should be ignored',
        );

        // Exercise SUT - no exception yet.
        $quba->process_all_actions($slot, $postdata);

        $postdata = array(
            $answername => 1,
            $certaintyname => 3,
            $prefix . ':sequencecheck' => 3,
            'irrelevant' => 'should be ignored',
        );

        // Exercise SUT - now it should fail.
        $this->expectException('question_out_of_sequence_exception');
        $quba->process_all_actions($slot, $postdata);
    }
}

/**
 * Unit tests for loading data into the {@link question_usage_by_activity} class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_db_test extends data_loading_method_test_base {
    public function test_load() {
        $records = new test_recordset(array(
        array('qubaid', 'contextid', 'component', 'preferredbehaviour',
                                               'questionattemptid', 'contextid', 'questionusageid', 'slot',
                                                              'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'flagged',
                                                                                                             'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                                                     'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                                     'timecreated', 'userid', 'name', 'value'),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 2.0000000, 0.0000000, 0, '', '', '', 1256233790, 1, 0, 'todo',             null, 1256233700, 1,       null, null),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 2.0000000, 0.0000000, 0, '', '', '', 1256233790, 2, 1, 'todo',             null, 1256233705, 1,   'answer',  '1'),
        array(1, 1, 'unit_test', 'interactive', 1, 123, 1, 1, 'interactive', -1, 1, 2.0000000, 0.0000000, 0, '', '', '', 1256233790, 5, 2, 'gradedright', 1.0000000, 1256233720, 1,  '-finish',  '1'),
        ));

        $question = test_question_maker::make_question('truefalse', 'true');
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $quba = question_usage_by_activity::load_from_records($records, 1);
        question_bank::end_unit_test();

        $this->assertEqual('unit_test', $quba->get_owning_component());
        $this->assertEqual(1, $quba->get_id());
        $this->assertIsA($quba->get_observer(), 'question_engine_unit_of_work');
        $this->assertEqual('interactive', $quba->get_preferred_behaviour());

        $qa = $quba->get_question_attempt(1);

        $this->assertEqual($question->questiontext, $qa->get_question()->questiontext);

        $this->assertEqual(3, $qa->get_num_steps());

        $step = $qa->get_step(0);
        $this->assertEqual(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEqual(1256233700, $step->get_timecreated());
        $this->assertEqual(1, $step->get_user_id());
        $this->assertEqual(array(), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEqual(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEqual(1256233705, $step->get_timecreated());
        $this->assertEqual(1, $step->get_user_id());
        $this->assertEqual(array('answer' => '1'), $step->get_all_data());

        $step = $qa->get_step(2);
        $this->assertEqual(question_state::$gradedright, $step->get_state());
        $this->assertEqual(1, $step->get_fraction());
        $this->assertEqual(1256233720, $step->get_timecreated());
        $this->assertEqual(1, $step->get_user_id());
        $this->assertEqual(array('-finish' => '1'), $step->get_all_data());
    }
}
