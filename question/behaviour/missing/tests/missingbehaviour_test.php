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

namespace qbehaviour_missing;

use qbehaviour_missing;
use question_attempt;
use question_attempt_pending_step;
use question_attempt_step;
use question_bank;
use question_display_options;
use question_state;
use question_test_recordset;
use question_usage_null_observer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../engine/lib.php');
require_once(__DIR__ . '/../../../engine/tests/helpers.php');
require_once(__DIR__ . '/../behaviour.php');

/**
 * Unit tests for the 'missing' behaviour.
 *
 * @package    qbehaviour_missing
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class missingbehaviour_test extends \advanced_testcase {

    public function test_missing_cannot_start() {
        $qa = new question_attempt(\test_question_maker::make_question('truefalse', 'true'), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException(\moodle_exception::class);
        $behaviour->init_first_step(new question_attempt_step(array()), 1);
    }

    public function test_missing_cannot_process() {
        $qa = new question_attempt(\test_question_maker::make_question('truefalse', 'true'), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException(\moodle_exception::class);
        $behaviour->process_action(new question_attempt_pending_step(array()));
    }

    public function test_missing_cannot_get_min_fraction() {
        $qa = new question_attempt(\test_question_maker::make_question('truefalse', 'true'), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException(\moodle_exception::class);
        $behaviour->get_min_fraction();
    }

    public function test_missing_cannot_get_max_fraction() {
        $qa = new question_attempt(\test_question_maker::make_question('truefalse', 'true'), 0);
        $behaviour = new qbehaviour_missing($qa, 'deferredfeedback');
        $this->expectException(\moodle_exception::class);
        $behaviour->get_max_fraction();
    }

    public function test_render_missing() {
        $records = new question_test_recordset(array(
            array('questionattemptid', 'contextid', 'questionusageid', 'slot',
                                   'behaviour', 'questionid', 'variant', 'maxmark', 'minfraction', 'maxfraction', 'flagged',
                                            'questionsummary', 'rightanswer', 'responsesummary',
                    'timemodified', 'attemptstepid', 'sequencenumber', 'state', 'fraction',
                                                       'timecreated', 'userid', 'name', 'value'),
            array(1, 123, 1, 1, 'strangeunknown', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '',
                    1256233790, 1, 0, 'todo',     null, 1256233700, 1,   '_order', '1,2,3'),
            array(1, 123, 1, 1, 'strangeunknown', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '',
                    1256233790, 2, 1, 'complete', 0.50, 1256233705, 1,  '-submit',  '1'),
            array(1, 123, 1, 1, 'strangeunknown', -1, 1, 2.0000000, 0.0000000, 1.0000000, 0, '', '', '',
                    1256233790, 2, 1, 'complete', 0.50, 1256233705, 1,  'choice0',  '1'),
        ));

        $question = \test_question_maker::make_question('truefalse', 'true');
        $question->id = -1;

        question_bank::start_unit_test();
        question_bank::load_test_question_data($question);
        $qa = question_attempt::load_from_records($records, 1,
                new question_usage_null_observer(), 'deferredfeedback');
        question_bank::end_unit_test();

        $this->assertEquals(2, $qa->get_num_steps());

        $step = $qa->get_step(0);
        $this->assertEquals(question_state::$todo, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256233700, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('_order' => '1,2,3'), $step->get_all_data());

        $step = $qa->get_step(1);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertEquals(0.5, $step->get_fraction());
        $this->assertEquals(1256233705, $step->get_timecreated());
        $this->assertEquals(1, $step->get_user_id());
        $this->assertEquals(array('-submit' => '1', 'choice0' => '1'), $step->get_all_data());

        $output = $qa->render(new question_display_options(), '1');
        $this->assertMatchesRegularExpression('/' . preg_quote($qa->get_question(false)->questiontext, '/') . '/', $output);
        $this->assertMatchesRegularExpression('/' . preg_quote(
                get_string('questionusedunknownmodel', 'qbehaviour_missing'), '/') . '/', $output);
        $this->assertTag(array('tag'=>'div', 'attributes'=>array('class'=>'warning')), $output);
    }
}
