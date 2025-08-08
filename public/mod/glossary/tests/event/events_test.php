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
 * Unit tests for lib.php
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\event;

/**
 * Unit tests for glossary events.
 *
 * @package   mod_glossary
 * @category  test
 * @copyright 2013 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test comment_created event.
     */
    public function test_comment_created(): void {
        // Create a record for adding comment.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');

        $entry = $glossarygenerator->create_content($glossary);

        $context = \context_module::instance($glossary->cmid);
        $cm = get_coursemodule_from_instance('glossary', $glossary->id, $course->id);
        $cmt = new \stdClass();
        $cmt->component = 'mod_glossary';
        $cmt->context = $context;
        $cmt->course = $course;
        $cmt->cm = $cm;
        $cmt->area = 'glossary_entry';
        $cmt->itemid = $entry->id;
        $cmt->showcount = true;
        $comment = new \core_comment\manager($cmt);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->add('New comment');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\comment_created', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/mod/glossary/view.php', array('id' => $glossary->cmid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test comment_deleted event.
     */
    public function test_comment_deleted(): void {
        // Create a record for deleting comment.
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');

        $entry = $glossarygenerator->create_content($glossary);

        $context = \context_module::instance($glossary->cmid);
        $cm = get_coursemodule_from_instance('glossary', $glossary->id, $course->id);
        $cmt = new \stdClass();
        $cmt->component = 'mod_glossary';
        $cmt->context = $context;
        $cmt->course = $course;
        $cmt->cm = $cm;
        $cmt->area = 'glossary_entry';
        $cmt->itemid = $entry->id;
        $cmt->showcount = true;
        $comment = new \core_comment\manager($cmt);
        $newcomment = $comment->add('New comment 1');

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $comment->delete($newcomment->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\comment_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/mod/glossary/view.php', array('id' => $glossary->cmid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    public function test_course_module_viewed(): void {
        global $DB;
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course->id));

        $dbcourse = $DB->get_record('course', array('id' => $course->id));
        $dbglossary = $DB->get_record('glossary', array('id' => $glossary->id));
        $context = \context_module::instance($glossary->cmid);
        $mode = 'letter';

        $event = \mod_glossary\event\course_module_viewed::create(array(
            'objectid' => $dbglossary->id,
            'context' => $context,
            'other' => array('mode' => $mode)
        ));

        $event->add_record_snapshot('course', $dbcourse);
        $event->add_record_snapshot('glossary', $dbglossary);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\course_module_viewed', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEquals($glossary->id, $event->objectid);
        $this->assertEquals(new \moodle_url('/mod/glossary/view.php', array('id' => $glossary->cmid, 'mode' => $mode)), $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    public function test_course_module_instance_list_viewed(): void {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();

        $event = \mod_glossary\event\course_module_instance_list_viewed::create(array(
            'context' => \context_course::instance($course->id)
        ));

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\course_module_instance_list_viewed', $event);
        $this->assertEquals(CONTEXT_COURSE, $event->contextlevel);
        $this->assertEquals($course->id, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_entry_created(): void {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $context = \context_module::instance($glossary->cmid);

        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry = $glossarygenerator->create_content($glossary);

        $eventparams = array(
            'context' => $context,
            'objectid' => $entry->id,
            'other' => array('concept' => $entry->concept)
        );
        $event = \mod_glossary\event\entry_created::create($eventparams);
        $event->add_record_snapshot('glossary_entries', $entry);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\entry_created', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_entry_updated(): void {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $context = \context_module::instance($glossary->cmid);

        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry = $glossarygenerator->create_content($glossary);

        $eventparams = array(
            'context' => $context,
            'objectid' => $entry->id,
            'other' => array('concept' => $entry->concept)
        );
        $event = \mod_glossary\event\entry_updated::create($eventparams);
        $event->add_record_snapshot('glossary_entries', $entry);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\entry_updated', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_entry_deleted(): void {
        global $DB;
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $context = \context_module::instance($glossary->cmid);
        $prevmode = 'view';
        $hook = 'ALL';

        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry = $glossarygenerator->create_content($glossary);

        $DB->delete_records('glossary_entries', array('id' => $entry->id));

        $eventparams = array(
            'context' => $context,
            'objectid' => $entry->id,
            'other' => array(
                'mode' => $prevmode,
                'hook' => $hook,
                'concept' => $entry->concept
            )
        );
        $event = \mod_glossary\event\entry_deleted::create($eventparams);
        $event->add_record_snapshot('glossary_entries', $entry);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\entry_deleted', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_category_created(): void {
        global $DB;
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $context = \context_module::instance($glossary->cmid);

        // Create category and trigger event.
        $category = new \stdClass();
        $category->name = 'New category';
        $category->usedynalink = 0;
        $category->id = $DB->insert_record('glossary_categories', $category);

        $event = \mod_glossary\event\category_created::create(array(
            'context' => $context,
            'objectid' => $category->id
        ));

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\category_created', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);

        // Update category and trigger event.
        $category->name = 'Updated category';
        $DB->update_record('glossary_categories', $category);

        $event = \mod_glossary\event\category_updated::create(array(
            'context' => $context,
            'objectid' => $category->id
        ));

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\category_updated', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);


        // Delete category and trigger event.
        $category = $DB->get_record('glossary_categories', array('id' => $category->id));
        $DB->delete_records('glossary_categories', array('id' => $category->id));

        $event = \mod_glossary\event\category_deleted::create(array(
            'context' => $context,
            'objectid' => $category->id
        ));
        $event->add_record_snapshot('glossary_categories', $category);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\category_deleted', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_entry_approved(): void {
        global $DB;
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_user();
        $rolestudent = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $rolestudent->id);
        $teacher = $this->getDataGenerator()->create_user();
        $roleteacher = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $roleteacher->id);

        $this->setUser($teacher);
        $glossary = $this->getDataGenerator()->create_module('glossary',
                array('course' => $course, 'defaultapproval' => 0));
        $context = \context_module::instance($glossary->cmid);

        $this->setUser($student);
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry = $glossarygenerator->create_content($glossary);
        $this->assertEquals(0, $entry->approved);

        // Approve entry, trigger and validate event.
        $this->setUser($teacher);
        $newentry = new \stdClass();
        $newentry->id           = $entry->id;
        $newentry->approved     = true;
        $newentry->timemodified = time();
        $DB->update_record("glossary_entries", $newentry);
        $params = array(
            'context' => $context,
            'objectid' => $entry->id
        );
        $event = \mod_glossary\event\entry_approved::create($params);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\entry_approved', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);


        // Disapprove entry, trigger and validate event.
        $this->setUser($teacher);
        $newentry = new \stdClass();
        $newentry->id           = $entry->id;
        $newentry->approved     = false;
        $newentry->timemodified = time();
        $DB->update_record("glossary_entries", $newentry);
        $params = array(
            'context' => $context,
            'objectid' => $entry->id
        );
        $event = \mod_glossary\event\entry_disapproved::create($params);

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\entry_disapproved', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_entry_viewed(): void {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary', array('course' => $course));
        $context = \context_module::instance($glossary->cmid);

        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $entry = $glossarygenerator->create_content($glossary);

        $event = \mod_glossary\event\entry_viewed::create(array(
            'objectid' => $entry->id,
            'context' => $context
        ));

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_glossary\event\entry_viewed', $event);
        $this->assertEquals(CONTEXT_MODULE, $event->contextlevel);
        $this->assertEquals($glossary->cmid, $event->contextinstanceid);
        $this->assertEventContextNotUsed($event);
    }
}
