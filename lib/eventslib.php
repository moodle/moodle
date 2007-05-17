<?php
/**
 * Library of functions for events manipulation.
 * 
 * @author Martin Dougiamas and many others
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */


/**
 * Loads the events definitions for the component (from file). If no
 * events are defined for the component, we simply return an empty array.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of capabilities
 */
function events_load_def($component) {
    global $CFG;

    if ($component == 'moodle') {
        $defpath = $CFG->libdir.'/db/events.php';

    } else {
        $compparts = explode('/', $component);

        if ($compparts[0] == 'block') {
            // Blocks are an exception. Blocks directory is 'blocks', and not
            // 'block'. So we need to jump through hoops.
            $defpath = $CFG->dirroot.'/'.$compparts[0].
                                's/'.$compparts[1].'/db/events.php';
        } else if ($compparts[0] == 'format') {
            // Similar to the above, course formats are 'format' while they 
            // are stored in 'course/format'.
            $defpath = $CFG->dirroot.'/course/'.$component.'/db/events.php';
        } else {
            $defpath = $CFG->dirroot.'/'.$component.'/db/events.php';
        }
    }

    if (file_exists($defpath)) {
        require($defpath);
        return $events;
    }
}

/**
 * Gets the capabilities that have been cached in the database for this
 * component.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of events
 */
function get_cached_events($component='moodle') {
    if ($component == 'moodle') {
        $storedevents = get_records_select('events_handlers',
                        "handlermodule LIKE 'moodle/%'");
    } else {
        $storedevents = get_records_select('events_handlers',
                        "handlermodule LIKE '$component%'");
    }
    
    if (!empty($storedevents)) {
        foreach ($storedevents as $storedevent) {
            $eventname = $storedevent->eventname;
            // not needed for comparisons
            unset($storedevent->handlermodule);
            //unset($storedevent->id);
            unset($storedevent->eventname);
            $cachedevents[$eventname] = (array)$storedevent;
        }
        return $cachedevents;
    }
}


/**
 * We can not removed all event handlers in table, then add them again
 * because event handlers could be referenced by queued items
 *
 * Updates the capabilities table with the component capability definitions.
 * If no parameters are given, the function updates the core moodle
 * capabilities.
 *
 * Note that the absence of the db/events.php event definition file
 * will cause any stored events for the component to be removed from
 * the database.
 *
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean
 */

function events_update_definition($component='moodle') {

    $storedevents = array();

    // load event definition from events.php
    $fileevents = events_load_def($component);
    
    // load event definitions from db tables
    // if we detect an event being already stored, we discard from this array later
    // the remaining needs to be removed
    
    $cachedevents = get_cached_events($component);
    
    if ($fileevents) {
        foreach ($fileevents as $eventname => $fileevent) {
            if (!empty($cachedevents[$eventname])) {
                // exact same event handler already present in db,
                // ignore this entry
                if ($cachedevents[$eventname]['handlerfile'] == $fileevent['handlerfile'] &&
                    $cachedevents[$eventname]['handlerfunction'] == $fileevent['handlerfunction'] &&
                    $cachedevents[$eventname]['schedule'] == $fileevent['schedule']) {                    
                    
                    unset($cachedevents[$eventname]);
                    continue;                    
                
                } else {
                    // same event name matches, this event has been updated,
                    // update the datebase
                    $event = new object;
                    $event->id = $cachedevents[$eventname]['id'];
                    $event->handlerfile = $fileevent['handlerfile'];
                    $event->handlerfunction = $fileevent['handlerfunction'];
                    $event->schedule = $fileevent['schedule'];
                    
                    update_record('events_handlers', $event);
                    
                    unset($cachedevents[$eventname]);
                    continue;                   
                }
                
            } else {            
                // if we are here, this event handler is not present in db (new)
                // add it
                $event = new object;
                $event->eventname =  $eventname;
                $event->handlermodule = $component;
                $event->handlerfile = $fileevent['handlerfile'];
                $event->handlerfunction = $fileevent['handlerfunction'];
                $event->schedule = $fileevent['schedule'];        
                insert_record('events_handlers', $event);
            } 
        }
    }
    
    // clean up the left overs, the entries in cachedevents array at this points are deprecated event handlers
    // and should be removed, delete from db
    events_cleanup($component, $cachedevents);

    return true;
}

/**
 * Deletes cached events that are no longer needed by the component.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @param $chachedevents - array of the cached events definitions that will be
 * @return int - number of deprecated capabilities that have been removed
 */
function events_cleanup($component, $cachedevents) {
    $deletecount = 0;
    if ($cachedevents) {
        foreach ($cachedevents as $eventname => $cachedevent) {
            if (delete_records('events_handlers', 'eventname', $eventname, 'handlermodule', $component)) {
                $deletecount++; 
            }
        }
    }
    return $deletecount;
}

