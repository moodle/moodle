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
 * Testcase class for group_rule class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Testcase class for group_rule class.
 *
 * @package    auth_iomadsaml2
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_group_rule_test_testcase extends advanced_testcase {

    /**
     * Test we can get list of rules from config string.
     */
    public function test_get_list() {
        $config = "allow group1=allowed\r\ngroup=blocked\r\ndeny group2=blocked\ndeny groups|blocked\ndeny groups= \ndeny  =test";

        $rules = \auth_iomadsaml2\group_rule::get_list($config);
        $this->assertCount(2, $rules);

        $this->assertEquals('group1', $rules[0]->get_attribute());
        $this->assertEquals('allowed', $rules[0]->get_group());
        $this->assertEquals(true, $rules[0]->is_allowed());

        $this->assertEquals('group2', $rules[1]->get_attribute());
        $this->assertEquals('blocked', $rules[1]->get_group());
        $this->assertEquals(false, $rules[1]->is_allowed());
    }

}
