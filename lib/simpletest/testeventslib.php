<?php

/** $Id */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->libdir . '/dmllib.php');

// dummy test function
function plusone($eventdata) {
  
    return $eventdata+1;  
}

class eventslib_test extends UnitTestCase {

    var $handlerid;
    var $handler;
    var $storedhandler;
    /**
     * Create temporary entries in the database for these tests.
     * These tests have to work no matter the data currently in the database
     * (meaning they should run on a brand new site). This means several items of
     * data have to be artificially inseminated (:-) in the DB.
     */
    function setUp() {
        
        global $CFG;
        
        // make a dummy event
        $eventhandler -> eventname = 'testevent';
        $eventhandler -> handlermodule = 'unittest';
        $eventhandler -> handlerfile = '/lib/simpletest/testeventslib.php';
        $eventhandler -> handlerfunction = 'plusone';
        $eventhandler -> schedule = 'instant';
        
        $this -> handler = $eventhandler;
        $this -> handlerid = insert_record('events_handlers', $eventhandler);
        $this -> handler->id = $this->handlerid;

    }

    /**
     * Delete temporary entries from the database
     */
    function tearDown() 
    {
        delete_records('events_handlers', 'id', $this->handlerid);
    }
    
    /**
     * tests queue_handler() and events_process_queued_handler() and trigger_event()
     */
    function test_events_process_queued_handler_handler() {
        
        $eventdata = new object;
        $eventdata->eventdata = serialize(1);
        $eventdata->schedule = 'instant';
        
        $eventid = insert_record('events_queue', $eventdata);

        $id = queue_handler($this->handler, $eventid);
        $storedhandler = get_record('events_queue_handlers', 'id', $id);

        $retval = events_process_queued_handler($storedhandler);
        $this->assertEqual(2, $retval);
        $this->storedhandler = $storedhandler;
    }
    
    /**
     * tests events_dequeue()
     */
    function test_events_dequeue() {
        $this->assertTrue(events_dequeue($this->storedhandler));        
    }
    
    /** 
     * tests trigger_event funtion()
     */
    function test_trigger_event() {
        $eventdata = 2;
        $this->assertEqual(0, trigger_event('testevent', $eventdata));
    }
    
    /** 
    * tests trigger_event_is_registered funtion()
    */
    function test_event_is_registered() {
        $this->assertTrue(event_is_registered('unittest', 'testevent'));
    }
    
}

?>