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
 * Unit tests for event observers.
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/blog/locallib.php');
require_once($CFG->dirroot . '/blog/lib.php');

/**
 * Class tool_monitor_eventobservers_testcase
 *
 * Tests for event observers
 */
class tool_monitor_eventobservers_testcase extends advanced_testcase {
    /**
     * Set up method.
     */
    public function setUp() {
        // Enable monitor.
        set_config('enablemonitor', 1, 'tool_monitor');
    }

    /**
     * Test observer for course delete event.
     */
    public function test_course_deleted() {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course1->id;
        $rule->plugin = 'test';

        $sub = new stdClass();
        $sub->courseid = $course1->id;
        $sub->userid = $user->id;

        // Add 10 rules for this course with subscriptions.
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Add 10 random rules for course 2.
        $rule->courseid = $course2->id;
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->courseid = $rule->courseid;
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Add a site rule.
        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = 0;
        $rule->plugin = 'core';
        $monitorgenerator->create_rule($rule);

        // Verify that if we do not specify that we do not want the site rules, they are returned.
        $courserules = \tool_monitor\rule_manager::get_rules_by_courseid($course1->id);
        $this->assertCount(11, $courserules);

        // Verify data before course delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(20, $totalrules);
        $courserules = \tool_monitor\rule_manager::get_rules_by_courseid($course1->id, 0, 0, false);
        $this->assertCount(10, $courserules);
        $this->assertEquals(20, $DB->count_records('tool_monitor_subscriptions'));
        $coursesubs = \tool_monitor\subscription_manager::get_user_subscriptions_for_course($course1->id, 0, 0, $user->id);
        $this->assertCount(10, $coursesubs);

        // Let us delete the course now.
        delete_course($course1->id, false);

        // Confirm the site rule still exists.
        $this->assertEquals(1, $DB->count_records('tool_monitor_rules', array('courseid' => 0)));

        // Verify data after course delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(10, $totalrules);
        $courserules = \tool_monitor\rule_manager::get_rules_by_courseid($course1->id, 0, 0, false);
        $this->assertCount(0, $courserules); // Making sure all rules are deleted.
        $this->assertEquals(10, $DB->count_records('tool_monitor_subscriptions'));
        $coursesubs = \tool_monitor\subscription_manager::get_user_subscriptions_for_course($course1->id, 0, 0, $user->id);
        $this->assertCount(0, $coursesubs); // Making sure all subscriptions are deleted.
    }

    /**
     * This tests if writing of the events to the table tool_monitor_events is working fine.
     */
    public function test_flush() {
        global $DB;

        $this->resetAfterTest();

        // Create the necessary items for testing.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Fire a bunch of events.
        // Trigger a bunch of other events.
        $eventparams = array(
            'context' => context_course::instance($course->id)
        );
        for ($i = 0; $i < 5; $i++) {
            \core\event\course_viewed::create($eventparams)->trigger();
            \mod_quiz\event\course_module_instance_list_viewed::create($eventparams)->trigger();
            \mod_scorm\event\course_module_instance_list_viewed::create($eventparams)->trigger();
        }

        // Confirm that nothing was stored in the tool_monitor_events table
        // as we do not have any subscriptions associated for the above events.
        $this->assertEquals(0, $DB->count_records('tool_monitor_events'));

        // Now, let's create a rule so an event can be stored.
        $rule = new stdClass();
        $rule->courseid = $course->id;
        $rule->plugin = 'mod_book';
        $rule->eventname = '\mod_book\event\course_module_instance_list_viewed';
        $rule = $monitorgenerator->create_rule($rule);

        // Let's subscribe to this rule.
        $sub = new stdClass;
        $sub->courseid = $course->id;
        $sub->ruleid = $rule->id;
        $sub->userid = $user->id;
        $monitorgenerator->create_subscription($sub);

        // Again, let's just fire more events to make sure they still aren't stored.
        for ($i = 0; $i < 5; $i++) {
            \core\event\course_viewed::create($eventparams)->trigger();
            \mod_quiz\event\course_module_instance_list_viewed::create($eventparams)->trigger();
            \mod_scorm\event\course_module_instance_list_viewed::create($eventparams)->trigger();
        }

        // Fire the event we want to capture.
        $event = \mod_book\event\course_module_instance_list_viewed::create_from_course($course);
        $event->trigger();

        // Check that the event data is valid.
        $events = $DB->get_records('tool_monitor_events');
        $this->assertEquals(1, count($events));
        $monitorevent = array_pop($events);
        $this->assertEquals($event->eventname, $monitorevent->eventname);
        $this->assertEquals($event->contextid, $monitorevent->contextid);
        $this->assertEquals($event->contextlevel, $monitorevent->contextlevel);
        $this->assertEquals($event->get_url()->out(), $monitorevent->link);
        $this->assertEquals($event->courseid, $monitorevent->courseid);
        $this->assertEquals($event->timecreated, $monitorevent->timecreated);

        // Remove the stored events.
        $DB->delete_records('tool_monitor_events');

        // Now, let's create a site wide rule.
        $rule = new stdClass();
        $rule->courseid = 0;
        $rule->plugin = 'mod_book';
        $rule->eventname = '\mod_book\event\course_module_instance_list_viewed';
        $rule = $monitorgenerator->create_rule($rule);

        // Let's subscribe to this rule.
        $sub = new stdClass;
        $sub->courseid = 0;
        $sub->ruleid = $rule->id;
        $sub->userid = $user->id;
        $monitorgenerator->create_subscription($sub);

        // Fire the event we want to capture - but in a different course.
        $event = \mod_book\event\course_module_instance_list_viewed::create_from_course($course2);
        $event->trigger();

        // Check that the event data is valid.
        $events = $DB->get_records('tool_monitor_events');
        $this->assertEquals(1, count($events));
        $monitorevent = array_pop($events);
        $this->assertEquals($event->eventname, $monitorevent->eventname);
        $this->assertEquals($event->contextid, $monitorevent->contextid);
        $this->assertEquals($event->contextlevel, $monitorevent->contextlevel);
        $this->assertEquals($event->get_url()->out(), $monitorevent->link);
        $this->assertEquals($event->courseid, $monitorevent->courseid);
        $this->assertEquals($event->timecreated, $monitorevent->timecreated);
    }

