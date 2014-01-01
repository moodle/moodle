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
 * @package core_tag
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/tag/lib.php');

class core_tag_events_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the tag updated event.
     */
    public function test_tag_updated() {
        $this->setAdminUser();

        // Save the system context.
        $systemcontext = context_system::instance();

        // Create a tag we are going to update.
        $tag = $this->getDataGenerator()->create_tag();

        // Store the name before we change it.
        $oldname = $tag->name;

        // Trigger and capture the event when renaming a tag.
        $sink = $this->redirectEvents();
        tag_rename($tag->id, 'newname');
        // Update the tag's name since we have renamed it.
        $tag->name = 'newname';
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\tag_updated', $event);
        $this->assertEquals($systemcontext, $event->get_context());
        $expected = array(SITEID, 'tag', 'update', 'index.php?id=' . $tag->id, $oldname . '->'. $tag->name);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event when setting the type of a tag.
        $sink = $this->redirectEvents();
        tag_type_set($tag->id, 'official');
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\tag_updated', $event);
        $this->assertEquals($systemcontext, $event->get_context());
        $expected = array(0, 'tag', 'update', 'index.php?id=' . $tag->id, $tag->name);
        $this->assertEventLegacyLogData($expected, $event);

        // Trigger and capture the event for setting the description of a tag.
        $sink = $this->redirectEvents();
        tag_description_set($tag->id, 'description', FORMAT_MOODLE);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\tag_updated', $event);
        $this->assertEquals($systemcontext, $event->get_context());
        $expected = array(0, 'tag', 'update', 'index.php?id=' . $tag->id, $tag->name);
        $this->assertEventLegacyLogData($expected, $event);
    }
}
