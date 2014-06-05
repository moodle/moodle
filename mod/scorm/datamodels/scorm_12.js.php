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

require_once($CFG->dirroot.'/mod/scorm/locallib.php');

if (isset($userdata->status)) {
    if ($userdata->status == '') {
        $userdata->entry = 'ab-initio';
    } else {
        if (isset($userdata->{'cmi.core.exit'}) && ($userdata->{'cmi.core.exit'} == 'suspend')) {
            $userdata->entry = 'resume';
        } else {
            $userdata->entry = '';
        }
    }
}
if (!isset($currentorg)) {
    $currentorg = '';
}

// If SCORM 1.2 standard mode is disabled allow higher datamodel limits.
if (intval(get_config("scorm", "scorm12standard"))) {
    $cmistring256 = '^[\\u0000-\\uFFFF]{0,255}$';
    $cmistring4096 = '^[\\u0000-\\uFFFF]{0,4096}$';
} else {
    $cmistring256 = '^[\\u0000-\\uFFFF]{0,64000}$';
    $cmistring4096 = $cmistring256;
}

// Set some vars to use as default values.
$def = array();
$def['cmi.core.student_id'] = $userdata->student_id;
$def['cmi.core.student_name'] = $userdata->student_name;
$def['cmi.core.credit'] = $userdata->credit;
$def['cmi.core.entry'] = $userdata->entry;
$def['cmi.core.lesson_mode'] = $userdata->mode;
$def['cmi.launch_data'] = scorm_isset($userdata, 'datafromlms');
$def['cmi.student_data.mastery_score'] = scorm_isset($userdata, 'masteryscore');
$def['cmi.student_data.max_time_allowed'] = scorm_isset($userdata, 'maxtimeallowed');
$def['cmi.student_data.time_limit_action'] = scorm_isset($userdata, 'timelimitaction');
$def['cmi.core.total_time'] = scorm_isset($userdata, 'cmi.core.total_time', '00:00:00');

// Now handle standard userdata items:
$def['cmi.core.lesson_location'] = scorm_isset($userdata, 'cmi.core.lesson_location');
$def['cmi.core.lesson_status'] = scorm_isset($userdata, 'cmi.core.lesson_status');
$def['cmi.core.score.raw'] = scorm_isset($userdata, 'cmi.core.score.raw');
$def['cmi.core.score.max'] = scorm_isset($userdata, 'cmi.core.score.max');
$def['cmi.core.score.min'] = scorm_isset($userdata, 'cmi.core.score.min');
$def['cmi.core.exit'] = scorm_isset($userdata, 'cmi.core.exit');
$def['cmi.suspend_data'] = scorm_isset($userdata, 'cmi.suspend_data');
$def['cmi.comments'] = scorm_isset($userdata, 'cmi.comments');
$def['cmi.student_preference.language'] = scorm_isset($userdata, 'cmi.student_preference.language');
$def['cmi.student_preference.audio'] = scorm_isset($userdata, 'cmi.student_preference.audio', '0');
$def['cmi.student_preference.speed'] = scorm_isset($userdata, 'cmi.student_preference.speed', '0');
$def['cmi.student_preference.text'] = scorm_isset($userdata, 'cmi.student_preference.text', '0');

echo js_writer::set_variable('def', $def);

echo js_writer::set_variable('cmistring256', $cmistring256);
echo js_writer::set_variable('cmistring4096', $cmistring4096);
echo js_writer::set_variable('scormdebugging', scorm_debugging($scorm));
echo js_writer::set_variable('scormauto', $scorm->auto);
echo js_writer::set_variable('scormid', $scorm->id);
echo js_writer::set_variable('cfgwwwroot', $CFG->wwwroot);
echo js_writer::set_variable('sesskey', sesskey());
echo js_writer::set_variable('scoid', $scoid);
echo js_writer::set_variable('attempt', $attempt);
echo js_writer::set_variable('viewmode', $mode);
echo js_writer::set_variable('cmid', $id);
echo js_writer::set_variable('currentorg', $currentorg);

 // reconstitute objectives
$cmiobj = scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.objectives', array('score'));
$cmiint = scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.interactions', array('objectives', 'correct_responses'));

echo js_writer::set_variable('cmiobj', $cmiobj);
echo js_writer::set_variable('cmiint', $cmiint);

// pull in the debugging utilities
if (scorm_debugging($scorm)) {
    include_once($CFG->dirroot.'/mod/scorm/datamodels/debug.js.php');
    echo 'AppendToLog("Moodle SCORM 1.2 API Loaded, Activity: '.$scorm->name.', SCO: '.$sco->identifier.'", 0);';
}