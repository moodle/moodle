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
 * @package core
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Gets the capabilities that have been cached in the database for this
 * component.
 *
 * @access protected To be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return array of events
 */
function events_get_cached($component) {
    global $DB;

    $cachedhandlers = array();

    if ($storedhandlers = $DB->get_records('events_handlers', array('component'=>$component))) {
        foreach ($storedhandlers as $handler) {
            $cachedhandlers[$handler->eventname] = array (
                'id'              => $handler->id,
                'handlerfile'     => $handler->handlerfile,
                'handlerfunction' => $handler->handlerfunction,
                'schedule'        => $handler->schedule,
                'internal'        => $handler->internal);
        }
    }

    return $cachedhandlers;
}

/**
 * Remove all event handlers and queued events
 *
 * @category event
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 */
function events_uninstall($component) {
    $cachedhandlers = events_get_cached($component);
    events_cleanup($component, $cachedhandlers);

    events_get_handlers('reset');
}

/**
 * Deletes cached events that are no longer needed by the component.
 *
 * @access protected To be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @param array $cachedhandlers array of the cached events definitions that will be
 * @return int number of unused handlers that have been removed
 */
function events_cleanup($component, $cachedhandlers) {
    global $DB;

    $deletecount = 0;
    foreach ($cachedhandlers as $eventname => $cachedhandler) {
        if ($qhandlers = $DB->get_records('events_queue_handlers', array('handlerid'=>$cachedhandler['id']))) {
            //debugging("Removing pending events from queue before deleting of event handler: $component - $eventname");
            foreach ($qhandlers as $qhandler) {
                events_dequeue($qhandler);
            }
        }
        $DB->delete_records('events_handlers', array('eventname'=>$eventname, 'component'=>$component));
        $deletecount++;
    }

    return $deletecount;
}

/**
 * Removes this queued handler from the events_queued_handler table
 *
 * Removes events_queue record from events_queue if no more references to this event object exists
 *
 * @access protected To be used from eventslib only
 *
 * @param stdClass $qhandler A row from the events_queued_handler table
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
 * @access protected To be used from eventslib only
 *
 * @staticvar array $handlers
 * @param string $eventname name of event or 'reset'
 * @return array|false array of handlers or false otherwise
 */
function events_get_handlers($eventname) {
    global $DB;
    static $handlers = array();

    if ($eventname === 'reset') {
        $handlers = array();
        return false;
    }

    if (!array_key_exists($eventname, $handlers)) {
        $handlers[$eventname] = $DB->get_records('events_handlers', array('eventname'=>$eventname));
    }

    return $handlers[$eventname];
}
