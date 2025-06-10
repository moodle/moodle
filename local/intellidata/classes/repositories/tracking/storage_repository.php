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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories\tracking;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
abstract class storage_repository {

    /** @var bool */
    public $ajaxrequest;

    /**
     * Storage repository construct.
     *
     * @param $ajaxrequest
     */
    public function __construct($ajaxrequest) {
        $this->ajaxrequest = $ajaxrequest;
    }

    /**
     * Save data.
     *
     * @param $trackdata
     * @return void
     */
    public function save_data($trackdata) {

    }

    /**
     * Export data.
     *
     * @return void
     */
    public function export_data() {

    }

    /**
     * Get default tracking.
     *
     * @param $trackdata
     * @return \stdClass
     */
    protected function get_default_tracking($trackdata) {
        $data = new \stdClass();
        $data->userid = $trackdata->userid;
        $data->courseid = $trackdata->courseid;
        $data->page = $trackdata->page;
        $data->param = $trackdata->param;
        $data->timespend = $trackdata->timespend;
        $data->firstaccess = time();
        $data->useragent = $trackdata->useragent;
        $data->ip = $trackdata->ip;
        $data->timemodified = time();

        if (!$this->ajaxrequest) {
            $data->visits = 1;
            $data->lastaccess = time();
        }

        return $data;
    }

    /**
     * Fill tracking.
     *
     * @param $tracking
     * @param $trackdata
     * @return void
     */
    protected function fill_tracking(&$tracking, $trackdata) {
        if (!$this->ajaxrequest) {
            $tracking->visits = $tracking->visits + 1;
            $tracking->lastaccess = time();
        } else {
            if ($tracking->lastaccess < strtotime('today')) {
                $tracking->lastaccess = time();
            }
        }
        if ($trackdata->timespend) {
            $tracking->timespend = $tracking->timespend + $trackdata->timespend;
        }
        $tracking->timemodified = time();
        $tracking->useragent = $trackdata->useragent;
        $tracking->ip = $trackdata->ip;
    }

    /**
     * Get default log.
     *
     * @param $trackdata
     * @param $tracking
     * @param $currentstamp
     * @return \stdClass
     */
    protected function get_default_log($trackdata, $tracking, $currentstamp) {
        $log = new \stdClass();
        $log->visits = ($this->ajaxrequest) ? 0 : 1;
        $log->timespend = $trackdata->timespend;
        $log->timepoint = $currentstamp;
        $log->trackid = $tracking->id;
        $log->timemodified = time();

        return $log;
    }

    /**
     * Fill log.
     *
     * @param $log
     * @param $trackdata
     * @return void
     */
    protected function fill_log(&$log, $trackdata) {
        if (!$this->ajaxrequest) {
            $log->visits = $log->visits + 1;
        }
        if (time() <= ($log->timemodified + $trackdata->timespend)) {
            $log->timespend = $log->timespend + $trackdata->timespend;
        }
        $log->timemodified = time();
    }

    /**
     * Get default log detail.
     *
     * @param $trackdata
     * @param $log
     * @param $currenthour
     * @return \stdClass
     */
    protected function get_default_log_detail($trackdata, $log, $currenthour) {
        $detail = new \stdClass();
        $detail->logid = $log->id;
        $detail->visits = ($this->ajaxrequest) ? 0 : 1;
        $detail->timespend = $trackdata->timespend;
        $detail->timepoint = $currenthour;
        $detail->timemodified = time();

        return $detail;
    }

    /**
     * Fill detail.
     *
     * @param $detail
     * @param $trackdata
     * @return void
     */
    protected function fill_detail(&$detail, $trackdata) {
        if (!$this->ajaxrequest) {
            $detail->visits = $detail->visits + 1;
        }
        if (time() <= ($detail->timemodified + $trackdata->timespend)) {
            $detail->timespend = $detail->timespend + $trackdata->timespend;
        }
        $detail->timemodified = time();
    }
}
