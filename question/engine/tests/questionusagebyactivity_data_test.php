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
use question_test_recordset;
use question_usage_by_activity;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');

/**
 * Unit tests for loading data into the {@link question_usage_by_activity} class.
 *
 * @package   core_question
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questionusagebyactivity_data_test extends \data_loading_method_test_base {
    public function test_load() {
        $scid = \context_system::instance()->id;
        $records = new question_test_recordset(array(
        array('qubaid', 'contextid', 'component', 'preferredbehaviour',
                                               'questionattemptid', 'questionusageid', 'slot',
                                                              'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                                             'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                                                     'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                                     'timecreated', 'userid', 'name', 'value'),
        array(1, $scid, 'unit_test', 'interactive', 1, 1, 1, 'interactive', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 1, 0, 'todo',             null, 1256233700, 1,       null, null),
        array(1, $scid, 'unit_test', 'interactive', 1, 1, 1, 'interactive', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 2, 1, 'todo',             null, 1256233705, 1,   'answer',  '1'),
        array(1, $scid, 'unit_test', 'interactive', 1, 1, 1, 'interactive', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 5, 2, 'gradedright', 1.0000000, 1256233720, 1,  '-finish',  '1'),
        ));

        $question = \test_question_maker::make_question('truefalse', 'true');
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $quba = question_usage_by_activity::load_from_records($records, 1);
        question_bank::end_unit_test();

        $this->assertEquals('unit_test', $quba->get_owning_component());
        $this->assertEquals(1, $quba->get_id());
        $this->assertInstanceOf('question_engine_unit_of_work', $quba->get_observer());
        $this->assertEquals('interactive', $quba->get_preferred_behaviour());

        $qa = $quba->get_question_attempt(1);

        $this->assertEquals($question->questiontext, $qa->get_question(false)->questiontext);

        $this->assertEquals(3, $qa->get_num_steps());

        $step = $qa->get_step(0);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233700, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array(), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233705, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '1'), $step->get_all_data());

        $step = $qa->get_step(2);
        $this->assertEquals(question_state::$gradedright, $step->get_state());
        $this->assertEquals(1, $step->get_fraction());
        $this->assertEquals(1256233720, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('-finish' => '1'), $step->get_all_data());
    }

    public function test_load_data_no_steps() {
        // The code had a bug where if one question_attempt had no steps,
        // load_from_records got stuck in an infinite loop. This test is to
        // verify that no longer happens.
        $scid = \context_system::instance()->id;
        $records = new question_test_recordset(array(
        array('qubaid', 'contextid', 'component', 'preferredbehaviour',
                                                   'questionattemptid', 'questionusageid', 'slot',
                                                             'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                                            'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                                                                                               'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                                                                         'timecreated', 'userid', 'name', 'value'),
        array(1, $scid, 'unit_test', 'interactive', 1, 1, 1, 'interactive', 0, 1, 1.0000000, 0.0000000, 1.0000000, 0, 'This question is missing. Unable to display anything.', '', '', 0, null, null, null, null, null, null, null, null),
        array(1, $scid, 'unit_test', 'interactive', 2, 1, 2, 'interactive', 0, 1, 1.0000000, 0.0000000, 1.0000000, 0, 'This question is missing. Unable to display anything.', '', '', 0, null, null, null, null, null, null, null, null),
        array(1, $scid, 'unit_test', 'interactive', 3, 1, 3, 'interactive', 0, 1, 1.0000000, 0.0000000, 1.0000000, 0, 'This question is missing. Unable to display anything.', '', '', 0, null, null, null, null, null, null, null, null),
        ));

        question_bank::start_unit_test();
        $quba = question_usage_by_activity::load_from_records($records, 1);
        question_bank::end_unit_test();

        $this->assertEquals('unit_test', $quba->get_owning_component());
        $this->assertEquals(1, $quba->get_id());
        $this->assertInstanceOf('question_engine_unit_of_work', $quba->get_observer());
        $this->assertEquals('interactive', $quba->get_preferred_behaviour());

        $this->assertEquals(array(1, 2, 3), $quba->get_slots());

        $qa = $quba->get_question_attempt(1);
        $this->assertEquals(0, $qa->get_num_steps());
    }

    public function test_load_data_no_qas() {
        // The code had a bug where if a question_usage had no question_attempts,
        // load_from_records got stuck in an infinite loop. This test is to
        // verify that no longer happens.
        $scid = \context_system::instance()->id;
        $records = new question_test_recordset(array(
        array('qubaid', 'contextid', 'component', 'preferredbehaviour',
                                                   'questionattemptid', 'questionusageid', 'slot',
                                                                        'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                                               'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                                                         'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                                   'timecreated', 'userid', 'name', 'value'),
        array(1, $scid, 'unit_test', 'interactive', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null),
        ));

        question_bank::start_unit_test();
        $quba = question_usage_by_activity::load_from_records($records, 1);
        question_bank::end_unit_test();

        $this->assertEquals('unit_test', $quba->get_owning_component());
        $this->assertEquals(1, $quba->get_id());
        $this->assertInstanceOf('question_engine_unit_of_work', $quba->get_observer());
        $this->assertEquals('interactive', $quba->get_preferred_behaviour());

        $this->assertEquals(array(), $quba->get_slots());
    }
}
