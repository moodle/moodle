<?php
/**
 * Library of functions for events manipulation.
 * 
 * The public API is all at the end of this file.
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
 * @return array of capabilities or empty array if not exists
 *
 * INTERNAL - to be used from eventslib only
 */
function events_load_def($component) {
    global $CFG;

    if ($component == 'moodle') {
        $defpath = $CFG->libdir.'/db/events.php';

    } else if ($component == 'unittest') {
        $defpath = $CFG->libdir.'/simpletest/fixtures/events.php';

    } else {
        $compparts = explode('/', $component);

        if ($compparts[0] == 'block') {
            // Blocks are an exception. Blocks directory is 'blocks', and not
            // 'block'. So we need to jump through hoops.
            $defpath = $CFG->dirroot.'/blocks/'.$compparts[1].'/db/events.php';

        } else if ($compparts[0] == 'format') {
            // Similar to the above, course formats are 'format' while they
            // are stored in 'course/format'.
            $defpath = $CFG->dirroot.'/course/format/'.$compparts[1].'/db/events.php';

        } else if ($compparts[0] == 'gradeimport') {
            $defpath = $CFG->dirroot.'/grade/import/'.$compparts[1].'/db/events.php';  
        
        } else if ($compparts[0] == 'gradeexport') {
            $defpath = $CFG->dirroot.'/grade/export/'.$compparts[1].'/db/events.php'; 
        
        } else if ($compparts[0] == 'gradereport') {
            $defpath = $CFG->dirroot.'/grade/report/'.$compparts[1].'/db/events.php'; 
        
        } else {
            $defpath = $CFG->dirroot.'/'.$component.'/db/events.php';
        }
    }

    $handlers = array();

    if (file_exists($defpath)) {
        require($defpath);
    }

    return $handlers;
}

/**
 * Gets the capabilities that have been cached in the database for this
 * component.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of events
 *
 * INTERNAL - to be used from eventslib only
 */
function events_get_cached($component) {
    $cachedhandlers = array();

    if ($storedhandlers = get_records('events_handlers', 'handlermodule', $component)) {
        foreach ($storedhandlers as $handler) {
            $cachedhandlers[$handler->eventname] = array (
                'id'              => $handler->id,
                'handlerfile'     => $handler->handlerfile,
                'handlerfunction' => $handler->handlerfunction,
                'schedule'        => $handler->schedule);
        }
    }

    return $cachedhandlers;
}

/**
 * We can not removed all event handlers in table, then add them again
 * because event handlers could be referenced by queued items
 *
 * Note that the absence of the db/events.php event definition file
 * will cause any queued events for the component to be removed from
 * the database.
 *
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean
 */
function events_update_definition($component='moodle') {

    // load event definition from events.php
    $filehandlers = events_load_def($component);

    // load event definitions from db tables
    // if we detect an event being already stored, we discard from this array later
    // the remaining needs to be removed
    $cachedhandlers = events_get_cached($component);

    foreach ($filehandlers as $eventname => $filehandler) {
        if (!empty($cachedhandlers[$eventname])) {
            if ($cachedhandlers[$eventname]['handlerfile'] == $filehandler['handlerfile'] &&
                $cachedhandlers[$eventname]['handlerfunction'] == serialize($filehandler['handlerfunction']) &&
                $cachedhandlers[$eventname]['schedule'] == $filehandler['schedule']) {
                // exact same event handler already present in db, ignore this entry

                unset($cachedhandlers[$eventname]);
                continue;

            } else {
                // same event name matches, this event has been updated, update the datebase
                $handler = new object();
                $handler->id              = $cachedhandlers[$eventname]['id'];
                $handler->handlerfile     = $filehandler['handlerfile'];
                $handler->handlerfunction = serialize($filehandler['handlerfunction']); // static class methods stored as array
                $handler->schedule        = $filehandler['schedule'];

                update_record('events_handlers', $handler);

                unset($cachedhandlers[$eventname]);
                continue;
            }

        } else {
            // if we are here, this event handler is not present in db (new)
            // add it
            $handler = new object();
            $handler->eventname       = $eventname;
            $handler->handlermodule   = $component;
            $handler->handlerfile     = $filehandler['handlerfile'];
            $handler->handlerfunction = serialize($filehandler['handlerfunction']); // static class methods stored as array
            $handler->schedule        = $filehandler['schedule'];

            insert_record('events_handlers', $handler);
        }
    }

    // clean up the left overs, the entries in cachedevents array at this points are deprecated event handlers
    // and should be removed, delete from db
    events_cleanup($component, $cachedhandlers);

    return true;
}

/**
 * Remove all event handlers and queued events
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 */
function events_uninstall($component) {
    $cachedhandlers = events_get_cached($component);
    events_cleanup($component, $cachedhandlers);
}

/**
 * Deletes cached events that are no longer needed by the component.
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @param $chachedevents - array of the cached events definitions that will be
 * @return int - number of deprecated capabilities that have been removed
 *
 * INTERNAL - to be used from eventslib only
 */