/****************** End of Events handler Definition code *******************/

/**
 * puts a handler on queue
 * @param object handler - event handler object from db
 * @param object eventdata - event data object
 * @param bool failed -  whether this handler is queued because of a failed event trigger
 * @return
 */
function queue_handler($handler, $eventid) {
    global $USER;
    
    // check if this event handler is already queued
    if (!$qh = get_record('events_queue_handlers', 'queuedeventid', $eventid, 'handlerid', $handler->id)) {
        // make a new one
        $qh = new object;
        $qh->queuedeventid = $eventid;
        $qh->handlerid = $handler->id;
        $qh->status = 0;
        $qh->errormessage = '';
        $qh->timemodified = time();
        return insert_record('events_queue_handlers', $qh);
    } else {
        // update existing one, failed again
        $qh->states++;
        $qh->timemodified = time();
        update_record('events_queue_handlers', $qh);
        return -1; // failed
    }
}

/**
 * function to call all eventhandlers when triggering an event
 * @param eventname - name of the event
 * @param eventdata - event data object
 * @return number of failed events
 */
function trigger_event($eventname, $eventdata) {
    $failedevent = 0; // number of failed events.
    $eventid = 0;
    
    // pull out all registered event handlers
    if ($handlers = get_records('events_handlers', 'eventname', $eventname)) {
        foreach ($handlers as $handler) {
            // either excute it now
            
            // if event type is 
            if ($handler->schedule == 'instant') {
                if (dispatch_event($handler, $eventdata)) {
                    continue;
                } else {
                    // update the failed flag
                    $failedevent ++;
                }
            }
            // if even type is not instant, or trigger failed, queue it
            $queuedevent++;
            // make and queue the event object here
            
            if (!$eventid) {
                $eq = new object;
                $eq->userid = $USER->id;
                $eq->schedule = $eventdata->schedule;
                $eq->eventdata = serialize($eventdata);
                $eq->stackdump = '';
                $eq->timecreated = time();
                $eventid = insert_record('events_queue', $eq);
            }
            queue_handler($handler, $eventid);       
        }      
    }
    return $failedevent;
}

/**
 * trigger a single event with a specified handler
 * @param handler - hander object from db
 * @param eventdata - event dataobject
 * @return bool - success or fail
 */
function dispatch_event($handler, $eventdata) {

    global $CFG;
    // checks for handler validity
    
    // check if the same handler is queued already, if so, return false so we can queue it
    // TODO  
    
    include_once($CFG->dirroot.$handler->handlerfile);
    return call_user_func($handler->handlerfunction, $eventdata);
}

/**
 * given a queued handler, call the respective event handler to process the event
 * @param object handler- events_queued_handler object from db
 * @return fail or custom function value
 */
function events_process_queued_handler($handler) {
    // checks for handler validity
    global $CFG;
    
    // get handler
    if (!$eventhandler = get_record('events_handlers', 'id', $handler->handlerid)) {
        // can't proceed with no handler
        return false;  
    }
    // get event object
    if (!$eventobject = get_record('events_queue', 'id', $handler->queuedeventid)) {
        // can't proceed with no event object
        return false;  
    }
    // call the function sepcified by the handler

    return dispatch_event($eventhandler, unserialize($eventobject->eventdata));
}

/**
 * Events cron will try to empty the events queue by processing all the queued events handlers
 */
function events_cron() {
    
    global $CFG;
    
    if ($handlers = get_records_select('events_queue_handlers', '', 'timemodified')) {
        foreach ($handlers as $handler) {
            if (events_process_queued_handler($handler)) {
                // dequeue();
                events_dequeue($handler);  
            } else {
                // failed again, put back on queue
                $handler->timemodified = time();
                $handler->status++;
                update_record('events_queue_handlers', $handler); 
            }
        }      
    }
}

/**
 * removes this queued handler from the events_queued_handler table
 * removes events_queue record from events_queue if no more references to this event object exists
 * @input object handler - events_queued_handler object from db
 */
function events_dequeue($handler) {
    
    if (delete_records('events_queue_handlers', 'id', $handler->id)) {
        // if no more queued handler is pointing to the same event
        if (!record_exists('events_queue_handlers', 'queuedeventid', $handler->queuedeventid)) {
            delete_records('events_queue', 'id', $handler->queuedeventid); 
        }
        return true;
    } else {
        return false;  
    }
}

/**
 * checks if an event is registered for this component
 * @param string component - component name, can be mod/data or moodle
 * @return bool
 */
function event_is_registered($component, $eventname) {
    return record_exists('events_handlers', 'handlermodule', $component, 'eventname', $eventname);  
}
?>
