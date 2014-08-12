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
 * Unknown service API called event tests
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_lti\event\unknown_service_api_called;

/**
 * Unknown service API called event tests
 *
 * @package    mod_lti
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_lti_event_unknown_service_api_called_test extends advanced_testcase {
    public function test_create_event() {
        $event = unknown_service_api_called::create();
        $this->assertInstanceOf('\mod_lti\event\unknown_service_api_called', $event);
    }

    public function test_event_context() {
        $event = unknown_service_api_called::create();
        $this->assertEquals(context_system::instance(), $event->get_context());
    }

    public function test_trigger_event() {
        $event = unknown_service_api_called::create();

        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
    }

    public function test_get_message_data() {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<foo>
    <bar>baz</bar>
</foo>
XML;

        /** @var unknown_service_api_called $event */
        $event = unknown_service_api_called::create(
            array('other' => array('rawbody' => $xml, 'foo' => 'bar'))
        );
        $data = $event->get_message_data();

        $this->assertInstanceOf('stdClass', $data);
        $this->assertCount(3, get_object_vars($data));
        $this->assertEquals('bar', $data->foo);
        $this->assertEquals($xml, $data->rawbody);
        $this->assertInstanceOf('SimpleXMLElement', $data->xml);
        $this->assertXmlStringEqualsXmlString($xml, $data->xml->asXML());
    }
}