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
 * Loglive report renderable class.
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Report loglive renderable class.
 *
 * @since      Moodle 2.7
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_loglive_renderable implements renderable {

    /** @const int number of seconds to show logs from, by default. */
    const CUTOFF = 3600;

    /** @var \core\log\manager log manager */
    protected $logmanager;

    /** @var string selected log reader pluginname */
    public $selectedlogreader = null;

    /** @var int page number */
    public $page;

    /** @var int perpage records to show */
    public $perpage;

    /** @var stdClass course record */
    public $course;

    /** @var moodle_url url of report page */
    public $url;

    /** @var int selected date from which records should be displayed */
    public $date;

    /** @var string order to sort */
    public $order;

    /** @var int group id */
    public $groupid;

    /** @var report_loglive_table_log table log which will be used for rendering logs */
    public $tablelog;

    /** @var  int refresh rate in seconds */
    protected $refresh  = 60;

    /**
     * Constructor.
     *
     * @param string $logreader (optional)reader pluginname from which logs will be fetched.
     * @param stdClass|int $course (optional) course record or id
     * @param moodle_url|string $url (optional) page url.
     * @param int $date date (optional) from which records will be fetched.
     * @param int $page (optional) page number.
     * @param int $perpage (optional) number of records to show per page.
     * @param string $order (optional) sortorder of fetched records
     */
    public function __construct($logreader = "", $course = 0, $url = "", $date = 0, $page = 0, $perpage = 100,
                                $order = "timecreated DESC") {

        global $PAGE;

        // Use first reader as selected reader, if not passed.
        if (empty($logreader)) {
            $readers = $this->get_readers();
            if (!empty($readers)) {
                reset($readers);
                $logreader = key($readers);
            } else {
                $logreader = null;
            }
        }
        $this->selectedlogreader = $logreader;

        // Use page url if empty.
        if (empty($url)) {
            $url = new moodle_url($PAGE->url);
        } else {
            $url = new moodle_url($url);
        }
        $this->url = $url;

        // Use site course id, if course is empty.
        if (!empty($course) && is_int($course)) {
            $course = get_course($course);
        }
        $this->course = $course;

        if ($date == 0 ) {
            $date = time() - self::CUTOFF;
        }
        $this->date = $date;

        $this->page = $page;
        $this->perpage = $perpage;
        $this->order = $order;
        $this->set_refresh_rate();
    }

    /**
     * Get a list of enabled sql_reader objects/name
     *
     * @param bool $nameonly if true only reader names will be returned.
     *
     * @return array core\log\sql_reader object or name.
     */
    public function get_readers($nameonly = false) {
        if (!isset($this->logmanager)) {
            $this->logmanager = get_log_manager();
        }

        $readers = $this->logmanager->get_readers('core\log\sql_reader');
        if ($nameonly) {
            foreach ($readers as $pluginname => $reader) {
                $readers[$pluginname] = $reader->get_name();
            }
        }
        return $readers;
    }

    /**
     * Setup table log.
     */
    protected function setup_table() {
        $filter = $this->setup_filters();
        $this->tablelog = new report_loglive_table_log('report_loglive', $filter);
        $this->tablelog->define_baseurl($this->url);
    }

    /**
     * Setup table log for ajax output.
     */
    protected function setup_table_ajax() {
        $filter = $this->setup_filters();
        $this->tablelog = new report_loglive_table_log_ajax('report_loglive', $filter);
        $this->tablelog->define_baseurl($this->url);
    }

    /**
     * Setup filters
     *
     * @return stdClass filters
     */
    protected function setup_filters() {
        $readers = $this->get_readers();

        // Set up filters.
        $filter = new \stdClass();
        if (!empty($this->course)) {
            $filter->courseid = $this->course->id;
        } else {
            $filter->courseid = 0;
        }
        $filter->logreader = $readers[$this->selectedlogreader];
        $filter->date = $this->date;
        $filter->orderby = $this->order;
        $filter->anonymous = 0;

        return $filter;
    }

    /**
     * Set refresh rate of the live updates.
     */
    protected function set_refresh_rate() {
        if (defined('BEHAT_SITE_RUNNING')) {
            // Hack for behat tests.
            $this->refresh = 5;
        } else {
            if (defined('REPORT_LOGLIVE_REFRESH')) {
                // Backward compatibility.
                $this->refresh = REPORT_LOGLIVE_REFERESH;
            } else {
                // Default.
                $this->refresh = 60;
            }
        }
    }

    /**
     * Get refresh rate of the live updates.
     */
    public function get_refresh_rate() {
        return $this->refresh;
    }

    /**
     * Setup table and return it.
     *
     * @param bool $ajax If set to true report_loglive_table_log_ajax is set instead of report_loglive_table_log.
     *
     * @return report_loglive_table_log|report_loglive_table_log_ajax table object
     */
    public function get_table($ajax = false) {
        if (empty($this->tablelog)) {
            if ($ajax) {
                $this->setup_table_ajax();
            } else {
                $this->setup_table();
            }
        }
        return $this->tablelog;
    }
}
