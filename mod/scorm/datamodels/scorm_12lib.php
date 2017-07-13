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
 * Sets up $userdata array and default values for SCORM 1.2 .
 *
 * @param stdClass $userdata an empty stdClass variable that should be set up with user values
 * @param object $scorm package record
 * @param string $scoid SCO Id
 * @param string $attempt attempt number for the user
 * @param string $mode scorm display mode type
 * @return array The default values that should be used for SCORM 1.2 package
 */
function get_scorm_default (&$userdata, $scorm, $scoid, $attempt, $mode) {
    global $USER;

    $userdata->student_id = $USER->username;
    $userdata->student_name = $USER->lastname .', '. $USER->firstname;

    if ($usertrack = scorm_get_tracks($scoid, $USER->id, $attempt)) {
        foreach ($usertrack as $key => $value) {
            $userdata->$key = $value;
        }
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }

    if ($scodatas = scorm_get_sco($scoid, SCO_DATA)) {
        foreach ($scodatas as $key => $value) {
            $userdata->$key = $value;
        }
    } else {
        print_error('cannotfindsco', 'scorm');
    }
    if (!$sco = scorm_get_sco($scoid)) {
        print_error('cannotfindsco', 'scorm');
    }

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

    $userdata->mode = 'normal';
    if (!empty($mode)) {
        $userdata->mode = $mode;
    }
    if ($userdata->mode == 'normal') {
        $userdata->credit = 'credit';
    } else {
        $userdata->credit = 'no-credit';
    }

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

    // Now handle standard userdata items.
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
    return $def;
}
