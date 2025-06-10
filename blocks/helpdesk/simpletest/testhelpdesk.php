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
 *
 * @package    block_helpdesk
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->dirroot . '/blocks/helpdesk/lib.php');

class helpdesk_test extends UnitTestCase {
    public function test_equality_translator() {
        $expected = array (
            "LIKE '%ell%'",
            "= 'sneebs'",
            "LIKE 'apple%'",
            "LIKE '%sauce'"
        );

        $actual = array (
            hdesk_translate_equality('contains', 'ell'),
            hdesk_translate_equality('equal', 'sneebs'),
            hdesk_translate_equality('starts', 'apple'),
            hdesk_translate_equality('ends', 'sauce')
        );

        foreach (range(0, 3) as $index) {
            $this->assertEqual($expected[$index], $actual[$index]);
        }
    }

    public function test_results_sql() {
        $expected = array (
            "fullname LIKE '%bo%'",
            "username = 'pcali1'",
            "lastname LIKE 'Smi%' AND firstname = 'John'",
            "email LIKE '%@lsu.edu'"
        );

        $buildstring = function(array $fields, array $equalities) {
            $data = new stdClass;
            $criterion = array();
            foreach ($fields as $field => $value) {
                $data->{$field . '_terms'} = $value;
                $data->{$field . '_equality'} = $equalities[$field];
                $criterion[$field] = $value;
            }
            return hdesk_get_results_sql($data, $criterion);
        };

        $actual = array (
            $buildstring(array('fullname' => 'bo'), array('fullname' => 'contains')),
            $buildstring(array('username' => 'pcali1'), array('username' => 'equal')),
            $buildstring(array('lastname' => 'Smi', 'firstname' => 'John'),
                          array('lastname' => 'starts', 'firstname' => 'equal')),
            $buildstring(array('email' => '@lsu.edu'), array('email' => 'ends'))
        );

        foreach (range(0, 3) as $index) {
            $this->assertEqual($expected[$index], $actual[$index]);
        }
    }
}
