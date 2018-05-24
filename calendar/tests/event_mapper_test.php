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
 * Event mapper test.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/calendar/lib.php');

use core_calendar\local\event\mappers\event_mapper;
use core_calendar\local\event\value_objects\action;
use core_calendar\local\event\value_objects\event_description;
use core_calendar\local\event\value_objects\event_times;
use core_calendar\local\event\factories\action_factory_interface;
use core_calendar\local\event\entities\event_collection_interface;
use core_calendar\local\event\factories\event_factory_interface;
use core_calendar\local\event\entities\event_interface;
use core_calendar\local\event\entities\action_event_interface;
use core_calendar\local\event\proxies\proxy_interface;

/**
 * Event mapper testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_mapper_testcase extends advanced_testcase {
    /**
     * Test legacy event -> event.
     */
    public function test_from_legacy_event_to_event() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $legacyevent = $this->create_event();
        $mapper = new event_mapper(
            new event_mapper_test_event_factory()
        );
        $event = $mapper->from_legacy_event_to_event($legacyevent);
        $this->assertInstanceOf(event_interface::class, $event);
    }

    /**
     * Test event -> legacy event.
     */
    public function test_from_event_to_legacy_event() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $legacyevent = $this->create_event(['modname' => 'assign', 'instance' => 1]);
        $event = new event_mapper_test_event($legacyevent);
        $mapper = new event_mapper(
            new event_mapper_test_event_factory()
        );
        $legacyevent = $mapper->from_event_to_legacy_event($event);
        $this->assertInstanceOf(calendar_event::class, $legacyevent);
    }

    /**
     * Test event -> stdClass.
     */
    public function test_from_event_to_stdclass() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $legacyevent = $this->create_event(['modname' => 'assign', 'instance' => 1]);
        $event = new event_mapper_test_event($legacyevent);
        $mapper = new event_mapper(
            new event_mapper_test_event_factory()
        );
        $obj = $mapper->from_event_to_stdclass($event);
        $this->assertInstanceOf(\stdClass::class, $obj);
        $this->assertEquals($obj->name, $event->get_name());
        $this->assertEquals($obj->eventtype, $event->get_type());
        $this->assertEquals($obj->timestart, $event->get_times()->get_start_time()->getTimestamp());
    }

    /**
     * Test event -> array.
     */
    public function test_from_event_to_assoc_array() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $legacyevent = $this->create_event(['modname' => 'assign', 'instance' => 1]);
        $event = new event_mapper_test_event($legacyevent);
        $mapper = new event_mapper(
            new event_mapper_test_event_factory()
        );
        $arr = $mapper->from_event_to_assoc_array($event);
        $this->assertTrue(is_array($arr));
        $this->assertEquals($arr['name'], $event->get_name());
        $this->assertEquals($arr['eventtype'], $event->get_type());
        $this->assertEquals($arr['timestart'], $event->get_times()->get_start_time()->getTimestamp());
    }

    /**
     * Test for action event -> legacy event.
     */
    public function test_from_action_event_to_legacy_event() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $legacyevent = $this->create_event(['modname' => 'assign', 'instance' => 1]);
        $event = new event_mapper_test_action_event(
            new event_mapper_test_event($legacyevent)
        );
        $mapper = new event_mapper(
            new event_mapper_test_event_factory()
        );
        $legacyevent = $mapper->from_event_to_legacy_event($event);

        $this->assertInstanceOf(calendar_event::class, $legacyevent);
        $this->assertEquals($legacyevent->actionname, 'test action');
        $this->assertInstanceOf(\moodle_url::class, $legacyevent->actionurl);
        $this->assertEquals($legacyevent->actionnum, 1729);
        $this->assertEquals($legacyevent->actionactionable, $event->get_action()->is_actionable());
    }

    /**
     * Helper function to create calendar events using the old code.
     *
     * @param array $properties A list of calendar event properties to set
     * @return calendar_event
     */
    protected function create_event($properties = []) {
        $record = new \stdClass();
        $record->name = 'event name';
        $record->eventtype = 'global';
        $record->timestart = time();
        $record->timeduration = 0;
        $record->timesort = 0;
        $record->type = 1;
        $record->courseid = 0;
        $record->categoryid = 0;

        foreach ($properties as $name => $value) {
            $record->$name = $value;
        }

        $event = new calendar_event($record);
        return $event->create($record, false);
    }
}

