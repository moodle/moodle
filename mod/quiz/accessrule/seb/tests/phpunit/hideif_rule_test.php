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
 * PHPUnit tests for hideif_rule.
 *
 * @package    quizaccess_seb
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use quizaccess_seb\hideif_rule;

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit tests for hideif_rule.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_seb_hideif_rule_testcase extends advanced_testcase {

    /**
     * Test that can get rule data.
     */
    public function test_can_get_what_set_in_constructor() {
        $rule = new hideif_rule('Element', 'Dependant', 'eq', 'Value');
        $this->assertEquals('Element', $rule->get_element());
        $this->assertEquals('Dependant', $rule->get_dependantname());
        $this->assertEquals('eq', $rule->get_condition());
        $this->assertEquals('Value', $rule->get_dependantvalue());
    }

}
