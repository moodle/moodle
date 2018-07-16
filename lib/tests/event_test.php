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
 * Tests for event manager, base event and observers.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/event_fixtures.php');

class core_event_testcase extends advanced_testcase {

    const DEBUGGING_MSG = 'Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.';

    public function test_event_properties() {
        global $USER;

        $system = \context_system::instance();
        $event = \core_tests\event\unittest_executed::create(array('context'=>$system, 'objectid'=>5, 'other'=>array('sample'=>null, 'xx'=>10)));

        $this->assertSame('\core_tests\event\unittest_executed', $event->eventname);
        $this->assertSame('core_tests', $event->component);
        $this->assertSame('executed', $event->action);
        $this->assertSame('unittest', $event->target);
        $this->assertSame(5, $event->objectid);
        $this->assertSame('u', $event->crud);
        $this->assertSame(\core\event\base::LEVEL_PARTICIPATING, $event->edulevel);

        $this->assertEquals($system, $event->get_context());
        $this->assertSame($system->id, $event->contextid);
        $this->assertSame($system->contextlevel, $event->contextlevel);
        $this->assertSame($system->instanceid, $event->contextinstanceid);

        $this->assertSame($USER->id, $event->userid);
        $this->assertSame(0, $event->courseid);

        $this->assertNull($event->relateduserid);
        $this->assertFalse(isset($event->relateduserid));

        $this->assertSame(0, $event->anonymous);

        $this->assertSame(array('sample'=>null, 'xx'=>10), $event->other);
        $this->assertTrue(isset($event->other['xx']));
        $this->assertFalse(isset($event->other['sample']));

        $this->assertLessThanOrEqual(time(), $event->timecreated);

        try {
            $event->courseid = 2;
            $this->fail('Exception expected on event modification');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            $event->xxxx = 1;
            $this->fail('Exception expected on event modification');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $event2 = \core_tests\event\unittest_executed::create(array('contextid'=>$system->id, 'objectid'=>5, 'anonymous'=>1, 'other'=>array('sample'=>null, 'xx'=>10)));
        $this->assertEquals($event->get_context(), $event2->get_context());
        $this->assertSame(1, $event2->anonymous);

        $event3 = \core_tests\event\unittest_executed::create(array('contextid'=>$system->id, 'objectid'=>5, 'anonymous'=>true, 'other'=>array('sample'=>null, 'xx'=>10)));
        $this->assertSame(1, $event3->anonymous);
    }

    public function test_event_properties_guessing() {
        global $USER;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);
        $event = \core_tests\event\unittest_executed::create(array('context' => $context, 'objectid' => 5));

        // Check guessed course ID, and default properties.
        $this->assertSame('\core_tests\event\unittest_executed', $event->eventname);
        $this->assertSame('core_tests', $event->component);
        $this->assertSame('executed', $event->action);
        $this->assertSame('unittest', $event->target);
        $this->assertSame(5, $event->objectid);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($course->id, $event->courseid);
        $this->assertSame($USER->id, $event->userid);
        $this->assertNull($event->relateduserid);

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        $event = \core_tests\event\unittest_executed::create(array('contextid' => $context->id, 'objectid' => 5));

        // Check guessing on contextid, and user context level.
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($context->contextlevel, $event->contextlevel);
        $this->assertSame(0, $event->courseid);
        $this->assertSame($USER->id, $event->userid);
        $this->assertSame($user->id, $event->relateduserid);
    }

