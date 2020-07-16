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

namespace scormreport_graphs;

defined('MOODLE_INTERNAL') || die();

use context_module;
use core\chart_bar;
use core\chart_series;
use moodle_url;

/**
 * Main class to control the graphs reporting
 *
 * @package    scormreport_graphs
 * @copyright  2012 Ankit Kumar Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class report extends \mod_scorm\report {

    /** Number of bars. */
    const BANDS = 11;

    /** Range of each bar. */
    const BANDWIDTH = 10;

    /**
     * Get the data for the report.
     *
     * @param int $scoid The sco ID.
     * @param array $allowedlist The SQL and params to get the userlist.
     * @return array of data indexed per bar.
     */
    protected function get_data($scoid, $allowedlistsql) {
        global $DB;
        $data = array_fill(0, self::BANDS, 0);

        list($allowedlist, $params) = $allowedlistsql;
        $params = array_merge($params, ['scoid' => $scoid]);

        // Construct the SQL.
        $sql = "SELECT DISTINCT " . $DB->sql_concat('st.userid', '\'#\'', 'COALESCE(st.attempt, 0)') . " AS uniqueid,
                       st.userid AS userid,
                       st.scormid AS scormid,
                       st.attempt AS attempt,
                       st.scoid AS scoid
                  FROM {scorm_scoes_track} st
                 WHERE st.userid IN ({$allowedlist}) AND st.scoid = :scoid";
        $attempts = $DB->get_records_sql($sql, $params);

        $usergrades = [];
        foreach ($attempts as $attempt) {
            if ($trackdata = scorm_get_tracks($scoid, $attempt->userid, $attempt->attempt)) {
                if (isset($trackdata->score_raw)) {
                    $score = (int) $trackdata->score_raw;
                    if (empty($trackdata->score_min)) {
                        $minmark = 0;
                    } else {
                        $minmark = $trackdata->score_min;
                    }
                    // TODO MDL-55004: Get this value from elsewhere?
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

        // Recording all users who attempted the SCO, but resulting data was invalid.
        foreach ($usergrades as $userpercent) {
            if ($userpercent === '*') {
                $data[0]++;
            } else {
                $gradeband = floor($userpercent / self::BANDWIDTH);
                if ($gradeband != (self::BANDS - 1)) {
                    $gradeband++;
                }
                $data[$gradeband]++;
            }
        }

        return $data;
    }

    /**
     * Displays the full report.
     *
     * @param \stdClass $scorm full SCORM object
     * @param \stdClass $cm - full course_module object
     * @param \stdClass $course - full course object
     * @param string $download - type of download being requested
     * @return void
     */
    public function display($scorm, $cm, $course, $download) {
        global $DB, $OUTPUT, $PAGE;

        $contextmodule = context_module::instance($cm->id);

        if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used.
            groups_print_activity_menu($cm, new moodle_url($PAGE->url));
        }

        // Find out current restriction.
        $group = groups_get_activity_group($cm, true);
        $allowedlistsql = get_enrolled_sql($contextmodule, 'mod/scorm:savetrack', (int) $group);

        // Labels.
        $labels = [get_string('invaliddata', 'scormreport_graphs')];
        for ($i = 1; $i <= self::BANDS - 1; $i++) {
            $labels[] = ($i - 1) * self::BANDWIDTH . ' - ' . $i * self::BANDWIDTH;
        }

        if ($scoes = $DB->get_records('scorm_scoes', array("scorm" => $scorm->id), 'sortorder, id')) {
            foreach ($scoes as $sco) {
                if ($sco->launch != '') {

                    $data = $this->get_data($sco->id, $allowedlistsql);
                    $series = new chart_series($sco->title, $data);

                    $chart = new chart_bar();
                    $chart->set_labels($labels);
                    $chart->add_series($series);
                    $chart->get_xaxis(0, true)->set_label(get_string('percent', 'scormreport_graphs'));
                    $yaxis = $chart->get_yaxis(0, true);
                    $yaxis->set_label(get_string('participants', 'scormreport_graphs'));
                    $yaxis->set_stepsize(max(1, round(max($data) / 10)));

                    echo $OUTPUT->heading($sco->title, 3);
                    echo $OUTPUT->render($chart);
                }
            }
        }
    }
}
