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
 * Unit tests for (some of) ../questionlib.php.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');


/**
 * Unit tests for (some of) ../questionlib.php.
 *
 * @copyright  2006 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class questionlib_test extends UnitTestCase {

    public static $includecoverage = array('lib/questionlib.php');

    public function test_question_reorder_qtypes() {
        $this->assertEqual(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't1', +1),
                array(0 => 't2', 1 => 't1', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't1', -1),
                array(0 => 't1', 1 => 't2', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't2', -1),
                array(0 => 't2', 1 => 't1', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 't3', +1),
                array(0 => 't1', 1 => 't2', 2 => 't3'));
        $this->assertEqual(question_reorder_qtypes(
                array('t1' => '', 't2' => '', 't3' => ''), 'missing', +1),
                array(0 => 't1', 1 => 't2', 2 => 't3'));
    }

}