    /**
     * Test the notification sending features.
     */
    public function test_process_event() {

        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $msgsink = $this->redirectMessages();

        // Generate data.
        $course = $this->getDataGenerator()->create_course();
        $toolgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $rulerecord = new stdClass();
        $rulerecord->courseid = $course->id;
        $rulerecord->eventname = '\mod_book\event\course_module_instance_list_viewed';
        $rulerecord->frequency = 1;

        $rule = $toolgenerator->create_rule($rulerecord);

        $subrecord = new stdClass();
        $subrecord->courseid = $course->id;
        $subrecord->ruleid = $rule->id;
        $subrecord->userid = $USER->id;
        $toolgenerator->create_subscription($subrecord);

        $recordexists = $DB->record_exists('task_adhoc', array('component' => 'tool_monitor'));
        $this->assertFalse($recordexists);

        // Now let us trigger the event.
        $event = \mod_book\event\course_module_instance_list_viewed::create_from_course($course);
        $event->trigger();

        $this->verify_processed_data($msgsink);

        // Clean up.
        \tool_monitor\rule_manager::delete_rule($rule->id);
        $DB->delete_records('tool_monitor_events');

        // Let us create a rule with more than 1 frequency.
        $rulerecord->frequency = 5;
        $rule = $toolgenerator->create_rule($rulerecord);
        $subrecord->ruleid = $rule->id;
        $toolgenerator->create_subscription($subrecord);

        // Let us trigger events.
        for ($i = 0; $i < 5; $i++) {
            $event = \mod_book\event\course_module_instance_list_viewed::create_from_course($course);
            $event->trigger();
            if ($i != 4) {
                $this->verify_message_not_sent_yet($msgsink);
            }
        }

        $this->verify_processed_data($msgsink);

        // Clean up.
        \tool_monitor\rule_manager::delete_rule($rule->id);
        $DB->delete_records('tool_monitor_events');

        // Now let us create a rule specific to a module instance.
        $cm = new stdClass();
        $cm->course = $course->id;
        $book = $this->getDataGenerator()->create_module('book', $cm);
        $rulerecord->eventname = '\mod_book\event\course_module_viewed';
        $rulerecord->cmid = $book->cmid;
        $rule = $toolgenerator->create_rule($rulerecord);
        $subrecord->ruleid = $rule->id;
        $toolgenerator->create_subscription($subrecord);

        // Let us trigger events.
        $params = array(
            'context' => context_module::instance($book->cmid),
            'objectid' => $book->id
        );
        for ($i = 0; $i < 5; $i++) {
            $event = \mod_book\event\course_module_viewed::create($params);
            $event->trigger();
            if ($i != 4) {
                $this->verify_message_not_sent_yet($msgsink);
            }
        }

        $this->verify_processed_data($msgsink);

        // Clean up.
        \tool_monitor\rule_manager::delete_rule($rule->id);
        $DB->delete_records('tool_monitor_events');

        // Now let us create a rule for event that happens in category context events.
        $rulerecord->eventname = '\core\event\course_category_created';
        $rulerecord->courseid = 0;
        $rule = $toolgenerator->create_rule($rulerecord);
        $subrecord->courseid = 0;
        $subrecord->ruleid = $rule->id;
        $toolgenerator->create_subscription($subrecord);

        // Let us trigger events.
        for ($i = 0; $i < 5; $i++) {
            $this->getDataGenerator()->create_category();
            if ($i != 4) {
                $this->verify_message_not_sent_yet($msgsink);
            }
        }
        $this->verify_processed_data($msgsink);

        // Clean up.
        \tool_monitor\rule_manager::delete_rule($rule->id);
        $DB->delete_records('tool_monitor_events');

        // Now let us create a rule at site level.
        $rulerecord->eventname = '\core\event\blog_entry_created';
        $rulerecord->courseid = 0;
        $rule = $toolgenerator->create_rule($rulerecord);
        $subrecord->courseid = 0;
        $subrecord->ruleid = $rule->id;
        $toolgenerator->create_subscription($subrecord);

        // Let us trigger events.
        $blog = new blog_entry();
        $blog->subject = "Subject of blog";
        $blog->userid = $USER->id;
        $states = blog_entry::get_applicable_publish_states();
        $blog->publishstate = reset($states);
        for ($i = 0; $i < 5; $i++) {
            $newblog = fullclone($blog);
            $newblog->add();
            if ($i != 4) {
                $this->verify_message_not_sent_yet($msgsink);
            }
        }

        $this->verify_processed_data($msgsink);
    }

