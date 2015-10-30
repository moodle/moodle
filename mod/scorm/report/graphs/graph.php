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
 * Core Report class of graphs reporting plugin
 *
 * @package    scormreport_graphs
 * @copyright  2012 Ankit Kumar Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/graphlib.php');
require_once($CFG->dirroot.'/mod/scorm/report/reportlib.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

$scoid = required_param('scoid', PARAM_INT);// SCO ID.

$sco = $DB->get_record('scorm_scoes', array('id' => $scoid), '*', MUST_EXIST);
$scorm = $DB->get_record('scorm', array('id' => $sco->scorm), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('scorm', $scorm->id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $scorm->course), '*', MUST_EXIST);


// Capability Checks.
require_login($course, false, $cm);
$contextmodule = context_module::instance($cm->id);
require_capability('mod/scorm:viewreport', $contextmodule);

// Find out current groups mode.
$currentgroup = groups_get_activity_group($cm, true);

// Group Check.
if (empty($currentgroup)) {
    // All users who can attempt scoes.
    $students = get_users_by_capability($contextmodule, 'mod/scorm:savetrack', 'u.id' , '', '', '', '', '', false);
    $allowedlist = empty($students) ? array() : array_keys($students);
} else {
    // All users who can attempt scoes and who are in the currently selected group.
    $groupstudents = get_users_by_capability($contextmodule, 'mod/scorm:savetrack', 'u.id', '', '', '', $currentgroup, '', false);
    $allowedlist = empty($groupstudents) ? array() : array_keys($groupstudents);
}

$params = array();
$bands = 11;
$bandwidth = 10;

// Determine maximum % secured for each user.
$usergrades = array();
$graphdata = array();
for ($i = 0; $i < $bands; $i++) {
    $graphdata[$i] = 0;
}

// Do this only if we have students to report.
if (!empty($allowedlist)) {
    list($usql, $params) = $DB->get_in_or_equal($allowedlist);
    $params[] = $scoid;
    // Construct the SQL.
    $select = 'SELECT DISTINCT '.$DB->sql_concat('st.userid', '\'#\'', 'COALESCE(st.attempt, 0)').' AS uniqueid, ';
    $select .= 'st.userid AS userid, st.scormid AS scormid, st.attempt AS attempt, st.scoid AS scoid ';
    $from = 'FROM {scorm_scoes_track} st ';
    $where = ' WHERE st.userid ' .$usql. ' and st.scoid = ?';
    $attempts = $DB->get_records_sql($select.$from.$where, $params);

    foreach ($attempts as $attempt) {
        if ($trackdata = scorm_get_tracks($scoid, $attempt->userid, $attempt->attempt)) {
            if (isset($trackdata->score_raw)) {
                $score = $trackdata->score_raw;
                if (empty($trackdata->score_min)) {
                    $minmark = 0;
                } else {
                    $minmark = $trackdata->score_min;
                }
                if (empty($trackdata->score_max)) {
                    $maxmark = 100;
                } else {
                    $maxmark = $trackdata->score_max;
                }
                $range = ($maxmark - $minmark);
                if (empty($range)) {
                    continue;
                }
                $percent = round((($score * 100) / $range), 2);
                if (empty($usergrades[$attempt->userid]) || !isset($usergrades[$attempt->userid])
                        || ($percent > $usergrades[$attempt->userid]) || ($usergrades[$attempt->userid] === '*')) {
                    $usergrades[$attempt->userid] = $percent;
                }
                unset($percent);
            } else {
                // User has made an attempt but either SCO was not able to record the score or something else is broken in SCO.
                if (!isset($usergrades[$attempt->userid])) {
                    $usergrades[$attempt->userid] = '*';
                }
            }
        }
    }
}

$bandlabels[] = get_string('invaliddata', 'scormreport_graphs');
for ($i = 1; $i <= $bands - 1; $i++) {
    $bandlabels[] = ($i - 1) * $bandwidth . ' - ' . $i * $bandwidth;
}
// Recording all users  who attempted the SCO,but resulting data was invalid.
foreach ($usergrades as $userpercent) {
    if ($userpercent === '*') {
        $graphdata[0]++;
    } else {
        $gradeband = floor($userpercent / 10);
        if ($gradeband != ($bands - 1)) {
            $gradeband++;
        }
        $graphdata[$gradeband]++;
    }
}

$line = new graph(800, 600);
$line->parameter['title'] = '';
$line->parameter['y_label_left'] = get_string('participants', 'scormreport_graphs');
$line->parameter['x_label'] = get_string('percent', 'scormreport_graphs');
$line->parameter['y_label_angle'] = 90;
$line->parameter['x_label_angle'] = 0;
$line->parameter['x_axis_angle'] = 60;

// Following two lines seem to silence notice warnings from graphlib.php.
$line->y_tick_labels = null;
$line->offset_relation = null;

$line->parameter['bar_size'] = 1;
// Don't forget to increase spacing so that graph doesn't become one big block of colour.
$line->parameter['bar_spacing'] = 10;
$line->x_data = $bandlabels;

$line->y_format['allusers'] = array(
    'colour' => 'red',
    'bar' => 'fill',
    'shadow_offset' => 1,
    'legend' => get_string('allparticipants')
);
ksort($graphdata);
$line->y_data['allusers'] = $graphdata;
$line->y_order = array('allusers');

$ymax = max($line->y_data['allusers']);
$line->parameter['y_min_left'] = 0;  // Start at 0.
$line->parameter['y_max_left'] = $ymax;
$line->parameter['y_decimal_left'] = 0; // 2 decimal places for y axis.

// Pick a sensible number of gridlines depending on max value on graph.
$gridlines = $ymax;
while ($gridlines >= 10) {
    if ($gridlines >= 50) {
        $gridlines /= 5;
    } else {
        $gridlines /= 2;
    }
}
$gridlines = max(2, ($gridlines + 1)); // We need a minimum of two lines.
$line->parameter['y_axis_gridlines'] = $gridlines;

$line->draw();