    public function test_observers_parsing() {
        global $CFG;

        $observers = array(
            array(
                'eventname'   => '*',
                'callback'    => array('\core_tests\event\unittest_observer', 'observe_all_alt'),
            ),
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
                'includefile' => 'lib/tests/fixtures/event_fixtures.php',
            ),
            array(
                'eventname'   => '*',
                'callback'    => array('\core_tests\event\unittest_observer', 'observe_all'),
                'includefile' => null,
                'internal'    => 1,
                'priority'    => 10,
            ),
            array(
                'eventname'   => '\core\event\unknown_executed',
                'callback'    => '\core_tests\event\unittest_observer::broken_observer',
                'priority'    => 100,
            ),
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::external_observer',
                'priority'    => 200,
                'internal'    => 0,
            ),
        );

        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(3, $result);

        $expected = array();
        $observer = new stdClass();
        $observer->callable = '\core_tests\event\unittest_observer::external_observer';
        $observer->priority = 200;
        $observer->internal = false;
        $observer->includefile = null;
        $observer->plugintype = null;
        $observer->plugin = null;
        $expected[0] = $observer;
        $observer = new stdClass();
        $observer->callable = '\core_tests\event\unittest_observer::observe_one';
        $observer->priority = 0;
        $observer->internal = true;
        $observer->includefile = $CFG->dirroot.'/lib/tests/fixtures/event_fixtures.php';
        $observer->plugintype = null;
        $observer->plugin = null;
        $expected[1] = $observer;

        $this->assertEquals($expected, $result['\core_tests\event\unittest_executed']);

        $expected = array();
        $observer = new stdClass();
        $observer->callable = '\core_tests\event\unittest_observer::broken_observer';
        $observer->priority = 100;
        $observer->internal = true;
        $observer->includefile = null;
        $observer->plugintype = null;
        $observer->plugin = null;
        $expected[0] = $observer;

        $this->assertEquals($expected, $result['\core\event\unknown_executed']);

        $expected = array();
        $observer = new stdClass();
        $observer->callable = array('\core_tests\event\unittest_observer', 'observe_all');
        $observer->priority = 10;
        $observer->internal = true;
        $observer->includefile = null;
        $observer->plugintype = null;
        $observer->plugin = null;
        $expected[0] = $observer;
        $observer = new stdClass();
        $observer->callable = array('\core_tests\event\unittest_observer', 'observe_all_alt');
        $observer->priority = 0;
        $observer->internal = true;
        $observer->includefile = null;
        $observer->plugintype = null;
        $observer->plugin = null;
        $expected[1] = $observer;

        $this->assertEquals($expected, $result['\core\event\base']);

        // Now test broken stuff...

        $observers = array(
            array(
                'eventname'   => 'core_tests\event\unittest_executed', // Fix leading backslash.
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
                'includefile' => 'lib/tests/fixtures/event_fixtures.php',
                'internal'    => 1, // Cast to bool.
            ),
        );
        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(1, $result);
        $expected = array();
        $observer = new stdClass();
        $observer->callable = '\core_tests\event\unittest_observer::observe_one';
        $observer->priority = 0;
        $observer->internal = true;
        $observer->includefile = $CFG->dirroot.'/lib/tests/fixtures/event_fixtures.php';
        $observer->plugintype = null;
        $observer->plugin = null;
        $expected[0] = $observer;
        $this->assertEquals($expected, $result['\core_tests\event\unittest_executed']);

        $observers = array(
            array(
                // Missing eventclass.
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
                'includefile' => 'lib/tests/fixtures/event_fixtures.php',
            ),
        );
        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(0, $result);
        $this->assertDebuggingCalled();

        $observers = array(
            array(
                'eventname'   => '', // Empty eventclass.
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
                'includefile' => 'lib/tests/fixtures/event_fixtures.php',
            ),
        );
        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(0, $result);
        $this->assertDebuggingCalled();

        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                // Missing callable.
                'includefile' => 'lib/tests/fixtures/event_fixtures.php',
            ),
        );
        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(0, $result);
        $this->assertDebuggingCalled();

        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '', // Empty callable.
                'includefile' => 'lib/tests/fixtures/event_fixtures.php',
            ),
        );
        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(0, $result);
        $this->assertDebuggingCalled();

        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
                'includefile' => 'lib/tests/fixtures/event_fixtures.php_xxx', // Missing file.
            ),
        );
        $result = \core\event\manager::phpunit_replace_observers($observers);
        $this->assertCount(0, $result);
        $this->assertDebuggingCalled();
    }

    public function test_normal_dispatching() {
        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
            ),
            array(
                'eventname'   => '*',
                'callback'    => '\core_tests\event\unittest_observer::observe_all',
                'includefile' => null,
                'internal'    => 1,
                'priority'    => 9999,
            ),
        );

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $event1->nest = 1;
        $this->assertFalse($event1->is_triggered());
        $this->assertFalse($event1->is_dispatched());
        $this->assertFalse($event1->is_restored());
        $event1->trigger();
        $this->assertTrue($event1->is_triggered());
        $this->assertTrue($event1->is_dispatched());
        $this->assertFalse($event1->is_restored());

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>2, 'xx'=>10)));
        $event1->trigger();

        $this->assertSame(
            array('observe_all-nesting-1', 'observe_one-1', 'observe_all-666', 'observe_one-666', 'observe_all-2', 'observe_one-2'),
            \core_tests\event\unittest_observer::$info);
    }

    public function test_event_sink() {
        $sink = $this->redirectEvents();
        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $event1->trigger();
        $this->assertSame(1, $sink->count());
        $retult = $sink->get_events();
        $this->assertSame($event1, $retult[0]);

        $event2 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>2, 'xx'=>10)));
        $event2->trigger();
        $this->assertSame(2, $sink->count());
        $retult = $sink->get_events();
        $this->assertSame($event1, $retult[0]);
        $this->assertSame($event2, $retult[1]);

        $sink->clear();
        $this->assertSame(0, $sink->count());
        $this->assertSame(array(), $sink->get_events());

        $event3 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>3, 'xx'=>10)));
        $event3->trigger();
        $this->assertSame(1, $sink->count());
        $retult = $sink->get_events();
        $this->assertSame($event3, $retult[0]);

        $sink->close();
        $event4 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>4, 'xx'=>10)));
        $event4->trigger();
        $this->assertSame(1, $sink->count());
        $retult = $sink->get_events();
        $this->assertSame($event3, $retult[0]);
    }

    public function test_ignore_exceptions() {
        $observers = array(

            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
            ),

            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::broken_observer',
                'priority'    => 100,
            ),
        );

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $event1->trigger();
        $this->assertDebuggingCalled();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>2, 'xx'=>10)));
        $event1->trigger();
        $this->assertDebuggingCalled();

        $this->assertSame(
            array('broken_observer-1', 'observe_one-1', 'broken_observer-2', 'observe_one-2'),
            \core_tests\event\unittest_observer::$info);
    }

    public function test_external_buffer() {
        global $DB;

        $this->preventResetByRollback();

        $observers = array(

            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
            ),

            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::external_observer',
                'priority'    => 200,
                'internal'    => 0,
            ),
        );

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $event1->trigger();
        $event2 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>2, 'xx'=>10)));
        $event2->trigger();

        $this->assertSame(
            array('external_observer-1', 'observe_one-1', 'external_observer-2', 'observe_one-2'),
            \core_tests\event\unittest_observer::$info);

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $this->assertSame(array(), \core_tests\event\unittest_observer::$info);

        $trans = $DB->start_delegated_transaction();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $event1->trigger();
        $event2 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>2, 'xx'=>10)));
        $event2->trigger();

        $this->assertSame(
            array('observe_one-1', 'observe_one-2'),
            \core_tests\event\unittest_observer::$info);

        $trans->allow_commit();

        $this->assertSame(
            array('observe_one-1', 'observe_one-2', 'external_observer-1', 'external_observer-2'),
            \core_tests\event\unittest_observer::$info);

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $event1->trigger();
        $trans = $DB->start_delegated_transaction();
        $event2 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>2, 'xx'=>10)));
        $event2->trigger();
        try {
            $trans->rollback(new \moodle_exception('xxx'));
            $this->fail('Expecting exception');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('moodle_exception', $e);
        }

        $this->assertSame(
            array('external_observer-1', 'observe_one-1', 'observe_one-2'),
            \core_tests\event\unittest_observer::$info);
    }

    public function test_rollback() {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::external_observer',
                'internal'    => 0,
            ),
        );

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(1, \core_tests\event\unittest_observer::$event);
        \core_tests\event\unittest_observer::reset();

        $transaction1 = $DB->start_delegated_transaction();

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        $transaction2 = $DB->start_delegated_transaction();

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        try {
            $transaction2->rollback(new Exception('x'));
            $this->fail('Expecting exception');
        } catch (Exception $e) {}
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        $this->assertTrue($DB->is_transaction_started());

        try {
            $transaction1->rollback(new Exception('x'));
            $this->fail('Expecting exception');
        } catch (Exception $e) {}
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        $this->assertFalse($DB->is_transaction_started());

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(1, \core_tests\event\unittest_observer::$event);
    }

    public function test_forced_rollback() {
        global $DB;

        $this->resetAfterTest();
        $this->preventResetByRollback();

        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::external_observer',
                'internal'    => 0,
            ),
        );

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(1, \core_tests\event\unittest_observer::$event);
        \core_tests\event\unittest_observer::reset();

        $transaction1 = $DB->start_delegated_transaction();

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        $transaction2 = $DB->start_delegated_transaction();

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        $DB->force_transaction_rollback();
        $this->assertCount(0, \core_tests\event\unittest_observer::$event);

        $this->assertFalse($DB->is_transaction_started());

        \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)))->trigger();
        $this->assertCount(1, \core_tests\event\unittest_observer::$event);
    }

    public function test_deprecated() {
        global $DB;

        $this->resetAfterTest(true);

        $event = \core_tests\event\deprecated_event1::create();
        $this->assertDebuggingCalled('level property is deprecated, use edulevel property instead');

        $this->assertSame($event::LEVEL_TEACHING, $event->level);
        $this->assertDebuggingCalled('level property is deprecated, use edulevel property instead');

        $this->assertTrue(isset($event->level));
        $this->assertDebuggingCalled('level property is deprecated, use edulevel property instead');

        $this->assertSame($event::LEVEL_TEACHING, $event->edulevel);
    }

    public function test_legacy() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $observers = array(
            array(
                'eventname'   => '\core_tests\event\unittest_executed',
                'callback'    => '\core_tests\event\unittest_observer::observe_one',
            ),
            array(
                'eventname'   => '*',
                'callback'    => '\core_tests\event\unittest_observer::observe_all',
                'includefile' => null,
                'internal'    => 1,
                'priority'    => 9999,
            ),
        );

        $DB->delete_records('log', array());
        $this->expectException('coding_exception');
        events_update_definition('unittest');

        $DB->delete_records_select('events_handlers', "component <> 'unittest'");
        events_get_handlers('reset');
        $this->assertDebuggingCalled(self::DEBUGGING_MSG, DEBUG_DEVELOPER);
        $this->assertEquals(3, $DB->count_records('events_handlers'));
        set_config('loglifetime', 60*60*24*5);

        \core\event\manager::phpunit_replace_observers($observers);
        \core_tests\event\unittest_observer::reset();

        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>5, 'xx'=>10)));
        $event1->trigger();

        $event2 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>6, 'xx'=>11)));
        $event2->nest = true;
        $event2->trigger();

        $this->assertSame(
            array('observe_all-5', 'observe_one-5', 'observe_all-nesting-6', 'observe_one-6', 'observe_all-666', 'observe_one-666'),
            \core_tests\event\unittest_observer::$info);

        $this->assertSame($event1, \core_tests\event\unittest_observer::$event[0]);
        $this->assertSame($event1, \core_tests\event\unittest_observer::$event[1]);

        $logs = $DB->get_records('log', array(), 'id ASC');
        $this->assertCount(0, $logs);
    }

    public function test_restore_event() {
        $event1 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $data1 = $event1->get_data();

        $event2 = \core\event\base::restore($data1, array('origin'=>'clid'));
        $data2 = $event2->get_data();

        $this->assertTrue($event2->is_triggered());
        $this->assertTrue($event2->is_restored());
        $this->assertEquals($data1, $data2);
        $this->assertInstanceOf('core_tests\event\unittest_executed', $event2);

        $this->assertEquals($event1->get_context(), $event2->get_context());

        // Now test problematic data.
        $data3 = $data1;
        $data3['eventname'] = '\\a\\b\\c';
        $event3 = \core\event\base::restore($data3, array());
        $this->assertFalse($event3, 'Class name must match');

        $data4 = $data1;
        unset($data4['userid']);
        $event4 = \core\event\base::restore($data4, array());
        $this->assertInstanceOf('core_tests\event\unittest_executed', $event4);
        $this->assertDebuggingCalled();

        $data5 = $data1;
        $data5['xx'] = 'xx';
        $event5 = \core\event\base::restore($data5, array());
        $this->assertInstanceOf('core_tests\event\unittest_executed', $event5);
        $this->assertDebuggingCalled();

    }

    public function test_trigger_problems() {
        $this->resetAfterTest(true);

        $event = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>5, 'xx'=>10)));
        $event->trigger();
        try {
            $event->trigger();
            $this->fail('Exception expected on double trigger');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $data = $event->get_data();
        $restored = \core_tests\event\unittest_executed::restore($data, array());
        $this->assertTrue($restored->is_triggered());
        $this->assertTrue($restored->is_restored());

        try {
            $restored->trigger();
            $this->fail('Exception expected on triggering of restored event');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $event = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>5, 'xx'=>10)));
        try {
            \core\event\manager::dispatch($event);
            $this->fail('Exception expected on manual event dispatching');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    public function test_bad_events() {
        $this->resetAfterTest(true);

        try {
            $event = \core_tests\event\unittest_executed::create(array('other'=>array('sample'=>5, 'xx'=>10)));
            $this->fail('Exception expected when context and contextid missing');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $event = \core_tests\event\bad_event1::create(array('context'=>\context_system::instance()));
        try {
            $event->trigger();
            $this->fail('Exception expected when $data not valid');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\coding_exception', $e);
        }

        $event = \core_tests\event\bad_event2::create(array('context'=>\context_system::instance()));
        try {
            $event->trigger();
            $this->fail('Exception expected when $data not valid');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\coding_exception', $e);
        }

        $event = \core_tests\event\bad_event2b::create(array('context'=>\context_system::instance()));
        @$event->trigger();
        $this->assertDebuggingCalled();

        $event = \core_tests\event\bad_event3::create(array('context'=>\context_system::instance()));
        @$event->trigger();
        $this->assertDebuggingCalled();

        $event = \core_tests\event\bad_event4::create(array('context'=>\context_system::instance()));
        @$event->trigger();
        $this->assertDebuggingCalled();

        $event = \core_tests\event\bad_event5::create(array('context'=>\context_system::instance()));
        @$event->trigger();
        $this->assertDebuggingCalled();

        $event = \core_tests\event\bad_event6::create(array('objectid'=>1, 'context'=>\context_system::instance()));
        $event->trigger();
        $this->assertDebuggingCalled('Unknown table specified in objecttable field');

        $event = \core_tests\event\bad_event7::create(array('objectid'=>1, 'context'=>\context_system::instance()));
        try {
            $event->trigger();
            $this->fail('Exception expected when $data contains objectid but objecttable not specified');
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\coding_exception', $e);
        }

        $event = \core_tests\event\bad_event8::create(array('context'=>\context_system::instance()));
        $event->trigger();
        $this->assertDebuggingCalled('Event property objectid must be set when objecttable is defined');
    }

    public function test_problematic_events() {
        $this->resetAfterTest(true);

        $event1 = \core_tests\event\problematic_event1::create(array('context'=>\context_system::instance()));
        $this->assertDebuggingNotCalled();
        $this->assertNull($event1->xxx);
        $this->assertDebuggingCalled();

        $event2 = \core_tests\event\problematic_event1::create(array('xxx'=>0, 'context'=>\context_system::instance()));
        $this->assertDebuggingCalled();

        set_debugging(DEBUG_NONE);
        $event3 = \core_tests\event\problematic_event1::create(array('xxx'=>0, 'context'=>\context_system::instance()));
        $this->assertDebuggingNotCalled();
        set_debugging(DEBUG_DEVELOPER);

        $event4 = \core_tests\event\problematic_event1::create(array('context'=>\context_system::instance(), 'other'=>array('a'=>1)));
        $event4->trigger();
        $this->assertDebuggingNotCalled();

        $event5 = \core_tests\event\problematic_event1::create(array('context'=>\context_system::instance(), 'other'=>(object)array('a'=>1)));
        $this->assertDebuggingNotCalled();
        $event5->trigger();
        $this->assertDebuggingCalled();

        $url = new moodle_url('/admin/');
        $event6 = \core_tests\event\problematic_event1::create(array('context'=>\context_system::instance(), 'other'=>array('a'=>$url)));
        $this->assertDebuggingNotCalled();
        $event6->trigger();
        $this->assertDebuggingCalled();

        // Check that whole float numbers do not trigger debugging messages.
        $event7 = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(),
            'other' => array('wholenumber' => 90.0000, 'numberwithdecimals' => 54.7656, 'sample' => 1)));
        $event7->trigger();
        $this->assertDebuggingNotCalled();

        $event = \core_tests\event\problematic_event2::create(array());
        $this->assertDebuggingNotCalled();
        $event = \core_tests\event\problematic_event2::create(array('context'=>\context_system::instance()));
        $this->assertDebuggingCalled();

        $event = \core_tests\event\problematic_event3::create(array('other'=>1));
        $this->assertDebuggingNotCalled();
        $event = \core_tests\event\problematic_event3::create(array());
        $this->assertDebuggingCalled();
    }

    public function test_record_snapshots() {
        global $DB;

        $this->resetAfterTest(true);

        $event = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));
        $course1 = $DB->get_record('course', array('id'=>1));
        $this->assertNotEmpty($course1);

        $event->add_record_snapshot('course', $course1);

        $result = $event->get_record_snapshot('course', $course1->id);
        // Convert to arrays because record snapshot returns a clone of the object.
        $this->assertSame((array)$course1, (array)$result);

        $user = $event->get_record_snapshot('user', 1);
        $this->assertEquals(1, $user->id);
        $this->assertSame('guest', $user->username);

        $event->add_record_snapshot('course', $course1);
        $event->trigger();
        try {
            $event->add_record_snapshot('course', $course1);
            $this->fail('Updating of snapshots after trigger is not ok');;
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\coding_exception', $e);
        }

        $event2 = \core_tests\event\unittest_executed::restore($event->get_data(), array());
        try {
            $event2->get_record_snapshot('course', $course1->id);
            $this->fail('Reading of snapshots from restored events is not ok');;
        } catch (\moodle_exception $e) {
            $this->assertInstanceOf('\coding_exception', $e);
        }
    }

    public function test_get_name() {
        $event = \core_tests\event\noname_event::create(array('other' => array('sample' => 1, 'xx' => 10)));
        $this->assertEquals("core_tests: noname event", $event->get_name());
    }

    public function test_iteration() {
        $event = \core_tests\event\unittest_executed::create(array('context'=>\context_system::instance(), 'other'=>array('sample'=>1, 'xx'=>10)));

        $data = array();
        foreach ($event as $k => $v) {
            $data[$k] = $v;
        }

        $this->assertSame($event->get_data(), $data);
    }

    /**
     * @expectedException PHPUnit\Framework\Error\Notice
     */
    public function test_context_not_used() {
        $event = \core_tests\event\context_used_in_event::create(array('other' => array('sample' => 1, 'xx' => 10)));
        $this->assertEventContextNotUsed($event);

        $eventcontext = phpunit_event_mock::testable_get_event_context($event);
        phpunit_event_mock::testable_set_event_context($event, null);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that all observer information is returned correctly.
     */
    public function test_get_all_observers() {
        // Retrieve all observers.
        $observers = \core\event\manager::get_all_observers();

        // Expected information from the workshop allocation scheduled observer.
        $expected = new stdClass();
        $expected->callable = '\workshopallocation_scheduled\observer::workshop_viewed';
        $expected->priority = 0;
        $expected->internal = true;
        $expected->includefile = null;
        $expected->plugintype = 'workshopallocation';
        $expected->plugin = 'scheduled';

        // May be more than one observer for the mod_workshop event.
        $found = false;
        foreach ($observers['\mod_workshop\event\course_module_viewed'] as $observer) {
            if ($expected == $observer) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Test formatting of the get_explanation method.
     * This formats the information from an events class docblock.
     */
    public function test_get_explanation() {
        $explanation = \core_tests\event\full_docblock::get_explanation();

        $expected = "This is an explanation of the event.
     - I'm making a point here.
     - I have a second {@link something}  point here.
     - whitespace is intentional to test it's removal.
I have something else *Yeah* that.";

        $this->assertEquals($explanation, $expected);

        $explanation = \core_tests\event\docblock_test2::get_explanation();

        $expected = "We have only the description in the docblock
and nothing else.";

        $this->assertEquals($explanation, $expected);

        $explanation = \core_tests\event\docblock_test3::get_explanation();
        $expected = "Calendar event created event.";
        $this->assertEquals($explanation, $expected);

    }

    /**
     * Test that general information about an event is returned
     * by the get_static_info() method.
     */
    public function test_get_static_info() {
        $staticinfo = \core_tests\event\static_info_viewing::get_static_info();

        $expected = array(
            'eventname'   => '\\core_tests\\event\\static_info_viewing',
            'component'   => 'core_tests',
            'target'      => 'static_info',
            'action'      => 'viewing',
            'crud'        => 'r',
            'edulevel'    => 0,
            'objecttable' => 'mod_unittest'
        );
        $this->assertEquals($staticinfo, $expected);
    }

    /**
     * This tests the internal method of \core\event\manager::get_observing_classes.
     *
     * What we are testing is if we can subscribe to a parent event class, instead of only
     * the base event class or the final, implemented event class.  This enables us to subscribe
     * to things like all course module view events, all comment created events, etc.
     */
    public function test_observe_parent_event() {
        $this->resetAfterTest();

        // Ensure this has been reset prior to using it.
        \core_tests\event\unittest_observer::reset();

        $course  = $this->getDataGenerator()->create_course();
        $feed    = $this->getDataGenerator()->create_module('feedback', ['course' => $course->id]);
        $context = context_module::instance($feed->cmid);
        $data    = [
            'context'  => $context,
            'courseid' => $course->id,
            'objectid' => $feed->id
        ];

        // This assertion ensures that basic observe use case did not break.
        \core\event\manager::phpunit_replace_observers([[
            'eventname' => '\core_tests\event\course_module_viewed',
            'callback'  => ['\core_tests\event\unittest_observer', 'observe_all_alt'],
        ]]);

        $pageevent = \core_tests\event\course_module_viewed::create($data);
        $pageevent->trigger();

        $this->assertSame(['observe_all_alt'], \core_tests\event\unittest_observer::$info, 'Error observing triggered event');

        \core_tests\event\unittest_observer::reset();

        // This assertion tests that we can observe an abstract (parent) class instead of the implemented class.
        \core\event\manager::phpunit_replace_observers([[
            'eventname' => '\core\event\course_module_viewed',
            'callback'  => ['\core_tests\event\unittest_observer', 'observe_all_alt'],
        ]]);

        $pageevent = \core_tests\event\course_module_viewed::create($data);
        $pageevent->trigger();

        $this->assertSame(['observe_all_alt'], \core_tests\event\unittest_observer::$info, 'Error observing parent class event');

        \core_tests\event\unittest_observer::reset();
    }
}