    /**
     * Test that same events are not used twice to calculate conditions for a single subscription.
     */
    public function test_multiple_notification_not_sent() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $messagesink = $this->redirectMessages();

        // Generate data.
        $course = $this->getDataGenerator()->create_course();
        $toolgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $rulerecord = new stdClass();
        $rulerecord->courseid = $course->id;
        $rulerecord->eventname = '\mod_book\event\course_module_instance_list_viewed';
        $rulerecord->frequency = 5;

        $rule = $toolgenerator->create_rule($rulerecord);

        $subrecord = new stdClass();
        $subrecord->courseid = $course->id;
        $subrecord->ruleid = $rule->id;
        $subrecord->userid = $USER->id;
        $toolgenerator->create_subscription($subrecord);

        for ($i = 0; $i < 7; $i++) {
            // Now let us trigger 7 instances of the event.
            $event = \mod_book\event\course_module_instance_list_viewed::create_from_course($course);
            $event->trigger();
            sleep(1); // Add a second delay, to prevent time collisions.
        }
        $this->run_adhock_tasks();
        $messages = $messagesink->get_messages();
        $this->assertCount(1, $messages); // There should be only one message not 3.
        for ($i = 0; $i < 3; $i++) {
            // Now let us trigger 5 more instances of the event.
            $event = \mod_book\event\course_module_instance_list_viewed::create_from_course($course);
            $event->trigger();
        }

