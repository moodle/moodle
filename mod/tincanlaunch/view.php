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
 * Displays an instance of tincanlaunch.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tincanlaunch;

require_once("../../config.php");
require('header.php');
require_login();

// Trigger module viewed event.
$event = \mod_tincanlaunch\event\course_module_viewed::create(array(
    'objectid' => $tincanlaunch->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('tincanlaunch', $tincanlaunch);
$event->add_record_snapshot('course_modules', $cm);
$event->trigger();

// Print the page header.
$PAGE->set_url('/mod/tincanlaunch/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($tincanlaunch->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
echo $OUTPUT->header();

// Display the completion requirements.
$cminfo = \cm_info::create($cm);
$completiondetails = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id); // Fetch completion information.
$activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id); // Fetch activity dates.

echo $OUTPUT->activity_information($cminfo, $completiondetails, $activitydates);

if ($tincanlaunch->intro) { // Conditions to show the intro can change to look for own settings.
    echo $OUTPUT->box(
        format_module_intro('tincanlaunch', $tincanlaunch, $cm->id),
        'generalbox mod_introbox',
        'tincanlaunchintro'
    );
}

$getregistrationdatafromlrsstate = tincanlaunch_get_global_parameters_and_get_state(
    "http://tincanapi.co.uk/stateapikeys/registrations"
);

$statuscode = $getregistrationdatafromlrsstate->httpResponse['status'];

// Generate a registration id for any new attempt.
$tincanphputil = new \TinCan\Util();
$newregistrationid = $tincanphputil->getUUID();

// Evaluate the LRS status code.
if ($statuscode != 200 && $statuscode != 404) { // Some error other than 404.
    echo $OUTPUT->notification(get_string('tincanlaunch_notavailable', 'tincanlaunch'), 'error');
    debugging("<p>Error attempting to get registration data from State API.</p><pre>" .
        var_dump($getregistrationdatafromlrsstate) . "</pre>", DEBUG_DEVELOPER);
    die();
} else if ($statuscode == 200) { // Registration data found on LRS.
    $registrationdatafromlrs = json_decode($getregistrationdatafromlrsstate->content->getContent(), true);
    $simplifiedregid = '';

    foreach ($registrationdatafromlrs as $key => $item) {

        if (!is_array($registrationdatafromlrs[$key])) {
            $reason = "Excepted array, found " . $registrationdatafromlrs[$key];
            throw new \moodle_exception($reason, 'tincanlaunch', '', $warnings[$reason]);
        }

        array_push(
            $registrationdatafromlrs[$key],
            "<a id='tincanrelaunch_attempt-".$key."'>"
            . get_string('tincanlaunchviewlaunchlink', 'tincanlaunch') . "</a>"
        );

        $registrationdatafromlrs[$key]['created'] = date_format(
            date_create($registrationdatafromlrs[$key]['created']),
            'D, d M Y H:i:s'
        );
        $registrationdatafromlrs[$key]['lastlaunched'] = date_format(
            date_create($registrationdatafromlrs[$key]['lastlaunched']),
            'D, d M Y H:i:s'
        );

        // For single registration, select the the most recent.
        if ($tincanlaunch->tincanmultipleregs == 0) {
            $simplifiedregid = $key;
            $registrationdatafromlrs = array($registrationdatafromlrs[$key]);
            break;
        }
    }

    // Classic launch navigation.
    if ($tincanlaunch->tincansimplelaunchnav == 0) {
        $table = new \html_table();
        $table->id = 'tincanlaunch_attempttable';

        $table->caption = get_string('modulenameplural', 'tincanlaunch');
        $table->head = array(
            get_string('tincanlaunchviewfirstlaunched', 'tincanlaunch'),
            get_string('tincanlaunchviewlastlaunched', 'tincanlaunch'),
            get_string('tincanlaunchviewlaunchlinkheader', 'tincanlaunch')
        );

        $table->data = $registrationdatafromlrs;
        echo \html_writer::table($table);

        // Multiple registrations for standard launch navigation - Display new registration attempt link.
        if ($tincanlaunch->tincanmultipleregs == 1) {
            echo '<div id=tincanlaunch_newattempt><a class="btn btn-primary" id=tincanlaunch_newattemptlink-'.
                $newregistrationid .'>'. get_string('tincanlaunch_attempt', 'tincanlaunch') .'</a></div>';
        }
    } else { // Simplified Navigation
        // Utilize the simplified registration ID.
        echo "<div id=tincanlaunch_simplified><a id=tincanlaunch_simplifiedlink-" . $simplifiedregid . ">" . "</a></div>";
    }
} else { // No registration data on LRS - LRS will return 404 status - {"errorId": "0c621409...","message": "No State found"}.
    if ($tincanlaunch->tincansimplelaunchnav == 1) {
        echo "<div id=tincanlaunch_simplified><a id=tincanlaunch_simplifiedlink-" . $newregistrationid . ">" . "</a></div>";
    } else {
        echo '<div id=tincanlaunch_newattempt><a class="btn btn-primary" id=tincanlaunch_newattemptlink-'. $newregistrationid .'>'.
        get_string('tincanlaunch_attempt', 'tincanlaunch') .'</a></div>';
    }
}

// Add status placeholder.
echo "<div id='tincanlaunch_status'></div>";

// New AMD module.
$courseid = $tincanlaunch->course;
$PAGE->requires->js_call_amd('mod_tincanlaunch/launch', 'init', [$courseid]);

echo $OUTPUT->footer();
