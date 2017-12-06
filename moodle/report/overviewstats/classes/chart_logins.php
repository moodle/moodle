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
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/accesslib.php');

/**
 * Reports various users related charts and figures
 */
class report_overviewstats_chart_logins extends report_overviewstats_chart {

    /**
     * @return array
     */
    public function get_content() {

        $this->prepare_data();

        $title = get_string('chart-logins', 'report_overviewstats');
        $titleperday = get_string('chart-logins-perday', 'report_overviewstats');

        return array($title => array(
            $titleperday => html_writer::tag('div', '', array(
                'id' => 'chart_logins_perday',
                'class' => 'chartplaceholder',
                'style' => 'min-height: 300px;',
            )),
        ));
    }

    /**
     * @see parent
     */
    public function inject_page_requirements(moodle_page $page) {

        $this->prepare_data();

        $page->requires->yui_module(
            'moodle-report_overviewstats-charts',
            'M.report_overviewstats.charts.logins.init',
            array($this->data)
        );
    }

    /**
     * Prepares data to report
     */
    protected function prepare_data() {
        global $DB, $CFG;

        if (!is_null($this->data)) {
            return;
        }

        $now = strtotime('today midnight');

        $lastmonth = array();
        for ($i = 30; $i >= 0; $i--) {
            $lastmonth[$now - $i * DAYSECS] = array();
        }

        if ($CFG->branch >= 27) {
            $logmanger = get_log_manager();
            if ($CFG->branch >= 29) {
                $readers = $logmanger->get_readers('\core\log\sql_reader');
            } else {
                $readers = $logmanger->get_readers('\core\log\sql_select_reader');
            }
            $reader = reset($readers);
            $params = array('component' => 'core',
                            'eventname' => '\core\event\user_loggedin',
                            'guestid' => $CFG->siteguest,
                            'timestart' => $now - 30 * DAYSECS);
            $select = "component = :component AND eventname = :eventname AND userid <> :guestid AND timecreated >= :timestart";
            $rs = $reader->get_events_select($select, $params, 'timecreated DESC', 0, 0);

            foreach ($rs as $record) {
                foreach (array_reverse($lastmonth, true) as $timestamp => $loggedin) {
                    $date = usergetdate($timestamp);
                    if ($record->timecreated >= $timestamp) {
                        $lastmonth[$timestamp][$record->userid] = true;
                        break;
                    }
                }
            }

        } else {
            $sql = "SELECT time, userid
                      FROM {log}
                     WHERE time >= :timestart
                       AND userid <> :guestid
                       AND action = 'login'";

            $params = array('timestart' => $now - 30 * DAYSECS, 'guestid' => $CFG->siteguest);

            $rs = $DB->get_recordset_sql($sql, $params);

            foreach ($rs as $record) {
                foreach (array_reverse($lastmonth, true) as $timestamp => $loggedin) {
                    $date = usergetdate($timestamp);
                    if ($record->time >= $timestamp) {
                        $lastmonth[$timestamp][$record->userid] = true;
                        break;
                    }
                }
            }
            $rs->close();
        }

        $this->data = array(
            'perday' => array(),
        );

        $format = get_string('strftimedateshort', 'core_langconfig');

        foreach ($lastmonth as $timestamp => $loggedin) {
            $date = userdate($timestamp, $format);
            $this->data['perday'][] = array('date' => $date, 'loggedin' => count($loggedin));
        }
        // Ignore today's stats (not complete yet).
        array_pop($this->data['perday']);
    }
}
