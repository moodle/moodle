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
 * Unit tests for (some of) mod/quiz/report/reportlib.php
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2008 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php'); // Include the test libraries
require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php'); // Include the code to test


/**
 * This class contains the test cases for the functions in reportlib.php.
 *
 * @copyright  2008 Jamie Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class question_reportlib_test extends UnitTestCase {
    public static $includecoverage = array('mod/quiz/report/reportlib.php');

    public function test_quiz_report_index_by_keys() {
        $datum = array();
        $object = new stdClass();
        $object->qid = 3;
        $object->aid = 101;
        $object->response = '';
        $object->grade = 3;
        $datum[] = $object;

        $indexed = quiz_report_index_by_keys($datum, array('aid', 'qid'));

        $this->assertEqual($indexed[101][3]->qid, 3);
        $this->assertEqual($indexed[101][3]->aid, 101);
        $this->assertEqual($indexed[101][3]->response, '');
        $this->assertEqual($indexed[101][3]->grade, 3);

        $indexed = quiz_report_index_by_keys($datum, array('aid', 'qid'), false);

        $this->assertEqual($indexed[101][3][0]->qid, 3);
        $this->assertEqual($indexed[101][3][0]->aid, 101);
        $this->assertEqual($indexed[101][3][0]->response, '');
        $this->assertEqual($indexed[101][3][0]->grade, 3);
    }

    public function test_quiz_report_scale_summarks_as_percentage() {
        $quiz = new stdClass();
        $quiz->sumgrades = 10;
        $quiz->decimalpoints = 2;

        $this->assertEqual('12.34567%',
                quiz_report_scale_summarks_as_percentage(1.234567, $quiz, false));
        $this->assertEqual('12.35%',
                quiz_report_scale_summarks_as_percentage(1.234567, $quiz, true));
        $this->assertEqual('-',
                quiz_report_scale_summarks_as_percentage('-', $quiz, true));
    }
}
