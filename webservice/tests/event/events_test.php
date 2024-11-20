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
 * Unit tests for Web service events.
 *
 * @package    webservice
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_webservice\event;

/**
 * Unit tests for Web service events.
 *
 * @package    webservice
 * @category   phpunit
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_function_called(): void {
        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        $params = array(
            'other' => array(
                'function' => 'A function'
            )
        );
        $event = \core\event\webservice_function_called::create($params);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals('A function', $event->other['function']);
        $this->assertEventContextNotUsed($event);
    }

    public function test_login_failed(): void {
        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        $params = array(
            'other' => array(
                'reason' => 'Unit Test',
                'method' => 'Some method',
                'tokenid' => '123'
            )
        );
        $event = \core\event\webservice_login_failed::create($params);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals($params['other']['reason'], $event->other['reason']);
        $this->assertEquals($params['other']['method'], $event->other['method']);
        $this->assertEquals($params['other']['tokenid'], $event->other['tokenid']);

        // We cannot set the token in the other properties.
        $params['other']['token'] = 'I should not be set';
        try {
            $event = \core\event\webservice_login_failed::create($params);
            $this->fail('The token cannot be allowed in \core\event\webservice_login_failed');
        } catch (\coding_exception $e) {
        }
        $this->assertEventContextNotUsed($event);
    }

    public function test_service_created(): void {
        global $CFG, $DB;

        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        // Creating a fake service.
        $service = (object) array(
            'name' => 'Test',
            'enabled' => 1,
            'requiredcapability' => '',
            'restrictedusers' => 0,
            'component' => null,
            'timecreated' => time(),
            'timemodified' => time(),
            'shortname' => null,
            'downloadfiles' => 0,
            'uploadfiles' => 0
        );
        $service->id = $DB->insert_record('external_services', $service);

        // Trigger the event.
        $params = array(
            'objectid' => $service->id,
        );
        $event = \core\event\webservice_service_created::create($params);
        $event->add_record_snapshot('external_services', $service);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Assert that the event contains the right information.
        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals($service->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_service_updated(): void {
        global $CFG, $DB;

        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        // Creating a fake service.
        $service = (object) array(
            'name' => 'Test',
            'enabled' => 1,
            'requiredcapability' => '',
            'restrictedusers' => 0,
            'component' => null,
            'timecreated' => time(),
            'timemodified' => time(),
            'shortname' => null,
            'downloadfiles' => 0,
            'uploadfiles' => 0
        );
        $service->id = $DB->insert_record('external_services', $service);

        // Trigger the event.
        $params = array(
            'objectid' => $service->id,
        );
        $event = \core\event\webservice_service_updated::create($params);
        $event->add_record_snapshot('external_services', $service);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Assert that the event contains the right information.
        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals($service->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_service_deleted(): void {
        global $CFG, $DB;

        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        // Creating a fake service.
        $service = (object) array(
            'name' => 'Test',
            'enabled' => 1,
            'requiredcapability' => '',
            'restrictedusers' => 0,
            'component' => null,
            'timecreated' => time(),
            'timemodified' => time(),
            'shortname' => null,
            'downloadfiles' => 0,
            'uploadfiles' => 0
        );
        $service->id = $DB->insert_record('external_services', $service);

        // Trigger the event.
        $params = array(
            'objectid' => $service->id,
        );
        $event = \core\event\webservice_service_deleted::create($params);
        $event->add_record_snapshot('external_services', $service);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Assert that the event contains the right information.
        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals($service->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_service_user_added(): void {
        global $CFG;

        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2
        );
        $event = \core\event\webservice_service_user_added::create($params);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals(1, $event->objectid);
        $this->assertEquals(2, $event->relateduserid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_service_user_removed(): void {
        global $CFG;

        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2
        );
        $event = \core\event\webservice_service_user_removed::create($params);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals(1, $event->objectid);
        $this->assertEquals(2, $event->relateduserid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_token_created(): void {
        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'other' => array(
                'auto' => true
            )
        );
        $event = \core\event\webservice_token_created::create($params);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals(1, $event->objectid);
        $this->assertEquals(2, $event->relateduserid);
        $this->assertEventContextNotUsed($event);
    }

    public function test_token_sent(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // The Web service API doesn't allow the testing of the events directly by
        // calling some functions which trigger the events, so what we are going here
        // is just checking that the event returns the expected information.

        $sink = $this->redirectEvents();

        $params = array(
            'objectid' => 1,
            'other' => array(
                'auto' => true
            )
        );
        $event = \core\event\webservice_token_sent::create($params);
        $event->trigger();

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEquals(1, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }
}
