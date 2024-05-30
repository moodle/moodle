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

use question_attempt;
use question_bank;
use question_engine;
use question_state;
use question_test_recordset;
use question_usage_null_observer;
use testable_question_engine_unit_of_work;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');

/**
 * Unit tests for loading data into the {@link question_attempt} class.
 *
 * Action methods like start, process_action and finish are assumed to be
 * tested by walkthrough tests in the various behaviours.
 *
 * @package    core_question
 * @category   test
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questionattempt_db_test extends \data_loading_method_test_base {
    public function test_load(): void {
        $records = new question_test_recordset(array(
            array('questionattemptid', 'contextid', 'questionusageid', 'slot',
                                   'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                       'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                               'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                'timecreated', 'userid', 'name', 'value'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 1, 0, 'todo',              null, 1256233700, 1,       null, null),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 2, 1, 'complete',          null, 1256233705, 1,   'answer',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 1, '', '', '', 1256233790, 3, 2, 'complete',          null, 1256233710, 1,   'answer',  '0'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 4, 3, 'complete',          null, 1256233715, 1,   'answer',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 5, 4, 'gradedright',  1.0000000, 1256233720, 1,  '-finish',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 6, 5, 'mangrpartial', 0.5000000, 1256233790, 1, '-comment', 'Not good enough!'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 6, 5, 'mangrpartial', 0.5000000, 1256233790, 1,    '-mark',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 6, 5, 'mangrpartial', 0.5000000, 1256233790, 1, '-maxmark',  '2'),
        ));

        $question = \test_question_maker::make_question('truefalse', 'true');
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $qa = question_attempt::load_from_records($records, 1, new question_usage_null_observer(), 'deferredfeedback');
        question_bank::end_unit_test();

        $this->assertEquals($question->questiontext, $qa->get_question(false)->questiontext);

        $this->assertEquals(6, $qa->get_num_steps());

        $step = $qa->get_step(0);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233700, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array(), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233705, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '1'), $step->get_all_data());

        $step = $qa->get_step(2);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233710, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '0'), $step->get_all_data());

        $step = $qa->get_step(3);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233715, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '1'), $step->get_all_data());

        $step = $qa->get_step(4);
        $this->assertEquals(question_state::$gradedright, $step->get_state());
        $this->assertEquals(1, $step->get_fraction());
        $this->assertEquals(1256233720, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('-finish' => '1'), $step->get_all_data());

        $step = $qa->get_step(5);
        $this->assertEquals(question_state::$mangrpartial, $step->get_state());
        $this->assertEquals(0.5, $step->get_fraction());
        $this->assertEquals(1256233790, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('-comment' => 'Not good enough!', '-mark' => '1', '-maxmark' => '2'),
                $step->get_all_data());
    }

    public function test_load_missing_question(): void {
        $records = new question_test_recordset(array(
            array('questionattemptid', 'contextid', 'questionusageid', 'slot',
                                   'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                       'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                               'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                'timecreated', 'userid', 'name', 'value'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 1, 0, 'todo',              null, 1256233700, 1,       null, null),
        ));

        question_bank::start_unit_test();
        $qa = question_attempt::load_from_records($records, 1, new question_usage_null_observer(), 'deferredfeedback');
        question_bank::end_unit_test();

        $missingq = question_bank::get_qtype('missingtype')->make_deleted_instance(-1, 2);
        $this->assertEquals($missingq, $qa->get_question(false));

        $this->assertEquals(1, $qa->get_num_steps());

        $step = $qa->get_step(0);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233700, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array(), $step->get_all_data());
    }

    public function test_load_with_autosaved_data(): void {
        $records = new question_test_recordset(array(
            array('questionattemptid', 'contextid', 'questionusageid', 'slot',
                                   'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                       'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                             'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                'timecreated', 'userid', 'name', 'value'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 4, -3, 'complete',          null, 1256233715, 1,   'answer',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 1,  0, 'todo',              null, 1256233700, 1,       null, null),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 2,  1, 'complete',          null, 1256233705, 1,   'answer',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 1, '', '', '', 1256233790, 3,  2, 'complete',          null, 1256233710, 1,   'answer',  '0'),
        ));

        $question = \test_question_maker::make_question('truefalse', 'true');
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $qa = question_attempt::load_from_records($records, 1, new question_usage_null_observer(), 'deferredfeedback');
        question_bank::end_unit_test();

        $this->assertEquals($question->questiontext, $qa->get_question(false)->questiontext);

        $this->assertEquals(4, $qa->get_num_steps());
        $this->assertTrue($qa->has_autosaved_step());

        $step = $qa->get_step(0);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233700, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array(), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233705, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '1'), $step->get_all_data());

        $step = $qa->get_step(2);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233710, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '0'), $step->get_all_data());

        $step = $qa->get_step(3);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233715, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '1'), $step->get_all_data());
    }

    public function test_load_with_unnecessary_autosaved_data(): void {
        // The point here is that the somehow (probably due to two things
        // happening concurrently, we have autosaved data in the database that
        // has already been superceded by real data, so it should be ignored.
        // There is also a second lot of redundant data to delete.
        $records = new question_test_recordset(array(
            array('questionattemptid', 'contextid', 'questionusageid', 'slot',
                                   'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                                                                       'questionsummary', 'rightanswer', 'responsesummary', 'timemodified',
                                                                                                             'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                                                                                                                'timecreated', 'userid', 'name', 'value'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 5, -2, 'complete',          null, 1256233715, 1,   'answer',  '0'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 4, -1, 'complete',          null, 1256233715, 1,   'answer',  '0'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 1,  0, 'todo',              null, 1256233700, 1,       null, null),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '', 1256233790, 2,  1, 'complete',          null, 1256233705, 1,   'answer',  '1'),
            array(1, 123, 1, 1, 'deferredfeedback', -1, 1, 2.0000000, 0.0000000, 1.0000000, 1, '', '', '', 1256233790, 3,  2, 'complete',          null, 1256233710, 1,   'answer',  '0'),
        ));

        $question = \test_question_maker::make_question('truefalse', 'true');
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $observer = new testable_question_engine_unit_of_work(
                question_engine::make_questions_usage_by_activity('unit_test', \context_system::instance()));
        $qa = question_attempt::load_from_records($records, 1, $observer, 'deferredfeedback');
        question_bank::end_unit_test();

        $this->assertEquals($question->questiontext, $qa->get_question(false)->questiontext);

        $this->assertEquals(3, $qa->get_num_steps());
        $this->assertFalse($qa->has_autosaved_step());

        $step = $qa->get_step(0);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233700, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array(), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233705, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '1'), $step->get_all_data());

        $step = $qa->get_step(2);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233710, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('answer' => '0'), $step->get_all_data());

        $this->assertEquals(2, count($observer->get_steps_deleted()));
    }
}
