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
 * Contains the event tests for the module customcert.
 *
 * @package   mod_customcert
 * @copyright 2023 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert\event;

/**
 * Contains the event tests for the module customcert.
 *
 * @package   mod_customcert
 * @copyright 2023 Mark Nelson <mdjnelson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Tests the events are fired correctly when creating a template.
     *
     * @covers \mod_customcert\template::create
     */
    public function test_creating_a_template(): void {
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\template_created', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
    }

    /**
     * Tests the events are fired correctly when creating a page.
     *
     * @covers \mod_customcert\template::add_page
     */
    public function test_creating_a_page(): void {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);

        $sink = $this->redirectEvents();
        $page = $template->add_page();
        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $pagecreatedevent = array_shift($events);
        $templateupdateevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\page_created', $pagecreatedevent);
        $this->assertEquals($page, $pagecreatedevent->objectid);
        $this->assertEquals(\context_system::instance()->id, $pagecreatedevent->contextid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_customcert\event\template_updated', $templateupdateevent);
        $this->assertEquals($template->get_id(), $templateupdateevent->objectid);
        $this->assertEquals(\context_system::instance()->id, $templateupdateevent->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when moving an item.
     *
     * @covers \mod_customcert\template::move_item
     */
    public function test_moving_item(): void {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();
        $template->add_page();

        $sink = $this->redirectEvents();
        $template->move_item('page', $page1id, 'down');
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $event = reset($events);
        $this->assertInstanceOf('\mod_customcert\event\template_updated', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when updating a template.
     *
     * @covers \mod_customcert\template::save
     */
    public function test_updating_a_template(): void {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);

        // Date we are updating to.
        $data = new \stdClass();
        $data->id = $template->get_id();
        $data->name = 'Test name 2';

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template->save($data);
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\template_updated', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
    }

    /**
     * Tests the events are fired correctly when updating a template with no
     * changes.
     *
     * @covers \mod_customcert\template::save
     */
    public function test_updating_a_template_no_change(): void {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);

        $data = new \stdClass();
        $data->id = $template->get_id();
        $data->name = $template->get_name();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $template->save($data);
        $events = $sink->get_events();

        // Check that no events were triggered.
        $this->assertCount(0, $events);
    }

    /**
     * Tests the events are fired correctly when deleting a template.
     *
     * @covers \mod_customcert\template::delete
     */
    public function test_deleting_a_template(): void {
        global $DB;

        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);

        $data = new \stdClass();
        $data->name = $template->get_name();
        $template->save($data);

        $page1id = $template->add_page();

        // Check the created objects exist in the database as we will check the
        // triggered events correspond to the deletion of these records.
        $templates = $DB->get_records('customcert_templates', ['id' => $template->get_id()]);
        $this->assertEquals(1, count($templates));
        $pages = $DB->get_records('customcert_pages', ['templateid' => $template->get_id()]);
        $this->assertEquals(1, count($pages));

        $sink = $this->redirectEvents();
        $template->delete();
        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $event = array_shift($events);
        $this->assertInstanceOf('\mod_customcert\event\page_deleted', $event);
        $this->assertEquals($page1id, $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
        $this->assertDebuggingNotCalled();

        $event = array_shift($events);
        $this->assertInstanceOf('\mod_customcert\event\template_deleted', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
        $this->assertDebuggingNotCalled();

        // Check the above page_deleted and template_deleted events correspond
        // to actual deletions in the database.
        $templates = $DB->get_records('customcert_templates', ['id' => $template->get_id()]);
        $this->assertEquals(0, count($templates));
        $pages = $DB->get_records('customcert_pages', ['templateid' => $template->get_id()]);
        $this->assertEquals(0, count($pages));
    }

    /**
     * Tests the events are fired correctly when deleting a page.
     *
     * @covers \mod_customcert\template::delete_page
     */
    public function test_deleting_a_page(): void {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        $sink = $this->redirectEvents();
        $template->delete_page($page1id);
        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $pagedeletedevent = array_shift($events);
        $templateupdatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\page_deleted', $pagedeletedevent);
        $this->assertEquals($page1id, $pagedeletedevent->objectid);
        $this->assertEquals(\context_system::instance()->id, $pagedeletedevent->contextid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_customcert\event\template_updated', $templateupdatedevent);
        $this->assertEquals($template->get_id(), $templateupdatedevent->objectid);
        $this->assertEquals(\context_system::instance()->id, $templateupdatedevent->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when saving a page.
     *
     * @covers \mod_customcert\template::save_page
     */
    public function test_updating_a_page() {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $pageid = $template->add_page();

        $width = 'pagewidth_' . $pageid;
        $height = 'pageheight_' . $pageid;
        $leftmargin = 'pageleftmargin_' . $pageid;
        $rightmargin = 'pagerightmargin_' . $pageid;

        $p = new \stdClass();
        $p->tid = $template->get_id();
        $p->$width = 1;
        $p->$height = 1;
        $p->$leftmargin = 1;
        $p->$rightmargin = 1;

        $sink = $this->redirectEvents();
        $template->save_page($p);
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $pageupdatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\page_updated', $pageupdatedevent);
        $this->assertEquals($pageid, $pageupdatedevent->objectid);
        $this->assertEquals(\context_system::instance()->id, $pageupdatedevent->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when saving form elements.
     *
     * @covers \mod_customcert\element::save_form_elements
     */
    public function test_save_form_elements_insert() {
        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        $data = new \stdClass();
        $data->pageid = $page1id;
        $data->name = 'A name';
        $data->element = 'text';
        $data->text = 'Some text';

        $sink = $this->redirectEvents();
        $e = \mod_customcert\element_factory::get_element_instance($data);
        $e->save_form_elements($data);
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\element_created', $event);
        $this->assertEquals($e->get_id(), $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when saving form elements.
     *
     * @covers \mod_customcert\element::save_form_elements
     */
    public function test_save_form_elements_update() {
        global $DB;

        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'Image';
        $elementid = $DB->insert_record('customcert_elements', $element);

        $element = $DB->get_record('customcert_elements', ['id' => $elementid]);

        // Add an element to the page.
        $element = new \customcertelement_text\element($element);

        $data = new \stdClass();
        $data->name = 'A new name';
        $data->text = 'New text';

        $sink = $this->redirectEvents();
        $element->save_form_elements($data);
        $events = $sink->get_events();
        $this->assertCount(1, $events);

        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\element_updated', $event);
        $this->assertEquals($element->get_id(), $event->objectid);
        $this->assertEquals(\context_system::instance()->id, $event->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when copying to a template.
     *
     * @covers \mod_customcert\element::copy_to_template
     */
    public function test_copy_to_template() {
        global $DB;

        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'image';
        $element->element = 'image';
        $element->data = '';
        $element->id = $DB->insert_record('customcert_elements', $element);

        // Add another template.
        $template2 = \mod_customcert\template::create('Test name 2', \context_system::instance()->id);

        $sink = $this->redirectEvents();
        $template->copy_to_template($template2);
        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $pagecreatedevent = array_shift($events);
        $elementcreatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\page_created', $pagecreatedevent);
        $this->assertEquals(\context_system::instance()->id, $pagecreatedevent->contextid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_customcert\event\element_created', $elementcreatedevent);
        $this->assertEquals(\context_system::instance()->id, $elementcreatedevent->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when loading a template into a
     * course-level certificate.
     *
     * @covers \mod_customcert\element::copy_to_template
     */
    public function test_load_template(): void {
        global $DB;

        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'image';
        $element->element = 'image';
        $element->data = '';
        $element->id = $DB->insert_record('customcert_elements', $element);

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('customcert', ['course' => $course->id]);
        $contextid = \context_module::instance($activity->cmid)->id;
        $template2 = \mod_customcert\template::create($activity->name, $contextid);

        $sink = $this->redirectEvents();
        $template->copy_to_template($template2);
        $events = $sink->get_events();
        $this->assertCount(3, $events);

        $pagecreatedevent = array_shift($events);
        $elementcreatedevent = array_shift($events);
        $templateupdatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\page_created', $pagecreatedevent);
        $this->assertEquals($contextid, $pagecreatedevent->contextid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_customcert\event\element_created', $elementcreatedevent);
        $this->assertEquals($contextid, $elementcreatedevent->contextid);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_customcert\event\template_updated', $templateupdatedevent);
        $this->assertEquals($contextid, $templateupdatedevent->contextid);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Tests the events are fired correctly when deleting an element
     *
     * @covers \mod_customcert\template::delete_element
     */
    public function test_deleting_an_element(): void {
        global $DB;

        $template = \mod_customcert\template::create('Test name', \context_system::instance()->id);
        $page1id = $template->add_page();

        // Add an element to the page.
        $element = new \stdClass();
        $element->pageid = $page1id;
        $element->name = 'image';
        $element->element = 'image';
        $element->data = '';
        $element->id = $DB->insert_record('customcert_elements', $element);

        $sink = $this->redirectEvents();
        $template->delete_element($element->id);
        $events = $sink->get_events();
        $this->assertCount(2, $events);

        $elementdeletedevent = array_shift($events);
        $templateupdatedevent = array_shift($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_customcert\event\element_deleted', $elementdeletedevent);
        $this->assertEquals($elementdeletedevent->objectid, $element->id);
        $this->assertEquals($elementdeletedevent->contextid, \context_system::instance()->id);
        $this->assertDebuggingNotCalled();

        $this->assertInstanceOf('\mod_customcert\event\template_updated', $templateupdatedevent);
        $this->assertEquals($templateupdatedevent->objectid, $template->get_id());
        $this->assertEquals($templateupdatedevent->contextid, \context_system::instance()->id);
        $this->assertDebuggingNotCalled();
    }
}
