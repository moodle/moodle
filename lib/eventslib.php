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
 * Library of functions for events manipulation.
 *
 * The public API is all at the end of this file.
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   moodlecore
 */

/**
 * Loads the events definitions for the component (from file). If no
 * events are defined for the component, we simply return an empty array.
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of capabilities or empty array if not exists
 */
function events_load_def($component) {
    $defpath = get_component_directory($component).'/db/events.php';

    $handlers = array();

    if (file_exists($defpath)) {
        require($defpath);
    }

    return $handlers;
}

/**
 * Gets the capabilities that have been cached in the database for this
 * component.
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return array of events
 */
function events_get_cached($component) {
    global $DB;

    $cachedhandlers = array();

    if ($storedhandlers = $DB->get_records('events_handlers', array('handlermodule'=>$component))) {
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
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return boolean
 */
function events_update_definition($component='moodle') {
    global $DB;

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

                $DB->update_record('events_handlers', $handler);

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

            $DB->insert_record('events_handlers', $handler);
        }
    }

    // clean up the left overs, the entries in cached events array at this points are deprecated event handlers
    // and should be removed, delete from db
    events_cleanup($component, $cachedhandlers);

    return true;
}

/**
 * Remove all event handlers and queued events
 *
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 */
function events_uninstall($component) {
    $cachedhandlers = events_get_cached($component);
    events_cleanup($component, $cachedhandlers);
}

/**
 * Deletes cached events that are no longer needed by the component.
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @param array $cachedhandlers array of the cached events definitions that will be
 * @return int number of deprecated capabilities that have been removed
 */
function events_cleanup($component, $cachedhandlers) {
    global $DB;

    $deletecount = 0;
    foreach ($cachedhandlers as $eventname => $cachedhandler) {
        if ($qhandlers = $DB->get_records('events_queue_handlers', array('handlerid'=>$cachedhandler['id']))) {
            debugging("Removing pending events from queue before deleting of event handler: $component - $eventname");
            foreach ($qhandlers as $qhandler) {
                events_dequeue($qhandler);
            }
        }
        if ($DB->delete_records('events_handlers', array('eventname'=>$eventname, 'handlermodule'=>$component))) {
            $deletecount++;
        }
    }
    return $deletecount;
}

/****************** End of Events handler Definition code *******************/

/**
 * puts a handler on queue
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param object $handler event handler object from db
 * @param object $event event data object
 * @param string $errormessage The error message indicating the problem
 * @return id number of new queue handler
 */
function events_queue_handler($handler, $event, $errormessage) {
    global $DB;

    if ($qhandler = $DB->get_record('events_queue_handlers', array('queuedeventid'=>$event->id, 'handlerid'=>$handler->id))) {
        debugging("Please check code: Event id $event->id is already queued in handler id $qhandler->id");
        return $qhandler->id;
    }

    // make a new queue handler
    $qhandler = new object();
    $qhandler->queuedeventid  = $event->id;
    $qhandler->handlerid      = $handler->id;
    $qhandler->errormessage   = $errormessage;
    $qhandler->timemodified   = time();
    if ($handler->schedule == 'instant' and $handler->status == 1) {
        $qhandler->status     = 1; //already one failed attempt to dispatch this event
    } else {
        $qhandler->status     = 0;
    }

    return $DB->insert_record('events_queue_handlers', $qhandler);
}

/**
 * trigger a single event with a specified handler
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param handler $hander object from db
 * @param eventdata $eventdata dataobject
 * @param string $errormessage error message indicating problem
 * @return bool success or fail
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
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param object $qhandler events_queued_handler object from db
 * @return boolean meaning success, or NULL on fatal failure
 */
function events_process_queued_handler($qhandler) {
    global $CFG, $DB;

    // get handler
    if (!$handler = $DB->get_record('events_handlers', array('id'=>$qhandler->handlerid))) {
        debugging("Error processing queue handler $qhandler->id, missing handler id: $qhandler->handlerid");
        //irrecoverable error, remove broken queue handler
        events_dequeue($qhandler);
        return NULL;
    }

    // get event object
    if (!$event = $DB->get_record('events_queue', array('id'=>$qhandler->queuedeventid))) {
        // can't proceed with no event object - might happen when two crons running at the same time
        debugging("Error processing queue handler $qhandler->id, missing event id: $qhandler->queuedeventid");
        //irrecoverable error, remove broken queue handler
        events_dequeue($qhandler);
        return NULL;
    }

    // call the function specified by the handler
    $errormessage = 'Unknown error';
    if (events_dispatch($handler, unserialize(base64_decode($event->eventdata)), $errormessage)) {
        //everything ok
        events_dequeue($qhandler);
        return true;

    } else {
        //dispatching failed
        $qh = new object();
        $qh->id           = $qhandler->id;
        $qh->errormessage = $errormessage;
        $qh->timemodified = time();
        $qh->status       = $qhandler->status + 1;
        $DB->update_record('events_queue_handlers', $qh);
        return false;
    }
}

/**
 * Removes this queued handler from the events_queued_handler table
 *
 * Removes events_queue record from events_queue if no more references to this event object exists
 *
 * INTERNAL - to be used from eventslib only
 *
 * @param object $qhandler events_queued_handler object from db
 */
