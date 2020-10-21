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
 * Unit tests for the tool_monitor clean events task.
 *
 * @package    tool_monitor
 * @category   test
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Class used to test the tool_monitor clean events task.
 */
class tool_monitor_task_clean_events_testcase extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp(): void {
        set_config('enablemonitor', 1, 'tool_monitor');
        $this->resetAfterTest(true);
    }

    /**
     * Tests the cleaning up of events.
     */
    public function test_clean_events() {
        global $DB;

        // Create the necessary items for testing.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $bookgenerator = $this->getDataGenerator()->get_plugin_generator('mod_book');
        $book = $this->getDataGenerator()->create_module('book', array('course' => $course->id));
        $bookcontext = context_module::instance($book->cmid);
        $bookchapter = $bookgenerator->create_chapter(array('bookid' => $book->id));
        $course2 = $this->getDataGenerator()->create_course();
        $book2 = $this->getDataGenerator()->create_module('book', array('course' => $course2->id));
        $book2context = context_module::instance($book2->cmid);
        $book2chapter = $bookgenerator->create_chapter(array('bookid' => $book2->id));
        $monitorgenerator = $this->getDataGenerator()->get_plugin_generator('tool_monitor');

        // Let's set some data for the rules we need before we can generate them.
        $rule = new stdClass();
        $rule->userid = $user->id;
        $rule->courseid = $course->id;
        $rule->plugin = 'mod_book';
        $rule->eventname = '\mod_book\event\course_module_viewed';
        $rule->timewindow = 500;

        // Let's add a few rules we want to monitor.
        $rule1 = $monitorgenerator->create_rule($rule);

        $rule->eventname = '\mod_book\event\course_module_instance_list_viewed';
        $rule2 = $monitorgenerator->create_rule($rule);

        // Add the same rules for the same course, but this time with a lower timewindow (used to test that we do not
        // remove an event for a course if there is still a rule where the maximum timewindow has not been reached).
        $rule->eventname = '\mod_book\event\course_module_viewed';
        $rule->timewindow = 200;
        $rule3 = $monitorgenerator->create_rule($rule);

        $rule->eventname = '\mod_book\event\course_module_instance_list_viewed';
        $rule4 = $monitorgenerator->create_rule($rule);

        // Add another rule in a different course.
        $rule->courseid = $course2->id;
        $rule->eventname = '\mod_book\event\chapter_viewed';
        $rule->timewindow = 200;
        $rule5 = $monitorgenerator->create_rule($rule);

        // Add a site wide rule.
        $rule->courseid = 0;
        $rule->eventname = '\mod_book\event\chapter_viewed';
        $rule->timewindow = 500;
        $rule6 = $monitorgenerator->create_rule($rule);


        // Let's subscribe to these rules.
        $sub = new stdClass;
        $sub->courseid = $course->id;
        $sub->ruleid = $rule1->id;
        $sub->userid = $user->id;
        $monitorgenerator->create_subscription($sub);

        $sub->ruleid = $rule2->id;
        $monitorgenerator->create_subscription($sub);

        $sub->ruleid = $rule3->id;
        $monitorgenerator->create_subscription($sub);

        $sub->ruleid = $rule4->id;
        $monitorgenerator->create_subscription($sub);

        $sub->ruleid = $rule5->id;
        $sub->courseid = $course2->id;
        $monitorgenerator->create_subscription($sub);

        $sub->ruleid = $rule6->id;
        $sub->courseid = 0;
        $monitorgenerator->create_subscription($sub);

        // Now let's populate the tool_monitor table with the events associated with those rules.
        \mod_book\event\course_module_viewed::create_from_book($book, $bookcontext)->trigger();
        \mod_book\event\course_module_instance_list_viewed::create_from_course($course)->trigger();
        \mod_book\event\chapter_viewed::create_from_chapter($book, $bookcontext, $bookchapter)->trigger();

        // Let's trigger the viewed events again, but in another course. The rules created for these events are
        // associated with another course, so these events should get deleted when we trigger the cleanup task.
        \mod_book\event\course_module_viewed::create_from_book($book2, $book2context)->trigger();
        \mod_book\event\course_module_instance_list_viewed::create_from_course($course2)->trigger();
        // Trigger a chapter_viewed event in this course - this should not get deleted as the rule is site wide.
        \mod_book\event\chapter_viewed::create_from_chapter($book2, $book2context, $book2chapter)->trigger();

        // Trigger a bunch of other events.
        $eventparams = array(
            'context' => context_course::instance($course->id)
        );
        for ($i = 0; $i < 5; $i++) {
            \mod_quiz\event\course_module_instance_list_viewed::create($eventparams)->trigger();
            \mod_scorm\event\course_module_instance_list_viewed::create($eventparams)->trigger();
        }

        // We do not store events that have no subscriptions - so there will be only 4 events.
        $this->assertEquals(4, $DB->count_records('tool_monitor_events'));

        // Run the task and check that all the quiz, scorm and rule events are removed as well as the course_module_*
        // viewed events in the second course.
        $task = new \tool_monitor\task\clean_events();
        $task->execute();

        $events = $DB->get_records('tool_monitor_events', array(), 'id');
        $this->assertEquals(4, count($events));
        $event1 = array_shift($events);
        $event2 = array_shift($events);
        $event3 = array_shift($events);
        $event4 = array_shift($events);
        $this->assertEquals('\mod_book\event\course_module_viewed', $event1->eventname);
        $this->assertEquals($course->id, $event1->courseid);
        $this->assertEquals('\mod_book\event\course_module_instance_list_viewed', $event2->eventname);
        $this->assertEquals($course->id, $event2->courseid);
        $this->assertEquals('\mod_book\event\chapter_viewed', $event3->eventname);
        $this->assertEquals($course->id, $event3->courseid);
        $this->assertEquals('\mod_book\event\chapter_viewed', $event4->eventname);
        $this->assertEquals($course2->id, $event4->courseid);

        // Update the timewindow for two of the rules.
        $updaterule = new stdClass();
        $updaterule->id = $rule1->id;
        $updaterule->timewindow = 0;
        \tool_monitor\rule_manager::update_rule($updaterule);
        $updaterule->id = $rule2->id;
        \tool_monitor\rule_manager::update_rule($updaterule);

        // Run the task and check that the events remain as we still have not reached the maximum timewindow.
        $task = new \tool_monitor\task\clean_events();
        $task->execute();

        $this->assertEquals(4, $DB->count_records('tool_monitor_events'));

        // Now, remove the rules associated with course_module_* events so they get deleted.
        \tool_monitor\rule_manager::delete_rule($rule1->id);
        \tool_monitor\rule_manager::delete_rule($rule2->id);
        \tool_monitor\rule_manager::delete_rule($rule3->id);
        \tool_monitor\rule_manager::delete_rule($rule4->id);

        // Run the task and check all the course_module_* events are gone.
        $task = new \tool_monitor\task\clean_events();
        $task->execute();

        // We now should only have the chapter_viewed events.
        $events = $DB->get_records('tool_monitor_events', array(), 'id');
        $this->assertEquals(2, count($events));
        $event1 = array_shift($events);
        $event2 = array_shift($events);
        $this->assertEquals('\mod_book\event\chapter_viewed', $event1->eventname);
        $this->assertEquals($course->id, $event1->courseid);
        $this->assertEquals('\mod_book\event\chapter_viewed', $event2->eventname);
        $this->assertEquals($course2->id, $event2->courseid);

        // Set the timewindow of the rule for the event chapter_viewed in the second course to 0.
        $updaterule->id = $rule5->id;
        \tool_monitor\rule_manager::update_rule($updaterule);

        // Run the task.
        $task = new \tool_monitor\task\clean_events();
        $task->execute();

        // Check that nothing was deleted as we still have a site wide rule for the chapter_viewed event.
        $this->assertEquals(2, $DB->count_records('tool_monitor_events'));

        // Set the timewindow back to 500.
        $updaterule->id = $rule5->id;
        $updaterule->timewindow = 500;
        \tool_monitor\rule_manager::update_rule($updaterule);

        // Set the site rule timewindow to 0.
        $updaterule->id = $rule6->id;
        $updaterule->timewindow = 0;
        \tool_monitor\rule_manager::update_rule($updaterule);

        // Run the task.
        $task = new \tool_monitor\task\clean_events();
        $task->execute();

        // We now should only have one chapter_viewed event for the second course.
        $events = $DB->get_records('tool_monitor_events');
        $this->assertEquals(1, count($events));
        $event1 = array_shift($events);
        $this->assertEquals('\mod_book\event\chapter_viewed', $event1->eventname);
        $this->assertEquals($course2->id, $event1->courseid);

        // Remove the site rule.
        \tool_monitor\rule_manager::delete_rule($rule6->id);

        // Remove the last remaining rule.
        \tool_monitor\rule_manager::delete_rule($rule5->id);

        // Run the task.
        $task = new \tool_monitor\task\clean_events();
        $task->execute();

        // There should be no events left.
        $this->assertEquals(0, $DB->count_records('tool_monitor_events'));
    }
}
