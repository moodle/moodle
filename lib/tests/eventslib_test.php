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
 * Tests events subsystem.
 *
 * @package    core_event
 * @subpackage phpunit
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class core_eventslib_testcase extends advanced_testcase {

    /**
     * Create temporary entries in the database for these tests.
     * These tests have to work no matter the data currently in the database
     * (meaning they should run on a brand new site). This means several items of
     * data have to be artificially inseminated (:-) in the DB.
     */
    protected function setUp() {
        parent::setUp();
        // Set global category settings to -1 (not force).
        eventslib_sample_function_handler('reset');
        eventslib_sample_handler_class::static_method('reset');
        events_update_definition('unittest');

        $this->resetAfterTest();
    }

    /**
     * Tests the installation of event handlers from file
     */
    public function test_events_update_definition__install() {
        global $DB;

        $dbcount = $DB->count_records('events_handlers', array('component'=>'unittest'));
        $handlers = array();
        require(__DIR__.'/fixtures/events.php');
        $this->assertCount($dbcount, $handlers, 'Equal number of handlers in file and db: %s');
    }

    /**
     * Tests the uninstallation of event handlers from file.
     */
    public function test_events_update_definition__uninstall() {
        global $DB;

        events_uninstall('unittest');
        $this->assertEquals(0, $DB->count_records('events_handlers', array('component'=>'unittest')), 'All handlers should be uninstalled: %s');
    }

    /**
     * Tests the update of event handlers from file.
     */
    public function test_events_update_definition__update() {
        global $DB;
        // First modify directly existing handler.
        $handler = $DB->get_record('events_handlers', array('component'=>'unittest', 'eventname'=>'test_instant'));

        $original = $handler->handlerfunction;

        // Change handler in db.
        $DB->set_field('events_handlers', 'handlerfunction', serialize('some_other_function_handler'), array('id'=>$handler->id));

        // Update the definition, it should revert the handler back.
        events_update_definition('unittest');
        $handler = $DB->get_record('events_handlers', array('component'=>'unittest', 'eventname'=>'test_instant'));
        $this->assertSame($handler->handlerfunction, $original, 'update should sync db with file definition: %s');
    }

    /**
     * Tests events_trigger_is_registered() function.
     */
    public function test_events_is_registered() {
        $this->assertTrue(events_is_registered('test_instant', 'unittest'));
    }

    /**
     * Tests events_trigger_legacy() function.
     */
    public function test_events_trigger_legacy_instant() {
        $this->assertEquals(0, events_trigger_legacy('test_instant', 'ok'));
        $this->assertEquals(0, events_trigger_legacy('test_instant', 'ok'));
        $this->assertEquals(2, eventslib_sample_function_handler('status'));
    }

    /**
     * Tests events_trigger_legacy() function.
     */
    public function test_events_trigger__cron() {
        $this->assertEquals(0, events_trigger_legacy('test_cron', 'ok'));
        $this->assertEquals(0, eventslib_sample_handler_class::static_method('status'));
        events_cron('test_cron');
        $this->assertEquals(1, eventslib_sample_handler_class::static_method('status'));
    }

    /**
     * Tests events_pending_count() function.
     */
    public function test_events_pending_count() {
        events_trigger_legacy('test_cron', 'ok');
        events_trigger_legacy('test_cron', 'ok');
        events_cron('test_cron');
        $this->assertEquals(0, events_pending_count('test_cron'), 'all messages should be already dequeued: %s');
    }

    /**
     * Tests events_trigger_legacy() function when instant handler fails.
     */
    public function test_events_trigger__failed_instant() {
        global $CFG;
        $olddebug = $CFG->debug;

        $this->assertEquals(1, events_trigger_legacy('test_instant', 'fail'), 'fail first event: %s');
        $this->assertEquals(1, events_trigger_legacy('test_instant', 'ok'), 'this one should fail too: %s');

        $this->assertEquals(0, events_cron('test_instant'), 'all events should stay in queue: %s');
        $this->assertDebuggingCalled();

        $this->assertEquals(2, events_pending_count('test_instant'), 'two events should in queue: %s');
        $this->assertEquals(0, eventslib_sample_function_handler('status'), 'verify no event dispatched yet: %s');
        eventslib_sample_function_handler('ignorefail'); // Ignore "fail" eventdata from now on.
        $this->assertEquals(1, events_trigger_legacy('test_instant', 'ok'), 'this one should go to queue directly: %s');
        $this->assertEquals(3, events_pending_count('test_instant'), 'three events should in queue: %s');
        $this->assertEquals(0, eventslib_sample_function_handler('status'), 'verify previous event was not dispatched: %s');
        $this->assertEquals(3, events_cron('test_instant'), 'all events should be dispatched: %s');
        $this->assertEquals(3, eventslib_sample_function_handler('status'), 'verify three events were dispatched: %s');
        $this->assertEquals(0, events_pending_count('test_instant'), 'no events should in queue: %s');
        $this->assertEquals(0, events_trigger_legacy('test_instant', 'ok'), 'this event should be dispatched immediately: %s');
        $this->assertEquals(4, eventslib_sample_function_handler('status'), 'verify event was dispatched: %s');
        $this->assertEquals(0, events_pending_count('test_instant'), 'no events should in queue: %s');
    }

    /**
     * Tests events_trigger() function.
     */
    public function test_events_trigger_debugging() {
        $this->assertEquals(0, events_trigger('test_instant', 'ok'));
        $this->assertDebuggingCalled();
    }
}

/**
 * Test handler function.
 */
function eventslib_sample_function_handler($eventdata) {
    static $called = 0;
    static $ignorefail = false;

    if ($eventdata == 'status') {
        return $called;

    } else if ($eventdata == 'reset') {
        $called = 0;
        $ignorefail = false;
        return;

    } else if ($eventdata == 'fail') {
        if ($ignorefail) {
            $called++;
            return true;
        } else {
            return false;
        }

    } else if ($eventdata == 'ignorefail') {
        $ignorefail = true;
        return;

    } else if ($eventdata == 'ok') {
        $called++;
        return true;
    }

    print_error('invalideventdata', '', '', $eventdata);
}


/**
 * Test handler class with static method.
 */
class eventslib_sample_handler_class {
    public static function static_method($eventdata) {
        static $called = 0;
        static $ignorefail = false;

        if ($eventdata == 'status') {
            return $called;

        } else if ($eventdata == 'reset') {
            $called = 0;
            $ignorefail = false;
            return;

        } else if ($eventdata == 'fail') {
            if ($ignorefail) {
                $called++;
                return true;
            } else {
                return false;
            }

        } else if ($eventdata == 'ignorefail') {
            $ignorefail = true;
            return;

        } else if ($eventdata == 'ok') {
            $called++;
            return true;
        }

        print_error('invalideventdata', '', '', $eventdata);
    }
}
