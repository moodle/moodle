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
 * Event developer detail.
 *
 * @package   report_eventlist
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Required parameters.
$eventname = required_param('eventname', PARAM_RAW);

admin_externalpage_setup('reporteventlists');

// Retrieve all events in a list.
$completelist = report_eventlist_list_generator::get_all_events_list(false);

// Check that $eventname is a valid event.
if (!array_key_exists($eventname, $completelist)) {
    print_error('errorinvalidevent', 'report_eventlist');
}

// Break up the full event name to usable parts.
$component = explode('\\', $eventname);
$directory = core_component::get_component_directory($component[1]);

// File and directory information.
$directory = $directory . '/classes/event';
// Verify that the directory is valid.
if (!is_dir($directory)) {
    print_error('errorinvaliddirectory', 'report_eventlist');
}
$filename = end($component);
$eventfiles = $directory . '/' . $filename . '.php';
$title = $eventname::get_name();

// Define event information.
$eventinformation = array('title' => $title);
$eventcontents = file_get_contents($eventfiles);
$eventinformation['filecontents'] = $eventcontents;

$ref = new \ReflectionClass($eventname);
$eventinformation['explanation'] = $eventname::get_explanation($eventname);
// Get event information nicely if we can.
if (!$ref->isAbstract()) {
    $eventinformation = array_merge($eventinformation, $eventname::get_static_info());
    $eventinformation['legacyevent'] = $eventname::get_legacy_eventname();
    $eventinformation['crud'] = report_eventlist_list_generator::get_crud_string($eventinformation['crud']);
    $eventinformation['edulevel'] = report_eventlist_list_generator::get_edulevel_string($eventinformation['edulevel']);
} else {
    $eventinformation['abstract'] = true;
    if ($eventname != '\core\event\base') {
        // No choice but to get information the hard way.
        // Strip out CRUD information.
        $crudpattern = "/(\['crud'\]\s=\s')(\w)/";
        $result = array();
        preg_match($crudpattern, $eventcontents, $result);
        if (!empty($result[2])) {
            $eventinformation['crud'] = report_eventlist_list_generator::get_crud_string($result[2]);
        }

        // Strip out edulevel information.
        $edulevelpattern = "/(\['edulevel'\]\s=\sself\:\:)(\w*)/";
        $result = array();
        preg_match($edulevelpattern, $eventcontents, $result);
        if (!empty($result[2])) {
            $educationlevel = constant('\core\event\base::' . $result[2]);
            $eventinformation['edulevel'] = report_eventlist_list_generator::get_edulevel_string($educationlevel);
        }

        // Retrieve object table information.
        $affectedtablepattern = "/(\['objecttable'\]\s=\s')(\w*)/";
        $result = array();
        preg_match($affectedtablepattern, $eventcontents, $result);
        if (!empty($result[2])) {
            $eventinformation['objecttable'] = $result[2];
        }
    }
}

// I can't think of a nice way to get the following information.
// Searching to see if @type has been used for the 'other' field in the event.
$othertypepattern = "/(@type\s([\w|\s|.]*))+/";
$typeparams = array();
preg_match_all($othertypepattern, $eventcontents, $typeparams);
if (!empty($typeparams[2])) {
    $eventinformation['typeparameter'] = array();
    foreach ($typeparams[2] as $typeparameter) {
        $eventinformation['typeparameter'][] = $typeparameter;
    }
}

// Retrieving the 'other' event field information.
$otherpattern = "/(\*\s{5}-([\w|\s]*\:[\w|\s|\(|\)|.]*))/";
$typeparams = array();
preg_match_all($otherpattern, $eventcontents, $typeparams);
if (!empty($typeparams[2])) {
    $eventinformation['otherparameter'] = array();
    foreach ($typeparams[2] as $typeparameter) {
        $eventinformation['otherparameter'][] = $typeparameter;
    }
}

// Get parent class information.
if ($parentclass = get_parent_class($eventname)) {
    $eventinformation['parentclass'] = '\\' . $parentclass;
}

// Fetch all the observers to be matched with this event.
$allobserverslist = report_eventlist_list_generator::get_observer_list();
$observers = array();

if (isset($allobserverslist['\\core\\event\\base'])) {
    $observers = $allobserverslist['\\core\\event\\base'];
}
if (isset($allobserverslist[$eventname])) {
    $observers = array_merge($observers, $allobserverslist[$eventname]);
}

// OUTPUT.
$renderer = $PAGE->get_renderer('report_eventlist');
echo $renderer->render_event_detail($observers, $eventinformation);

