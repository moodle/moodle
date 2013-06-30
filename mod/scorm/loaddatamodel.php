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
 * This page loads the correct JS file based on package type
 *
 * @package mod_scorm
 * @copyright 1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once('locallib.php');

$id = optional_param('id', 0, PARAM_INT);       // Course Module ID, or
$a = optional_param('a', 0, PARAM_INT);         // scorm ID.
$scoid = required_param('scoid', PARAM_INT);     // sco ID.
$mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode.
$currentorg = optional_param('currentorg', '', PARAM_RAW); // Selected organization.
$attempt = required_param('attempt', PARAM_INT); // new attempt.

if (!empty($id)) {
    $cm = get_coursemodule_from_id('scorm', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $scorm = $DB->get_record('scorm', array('id'=>$cm->instance), '*', MUST_EXIST);
} else if (!empty($a)) {
    $scorm = $DB->get_record('scorm', array('id'=>$a), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$scorm->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id, false, MUST_EXIST);
} else {
    print_error('missingparameter');
}

$PAGE->set_url('/mod/scorm/loaddatamodel.php', array('scoid'=>$scoid, 'id'=>$cm->id));

require_login($course, false, $cm);

$userdata = new stdClass();
if ($usertrack = scorm_get_tracks($scoid, $USER->id, $attempt)) {
    // According to SCORM 2004 spec(RTE V1, 4.2.8), only cmi.exit==suspend should allow previous datamodel elements on re-launch.
    if (!scorm_version_check($scorm->version, SCORM_13) ||
        (isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} == 'suspend'))) {
        foreach ($usertrack as $key => $value) {
            $userdata->$key = addslashes_js($value);
        }
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }
} else {
    $userdata->status = '';
    $userdata->score_raw = '';
}
$userdata->student_id = addslashes_js($USER->username);
$userdata->student_name = addslashes_js($USER->lastname .', '. $USER->firstname);
$userdata->mode = 'normal';
if (!empty($mode)) {
    $userdata->mode = $mode;
}
if ($userdata->mode == 'normal') {
    $userdata->credit = 'credit';
} else {
    $userdata->credit = 'no-credit';
}
if ($scodatas = scorm_get_sco($scoid, SCO_DATA)) {
    foreach ($scodatas as $key => $value) {
        $userdata->$key = addslashes_js($value);
    }
} else {
    print_error('cannotfindsco', 'scorm');
}
if (!$sco = scorm_get_sco($scoid)) {
    print_error('cannotfindsco', 'scorm');
}
if (scorm_version_check($scorm->version, SCORM_13)) {
    $objectives = $DB->get_records('scorm_seq_objective', array('scoid' => $scoid));
    $index = 0;
    foreach ($objectives as $objective) {
        if (!empty($objective->minnormalizedmeasure)) {
            $userdata->{'cmi.scaled_passing_score'} = $objective->minnormalizedmeasure;
        }
        if (!empty($objective->objectiveid)) {
            $userdata->{'cmi.objectives.N'.$index.'.id'} = $objective->objectiveid;
            $index++;
        }
    }
}

header('Content-Type: text/javascript; charset=UTF-8');

$scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe.
if (file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php')) {
    include_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php');
} else {
    include_once($CFG->dirroot.'/mod/scorm/datamodels/scorm_12.js.php');
}
// Set the start time of this SCO.
scorm_insert_track($USER->id, $scorm->id, $scoid, $attempt, 'x.start.time', time());
?>


var errorCode = "0";
function underscore(str) {
    str = String(str).replace(/.N/g,".");
    return str.replace(/\./g,"__");
}