function events_dequeue($qhandler) {
    global $DB;

    // first delete the queue handler
    $DB->delete_records('events_queue_handlers', array('id'=>$qhandler->id));

    // if no more queued handler is pointing to the same event - delete the event too
    if (!$DB->record_exists('events_queue_handlers', array('queuedeventid'=>$qhandler->queuedeventid))) {
        $DB->delete_records('events_queue', array('id'=>$qhandler->queuedeventid));
    }
}

/**
 * Returns handlers for given event. Uses caching for better perf.
 *
 * INTERNAL - to be used from eventslib only
 *
 * @staticvar array $handlers
 * @param string $eventanme name of even or 'reset'
 * @return mixed array of handlers or false otherwise
 */
function events_get_handlers($eventname) {
    global $DB;
    static $handlers = array();

    if ($eventname == 'reset') {
        $handlers = array();
        return false;
    }

    if (!array_key_exists($eventname, $handlers)) {
        $handlers[$eventname] = $DB->get_records('events_handlers', array('eventname'=>$eventname));
    }

    return $handlers[$eventname];
}

/****** Public events API starts here, do not use functions above in 3rd party code ******/


/**
 * Events cron will try to empty the events queue by processing all the queued events handlers
 *
 * PUBLIC
 *
 * @param string $eventname empty means all
 * @return number of dispatched+removed broken events
 */
function events_cron($eventname='') {
    global $DB;

    $failed = array();
    $processed = 0;

    if ($eventname) {
        $sql = "SELECT qh.*
                  FROM {events_queue_handlers} qh, {events_handlers} h
                 WHERE qh.handlerid = h.id AND h.eventname=?
              ORDER BY qh.id";
        $params = array($eventname);
    } else {
        $sql = "SELECT *
                  FROM {events_queue_handlers}
              ORDER BY id";
        $params = array();
    }

    if ($rs = $DB->get_recordset_sql($sql, $params)) {
        foreach ($rs as $qhandler) {
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
        $rs->close();
    }
    return $processed;
}


/**
 * Function to call all event handlers when triggering an event
 *
 * PUBLIC
 *
 * @param string $eventname name of the event
 * @param object $eventdata event data object
 * @return int number of failed events
 */
function events_trigger($eventname, $eventdata) {
    global $CFG, $USER, $DB;

    $failedcount = 0; // number of failed events.
    $event = false;

    // pull out all registered event handlers
    if ($handlers = events_get_handlers($eventname)) {
        foreach ($handlers as $handler) {

           $errormessage = '';

           if ($handler->schedule == 'instant' and $DB->is_transaction_started()) {
                // Instant events can not be rolled back in DB transactions,
                // we need to send them to queue instead - this is slow but necessary.
                // It could be improved in future by adding internal/external flag to each handler.

                $DB->set_field('events_handlers', 'status', ($handler->status+1), array('id'=>$handler->id));
                // reset static handler cache
                events_get_handlers('reset');

           } else if ($handler->schedule == 'instant') {
                if ($handler->status) {
                    //check if previous pending events processed
                    if (!$DB->record_exists('events_queue_handlers', array('handlerid'=>$handler->id))) {
                        // ok, queue is empty, lets reset the status back to 0 == ok
                        $handler->status = 0;
                        $DB->set_field('events_handlers', 'status', 0, array('id'=>$handler->id));
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
                    $DB->set_field('events_handlers', 'status', 1, array('id'=>$handler->id));
                    // reset static handler cache
                    events_get_handlers('reset');

                } else {
                    // increment the error status counter
                    $handler->status++;
                    $DB->set_field('events_handlers', 'status', $handler->status, array('id'=>$handler->id));
                    // reset static handler cache
                    events_get_handlers('reset');
                }

                // update the failed counter
                $failedcount ++;

            } else if ($handler->schedule == 'cron') {
                //ok - use queueing of events only

            } else {
                // unknown schedule - fallback to cron type
                debugging("Unknown handler schedule type: $handler->schedule");
            }

            // if even type is not instant, or dispatch failed, queue it
            if ($event === false) {
                $event = new object();
                $event->userid      = $USER->id;
                $event->eventdata   = base64_encode(serialize($eventdata));
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
                    $event->stackdump = $dump;
               } else {
                    $event->stackdump = '';
                }
                $event->id = $DB->insert_record('events_queue', $event);
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
 *
 * @param string $eventname name of the event
 * @param string $component component name, can be mod/data or moodle
 * @return bool
 */
function events_is_registered($eventname, $component) {
    global $DB;
    return $DB->record_exists('events_handlers', array('handlermodule'=>$component, 'eventname'=>$eventname));
}

/**
 * checks if an event is queued for processing - either cron handlers attached or failed instant handlers
 *
 * PUBLIC
 *
 * @param string $eventname name of the event
 * @return int number of queued events
 */
function events_pending_count($eventname) {
    global $CFG, $DB;

    $sql = "SELECT COUNT('x')
              FROM {events_queue_handlers} qh, {events_handlers} h
             WHERE qh.handlerid = h.id AND h.eventname=?";
    return $DB->count_records_sql($sql, array($eventname));
}