/**
 * A test event factory.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_mapper_test_event_factory implements event_factory_interface {

    public function create_instance(\stdClass $dbrow) {
        return new event_mapper_test_event();
    }
}

/**
 * A test action event
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_mapper_test_action_event implements action_event_interface {
    /**
     * @var event_interface $event The event to delegate to.
     */
    protected $event;

    /**
     * event_mapper_test_action_event constructor.
     * @param event_interface $event
     */
    public function __construct(event_interface $event) {
        $this->event = $event;
    }

    public function get_id() {
        return $this->event->get_id();
    }

    public function get_name() {
        return $this->event->get_name();
    }

    public function get_description() {
        return $this->event->get_description();
    }

    public function get_location() {
        return $this->event->get_location();
    }

    public function get_category() {
        return $this->event->get_category();
    }

    public function get_course() {
        return $this->event->get_course();
    }

    public function get_course_module() {
        return $this->event->get_course_module();
    }

    public function get_group() {
        return $this->event->get_group();
    }

    public function get_user() {
        return $this->event->get_user();
    }

    public function get_type() {
        return $this->event->get_type();
    }

    public function get_times() {
        return $this->event->get_times();
    }

    public function get_repeats() {
        return $this->event->get_repeats();
    }

    public function get_subscription() {
        return $this->event->get_subscription();
    }

    public function is_visible() {
        return $this->event->is_visible();
    }

    public function get_action() {
        return new action(
            'test action',
            new \moodle_url('http://example.com'),
            1729,
            true
        );
    }
}

/**
 * A test event.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_mapper_test_event implements event_interface {
    /**
     * @var proxy_interface $categoryproxy Category proxy.
     */
    protected $categoryproxy;

    /**
     * @var proxy_interface $courseproxy Course proxy.
     */
    protected $courseproxy;

    /**
     * @var proxy_interface $cmproxy Course module proxy.
     */
    protected $cmproxy;

    /**
     * @var proxy_interface $groupproxy Group proxy.
     */
    protected $groupproxy;

    /**
     * @var proxy_interface $userproxy User proxy.
     */
    protected $userproxy;

    /**
     * @var proxy_interface $subscriptionproxy Subscription proxy.
     */
    protected $subscriptionproxy;

    /**
     * Constructor.
     *
     * @param calendar_event $legacyevent Legacy event to extract IDs etc from.
     */
    public function __construct($legacyevent = null) {
        if ($legacyevent) {
            $this->courseproxy = new event_mapper_test_proxy($legacyevent->courseid);
            $this->cmproxy = new event_mapper_test_proxy(1729,
                    [
                        'modname' => $legacyevent->modname,
                        'instance' => $legacyevent->instance
                    ]
            );
            $this->groupproxy = new event_mapper_test_proxy(0);
            $this->userproxy = new event_mapper_test_proxy($legacyevent->userid);
            $this->subscriptionproxy = new event_mapper_test_proxy(null);
        }
    }

    public function get_id() {
        return 1729;
    }

    public function get_name() {
        return 'Jeff';
    }

    public function get_description() {
        return new event_description('asdf', 1);
    }

    public function get_location() {
        return 'Cube office';
    }

    public function get_category() {
        return $this->categoryproxy;
    }

    public function get_course() {
        return $this->courseproxy;
    }

    public function get_course_module() {
        return $this->cmproxy;
    }

    public function get_group() {
        return $this->groupproxy;
    }

    public function get_user() {
        return $this->userproxy;
    }

    public function get_type() {
        return 'asdf';
    }

    public function get_times() {
        return new event_times(
            (new \DateTimeImmutable())->setTimestamp(-386380800),
            (new \DateTimeImmutable())->setTimestamp(115776000),
            (new \DateTimeImmutable())->setTimestamp(115776000),
            (new \DateTimeImmutable())->setTimestamp(time())
        );
    }

    public function get_repeats() {
        return new core_calendar_event_mapper_test_event_collection();
    }

    public function get_subscription() {
        return $this->subscriptionproxy;
    }

    public function is_visible() {
        return true;
    }
}

/**
 * A test proxy.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_mapper_test_proxy implements proxy_interface {
    /**
     * @var int $id Proxied ID.
     */
    protected $id;

    /**
     * @var array $params Params to proxy.
     */
    protected $params;

    /**
     * Constructor.
     *
     * @param int   $id Proxied ID.
     * @param array $params Params to proxy.
     */
    public function __construct($id, $params = []) {
        $this->params = $params;
    }

    public function get($member) {
        if ($member === 'id') {
            return $this->id;
        }
        return isset($params[$member]) ? $params[$member] : null;
    }

    public function get_proxied_instance() {
    }
}

/**
 * A test event.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_event_mapper_test_event_collection implements event_collection_interface {
    /**
     * @var array $events Array of events.
     */
    protected $events;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->events = [
            'not really an event hahaha',
            'also not really. gottem.'
        ];
    }

    public function get_id() {
        return 1729;
    }

    public function get_num() {
        return 2;
    }

    public function getIterator() {
        foreach ($this->events as $event) {
            yield $event;
        }
    }
}
