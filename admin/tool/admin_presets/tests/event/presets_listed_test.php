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

namespace tool_admin_presets\event;

/**
 * Tests for the presets_listed event class.
 *
 * @package    tool_admin_presets
 * @category   test
 * @copyright  2021 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \tool_admin_presets\event\presets_listed
 */
class presets_listed_test extends \advanced_testcase {

    /**
     * Test presets_listed event.
     */
    public function test_presets_listed_event(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a preset.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_adminpresets');
        $presetid = $generator->create_preset();

        $params = [
            'context' => \context_system::instance(),
            'objectid' => $presetid,
        ];
        $event = presets_listed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\tool_admin_presets\event\presets_listed', $event);
        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals($presetid, $event->objectid);
    }
}
