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
 * Tests the events related to the user profile fields and categories.
 *
 * @package   core
 * @category  test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/user/profile/definelib.php');

/**
 * Tests the events related to the user profile fields and categories.
 *
 * @package   core
 * @category  test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_event_profile_field_testcase extends advanced_testcase {

    /**
     * Test set up.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test that triggering the user_info_category_created event works as expected.
     */
    public function test_user_info_category_created_event() {
        global $DB;

        // Create a new profile category.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        // Trigger the event.
        $sink = $this->redirectEvents();
        \core\event\user_info_category_created::create_from_category($cat1)->trigger();
        $events = $sink->get_events();
        $sink->close();

        // Confirm we got the right number of events.
        $this->assertCount(1, $events);

        // Validate that the event was correctly triggered.
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_info_category_created', $event);
        $this->assertEquals($event->objectid, $cat1->id);
        $this->assertEquals($event->other['name'], $cat1->name);
    }

    /**
     * Test that moving a user info category triggers an updated event.
     */
    public function test_user_info_category_updated_event() {
        global $DB;

        // Create new profile categories.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        $cat2 = new stdClass();
        $cat2->name = 'Example category 2';
        $cat2->sortorder = $DB->count_records('user_info_category') + 1;
        $cat2->id = $DB->insert_record('user_info_category', $cat2);

        // Trigger the events.
        $sink = $this->redirectEvents();
        profile_move_category($cat1->id, 'down');
        $events = $sink->get_events();
        $sink->close();

        // Should now have two events.
        $this->assertCount(2, $events);
        $event1 = array_shift($events);
        $event2 = array_shift($events);

        // Validate that the events were correctly triggered.
        $this->assertInstanceOf('\core\event\user_info_category_updated', $event1);
        $this->assertEquals($event1->objectid, $cat1->id);
        $this->assertEquals($event1->other['name'], $cat1->name);

        $this->assertInstanceOf('\core\event\user_info_category_updated', $event2);
        $this->assertEquals($event2->objectid, $cat2->id);
        $this->assertEquals($event2->other['name'], $cat2->name);
    }

    /**
     * Test that deleting a user info category triggers a delete event.
     */
    public function test_user_info_category_deleted_event() {
        global $DB;

        // Create new profile categories.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        $cat2 = new stdClass();
        $cat2->name = 'Example category 2';
        $cat2->sortorder = $DB->count_records('user_info_category') + 1;
        $cat2->id = $DB->insert_record('user_info_category', $cat2);

        // Trigger the event.
        $sink = $this->redirectEvents();
        profile_delete_category($cat2->id);
        $events = $sink->get_events();
        $sink->close();

        // Confirm we got the right number of events.
        $this->assertCount(1, $events);

        // Validate that the event was correctly triggered.
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_info_category_deleted', $event);
        $this->assertEquals($event->objectid, $cat2->id);
        $this->assertEquals($event->other['name'], $cat2->name);
    }

    /**
     * Test that creating a user info field triggers a create event.
     */
    public function test_user_info_field_created_event() {
        global $DB;

        // Create a new profile category.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        // Create a new profile field.
        $data = new stdClass();
        $data->datatype = 'text';
        $data->shortname = 'example';
        $data->name = 'Example field';
        $data->description = 'Hello this is an example.';
        $data->required = false;
        $data->locked = false;
        $data->forceunique = false;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = $cat1->id;

        // Trigger the event.
        $sink = $this->redirectEvents();
        $field = new profile_define_base();
        $field->define_save($data);
        $events = $sink->get_events();
        $sink->close();

        // Get the field that was created.
        $field = $DB->get_record('user_info_field', array('shortname' => $data->shortname));

        // Confirm we got the right number of events.
        $this->assertCount(1, $events);

        // Validate that the event was correctly triggered.
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_info_field_created', $event);
        $this->assertEquals($event->objectid, $field->id);
        $this->assertEquals($event->other['shortname'], $field->shortname);
        $this->assertEquals($event->other['name'], $field->name);
        $this->assertEquals($event->other['datatype'], $field->datatype);
    }

    /**
     * Test that updating a user info field triggers an update event.
     */
    public function test_user_info_field_updated_event() {
        global $DB;

        // Create a new profile category.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        // Create a new profile field.
        $data = new stdClass();
        $data->datatype = 'text';
        $data->shortname = 'example';
        $data->name = 'Example field';
        $data->description = 'Hello this is an example.';
        $data->required = false;
        $data->locked = false;
        $data->forceunique = false;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = $cat1->id;
        $data->id = $DB->insert_record('user_info_field', $data);

        // Trigger the event.
        $sink = $this->redirectEvents();
        $field = new profile_define_base();
        $field->define_save($data);
        $events = $sink->get_events();
        $sink->close();

        // Confirm we got the right number of events.
        $this->assertCount(1, $events);

        // Validate that the event was correctly triggered.
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_info_field_updated', $event);
        $this->assertEquals($event->objectid, $data->id);
        $this->assertEquals($event->other['shortname'], $data->shortname);
        $this->assertEquals($event->other['name'], $data->name);
        $this->assertEquals($event->other['datatype'], $data->datatype);
    }

    /**
     * Test that moving a field triggers update events.
     */
    public function test_user_info_field_updated_event_move_field() {
        global $DB;

        // Create a new profile category.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        // Create a new profile field.
        $field1 = new stdClass();
        $field1->datatype = 'text';
        $field1->shortname = 'example';
        $field1->name = 'Example field';
        $field1->description = 'Hello this is an example.';
        $field1->required = false;
        $field1->locked = false;
        $field1->forceunique = false;
        $field1->signup = false;
        $field1->visible = '0';
        $field1->categoryid = $cat1->id;
        $field1->sortorder = $DB->count_records('user_info_field') + 1;
        $field1->id = $DB->insert_record('user_info_field', $field1);

        // Create another that we will be moving.
        $field2 = clone $field1;
        $field2->datatype = 'text';
        $field2->shortname = 'example2';
        $field2->name = 'Example field 2';
        $field2->sortorder = $DB->count_records('user_info_field') + 1;
        $field2->id = $DB->insert_record('user_info_field', $field2);

        // Trigger the events.
        $sink = $this->redirectEvents();
        profile_move_field($field2->id, 'up');
        $events = $sink->get_events();
        $sink->close();

        // Should now have two events.
        $this->assertCount(2, $events);
        $event1 = array_shift($events);
        $event2 = array_shift($events);

        // Validate that the events were correctly triggered.
        $this->assertInstanceOf('\core\event\user_info_field_updated', $event1);
        $this->assertEquals($event1->objectid, $field2->id);
        $this->assertEquals($event1->other['shortname'], $field2->shortname);
        $this->assertEquals($event1->other['name'], $field2->name);
        $this->assertEquals($event1->other['datatype'], $field2->datatype);

        $this->assertInstanceOf('\core\event\user_info_field_updated', $event2);
        $this->assertEquals($event2->objectid, $field1->id);
        $this->assertEquals($event2->other['shortname'], $field1->shortname);
        $this->assertEquals($event2->other['name'], $field1->name);
        $this->assertEquals($event2->other['datatype'], $field1->datatype);
    }

    /**
     * Test that when we delete a category that contains a field, that the field being moved to
     * another category triggers an update event.
     */
    public function test_user_info_field_updated_event_delete_category() {
        global $DB;

        // Create a new profile category.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        $cat2 = new stdClass();
        $cat2->name = 'Example category';
        $cat2->sortorder = $DB->count_records('user_info_category') + 1;
        $cat2->id = $DB->insert_record('user_info_category', $cat2);

        // Create a new profile field.
        $field = new stdClass();
        $field->datatype = 'text';
        $field->shortname = 'example';
        $field->name = 'Example field';
        $field->description = 'Hello this is an example.';
        $field->required = false;
        $field->locked = false;
        $field->forceunique = false;
        $field->signup = false;
        $field->visible = '0';
        $field->categoryid = $cat1->id;
        $field->id = $DB->insert_record('user_info_field', $field);

        // Trigger the event.
        $sink = $this->redirectEvents();
        profile_delete_category($cat1->id);
        $events = $sink->get_events();
        $sink->close();

        // Check we got the right number of events.
        $this->assertCount(2, $events);

        // Validate that the event was correctly triggered.
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_info_field_updated', $event);
        $this->assertEquals($event->objectid, $field->id);
        $this->assertEquals($event->other['shortname'], $field->shortname);
        $this->assertEquals($event->other['name'], $field->name);
        $this->assertEquals($event->other['datatype'], $field->datatype);
    }

    /**
     * Test that deleting a user info field triggers a delete event.
     */
    public function test_user_info_field_deleted_event() {
        global $DB;

        // Create a new profile category.
        $cat1 = new stdClass();
        $cat1->name = 'Example category';
        $cat1->sortorder = $DB->count_records('user_info_category') + 1;
        $cat1->id = $DB->insert_record('user_info_category', $cat1);

        // Create a new profile field.
        $data = new stdClass();
        $data->datatype = 'text';
        $data->shortname = 'delete';
        $data->name = 'Example field for delete';
        $data->description = 'Hello this is an example.';
        $data->required = false;
        $data->locked = false;
        $data->forceunique = false;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = $cat1->id;
        $data->id = $DB->insert_record('user_info_field', $data, true);

        // Trigger the event.
        $sink = $this->redirectEvents();
        profile_delete_field($data->id);
        $events = $sink->get_events();
        $sink->close();

        // Confirm we got the right number of events.
        $this->assertCount(1, $events);

        // Validate that the event was correctly triggered.
        $event = reset($events);
        $this->assertInstanceOf('\core\event\user_info_field_deleted', $event);
        $this->assertEquals($event->objectid, $data->id);
        $this->assertEquals($event->other['shortname'], $data->shortname);
        $this->assertEquals($event->other['name'], $data->name);
        $this->assertEquals($event->other['datatype'], $data->datatype);
    }
}
