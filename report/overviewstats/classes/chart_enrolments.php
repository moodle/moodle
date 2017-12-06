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
 * Reports the new enrolments over time
 */
class report_overviewstats_chart_enrolments extends report_overviewstats_chart {

    /** @var int the number of currently enrolled users */
    protected $current = null;

    /**
     * @return array
     */
    public function get_content() {

        $this->prepare_data();

        $title = get_string('chart-enrolments', 'report_overviewstats');
        $titlemonth = get_string('chart-enrolments-month', 'report_overviewstats');
        $titleyear = get_string('chart-enrolments-year', 'report_overviewstats');

        return array($title => array(
            $titlemonth => html_writer::tag('div', '', array(
                'id' => 'chart_enrolments_lastmonth',
                'class' => 'chartplaceholder',
                'style' => 'min-height: 300px;',
            )),
            $titleyear => html_writer::tag('div', '', array(
                'id' => 'chart_enrolments_lastyear',
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
            'M.report_overviewstats.charts.enrolments.init',
            array($this->data)
        );
    }

    /**
     * Prepares data to report
     *
     * It is pretty tricky (actually impossible) to reconstruct the chart of the history
     * of enrolments. The only reliable solution would be to run a cron job every day and
     * store the actual number of enrolled users somewhere.
     *
     * To get at least some estimation, the report models the history using the following
     * algorithm. It starts at the current timestamp and gets the number of currently
     * enrolled users. Let us say there 42 users enrolled right now. Then it looks into
     * the {log} table and searches for recent 'enrol' and 'unenrol' actions. If, for example,
     * there is an 'enrol' action logged yesterday, then the report expects that there had
     * been just 41 users enrolled before that. It's not 100% accurate (for example it ignores
     * the enrolment duration, status etc) but gives at least something.
     */
    protected function prepare_data() {
        global $DB, $CFG;

        if (is_null($this->course)) {
            throw new coding_exception('Course level report invoked without the reference to the course!');
        }

        if (!is_null($this->data)) {
            return;
        }

        // Get the number of currently enrolled users.

        $context = context_course::instance($this->course->id);
        list($esql, $params) = get_enrolled_sql($context);
        $sql = "SELECT COUNT(u.id)
                  FROM {user} u
                  JOIN ($esql) je ON je.id = u.id
                 WHERE u.deleted = 0";

        $this->current = $DB->count_records_sql($sql, $params);

        // Construct the estimated number of enrolled users in the last month
        // and the last year using the current number and the log records.

        $now = time();

        $lastmonth = array();
        for ($i = 30; $i >= 0; $i--) {
            $lastmonth[$now - $i * DAYSECS] = $this->current;
        }

        $lastyear = array();
        for ($i = 12; $i >= 0; $i--) {
            $lastyear[$now - $i * 30 * DAYSECS] = $this->current;
        }

        // Fetch all the enrol/unrol log entries from the last year

        if ($CFG->branch >= 27) {

            $logmanger = get_log_manager();
            if ($CFG->branch >= 29) {
                $readers = $logmanger->get_readers('\core\log\sql_reader');
            } else {
                $readers = $logmanger->get_readers('\core\log\sql_select_reader');
            }
            $reader = reset($readers);
            $select = "component = :component AND (eventname = :eventname1 OR eventname = :eventname2) AND timecreated >= :timestart";
            $params = array(
                'component' => 'core',
                'eventname1' => '\core\event\user_enrolment_created',
                'eventname2' => '\core\event\user_enrolment_deleted',
                'timestart' => $now - 30 * DAYSECS
            );
            $events = $reader->get_events_select($select, $params, 'timecreated DESC', 0, 0);

            foreach ($events as $event) {
                foreach (array_reverse($lastmonth, true) as $key => $value) {
                    if ($event->timecreated >= $key) {
                        // We need to amend all days up to the key.
                        foreach ($lastmonth as $mkey => $mvalue) {
                            if ($mkey <= $key) {
                                if ($event->eventname === '\core\event\user_enrolment_created' and $lastmonth[$mkey] > 0) {
                                    $lastmonth[$mkey]--;
                                } else if ($event->eventname === '\core\event\user_enrolment_deleted') {
                                    $lastmonth[$mkey]++;
                                }
                            }
                        }
                        break;
                    }
                }
                foreach (array_reverse($lastyear, true) as $key => $value) {
                    if ($event->timecreated >= $key) {
                        // We need to amend all months up to the key.
                        foreach ($lastyear as $ykey => $yvalue) {
                            if ($ykey <= $key) {
                                if ($event->eventname === '\core\event\user_enrolment_created' and $lastyear[$ykey] > 0) {
                                    $lastyear[$ykey]--;
                                } else if ($event->eventname === '\core\event\user_enrolment_deleted') {
                                    $lastyear[$ykey]++;
                                }
                            }
                        }
                        break;
                    }
                }
            }

        } else {
            $sql = "SELECT time, action
                      FROM {log}
                     WHERE time >= :timestart
                       AND course = :courseid
                       AND (action = 'enrol' OR action = 'unenrol')";

            $params = array(
                'timestart' => $now - YEARSECS,
                'courseid' => $this->course->id,
            );

            $rs = $DB->get_recordset_sql($sql, $params);

            foreach ($rs as $record) {
                foreach (array_reverse($lastmonth, true) as $key => $value) {
                    if ($record->time >= $key) {
                        // We need to amend all days up to the key.
                        foreach ($lastmonth as $mkey => $mvalue) {
                            if ($mkey <= $key) {
                                if ($record->action === 'enrol' and $lastmonth[$mkey] > 0) {
                                    $lastmonth[$mkey]--;
                                } else if ($record->action === 'unenrol') {
                                    $lastmonth[$mkey]++;
                                }
                            }
                        }
                        break;
                    }
                }
                foreach (array_reverse($lastyear, true) as $key => $value) {
                    if ($record->time >= $key) {
                        // We need to amend all months up to the key.
                        foreach ($lastyear as $ykey => $yvalue) {
                            if ($ykey <= $key) {
                                if ($record->action === 'enrol' and $lastyear[$ykey] > 0) {
                                    $lastyear[$ykey]--;
                                } else if ($record->action === 'unenrol') {
                                    $lastyear[$ykey]++;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            $rs->close();
        }

        $this->data = array(
            'lastmonth' => array(),
            'lastyear' => array(),
        );

        $format = get_string('strftimedateshort', 'core_langconfig');
        foreach ($lastmonth as $timestamp => $enrolled) {
            $date = userdate($timestamp, $format);
            $this->data['lastmonth'][] = array('date' => $date, 'enrolled' => $enrolled);
        }
        foreach ($lastyear as $timestamp => $enrolled) {
            $date = userdate($timestamp, $format);
            $this->data['lastyear'][] = array('date' => $date, 'enrolled' => $enrolled);
        }
    }
}
