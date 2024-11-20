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

use qubaid_condition;
use qubaid_join;
use qubaid_list;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../lib.php');

/**
 * Unit tests for qubaid_condition and subclasses.
 *
 * @package    core_question
 * @category   test
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qubaid_condition_test extends \advanced_testcase {

    protected function normalize_sql($sql, $params) {
        $newparams = array();
        preg_match_all('/(?<!:):([a-z][a-z0-9_]*)/', $sql, $named_matches);
        foreach($named_matches[1] as $param) {
            if (array_key_exists($param, $params)) {
                $newparams[] = $params[$param];
            }
        }
        $newsql = preg_replace('/(?<!:):[a-z][a-z0-9_]*/', '?', $sql);
        return array($newsql, $newparams);
    }

    protected function check_typical_question_attempts_query(
            qubaid_condition $qubaids, $expectedsql, $expectedparams) {
        $sql = "SELECT qa.id, qa.maxmark
            FROM {$qubaids->from_question_attempts('qa')}
            WHERE {$qubaids->where()} AND qa.slot = :slot";
        $params = $qubaids->from_where_params();
        $params['slot'] = 1;

        // NOTE: parameter names may change thanks to $DB->inorequaluniqueindex, normal comparison is very wrong!!
        list($sql, $params) = $this->normalize_sql($sql, $params);
        list($expectedsql, $expectedparams) = $this->normalize_sql($expectedsql, $expectedparams);

        $this->assertEquals($expectedsql, $sql);
        $this->assertEquals($expectedparams, $params);
    }

    protected function check_typical_in_query(qubaid_condition $qubaids,
            $expectedsql, $expectedparams) {
        $sql = "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid {$qubaids->usage_id_in()}";

        // NOTE: parameter names may change thanks to $DB->inorequaluniqueindex, normal comparison is very wrong!!
        list($sql, $params) = $this->normalize_sql($sql, $qubaids->usage_id_in_params());
        list($expectedsql, $expectedparams) = $this->normalize_sql($expectedsql, $expectedparams);

        $this->assertEquals($expectedsql, $sql);
        $this->assertEquals($expectedparams, $params);
    }

    public function test_qubaid_list_one_join(): void {
        $qubaids = new qubaid_list(array(1));
        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid = :qubaid1 AND qa.slot = :slot",
            array('qubaid1' => 1, 'slot' => 1));
    }

    public function test_qubaid_list_several_join(): void {
        $qubaids = new qubaid_list(array(1, 3, 7));
        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid IN (:qubaid2,:qubaid3,:qubaid4) AND qa.slot = :slot",
            array('qubaid2' => 1, 'qubaid3' => 3, 'qubaid4' => 7, 'slot' => 1));
    }

    public function test_qubaid_join(): void {
        $qubaids = new qubaid_join("{other_table} ot", 'ot.usageid', 'ot.id = 1');

        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {other_table} ot
                JOIN {question_attempts} qa ON qa.questionusageid = ot.usageid
            WHERE ot.id = 1 AND qa.slot = :slot", array('slot' => 1));
    }

    public function test_qubaid_join_no_where_join(): void {
        $qubaids = new qubaid_join("{other_table} ot", 'ot.usageid');

        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {other_table} ot
                JOIN {question_attempts} qa ON qa.questionusageid = ot.usageid
            WHERE 1 = 1 AND qa.slot = :slot", array('slot' => 1));
    }

    public function test_qubaid_list_one_in(): void {
        global $CFG;
        $qubaids = new qubaid_list(array(1));
        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid = :qubaid5", array('qubaid5' => 1));
    }

    public function test_qubaid_list_several_in(): void {
        global $CFG;
        $qubaids = new qubaid_list(array(1, 2, 3));
        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid IN (:qubaid6,:qubaid7,:qubaid8)",
                array('qubaid6' => 1, 'qubaid7' => 2, 'qubaid8' => 3));
    }

    public function test_qubaid_join_in(): void {
        global $CFG;
        $qubaids = new qubaid_join("{other_table} ot", 'ot.usageid', 'ot.id = 1');

        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid IN (SELECT ot.usageid FROM {other_table} ot WHERE ot.id = 1)",
                array());
    }

    public function test_qubaid_join_no_where_in(): void {
        global $CFG;
        $qubaids = new qubaid_join("{other_table} ot", 'ot.usageid');

        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {question_attempts} qa
            WHERE qa.questionusageid IN (SELECT ot.usageid FROM {other_table} ot WHERE 1 = 1)",
                array());
    }
}
