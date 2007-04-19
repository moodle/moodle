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
        $varprefix = 'moodle';
    } else {
        $compparts = explode('/', $component);

        if ($compparts[0] == 'block') {
            // Blocks are an exception. Blocks directory is 'blocks', and not
            // 'block'. So we need to jump through hoops.
            $defpath = $CFG->dirroot.'/'.$compparts[0].
                                's/'.$compparts[1].'/db/events.php';
            $varprefix = $compparts[0].'_'.$compparts[1];
        } else if ($compparts[0] == 'format') {
            // Similar to the above, course formats are 'format' while they 
            // are stored in 'course/format'.
            $defpath = $CFG->dirroot.'/course/'.$component.'/db/events.php';
            $varprefix = $compparts[0].'_'.$compparts[1];
        } else {
            $defpath = $CFG->dirroot.'/'.$component.'/db/events.php';
            $varprefix = str_replace('/', '_', $component);
        }
    }
    $events = array();

    if (file_exists($defpath)) {
        require($defpath);
        $events = ${$varprefix.'_events'};
    }
    
    return $events;
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
            unset($storedevent->id);
            unset($storedevent->eventname);
            $cachedevents[$eventname] = (array)$storedevent;
        }
        return $cachedevents;
    }
}


/**
 * Updates the capabilities table with the component capability definitions.
 * If no parameters are given, the function updates the core moodle
 * capabilities.
 *
 * Note that the absence of the db/access.php capabilities definition file
 * will cause any stored capabilities for the component to be removed from
 * the database.
 *
 * @param $component - examples: 'moodle', 'mod/forum', 'block/quiz_results'
 * @return boolean
 */

function events_update_definition($component='moodle') {

    $storedevents = array();

    $fileevents = events_load_def($component);
    // load event definitions from db tables
    // if we detect an event being already stored, we discard from this array later
    // the remaining needs to be removed
    
    $cachedevents = get_cached_events($component);
    
    /// compare the 2 arrays, and make adjustments
    /// array_udiff is php 5 only =(
    
    if ($fileevents) {
        foreach ($fileevents as $eventname => $fileevent) {
            if (!empty($cachedevents[$eventname])) {
                if ($cachedevents[$eventname] == $fileevent) {
                    unset($cachedevents[$eventname]);  
                    continue; // breaks the cachedevents loop                
                }
            }
            // if we are here, no break is called, file event is new
            $event = new object;
            $event->eventname =  $eventname;
            $event->handlermodule = $component;
            $event->handlerfile = $fileevent['handlerfile'];
            $event->handlerfunction = $fileevent['handlerfunction'];            
            insert_record('events_handlers', $event);      
        }
    }
    
    // clean up the left overs
    // delete from db
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

?>