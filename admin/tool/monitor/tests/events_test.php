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
 * Events tests.
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests that the tool_monitor events are valid and triggered correctly.
 */
class tool_monitor_events_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the rule created event.
     */
    public function test_rule_created() {
        // Create the items we need to create a rule.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        // Create the variables for the rule we want to create.
        $ruledata = new stdClass();
        $ruledata->userid = $user->id;
        $ruledata->courseid = $course->id;
        $ruledata->description = 'Rule description';
        $ruledata->descriptionformat = FORMAT_HTML;
        $ruledata->template = 'A message template';
        $ruledata->templateformat = FORMAT_HTML;
        $ruledata->frequency = 1;
        $ruledata->timewindow = 60;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $rule = \tool_monitor\rule_manager::add_rule($ruledata);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event contains the expected values.
        $this->assertInstanceOf('\tool_monitor\event\rule_created', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($rule->id, $event->objectid);
        $this->assertEventContextNotUsed($event);

        // Now let's add a system rule (courseid = 0).
        $ruledata->courseid = 0;

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\rule_manager::add_rule($ruledata);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event uses the system context.
        $this->assertInstanceOf('\tool_monitor\event\rule_created', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
    }

    /**
     * Test the rule updated event.
     */
    public function test_rule_updated() {
        // Create the items we need.
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $course = $this->getDataGenerator()->create_course();

        // Create the rule we are going to update.
        $createrule = new stdClass();
        $createrule->courseid = $course->id;
        $rule = $monitorgenerator->create_rule($createrule);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $updaterule = new stdClass();
        $updaterule->id = $rule->id;
        \tool_monitor\rule_manager::update_rule($updaterule);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event contains the expected values.
        $this->assertInstanceOf('\tool_monitor\event\rule_updated', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($rule->id, $event->objectid);
        $this->assertEventContextNotUsed($event);

        // Now let's update a system rule (courseid = 0).
        $createrule->courseid = 0;
        $rule = $monitorgenerator->create_rule($createrule);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $updaterule = new stdClass();
        $updaterule->id = $rule->id;
        \tool_monitor\rule_manager::update_rule($updaterule);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event uses the system context.
        $this->assertInstanceOf('\tool_monitor\event\rule_updated', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
    }

    /**
     * Test the rule deleted event.
     */
    public function test_rule_deleted() {
        // Create the items we need.
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $course = $this->getDataGenerator()->create_course();

        // Create the rule we are going to delete.
        $createrule = new stdClass();
        $createrule->courseid = $course->id;
        $rule = $monitorgenerator->create_rule($createrule);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\rule_manager::delete_rule($rule->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event contains the expected values.
        $this->assertInstanceOf('\tool_monitor\event\rule_deleted', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($rule->id, $event->objectid);
        $this->assertEventContextNotUsed($event);

        // Now let's delete a system rule (courseid = 0).
        $createrule = new stdClass();
        $createrule->courseid = 0;
        $rule = $monitorgenerator->create_rule($createrule);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\rule_manager::delete_rule($rule->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event uses the system context.
        $this->assertInstanceOf('\tool_monitor\event\rule_deleted', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
    }
}
