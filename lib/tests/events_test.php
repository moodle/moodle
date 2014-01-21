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
 * @package   core
 * @category  test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class core_events_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the course category created event.
     */
    public function test_course_category_created() {
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $category = $this->getDataGenerator()->create_category();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_created', $event);
        $this->assertEquals(context_coursecat::instance($category->id), $event->get_context());
        $expected = array(SITEID, 'category', 'add', 'editcategory.php?id=' . $category->id, $category->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the course category updated event.
     */
    public function test_course_category_updated() {
        // Create a category.
        $category = $this->getDataGenerator()->create_category();

        // Create some data we are going to use to update this category.
        $data = new stdClass();
        $data->name = 'Category name change';

        // Trigger and capture the event for updating a category.
        $sink = $this->redirectEvents();
        $category->update($data);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category->id), $event->get_context());
        $expected = array(SITEID, 'category', 'update', 'editcategory.php?id=' . $category->id, $category->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Create another category and a child category.
        $category2 = $this->getDataGenerator()->create_category();
        $childcat = $this->getDataGenerator()->create_category(array('parent' => $category2->id));

        // Trigger and capture the event for changing the parent of a category.
        $sink = $this->redirectEvents();
        $childcat->change_parent($category);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($childcat->id), $event->get_context());
        $expected = array(SITEID, 'category', 'move', 'editcategory.php?id=' . $childcat->id, $childcat->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for changing the sortorder of a category.
        $sink = $this->redirectEvents();
        $category2->change_sortorder_by_one(true);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category2->id), $event->get_context());
        $expected = array(SITEID, 'category', 'move', 'management.php?categoryid=' . $category2->id, $category2->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for deleting a category and moving it's children to another.
        $sink = $this->redirectEvents();
        $category->delete_move($category->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($childcat->id), $event->get_context());
        $expected = array(SITEID, 'category', 'move', 'editcategory.php?id=' . $childcat->id, $childcat->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for hiding a category.
        $sink = $this->redirectEvents();
        $category2->hide();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category2->id), $event->get_context());
        $expected = array(SITEID, 'category', 'hide', 'editcategory.php?id=' . $category2->id, $category2->id);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for unhiding a category.
        $sink = $this->redirectEvents();
        $category2->show();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_category_updated', $event);
        $this->assertEquals(context_coursecat::instance($category2->id), $event->get_context());
        $expected = array(SITEID, 'category', 'show', 'editcategory.php?id=' . $category2->id, $category2->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the email failed event.
     *
     * It's not possible to use the moodle API to simulate the failure of sending
     * an email, so here we simply create the event and trigger it.
     */
    public function test_email_failed() {
        // Trigger event for failing to send email.
        $event = \core\event\email_failed::create(array(
            'context' => context_system::instance(),
            'userid' => 1,
            'relateduserid' => 2,
            'other' => array(
                'subject' => 'This is a subject',
                'message' => 'This is a message',
                'errorinfo' => 'The email failed to send!'
            )
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\email_failed', $event);
        $this->assertEquals(context_system::instance(), $event->get_context());
        $expected = array(SITEID, 'library', 'mailer', qualified_me(), 'ERROR: The email failed to send!');
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }
}
