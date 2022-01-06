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
    public function setUp(): void {
        set_config('enablemonitor', 1, 'tool_monitor');
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
        $ruledata->plugin = 'mod_assign';
        $ruledata->eventname = '\mod_assign\event\submission_viewed';
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

    /**
     * Test the subscription created event.
     */
    public function test_subscription_created() {
        // Create the items we need to test this.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Create a rule to subscribe to.
        $rule = $monitorgenerator->create_rule();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $subscriptionid = \tool_monitor\subscription_manager::create_subscription($rule->id, $course->id, 0, $user->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event contains the expected values.
        $this->assertInstanceOf('\tool_monitor\event\subscription_created', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($subscriptionid, $event->objectid);
        $this->assertEventContextNotUsed($event);

        // Create a system subscription - trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\subscription_manager::create_subscription($rule->id, 0, 0, $user->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event uses the system context.
        $this->assertInstanceOf('\tool_monitor\event\subscription_created', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
    }

    /**
     * Test the subscription deleted event.
     */
    public function test_subscription_deleted() {
        // Create the items we need to test this.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Create a rule to subscribe to.
        $rule = $monitorgenerator->create_rule();

        $sub = new stdClass();
        $sub->courseid = $course->id;
        $sub->userid = $user->id;
        $sub->ruleid = $rule->id;

        // Create the subscription we are going to delete.
        $subscription = $monitorgenerator->create_subscription($sub);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\subscription_manager::delete_subscription($subscription->id, false);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event contains the expected values.
        $this->assertInstanceOf('\tool_monitor\event\subscription_deleted', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($subscription->id, $event->objectid);
        $this->assertEventContextNotUsed($event);

        // Now let's delete a system subscription.
        $sub = new stdClass();
        $sub->courseid = 0;
        $sub->userid = $user->id;
        $sub->ruleid = $rule->id;

        // Create the subscription we are going to delete.
        $subscription = $monitorgenerator->create_subscription($sub);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\subscription_manager::delete_subscription($subscription->id, false);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event uses the system context.
        $this->assertInstanceOf('\tool_monitor\event\subscription_deleted', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());

        // Now, create a bunch of subscriptions for the rule we created.
        $subids = array();
        $sub->courseid = $course->id;
        for ($i = 1; $i <= 10; $i++) {
            $sub->userid = $i;
            $subscription = $monitorgenerator->create_subscription($sub);
            $subids[$subscription->id] = $subscription;
        }

        // Trigger and capture the events.
        $sink = $this->redirectEvents();
        \tool_monitor\subscription_manager::remove_all_subscriptions_for_rule($rule->id);
        $events = $sink->get_events();

        // Check that there were 10 events in total.
        $this->assertCount(10, $events);

        // Get all the events and ensure they are valid.
        foreach ($events as $event) {
            $this->assertInstanceOf('\tool_monitor\event\subscription_deleted', $event);
            $this->assertEquals(context_course::instance($course->id), $event->get_context());
            $this->assertEventContextNotUsed($event);
            $this->assertArrayHasKey($event->objectid, $subids);
            unset($subids[$event->objectid]);
        }

        // We should have found all the subscriptions.
        $this->assertEmpty($subids);
    }

    /**
     * Test the subscription criteria met event.
     */
    public function test_subscription_criteria_met() {
        // Create the items we need to test this.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $chapter = $bookgenerator->create_chapter(array('bookid' => $book->id));
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Create a rule we want to subscribe to.
        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course->id;
        $rule->plugin = 'mod_book';
        $rule->eventname = '\mod_book\event\chapter_viewed';
        $rule->frequency = 1;
        $rule->timewindow = 60;
        $rule = $monitorgenerator->create_rule($rule);

        // Create the subscription.
        $sub = new stdClass();
        $sub->courseid = $course->id;
        $sub->userid = $user->id;
        $sub->ruleid = $rule->id;
        $monitorgenerator->create_subscription($sub);

        // Now create the \mod_book\event\chapter_viewed event we are listening for.
        $context = context_module::instance($book->cmid);
        $event = \mod_book\event\chapter_viewed::create_from_chapter($book, $context, $chapter);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        \tool_monitor\eventobservers::process_event($event);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Confirm that the event contains the expected values.
        $this->assertInstanceOf('\tool_monitor\event\subscription_criteria_met', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }
}
