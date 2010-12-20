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
 * This file contains tests for the question_state class.
 *
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../lib.php');

class qubaid_condition_test extends UnitTestCase {

    protected function check_typical_question_attempts_query(
            qubaid_condition $qubaids, $expectedsql, $expectedparams) {
        $sql = "SELECT qa.id, qa.maxmark
            FROM {$qubaids->from_question_attempts('qa')}
            WHERE {$qubaids->where()} AND qa.slot = :slot";
        $this->assertEqual($expectedsql, $sql);

        $params = $qubaids->from_where_params();
        $params['slot'] = 1;
        $this->assertEqual($expectedparams, $params);
    }

    protected function check_typical_in_query(qubaid_condition $qubaids, $expectedsql, $expectedparams) {
        global $CFG;
        $sql = "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid {$qubaids->usage_id_in()}";
        $this->assertEqual($expectedsql, $sql);

        $this->assertEqual($expectedparams, $qubaids->usage_id_in_params());
    }

    public function test_qubaid_list_one_join() {
        global $CFG;
        $qubaids = new qubaid_list(array(1));
        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid = :qubaid0000 AND qa.slot = :slot",
            array('qubaid0000' => 1, 'slot' => 1));
    }

    public function test_qubaid_list_several_join() {
        global $CFG;
        $qubaids = new qubaid_list(array(1, 3, 7));
        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid IN (:qubaid0000,:qubaid0001,:qubaid0002) AND qa.slot = :slot",
            array('qubaid0000' => 1, 'qubaid0001' => 3, 'qubaid0002' => 7, 'slot' => 1));
    }

    public function test_qubaid_join() {
        global $CFG;
        $qubaids = new qubaid_join("{$CFG->prefix}other_table ot", 'ot.usageid', 'ot.id = 1');

        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}other_table ot
                JOIN {$CFG->prefix}question_attempts qa ON qa.questionusageid = ot.usageid
            WHERE ot.id = 1 AND qa.slot = :slot", array('slot' => 1));
    }

    public function test_qubaid_join_no_where_join() {
        global $CFG;
        $qubaids = new qubaid_join("{$CFG->prefix}other_table ot", 'ot.usageid');

        $this->check_typical_question_attempts_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}other_table ot
                JOIN {$CFG->prefix}question_attempts qa ON qa.questionusageid = ot.usageid
            WHERE 1 = 1 AND qa.slot = :slot", array('slot' => 1));
    }

    public function test_qubaid_list_one_in() {
        global $CFG;
        $qubaids = new qubaid_list(array(1));
        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid = :qubaid0000", array('qubaid0000' => 1));
    }

    public function test_qubaid_list_several_in() {
        global $CFG;
        $qubaids = new qubaid_list(array(1, 2, 3));
        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid IN (:qubaid0000,:qubaid0001,:qubaid0002)",
                array('qubaid0000' => 1, 'qubaid0001' => 2, 'qubaid0002' => 3));
    }

    public function test_qubaid_join_in() {
        global $CFG;
        $qubaids = new qubaid_join("{$CFG->prefix}other_table ot", 'ot.usageid', 'ot.id = 1');

        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid IN (SELECT ot.usageid FROM {$CFG->prefix}other_table ot WHERE ot.id = 1)",
                array());
    }

    public function test_qubaid_join_no_where_in() {
        global $CFG;
        $qubaids = new qubaid_join("{$CFG->prefix}other_table ot", 'ot.usageid');

        $this->check_typical_in_query($qubaids,
                "SELECT qa.id, qa.maxmark
            FROM {$CFG->prefix}question_attempts qa
            WHERE qa.questionusageid IN (SELECT ot.usageid FROM {$CFG->prefix}other_table ot WHERE 1 = 1)",
                array());
    }
}