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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class block_configurable_reports
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class block_configurable_reports extends block_list {

    /**
     * Sets the block name and version number
     *
     * @return void
     **/
    public function init(): void {
        $this->title = get_string('pluginname', 'block_configurable_reports');
    }

    /**
     * Act on instance data.
     */
    public function specialization(): void {

        if ($this->config) {
            $this->title = $this->config->title ?: get_string('pluginname', 'block_configurable_reports');
        } else {
            $this->title = get_string('pluginname', 'block_configurable_reports');
            $this->config = new stdClass();
            $this->config->displayglobalreports = true;
        }
    }

    /**
     * instance_allow_config
     *
     * @return bool
     */
    public function instance_allow_config(): bool {
        return true;
    }

    /**
     * Where to add the block
     *
     * @return array
     **/
    public function applicable_formats(): array {
        return [
            'site' => true,
            'course' => true,
            'my' => true,
        ];
    }

    /**
     * Global Config?
     *
     * @return boolean
     **/
    public function has_config(): bool {
        return true;
    }

    /**
     * Gets the contents of the block (course view)
     *
     * @return object An object with the contents
     **/
    public function get_content() {
        global $DB, $USER, $CFG, $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->icons = [];

        if (!isloggedin()) {
            return $this->content;
        }

        require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");
        $course = $DB->get_record('course', ['id' => $COURSE->id]);

        if (!$course) {
            throw new moodle_exception('coursedoesnotexists');
        }

        if ($course->id == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($course->id);
        }

        // Site (Shared) reports.
        if (!empty($this->config->displayglobalreports)) {
            $reports = $DB->get_records('block_configurable_reports', ['global' => 1], 'name ASC');

            if ($reports) {
                foreach ($reports as $report) {
                    if ($report->visible && cr_check_report_permissions($report, $USER->id, $context)) {
                        $rname = format_string($report->name);
                        $params = ['id' => $report->id, 'courseid' => $course->id];
                        $url = new moodle_url('/blocks/configurable_reports/viewreport.php', $params);
                        $attrs = ['alt' => $rname];
                        $this->content->items[] = html_writer::link($url, $rname, $attrs);
                    }
                }
                if (!empty($this->content->items)) {
                    $this->content->items[] = '========';
                }
            }
        }

        // Course reports.
        if (!property_exists($this, 'config')
            || !isset($this->config->displayreportslist)
            || $this->config->displayreportslist) {
            $reports = $DB->get_records('block_configurable_reports', ['courseid' => $course->id], 'name ASC');

            if ($reports) {
                foreach ($reports as $report) {
                    if (!$report->global && $report->visible && cr_check_report_permissions($report, $USER->id, $context)) {
                        $rname = format_string($report->name);
                        $params = ['id' => $report->id, 'courseid' => $course->id];
                        $url = new moodle_url('/blocks/configurable_reports/viewreport.php', $params);
                        $attrs = ['alt' => $rname];
                        $this->content->items[] = html_writer::link($url, $rname, $attrs);
                    }
                }
                if (!empty($this->content->items)) {
                    $this->content->items[] = '========';
                }
            }
        }

        if (has_capability('block/configurable_reports:managereports', $context)
            || has_capability('block/configurable_reports:manageownreports', $context)) {
            $url = new moodle_url('/blocks/configurable_reports/managereport.php', ['courseid' => $course->id]);
            $linktext = get_string('managereports', 'block_configurable_reports');
            $this->content->items[] = html_writer::link($url, $linktext);
        }

        return $this->content;
    }

    /**
     * Cron
     *
     * @return bool
     */
    public function cron(): bool {
        global $CFG, $DB;

        $hour = get_config('block_configurable_reports', 'cron_hour');
        $min = get_config('block_configurable_reports', 'cron_minute');

        $date = usergetdate(time());
        $usertime = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year']);

        $crontime = mktime($hour, $min, $date['seconds'], $date['mon'], $date['mday'], $date['year']);

        if (($crontime - $usertime) < 0) {
            return false;
        }

        $lastcron = $DB->get_field('block', 'lastcron', ['name' => 'configurable_reports']);
        if (!$lastcron && ($lastcron + $this->cron < time())) {
            return false;
        }

        // Starting to run...
        require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");
        require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
        require_once($CFG->dirroot . '/blocks/configurable_reports/reports/sql/report.class.php');

        mtrace("\nConfigurable report (block)");

        $reports = $DB->get_records('block_configurable_reports');
        if ($reports) {
            foreach ($reports as $report) {
                // Running only SQL reports. $report->type == 'sql'.
                if ($report->type === 'sql' && (!empty($report->cron) && $report->cron == '1')) {
                    $reportclass = new report_sql($report);

                    // Execute it using $remotedb.
                    $starttime = microtime(true);
                    mtrace("\nExecuting query '$report->name'");

                    $components = cr_unserialize($reportclass->config->components);
                    $config = $components['customsql']['config'] ?? (object) [];
                    $sql = $reportclass->prepare_sql($config->querysql);

                    $sqlqueries = explode(';', $sql);

                    foreach ($sqlqueries as $sql) {
                        mtrace(substr($sql, 0, 60)); // Show some SQL.
                        $results = $reportclass->execute_query($sql);
                        if ($results == 1) {
                            mtrace('...OK time=' . round((microtime(true) - $starttime) * 1000) . 'mSec');
                        } else {
                            mtrace('Some SQL Error' . '\n');
                        }
                    }
                    unset($reportclass);
                }
            }
        }

        return true; // Finished OK.
    }

}