function events_cleanup($component, $cachedhandlers) {
    $deletecount = 0;
    foreach ($cachedhandlers as $eventname => $cachedhandler) {
        if ($qhandlers = get_records('events_queue_handlers', 'handlerid', $cachedhandler['id'])) {
            debugging("Removing pending events from queue before deleting of event handler: $component - $eventname");
            foreach ($qhandlers as $qhandler) {
                events_dequeue($qhandler);
            }
        }
        if (delete_records('events_handlers', 'eventname', $eventname, 'handlermodule', $component)) {
            $deletecount++;
        }
    }

    // reset static handler cache
    events_get_handlers('reset');

    return $deletecount;
}

/****************** End of Events handler Definition code *******************/

/**
 * puts a handler on queue
 * @param object handler - event handler object from db
 * @param object eventdata - event data object
 * @return id number of new queue handler
 *
 * INTERNAL - to be used from eventslib only
 */
function events_queue_handler($handler, $event, $errormessage) {

    if ($qhandler = get_record('events_queue_handlers', 'queuedeventid', $event->id, 'handlerid', $handler->id)) {
        debugging("Please check code: Event id $event->id is already queued in handler id $qhandler->id");
        return $qhandler->id;
    }

    // make a new queue handler
    $qhandler = new object();
    $qhandler->queuedeventid  = $event->id;
    $qhandler->handlerid      = $handler->id;
    $qhandler->errormessage   = addslashes($errormessage);
    $qhandler->timemodified   = time();
    if ($handler->schedule == 'instant' and $handler->status == 1) {
        $qhandler->status     = 1; //already one failed attempt to dispatch this event
    } else {
        $qhandler->status     = 0;
    }

    return insert_record('events_queue_handlers', $qhandler);
}

/**
 * trigger a single event with a specified handler
 * @param handler - hander object from db
 * @param eventdata - event dataobject
 * @param errormessage - error message indicating problem
 * @return bool - success or fail
 *
 * INTERNAL - to be used from eventslib only
 */
function events_dispatch($handler, $eventdata, &$errormessage) {
    global $CFG;

    $function = unserialize($handler->handlerfunction);

    if (is_callable($function)) {
        // oki, no need for includes

    } else if (file_exists($CFG->dirroot.$handler->handlerfile)) {
        include_once($CFG->dirroot.$handler->handlerfile);

    } else {
        $errormessage = "Handler file of component $handler->handlermodule: $handler->handlerfile can not be found!";
        return false;
    }

    // checks for handler validity
    if (is_callable($function)) {
        return call_user_func($function, $eventdata);

    } else {
        $errormessage = "Handler function of component $handler->handlermodule: $handler->handlerfunction not callable function or class method!";
        return false;
    }
}

/**
 * given a queued handler, call the respective event handler to process the event
 * @param object qhandler - events_queued_handler object from db
 * @return boolean meaning success, or NULL on fatal failure
 *
 * INTERNAL - to be used from eventslib only
 */
function events_process_queued_handler($qhandler) {
    global $CFG;

    // get handler
    if (!$handler = get_record('events_handlers', 'id', $qhandler->handlerid)) {
        debugging("Error processing queue handler $qhandler->id, missing handler id: $qhandler->handlerid");
        //irrecoverable error, remove broken queue handler
        events_dequeue($qhandler);
        return NULL;
    }

    // get event object
    if (!$event = get_record('events_queue', 'id', $qhandler->queuedeventid)) {
        // can't proceed with no event object - might happen when two crons running at the same time
        debugging("Error processing queue handler $qhandler->id, missing event id: $qhandler->queuedeventid");
        //irrecoverable error, remove broken queue handler
        events_dequeue($qhandler);
        return NULL;
    }

    // call the function specified by the handler
    $errormessage = 'Unknown error';
    if (events_dispatch($handler, unserialize($event->eventdata), $errormessage)) {
        //everything ok
        events_dequeue($qhandler);
        return true;

    } else {
        //dispatching failed
        $qh = new object();
        $qh->id           = $qhandler->id;
        $qh->errormessage = addslashes($errormessage);
        $qh->timemodified = time();
        $qh->status       = $qhandler->status + 1;
        update_record('events_queue_handlers', $qh);
        return false;
    }
}

/**
 * removes this queued handler from the events_queued_handler table
 * removes events_queue record from events_queue if no more references to this event object exists
 * @param object qhandler - events_queued_handler object from db
 *
 * INTERNAL - to be used from eventslib only
 */
function events_dequeue($qhandler) {
    // first delete the queue handler
    delete_records('events_queue_handlers', 'id', $qhandler->id);

    // if no more queued handler is pointing to the same event - delete the event too
    if (!record_exists('events_queue_handlers', 'queuedeventid', $qhandler->queuedeventid)) {
        delete_records('events_queue', 'id', $qhandler->queuedeventid);
    }
}

/**
 * Returns hanflers for given event. Uses caching for better perf.
 * @param string $eventanme name of even or 'reset'
 * @return mixed array of handlers or false otherwise
 *
 * INTERNAL - to be used from eventslib only
 */
function events_get_handlers($eventname) {
    static $handlers = array();

    if ($eventname == 'reset') {
        $handlers = array();
        return false;
    }

    if (!array_key_exists($eventname, $handlers)) {
        $handlers[$eventname] = get_records('events_handlers', 'eventname', $eventname);
    }

    return $handlers[$eventname];
}

