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
 * PHPUnit data generator tests.
 *
 * @package    tool_monitor
 * @category   phpunit
 * @copyright  2014 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * PHPUnit data generator test case.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @category   phpunit
 * @copyright  2014 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_monitor_generator_testcase extends advanced_testcase {

    /**
     * Test create_rule data generator.
     */
    public function test_create_rule() {
        $this->setAdminUser();
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        $rulegenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $record = new stdClass();
        $record->courseid = $course->id;
        $record->userid = $user->id;

        $rule = $rulegenerator->create_rule($record);
        $this->assertInstanceOf('tool_monitor\rule', $rule);
        $this->assertEquals($rule->userid, $record->userid);
        $this->assertEquals($rule->courseid, $record->courseid);
    }

    /**
     * Test create_subscription data generator.
     */
    public function test_create_subscription() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();

        $record = new stdClass();
        $record->courseid = $course->id;
        $record->userid = $user->id;
        $record->ruleid = $rule->id;

        $sid = $monitorgenerator->create_subscription($record);
        $subscription = \tool_monitor\subscription_manager::get_subscription($sid);
        $this->assertEquals($record->courseid, $subscription->courseid);
        $this->assertEquals($record->ruleid, $subscription->ruleid);
        $this->assertEquals($record->userid, $subscription->userid);
        $this->assertEquals(0, $subscription->cmid);
    }

    /**
     * Test create_event data generator.
     */
    public function test_create_event_entries() {
        $this->setAdminUser();
        $this->resetAfterTest(true);
        $record = new \stdClass();
        $context = \context_system::instance();

        // Default data generator values.
        $record->eventname = '\core\event\user_loggedin';
        $record->contextid = $context->id;
        $record->contextlevel = $context->contextlevel;
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // First create and assertdata using default values.
        $eventdata = $monitorgenerator->create_event_entries();
        $this->assertEquals($record->eventname, $eventdata->eventname);
        $this->assertEquals($record->contextid, $eventdata->contextid);
        $this->assertEquals($record->contextlevel, $eventdata->contextlevel);
    }

    /**
     * Test create_history data generator.
     */
    public function test_create_history() {
        $this->setAdminUser();
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $rule = $monitorgenerator->create_rule();

        $record = new \stdClass();
        $record->userid = $user->id;
        $record->ruleid = $rule->id;
        $sid = $monitorgenerator->create_subscription($record);
        \tool_monitor\subscription_manager::get_subscription($sid);
        $record->sid = $sid;
        $historydata = $monitorgenerator->create_history($record);
        $this->assertEquals($record->userid, $historydata->userid);
        $this->assertEquals($record->sid, $historydata->sid);

        // Test using default values.
        $record->userid = 1;
        $record->sid = 1;
        $historydata = $monitorgenerator->create_history($record);
        $this->assertEquals(1, $historydata->userid);
        $this->assertEquals(1, $historydata->sid);
    }
}