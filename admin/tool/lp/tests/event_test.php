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
 * Event tests.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use tool_lp\api;

/**
 * Event tests.
 *
 * @package    tool_lp
 * @copyright  2016 Serge Gauthier <serge.gauthier.2@umontreal.ca>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_event_testcase extends advanced_testcase {

    /**
     * Test the competency framework created event.
     *
     */
    public function test_competency_framework_created() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        // Use DataGenerator to have a record framework with the right format.
        $record = $lpg->create_framework()->to_record();
        $record->id = 0;
        $record->shortname = "New shortname";
        $record->idnumber = "New idnumber";

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $framework = api::create_framework((object) $record);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_created', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency framework deleted event.
     *
     */
    public function test_competency_framework_deleted() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::delete_framework($framework->get_id());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_deleted', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency framework updated event.
     *
     */
    public function test_competency_framework_updated() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $framework->set_shortname('Shortname modified');
        api::update_framework($framework->to_record());

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_updated', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the competency framework viewed event.
     *
     */
    public function test_competency_framework_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $framework = $lpg->create_framework();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::competency_framework_viewed($framework);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\competency_framework_viewed', $event);
        $this->assertEquals($framework->get_id(), $event->objectid);
        $this->assertEquals($framework->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the template viewed event.
     *
     */
    public function test_template_viewed() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $template = $lpg->create_template();
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        api::template_viewed($template);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\tool_lp\event\template_viewed', $event);
        $this->assertEquals($template->get_id(), $event->objectid);
        $this->assertEquals($template->get_contextid(), $event->contextid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

}