/****** Public events API starts here, do not use functions above in 3rd party code ******/


/**
 * Events cron will try to empty the events queue by processing all the queued events handlers
 * @param string eventname - empty means all
 * @return number of dispatched+removed broken events
 *
 * PUBLIC
 */
function events_cron($eventname='') {
    global $CFG;

    $failed = array();
    $processed = 0;

    if ($eventname) {
        $sql = "SELECT qh.* FROM {$CFG->prefix}events_queue_handlers qh, {$CFG->prefix}events_handlers h
                WHERE qh.handlerid = h.id AND h.eventname='$eventname'
                ORDER BY qh.id";
    } else {
        $sql = "SELECT * FROM {$CFG->prefix}events_queue_handlers
                ORDER BY id";
    }

    if ($rs = get_recordset_sql($sql)) {
        while ($qhandler = rs_fetch_next_record($rs)) {
            if (in_array($qhandler->handlerid, $failed)) {
                // do not try to dispatch any later events when one already failed
                continue;
            }
            $status = events_process_queued_handler($qhandler);
            if ($status === false) {
                $failed[] = $qhandler->handlerid;
            } else {
                $processed++;
            }
        }
        rs_close($rs);
    }
    return $processed;
}


/**
 * Function to call all eventhandlers when triggering an event
 * @param eventname - name of the event
 * @param eventdata - event data object (without magic quotes)
 * @return number of failed events
 *
 * PUBLIC
 */
function events_trigger($eventname, $eventdata) {
    global $CFG, $USER;

    $failedcount = 0; // number of failed events.
    $event = false;

    // pull out all registered event handlers
    if ($handlers = events_get_handlers($eventname)) {
        foreach ($handlers as $handler) {

           $errormessage = '';

           if ($handler->schedule == 'instant') {
                if ($handler->status) {
                    //check if previous pending events processed
                    if (!record_exists('events_queue_handlers', 'handlerid', $handler->id)) {
                        // ok, queue is empty, lets reset the status back to 0 == ok
                        $handler->status = 0;
                        set_field('events_handlers', 'status', 0, 'id', $handler->id);
                        // reset static handler cache
                        events_get_handlers('reset');
                    }
                }

                // dispatch the event only if instant schedule and status ok
                if (!$handler->status) {
                    $errormessage = 'Unknown error';;
                    if (events_dispatch($handler, $eventdata, $errormessage)) {
                        continue;
                    }
                    // set error count to 1 == send next instant into cron queue
                    set_field('events_handlers', 'status', 1, 'id', $handler->id);
                    // reset static handler cache
                    events_get_handlers('reset');

                } else {
                    // increment the error status counter
                    $handler->status++;
                    set_field('events_handlers', 'status', $handler->status, 'id', $handler->id);
                    // reset static handler cache
                    events_get_handlers('reset');
                }

                // update the failed counter
                $failedcount ++;

            } else if ($handler->schedule == 'cron') {
                //ok - use queuing of events only

            } else {
                // unknown schedule - fallback to cron type
                debugging("Unknown handler schedule type: $handler->schedule");
            }

            // if even type is not instant, or dispatch failed, queue it
            if ($event === false) {
                $event = new object();
                $event->userid      = $USER->id;
                $event->eventdata   = addslashes(serialize($eventdata));
                $event->timecreated = time();
                if (debugging()) {
                    $dump = '';
                    $callers = debug_backtrace();
                    foreach ($callers as $caller) {
                        $dump .= 'line ' . $caller['line'] . ' of ' . substr($caller['file'], strlen($CFG->dirroot) + 1);
                        if (isset($caller['function'])) {
                            $dump .= ': call to ';
                            if (isset($caller['class'])) {
                                $dump .= $caller['class'] . $caller['type'];
                            }
                            $dump .= $caller['function'] . '()';
                        }
                        $dump .= "\n";
                    }
                    $event->stackdump = addslashes($dump);
               } else {
                    $event->stackdump = '';
                }
                $event->id = insert_record('events_queue', $event);
            }
            events_queue_handler($handler, $event, $errormessage);
        }
    } else {
        //debugging("No handler found for event: $eventname");
    }

    return $failedcount;
}

/**
 * checks if an event is registered for this component
 * @param string eventname - name of the event
 * @param string component - component name, can be mod/data or moodle
 * @return bool
 *
 * PUBLIC
 */
function events_is_registered($eventname, $component) {
    return record_exists('events_handlers', 'handlermodule', $component, 'eventname', $eventname);
}

/**
 * checks if an event is queued for processing - either cron handlers attached or failed instant handlers
 * @param string eventname - name of the event
 * @return int number of queued events
 *
 * PUBLIC
 */
function events_pending_count($eventname) {
    global $CFG;

    $sql = "SELECT COUNT(*) FROM {$CFG->prefix}events_queue_handlers qh, {$CFG->prefix}events_handlers h
            WHERE qh.handlerid = h.id AND h.eventname='$eventname'";
    return count_records_sql($sql);
}
?>
