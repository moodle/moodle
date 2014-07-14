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

$userdata = new stdClass();
$def = get_scorm_default($userdata, $scorm, $scoid, $attempt, $mode);

if (!isset($currentorg)) {
    $currentorg = '';
}

$cmiobj = '';
$currentobj = '';
$count = 0;
foreach ($userdata as $element => $value) {
    if (substr($element, 0, 14) == 'cmi.objectives') {
        $element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
        preg_match('/\_(\d+)\./', $element, $matches);
        if (count($matches) > 0 && $currentobj != $matches[1]) {
            $currentobj = $matches[1];
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

$PAGE->requires->js_init_call('M.scorm_api.init', array($def, $cmiobj, $scorm->auto, $CFG->wwwroot, $scorm->id, $scoid, $attempt,
                                                         $mode, $currentorg, sesskey(), $id));
