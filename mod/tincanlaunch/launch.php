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
 * This launches the experience with the requested registration.
 *
 * @package mod_tincanlaunch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_tincanlaunch;

require(__DIR__ . '/../../config.php');
require_once('header.php');
require_login();

// Trigger Activity launched event.
$event = \mod_tincanlaunch\event\activity_launched::create(array(
    'objectid' => $tincanlaunch->id,
    'context' => $context,
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('tincanlaunch', $tincanlaunch);
$event->trigger();

// Get the registration id.
$registrationid = required_param('launchform_registration', PARAM_TEXT);

if (empty($registrationid)) {
    echo $OUTPUT->notification(get_string('tincanlaunch_regidempty', 'tincanlaunch'), 'error');
    debugging("Error attempting to get registration id querystring parameter.", DEBUG_DEVELOPER);
    die();
}

// Get record(s) of registration(s) from the LRS state API.
$getregistrationdatafromlrsstate = tincanlaunch_get_global_parameters_and_get_state(
    "http://tincanapi.co.uk/stateapikeys/registrations"
);

$lrsrespond = $getregistrationdatafromlrsstate->httpResponse['status'];
// Failed to connect to LRS.
if ($lrsrespond != 200 && $lrsrespond != 404) {
    echo $OUTPUT->notification(get_string('tincanlaunch_notavailable', 'tincanlaunch'), 'error');
    debugging("<p>Error attempting to get registration data from State API.</p><pre>" .
        var_dump($getregistrationdatafromlrsstate) . "</pre>", DEBUG_DEVELOPER);
    die();
}
if ($lrsrespond == 200) {
    $registrationdata = json_decode($getregistrationdatafromlrsstate->content->getContent(), true);
} else {
    $registrationdata = null;
}
$registrationdataetag = $getregistrationdatafromlrsstate->content->getEtag();

$datenow = date("c");

$registrationdataforthisattempt = array(
    $registrationid => array(
        "created" => $datenow,
        "lastlaunched" => $datenow
    )
);

// If registrationdata is null (could be from 404 above) create a new registration data array.
if (is_null($registrationdata)) {
    $registrationdata = $registrationdataforthisattempt;
} else if (array_key_exists($registrationid, $registrationdata)) {
    // Else if the registration exists update the lastlaunched date.
    $registrationdata[$registrationid]["lastlaunched"] = $datenow;
} else { // Push the new data on the end.
    $registrationdata[$registrationid] = $registrationdataforthisattempt[$registrationid];
}

// Sort the registration data by last launched (most recent first).
uasort($registrationdata, function ($a, $b) {
    return strtotime($b['lastlaunched']) - strtotime($a['lastlaunched']);
});

// TODO: Currently this is re-PUTting all of the data - it may be better just to POST the new data.
// This will prevent us sorting, but sorting could be done on output.
$saveresgistrationdata = tincanlaunch_get_global_parameters_and_save_state(
    $registrationdata,
    "http://tincanapi.co.uk/stateapikeys/registrations",
    $registrationdataetag
);
$lrsrespond = $saveresgistrationdata->httpResponse['status'];
// Failed to connect to LRS.
if ($lrsrespond != 204) {
    echo $OUTPUT->notification(get_string('tincanlaunch_notavailable', 'tincanlaunch'), 'error');
    debugging("<p>Error attempting to set registration data to State API.</p><pre>" .
        var_dump($saveresgistrationdata) . "</pre>", DEBUG_DEVELOPER);
    die();
}

// Compile user data to send to agent profile.
$agentprofiles['CMI5LearnerPreferences'] = ["languagePreference" => tincanlaunch_get_moodle_language()];

// Check if there are any profile fields needing to be synced.
$profilefields = explode(',', get_config('tincanlaunch', 'profilefields'));
if (count($profilefields) > 0) {
    $agentprofiles['LMSUserFields'] = [];
    foreach ($profilefields as $profilefield) {
        $profilefield = strtolower($profilefield);
        // Lookup profile field value.
        if (array_key_exists($profilefield, $USER->profile)) {
            $agentprofiles['LMSUserFields'] = $agentprofiles['LMSUserFields'] +
                [$profilefield => $USER->profile[$profilefield]];
        }
    }
}

foreach ($agentprofiles as $key => $value) {
    $saveagentprofile = tincanlaunch_get_global_parameters_and_save_agentprofile($key, $value);

    $lrsrespond = $saveagentprofile->httpResponse['status'];
    if ($lrsrespond != 204) {
        // Failed to connect to LRS.
        echo $OUTPUT->notification(get_string('tincanlaunch_notavailable', 'tincanlaunch'), 'error');
        debugging("<p>Error attempting to set learner preferences (" . key($agentprofile) .
            ") to Agent Profile API.</p><pre>" . var_dump($saveagentprofile) . "</pre>", DEBUG_DEVELOPER);
        die();
    }

}

// Send launched statement.
$savelaunchedstatement = tincan_launched_statement($registrationid);

$lrsrespond = $savelaunchedstatement->httpResponse['status'];
if ($lrsrespond != 204) {
    // Failed to connect to LRS.
    echo $OUTPUT->notification(get_string('tincanlaunch_notavailable', 'tincanlaunch'), 'error');
    debugging("<p>Error attempting to send 'launched' statement.</p><pre>" .
        var_dump($savelaunchedstatement) . "</pre>", DEBUG_DEVELOPER);
    die();
}

// Set completion for module_viewed.
$completion = new \completion_info($course);
$completion->set_module_viewed($cm);

// Launch the experience.
header("Location: " . tincanlaunch_get_launch_url($registrationid));

exit;
