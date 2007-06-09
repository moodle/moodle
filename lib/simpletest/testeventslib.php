<?php

/* $Id$ */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

// test handler function
function sample_function_handler($eventdata) {
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

    error('Incorrect eventadata submitted: '.$eventdata);
}

// test handler class with static method
class sample_handler_class {
    function static_method($eventdata) {
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

        error('Incorrect eventadata submitted: '.$eventdata);
    }
}

class eventslib_test extends UnitTestCase {

    /**
     * Create temporary entries in the database for these tests.
     * These tests have to work no matter the data currently in the database
     * (meaning they should run on a brand new site). This means several items of
     * data have to be artificially inseminated (:-) in the DB.
     */
    function setUp() {
        events_uninstall('unittest');
        sample_function_handler('reset');
        sample_handler_class::static_method('reset');
        events_update_definition('unittest');
    }

    /**
     * Delete temporary entries from the database
     */
    function tearDown() {
       events_uninstall('unittest');
    }

    /**
     * Tests the installation of event handlers from file
     */
    function test__events_update_definition__install() {
        global $CFG;

        $dbcount = count_records('events_handlers', 'handlermodule', 'unittest');
        $handlers = array();
        require($CFG->libdir.'/simpletest/fixtures/events.php');
        $filecount = count($handlers);
        $this->assertEqual($dbcount, $filecount, 'Equal number of handlers in file and db: %s');
    }

    /**
     * Tests the uninstallation of event handlers from file
     */
    function test__events_update_definition__uninstall() {
        events_uninstall('unittest');
        $this->assertEqual(0, count_records('events_handlers', 'handlermodule', 'unittest'), 'All handlers should be uninstalled: %s');
    }

    /**
     * Tests the update of event handlers from file
     */
    function test__events_update_definition__update() {
        // first modify directly existing handler
        $handler = get_record('events_handlers', 'handlermodule', 'unittest', 'eventname', 'test_instant');

        $original = $handler->handlerfunction;

        // change handler in db
        set_field('events_handlers', 'handlerfunction', serialize('some_other_function_handler'), 'id', $handler->id);

        // update the definition, it should revert the handler back
        events_update_definition('unittest');
        $handler = get_record('events_handlers', 'handlermodule', 'unittest', 'eventname', 'test_instant');
        $this->assertEqual($handler->handlerfunction, $original, 'update should sync db with file definition: %s');
    }

    /**
    * tests events_trigger_is_registered funtion()
    */
    function test__events_is_registered() {
        $this->assertTrue(events_is_registered('test_instant', 'unittest'));
    }

    /**
     * tests events_trigger funtion()
     */
    function test__events_trigger__instant() {
        $this->assertEqual(0, events_trigger('test_instant', 'ok'));
        $this->assertEqual(0, events_trigger('test_instant', 'ok'));
        $this->assertEqual(2, sample_function_handler('status'));
    }

    /**
     * tests events_trigger funtion()
     */
    function test__events_trigger__cron() {
        $this->assertEqual(0, events_trigger('test_cron', 'ok'));
        $this->assertEqual(0, sample_handler_class::static_method('status'));
        events_cron();
        $this->assertEqual(1, sample_handler_class::static_method('status'));
    }

    /**
     * tests events_pending_count()
     */
    function test__events_pending_count() {
        events_trigger('test_cron', 'ok');
        events_trigger('test_cron', 'ok');
        $this->assertEqual(2, events_pending_count('test_cron'), 'two events should in queue: %s');
        events_cron('test_cron');
        $this->assertEqual(0, events_pending_count('test_cron'), 'all messages should be already dequeued: %s');
    }

    /**
     * tests events_trigger funtion() when instant handler fails
     */
    function test__events_trigger__failed_instant() {
        $this->assertEqual(1, events_trigger('test_instant', 'fail'), 'fail first event: %s');
        $this->assertEqual(1, events_trigger('test_instant', 'ok'), 'this one should fail too: %s');
        $this->assertEqual(0, events_cron('test_instant'), 'all events should stay in queue: %s');
        $this->assertEqual(2, events_pending_count('test_instant'), 'two events should in queue: %s');
        $this->assertEqual(0, sample_function_handler('status'), 'verify no event dispatched yet: %s');
        sample_function_handler('ignorefail'); //ignore "fail" eventdata from now on
        $this->assertEqual(1, events_trigger('test_instant', 'ok'), 'this one should go to queue directly: %s');
        $this->assertEqual(3, events_pending_count('test_instant'), 'three events should in queue: %s');
        $this->assertEqual(0, sample_function_handler('status'), 'verify previous event was not dispatched: %s');
        $this->assertEqual(3, events_cron('test_instant'), 'all events should be dispatched: %s');
        $this->assertEqual(3, sample_function_handler('status'), 'verify three events were dispatched: %s');
        $this->assertEqual(0, events_pending_count('test_instant'), 'no events should in queue: %s');
        $this->assertEqual(0, events_trigger('test_instant', 'ok'), 'this event should be dispatched immediately: %s');
        $this->assertEqual(4, sample_function_handler('status'), 'verify event was dispatched: %s');
        $this->assertEqual(0, events_pending_count('test_instant'), 'no events should in queue: %s');
    }



}

?>