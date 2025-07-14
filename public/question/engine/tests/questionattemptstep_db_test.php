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

use question_attempt_step;
use question_state;
use question_test_recordset;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/helpers.php');

/**
 * Unit tests for the loading data into the {@link question_attempt_step} class.
 *
 * @package    core_question
 * @category   test
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class questionattemptstep_db_test extends \data_loading_method_test_base {
    public function test_load_with_data(): void {
        $records = new question_test_recordset(array(
            array('attemptstepid', 'questionattemptid', 'sequencenumber', 'state', 'fraction', 'timecreated', 'userid', 'name', 'value', 'qtype', 'contextid'),
            array(             1,                   1,                0,  'todo',       null,    1256228502,       13,   null,    null, 'description', 1),
            array(             2,                   1,                1,  'complete',   null,    1256228505,       13,    'x',     'a', 'description', 1),
            array(             2,                   1,                1,  'complete',   null,    1256228505,       13,   '_y',    '_b', 'description', 1),
            array(             2,                   1,                1,  'complete',   null,    1256228505,       13,   '-z',    '!c', 'description', 1),
            array(             2,                   1,                1,  'complete',   null,    1256228505,       13, '-_t',    '!_d', 'description', 1),
            array(             3,                   1,                2,  'gradedright', 1.0,    1256228515,       13, '-finish',  '1', 'description', 1),
        ));

        $step = question_attempt_step::load_from_records($records, 2);
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256228505, $step->get_timecreated());
        $this->assertEquals(13, $step->get_user_id());
        $this->assertEquals(array('x' => 'a', '_y' => '_b', '-z' => '!c', '-_t' => '!_d'), $step->get_all_data());
    }

    public function test_load_without_data(): void {
        $records = new question_test_recordset(array(
            array('attemptstepid', 'questionattemptid', 'sequencenumber', 'state', 'fraction', 'timecreated', 'userid', 'name', 'value', 'contextid'),
            array(             2,                   1,                1,  'complete',   null,    1256228505,       13,   null,    null, 1),
        ));

        $step = question_attempt_step::load_from_records($records, 2, 'description');
        $this->assertEquals(question_state::$complete, $step->get_state());
        $this->assertNull($step->get_fraction());
        $this->assertEquals(1256228505, $step->get_timecreated());
        $this->assertEquals(13, $step->get_user_id());
        $this->assertEquals(array(), $step->get_all_data());
    }

    public function test_load_dont_be_too_greedy(): void {
        $records = new question_test_recordset(array(
            array('attemptstepid', 'questionattemptid', 'sequencenumber', 'state', 'fraction', 'timecreated', 'userid', 'name', 'value', 'contextid'),
            array(             1,                   1,                0,  'todo',       null,    1256228502,       13,    'x',  'right', 1),
            array(             2,                   2,                0,  'complete',   null,    1256228505,       13,    'x',  'wrong', 1),
        ));

        $step = question_attempt_step::load_from_records($records, 1, 'description');
        $this->assertEquals(array('x' => 'right'), $step->get_all_data());
    }
}