        $this->run_adhock_tasks();
        $messages = $messagesink->get_messages();
        $this->assertCount(2, $messages); // There should be two messages now.
    }

    /**
     * Run adhoc tasks.
     */
    protected function run_adhock_tasks() {
        while ($task = \core\task\manager::get_next_adhoc_task(time())) {
            $task->execute();
            \core\task\manager::adhoc_task_complete($task);
        }
        $this->expectOutputRegex("/^Sending message to the user with id \d+ for the subscription with id \d+\.\.\..Sent./ms");
    }

    /**
     * Verify that task was scheduled and a message was sent as expected.
     *
     * @param phpunit_message_sink $msgsink Message sink
     */
    protected function verify_processed_data(phpunit_message_sink $msgsink) {
        global $DB, $USER;

        $recordexists = $DB->count_records('task_adhoc', array('component' => 'tool_monitor'));
        $this->assertEquals(1, $recordexists); // We should have an adhock task now to send notifications.
        $this->run_adhock_tasks();
        $this->assertEquals(1, $msgsink->count());
        $msgs = $msgsink->get_messages();
        $msg = array_pop($msgs);
        $this->assertEquals($USER->id, $msg->useridto);
        $this->assertEquals(1, $msg->notification);
        $msgsink->clear();
    }

    /**
     * Verify that a message was not sent.
     *
     * @param phpunit_message_sink $msgsink Message sink
     */
    protected function verify_message_not_sent_yet(phpunit_message_sink $msgsink) {
        $msgs = $msgsink->get_messages();
        $this->assertCount(0, $msgs);
        $msgsink->clear();
    }

    /**
     * Tests for replace_placeholders method.
     */
    public function test_replace_placeholders() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $msgsink = $this->redirectMessages();

        // Generate data.
        $course = $this->getDataGenerator()->create_course();
        $toolgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');
        $context = \context_user::instance($USER->id, IGNORE_MISSING);

        // Creating book.
        $cm = new stdClass();
        $cm->course = $course->id;
        $book = $this->getDataGenerator()->create_module('book', $cm);

        // Creating rule.
        $rulerecord = new stdClass();
        $rulerecord->courseid = $course->id;
        $rulerecord->eventname = '\mod_book\event\course_module_viewed';
        $rulerecord->cmid = $book->cmid;
        $rulerecord->frequency = 1;
        $rulerecord->template = '{link} {modulelink} {rulename} {description} {eventname}';

        $rule = $toolgenerator->create_rule($rulerecord);

        // Creating subscription.
        $subrecord = new stdClass();
        $subrecord->courseid = $course->id;
        $subrecord->ruleid = $rule->id;
        $subrecord->userid = $USER->id;
        $toolgenerator->create_subscription($subrecord);

        // Now let us trigger the event.
        $params = array(
            'context' => context_module::instance($book->cmid),
            'objectid' => $book->id
        );

        $event = \mod_book\event\course_module_viewed::create($params);
        $event->trigger();
        $this->run_adhock_tasks();
        $msgs = $msgsink->get_messages();
        $msg = array_pop($msgs);

        $modurl = new moodle_url('/mod/book/view.php', array('id' => $book->cmid));
        $expectedmsg = $event->get_url()->out() . ' ' .
                        $modurl->out()  . ' ' .
                        $rule->get_name($context) . ' ' .
                        $rule->get_description($context) . ' ' .
                        $rule->get_event_name();

        $this->assertEquals($expectedmsg, $msg->fullmessage);
    }

    /**
     * Test observer for user delete event.
     */
    public function test_user_deleted() {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course1->id;
        $rule->plugin = 'test';

        $sub = new stdClass();
        $sub->courseid = $course1->id;
        $sub->userid = $user->id;

        // Add 10 rules for this course with subscriptions.
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Add 10 random rules for course 2.
        $rule->courseid = $course2->id;
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->courseid = $rule->courseid;
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Verify data before user delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(20, $totalrules);
        $totalsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(20, $totalsubs);

        // Let us delete the user now.
        delete_user($user);

        // Verify data after course delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(20, $totalrules);
        $totalsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(0, $totalsubs); // Make sure all subscriptions are deleted.
    }

    /**
     * Test observer for course module delete event.
     */
    public function test_course_module_deleted() {
        global $DB;

        $this->setAdminUser();
        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Now let us create a rule specific to a module instance.
        $cm = new stdClass();
        $cm->course = $course1->id;
        $book = $this->getDataGenerator()->create_module('book', $cm);

        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course1->id;
        $rule->plugin = 'test';

        $sub = new stdClass();
        $sub->courseid = $course1->id;
        $sub->userid = $user->id;
        $sub->cmid = $book->cmid;

        // Add 10 rules for this course with subscriptions for this module.
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->ruleid = $createdrule->id;
            $monitorgenerator->create_subscription($sub);
        }

        // Add 10 random rules for course 2.
        $rule->courseid = $course2->id;
        for ($i = 0; $i < 10; $i++) {
            $createdrule = $monitorgenerator->create_rule($rule);
            $sub->courseid = $rule->courseid;
            $sub->ruleid = $createdrule->id;
            $sub->cmid = 0;
            $monitorgenerator->create_subscription($sub);
        }

        // Verify data before module delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(20, $totalrules);
        $totalsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(20, $totalsubs);

        // Let us delete the user now.
        course_delete_module($book->cmid);

        // Verify data after course delete.
        $totalrules = \tool_monitor\rule_manager::get_rules_by_plugin('test');
        $this->assertCount(20, $totalrules);
        $totalsubs = $DB->get_records('tool_monitor_subscriptions');
        $this->assertCount(10, $totalsubs); // Make sure only relevant subscriptions are deleted.
    }

}
