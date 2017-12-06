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
$def = new stdClass();
$cmiobj = new stdClass();

if (!isset($currentorg)) {
    $currentorg = '';
}

if ($scoes = $DB->get_records('scorm_scoes', array('scorm' => $scorm->id), 'sortorder, id')) {
    // Drop keys so that it is a simple array.
    $scoes = array_values($scoes);
    foreach ($scoes as $sco) {
        $def->{($sco->id)} = new stdClass();
        $userdata->{($sco->id)} = new stdClass();
        $def->{($sco->id)} = get_scorm_default($userdata->{($sco->id)}, $scorm, $sco->id, $attempt, $mode);

        // Reconstitute objectives, comments_from_learner and comments_from_lms.
        $cmiobj->{($sco->id)} = '';
        $currentobj = '';
        $count = 0;
        foreach ($userdata as $element => $value) {
            if (substr($element, 0, 14) == 'cmi.objectives') {
                $element = preg_replace('/\.(\d+)\./', "_\$1.", $element);
                preg_match('/\_(\d+)\./', $element, $matches);
                if (count($matches) > 0 && $currentobj != $matches[1]) {
                    $currentobj = $matches[1];
                    $count++;
                    $end = strpos($element, $matches[1]) + strlen($matches[1]);
                    $subelement = substr($element, 0, $end);
                    $cmiobj->{($sco->id)} .= '    '.$subelement." = new Object();\n";
                    $cmiobj->{($sco->id)} .= '    '.$subelement.".score = new Object();\n";
                    $cmiobj->{($sco->id)} .= '    '.$subelement.".score._children = score_children;\n";
                    $cmiobj->{($sco->id)} .= '    '.$subelement.".score.raw = '';\n";
                    $cmiobj->{($sco->id)} .= '    '.$subelement.".score.min = '';\n";
                    $cmiobj->{($sco->id)} .= '    '.$subelement.".score.max = '';\n";
                }
                $cmiobj->{($sco->id)} .= '    '.$element.' = \''.$value."';\n";
            }
        }
        if ($count > 0) {
            $cmiobj->{($sco->id)} .= '    cmi.objectives._count = '.$count.";\n";
        }
    }
}


$PAGE->requires->js_init_call('M.scorm_api.init', array($def, $cmiobj, $scorm->auto, $CFG->wwwroot, $scorm->id, $scoid,
                                                            $attempt, $mode, $currentorg, sesskey(), $id, $scorm->autocommit));
