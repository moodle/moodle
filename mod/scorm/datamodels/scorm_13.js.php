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
    if (!isset($userdata->{'cmi.exit'}) || (($userdata->{'cmi.exit'} == 'time-out') || ($userdata->{'cmi.exit'} == 'normal'))) {
            $userdata->entry = 'ab-initio';
    } else {
        if (isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))) {
            $userdata->entry = 'resume';
        } else {
            $userdata->entry = '';
        }
    }
}
if (!isset($currentorg)) {
    $currentorg = '';
}

$def = array();
$def['cmi.learner_id'] = $userdata->student_id;
$def['cmi.learner_name'] = $userdata->student_name;
$def['cmi.mode'] = $userdata->mode;
$def['cmi.entry'] = $userdata->entry;
$def['cmi.exit'] = scorm_empty($userdata, 'cmi.exit');
$def['cmi.credit'] = scorm_empty($userdata, 'credit');
$def['cmi.completion_status'] = scorm_empty($userdata, 'cmi.completion_status', 'unknown');
$def['cmi.completion_threshold'] = scorm_empty($userdata, 'threshold', 'null', true);
$def['cmi.learner_preference.audio_level'] = scorm_empty($userdata, 'cmi.learner_preference.audio_level', '\'1\'', true);
$def['cmi.learner_preference.language'] = scorm_empty($userdata, 'cmi.learner_preference.language', '\'\'', true);
$def['cmi.learner_preference.delivery_speed'] = scorm_empty($userdata, 'cmi.learner_preference.delivery_speed', '\'1\'', true);
$def['cmi.learner_preference.audio_captioning'] = scorm_empty($userdata, 'cmi.learner_preference.audio_captioning', '\'0\'', true);
$def['cmi.location'] = scorm_empty($userdata, 'cmi.location', 'null', true);
$def['cmi.max_time_allowed'] = scorm_empty($userdata, 'attemptAbsoluteDurationLimit', 'null', true);
$def['cmi.progress_measure'] = scorm_empty($userdata, 'cmi.progress_measure', 'null', true);
$def['cmi.scaled_passing_score'] = scorm_empty($userdata, 'cmi.scaled_passing_score', 'null', true);
$def['cmi.score.scaled'] = scorm_empty($userdata, 'cmi.score.scaled', 'null', true);
$def['cmi.score.raw'] = scorm_empty($userdata, 'cmi.score.raw', 'null', true);
$def['cmi.score.min'] = scorm_empty($userdata, 'cmi.score.min', 'null', true);
$def['cmi.score.max'] = scorm_empty($userdata, 'cmi.score.max', 'null', true);
$def['cmi.success_status'] = scorm_empty($userdata, 'cmi.success_status', 'unknown');
$def['cmi.suspend_data'] = scorm_empty($userdata, 'cmi.suspend_data', 'null', true);
$def['cmi.time_limit_action'] = scorm_empty($userdata, 'timelimitaction', 'null', true);
$def['cmi.total_time'] = scorm_empty($userdata, 'cmi.total_time', 'PT0H0M0S');

echo js_writer::set_variable('def', $def);

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

// reconstitute objectives, comments_from_learner and comments_from_lms
$cmiobj = scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.objectives', array('score'));
$cmiint = scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.interactions', array('objectives', 'correct_responses'));
$cmicommentsuser = scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.comments_from_learner', array());
$cmicommentslms = scorm_reconstitute_array_element($scorm->version, $userdata, 'cmi.comments_from_lms', array());

echo js_writer::set_variable('cmiobj', $cmiobj);
echo js_writer::set_variable('cmiint', $cmiint);
echo js_writer::set_variable('cmicommentsuser', $cmicommentsuser);
echo js_writer::set_variable('cmicommentslms', $cmicommentslms);

// pull in the debugging utilities
if (scorm_debugging($scorm)) {
    include_once($CFG->dirroot.'/mod/scorm/datamodels/debug.js.php');
    echo 'AppendToLog("Moodle SCORM 1.3 API Loaded, Activity: '.$scorm->name.', SCO: '.$sco->identifier.'", 0);';
}
