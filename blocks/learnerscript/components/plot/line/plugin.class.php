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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;

class plugin_line extends pluginbase {

    function init() {
        $this->fullname = get_string('line', 'block_learnerscript');
        $this->form = true;
        $this->ordering = true;
        $this->reporttypes = array('timeline', 'sql', 'assignment', 
                            'courseactivities', 'courseparticipation', 'courses', 'coursesoverview',
                            'gradedactivity', 'myassignments', 'myquizs', 'myresources','myscorm',
                            'quizzes', 'scorm', 'student_overall_performance', 
                            'student_performance', 'useractivities', 'userassignments',
                            'usercourses', 'userquizzes', 'usersresources', 'usersscorm', 'forum', 'myforums', 'assignstatus', 'userattendance', 'attendanceoverview', 'resources', 'coursecompetency', 'bigbluebutton', 'monthlysessions', 'dailysessions', 'weeklysessions', 'courseviews');
    }

    function summary($data) {
        return get_string('linesummary', 'block_learnerscript');
    }

    // data -> Plugin configuration data
    function execute($id, $data, $finalreport) {
        global $DB, $CFG;

        $series = array();
        $data->yaxis[0] --;
        $data->serieid--;
        $minvalue = 0;
        $maxvalue = 0;

        if ($finalreport) {
            foreach ($finalreport as $r) {
                $hash = md5(strtolower($r[$data->serieid]));
                $sname[$hash] = $r[$data->serieid];
                $val = (isset($r[$data->yaxis[0]]) && is_numeric($r[$data->yaxis[0]])) ? $r[$data->yaxis[0]] : 0;
                $series[$hash][] = $val;
                $minvalue = ($val < $minvalue) ? $val : $minvalue;
                $maxvalue = ($val > $maxvalue) ? $val : $maxvalue;
            }
        }

        $params = '';

        $i = 0;
        foreach ($series as $h => $s) {
            $params .= "&amp;serie$i=" . base64_encode($sname[$h] . '||' . implode(',', $s));
            $i++;
        }

        return $CFG->wwwroot . '/blocks/learnerscript/components/plot/line/graph.php?reportid=' . $this->report->id . '&id=' . $id . $params . '&amp;min=' . $minvalue . '&amp;max=' . $maxvalue;
    }

    function get_series($data) {
        $series = array();
        foreach ($_GET as $key => $val) {
            if (strpos($key, 'serie') !== false) {
                $id = (int) str_replace('serie', '', $key);
                list($name, $values) = explode('||', base64_decode($val));
                $series[$id] = array('serie' => explode(',', $values), 'name' => $name);
            }
        }
        return $series;
    }

}
