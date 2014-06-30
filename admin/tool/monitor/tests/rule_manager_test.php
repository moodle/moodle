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
 * Unit tests for rule manager api.
 *
 * @package    tool_monitor
 * @category   phpunit
 * @copyright  2014 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

class rule_manager_testcase extends advanced_testcase {

    /**
     * Test add_rule method.
     */
    public function test_add_rule() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $now = time();

        $rule = new \stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course->id;
        $rule->name = 'test rule 1';
        $rule->plugin = 'core';
        $rule->eventname = '\core\event\course_updated';
        $rule->description = 'test description 1';
        $rule->frequency = 15;
        $rule->message_template = 'test template message';
        $rule->timewindow = null;
        $rule->timecreated = $now;
        $rule->timemodified = $now;

        $ruledata = \tool_monitor\rule_manager::add_rule($rule);

        $this->assertEquals($rule->eventname, $ruledata->eventname);
        $this->assertEquals($rule->userid, $ruledata->userid);
        $this->assertEquals($rule->courseid, $ruledata->courseid);

    }

    /**
     * Test get_rule method.
     */
    public function test_get_rule() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();
        $rules1 = \tool_monitor\rule_manager::get_rule($rule->id);
        $this->assertInstanceOf('tool_monitor\rule', $rules1);
    }

    /**
     * Test update_rule method.
     */
    public function test_update_rule() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();

        $ruledata = new stdClass;
        $ruledata->id = $rule->id;
        $ruledata->frequency = 25;

        \tool_monitor\rule_manager::update_rule($ruledata);
        $this->assertNotEquals($ruledata->frequency, $rule->frequency);

    }

    /**
     * Test get_rules_by_courseid method.
     */
    public function test_get_rules_by_courseid() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();
        $ruledata = \tool_monitor\rule_manager::get_rules_by_courseid($rule->courseid);
        $this->assertEquals($rule->courseid, 0);
    }

    /**
     * Test get_rules_by_plugin method.
     */
    public function test_get_rules_by_plugin() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();
        $rules1 = \tool_monitor\rule_manager::get_rules_by_plugin($rule->plugin);
        $this->assertEquals(1, count($rules1));
    }

    /**
     * Test get_rules_by_event method.
     */
    public function test_get_rules_by_event() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();
        $rules1 = \tool_monitor\rule_manager::get_rules_by_event($rule->eventname);
        $this->assertEquals(1, count($rules1));
    }
}