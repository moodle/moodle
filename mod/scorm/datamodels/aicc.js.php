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

$def = array();
$def['cmi.core.student_id'] = $userdata->student_id;
$def['cmi.core.student_name'] = $userdata->student_name;
$def['cmi.core.credit'] = $userdata->credit;
$def['cmi.core.entry'] = $userdata->entry;
$def['cmi.launch_data'] = scorm_isset($userdata, 'datafromlms');
$def['cmi.core.lesson_mode'] = $userdata->mode;
$def['cmi.student_data.attempt_number'] = scorm_isset($userdata, 'cmi.student_data.attempt_number');
$def['cmi.student_data.mastery_score'] = scorm_isset($userdata, 'mastery_score');
$def['cmi.student_data.max_time_allowed'] = scorm_isset($userdata, 'max_time_allowed');
$def['cmi.student_data.time_limit_action'] = scorm_isset($userdata, 'time_limit_action');
$def['cmi.student_data.tries_during_lesson'] = scorm_isset($userdata, 'cmi.student_data.tries_during_lesson');

$def['cmi.core.lesson_location'] = scorm_isset($userdata, 'cmi.core.lesson_location');
$def['cmi.core.lesson_status'] = scorm_isset($userdata, 'cmi.core.lesson_status');
$def['cmi.core.exit'] = scorm_isset($userdata, 'cmi.core.exit');
$def['cmi.core.score.raw'] = scorm_isset($userdata, 'cmi.core.score.raw');
$def['cmi.core.score.max'] = scorm_isset($userdata, 'cmi.core.score.max');
$def['cmi.core.score.min'] = scorm_isset($userdata, 'cmi.core.score.min');
$def['cmi.core.total_time'] = scorm_isset($userdata, 'cmi.core.total_time', '00:00:00');
$def['cmi.suspend_data'] = scorm_isset($userdata, 'cmi.suspend_data');
$def['cmi.comments'] = scorm_isset($userdata, 'cmi.comments');

echo js_writer::set_variable('def', $def);

echo js_writer::set_variable('scormauto', $scorm->auto);
echo js_writer::set_variable('cfgwwwroot', $CFG->wwwroot);
echo js_writer::set_variable('scormid', $scorm->id);
echo js_writer::set_variable('scoid', $scoid);
echo js_writer::set_variable('attempt', $attempt);
echo js_writer::set_variable('viewmode', $mode);
echo js_writer::set_variable('currentorg', $currentorg);
echo js_writer::set_variable('sesskey', sesskey());
echo js_writer::set_variable('cmid', $id);

$cmiobj = '';
$current_objective = '';
$count = 0;
$objectives = '';
foreach ($userdata as $element => $value) {
    if (substr($element, 0, 14) == 'cmi.objectives') {
        $element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
        preg_match('/\_(\d+)\./', $element, $matches);
        if (count($matches) > 0 && $current_objective != $matches[1]) {
            $current_objective = $matches[1];
            $count++;
            $end = strpos($element, $matches[1])+strlen($matches[1]);
            $subelement = substr($element, 0, $end);
            $cmiobj .= '    '.$subelement." = new Object();\n";
            $cmiobj .= '    '.$subelement.".score = new Object();\n";
            $cmiobj .= '    '.$subelement.".score._children = score_children;\n";
            $cmiobj .= '    '.$subelement.".score.raw = '';\n";
            $cmiobj .= '    '.$subelement.".score.min = '';\n";
            $cmiobj .= '    '.$subelement.".score.max = '';\n";
        }
        $cmiobj .= '    '.$element.' = \''.$value."';\n";
    }
}
if ($count > 0) {
    $cmiobj .= '    cmi.objectives._count = '.$count.";\n";
}

echo js_writer::set_variable('cmiobj', $cmiobj);

echo 'var API = new AICCapi();';