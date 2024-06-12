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

namespace mod_lti\event;

/**
 * Unknown service API called event tests
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unknown_service_api_called_test extends \advanced_testcase {
    /*
     * Ensure create event works.
     */
    public function test_create_event(): void {
        $event = unknown_service_api_called::create();
        $this->assertInstanceOf('\mod_lti\event\unknown_service_api_called', $event);
    }

    /*
     * Ensure event context works.
     */
    public function test_event_context(): void {
        $event = unknown_service_api_called::create();
        $this->assertEquals(\context_system::instance(), $event->get_context());
    }

    /*
     * Ensure we can trigger the event.
     */
    public function test_trigger_event(): void {
        $event = unknown_service_api_called::create();

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
    }

    /*
     * Ensure get/set message data is functioning as expected.
     */
    public function test_get_message_data(): void {
        $data = (object) array(
            'foo' => 'bar',
            'bat' => 'baz',
        );

        /*
         * @var unknown_service_api_called $event
         */
        $event = unknown_service_api_called::create();
        $event->set_message_data($data);
        $this->assertSame($data, $event->get_message_data());
    }
}
