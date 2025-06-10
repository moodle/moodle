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

use local_intellidata\helpers\SettingsHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class file_storage_repository extends storage_repository {

    /** @var null */
    private $trackingstorage = null;

    /**
     * Save data.
     *
     * @param $trackdata
     * @throws \dml_exception
     */
    public function save_data($trackdata) {
        global $USER;

        $trackingstorage = new tracking_storage_repository($USER->id);

        $data = $this->get_default_file_tracking($trackdata);
        $trackingstorage->save_data(json_encode($data));

        $tracklogs = SettingsHelper::get_setting('tracklogs');
        $trackdetails = SettingsHelper::get_setting( 'trackdetails');

        $currentstamp = strtotime('today');
        if ($tracklogs) {
            $log = $this->get_default_file_log($trackdata, $currentstamp);
            $trackingstorage->save_data(json_encode($log));

            if ($trackdetails) {
                $currenthour = date('G');
                $detail = $this->get_default_file_log_detail($trackdata, $currenthour, $currentstamp);
                $trackingstorage->save_data(json_encode($detail));
            }
        }
    }

    /**
     * Get default file tracking.
     *
     * @param $trackdata
     * @return \stdClass
     */
    protected function get_default_file_tracking($trackdata) {
        $data = new \stdClass();
        $data->userid = $trackdata->userid;
        $data->courseid = $trackdata->courseid;
        $data->page = $trackdata->page;
        $data->param = $trackdata->param;
        $data->timespend = $trackdata->timespend;
        $data->firstaccess = time();
        $data->useragent = $trackdata->useragent;
        $data->ip = $trackdata->ip;
        $data->table = 'tracking';
        $data->ajaxrequest = $this->ajaxrequest;
        $data->timemodified = time();

        if (!$this->ajaxrequest) {
            $data->visits = 1;
            $data->lastaccess = time();
        }

        return $data;
    }

    /**
     * Get default file log.
     *
     * @param $trackdata
     * @param $currentstamp
     * @return \stdClass
     */
    protected function get_default_file_log($trackdata, $currentstamp) {
        $log = new \stdClass();
        $log->visits = ($this->ajaxrequest) ? 0 : 1;
        $log->timespend = $trackdata->timespend;
        $log->timepoint = $currentstamp;
        $log->timemodified = time();
        $log->table = 'logs';
        $log->ajaxrequest = $this->ajaxrequest;
        $log->userid = $trackdata->userid;
        $log->page = $trackdata->page;
        $log->param = $trackdata->param;

        return $log;
    }

    /**
     * Get default file log detail.
     *
     * @param $trackdata
     * @param $currenthour
     * @param $currentstamp
     * @return \stdClass
     */
    protected function get_default_file_log_detail($trackdata, $currenthour, $currentstamp) {
        $detail = new \stdClass();
        $detail->visits = (!$this->ajaxrequest) ? 1 : 0;
        $detail->timespend = $trackdata->timespend;
        $detail->timepoint = $currenthour;
        $detail->currentstamp = $currentstamp;
        $detail->table = 'details';
        $detail->ajaxrequest = $this->ajaxrequest;
        $detail->userid = $trackdata->userid;
        $detail->page = $trackdata->page;
        $detail->param = $trackdata->param;
        $detail->timemodified = time();

        return $detail;
    }

    /**
     * Export data method.
     *
     * @return void
     */
    public function export_data() {
        $starttime = microtime();
        mtrace("Tracking Files Export started at " . date('r') . "...");

        $this->trackingstorage = new tracking_storage_repository();
        $files = $this->trackingstorage->get_files();

        foreach ($files as $filename) {
            $this->export_data_from_file($filename);
        }

        $difftime = microtime_diff($starttime, microtime());
        mtrace("Tracking Files Export completed at " . date('r') . ".");
        mtrace("Export Tracking Execution took " . $difftime . " seconds.");
        mtrace("-------------------------------------------");
    }

    /**
     * Export data from file.
     *
     * @param $filename
     */
    private function export_data_from_file($filename) {

        // Get data from specific tracking file.
        $usersdata = $this->trackingstorage->get_usersdata_from_file($filename);
        $repository = new usertracking_repository();

        if (count($usersdata)) {
            mtrace("Start Import tracking for " . count($usersdata) ." users");
            foreach ($usersdata as $userid => $userdata) {

                // Export tracking records for individual user.
                $repository->save_tracking_records($userid, $this->trackingstorage->prepare_usersdata($usersdata));
            }
        }
    }
}
