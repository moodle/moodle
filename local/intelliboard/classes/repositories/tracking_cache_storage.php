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
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace local_intelliboard\repositories;


use core_message\helper;

class tracking_cache_storage {

    public function saveData($ajaxRequest, $intelliboardTime, $intelliboardPage, $intelliboardParam){
        global $DB, $USER;

        $intelliboardMediaTrack = get_config('local_intelliboard', 'trackmedia');
        $cache = \cache::make('local_intelliboard', 'tracking');
        $userkey = 'user_' . $USER->id . '_page_' . $intelliboardPage . '_param_' . $intelliboardParam;
        if ($cache->has($userkey)) {
            $cacherecord = $cache->get($userkey);
        } else {
            $cacherecord = array('tracking'=> null, 'logs' => [], 'details'=> []);
        }

        if($cacherecord['tracking'] != null){
            $data = $cacherecord['tracking'];
        } elseif ($data = $DB->get_record('local_intelliboard_tracking', array('userid' => $USER->id, 'page' => $intelliboardPage, 'param' => $intelliboardParam), 'id, visits, timespend, lastaccess')) {
            //do nothing
        } else {
            $data = $this->getDefaultUserDetails($intelliboardPage, $intelliboardParam, $intelliboardTime);
            $data->id = $DB->insert_record('local_intelliboard_tracking', $data, true);
        }

        if ($intelliboardMediaTrack) {
            if ($data->lastaccess <= (time() - $intelliboardTime)) {
                $data->lastaccess = time();
            } else {
                $intelliboardTime = 0;
            }
        } else {
            if (!$ajaxRequest) {
                $data->visits = $data->visits + 1;
                $data->lastaccess = time();
            } else {
                if ($data->lastaccess < strtotime('today')) {
                    $data->lastaccess = time();
                }
            }
        }
        if ($intelliboardTime) {
            $data->timespend = $data->timespend + $intelliboardTime;
        }

        $tracklogs = get_config('local_intelliboard', 'tracklogs');
        $trackdetails = get_config('local_intelliboard', 'trackdetails');
        $currentstamp = strtotime('today');

        if ($tracklogs) {
            if(isset($cacherecord['logs'][$currentstamp])){
                $log = $cacherecord['logs'][$currentstamp];
            } elseif ($log = $DB->get_record('local_intelliboard_logs', array('trackid' => $data->id, 'timepoint' => $currentstamp))) {
                //do nothing
            } else {
                $log = new \stdClass();
                $log->visits = 0;
                $log->timespend = 0;
                $log->timepoint = $currentstamp;
                $log->trackid = $data->id;
                $log->id = $DB->insert_record('local_intelliboard_logs', $log, true);
            }

            if (!$ajaxRequest) {
                $log->visits = $log->visits + 1;
            }
            $log->timespend = $log->timespend + $intelliboardTime;
            $cacherecord['logs'][$currentstamp] = $log;

            if ($trackdetails) {
                $currenthour = date('G');

                if(isset($cacherecord['details'][$currentstamp][$currenthour])){
                    $detail = $cacherecord['details'][$currentstamp][$currenthour];
                } elseif (isset($log->id) && $detail = $DB->get_record('local_intelliboard_details', array('logid' => $log->id, 'timepoint' => $currenthour))) {

                } else {
                    $detail = new \stdClass();
                    $detail->visits = 0;
                    $detail->timespend = 0;
                    $detail->timepoint = $currenthour;
                    $detail->logid = $log->id;
                    $detail->id = $DB->insert_record('local_intelliboard_details', $detail, true);
                }

                if (!$ajaxRequest) {
                    $detail->visits = $detail->visits + 1;
                }
                $detail->timespend = $detail->timespend + $intelliboardTime;

                $cacherecord['details'][$currentstamp][$currenthour] = $detail;
            }
        }

        $cacherecord['tracking'] = $data;

        if (!$cache->set($userkey, $cacherecord)){
            //something wrong
            error_log("Intelliboard compress tracking: error save track to cache, key:{$userkey}, data:" . json_encode($cacherecord));
        }
    }

    protected function getDefaultUserDetails($intelliboardPage, $intelliboardParam, $intelliboardTime){
        global $DB, $USER;

        $userDetails = (object)local_intelliboard_user_details();
        $courseid = 0;
        if ($intelliboardPage == "module") {
            $courseid = $DB->get_field_sql("SELECT c.id FROM {course} c, {course_modules} cm WHERE c.id = cm.course AND cm.id = $intelliboardParam");
        } elseif ($intelliboardPage == "course") {
            $courseid = $intelliboardParam;
        }
        $data = new \stdClass();
        $data->userid = $USER->id;
        $data->courseid = $courseid;
        $data->page = $intelliboardPage;
        $data->param = $intelliboardParam;
        $data->visits = 0;
        $data->timespend = $intelliboardTime;
        $data->firstaccess = time();
        $data->lastaccess = time();
        $data->useragent = $userDetails->useragent;
        $data->useros = $userDetails->useros;
        $data->userlang = $userDetails->userlang;
        $data->userip = $userDetails->userip;

        return $data;
    }

    public function exportData(){
        global $DB;

        mtrace("IntelliBoard Tracking Cache Export CRON started!");
        $cache = \cache::make('local_intelliboard', 'tracking');

        $keys = $cache->get_all_keys();
        foreach ($keys as $key) {
            mtrace("Cache key started: " . $key);
            $record = $cache->get($key);

            try {
                $transaction = $DB->start_delegated_transaction();
                $DB->update_record('local_intelliboard_tracking', $record['tracking']);

                foreach ($record['logs'] as $log) {
                    $DB->update_record('local_intelliboard_logs', $log);
                }

                foreach ($record['details'] as $logs) {
                    foreach ($logs as $detail) {
                        $DB->update_record('local_intelliboard_details', $detail);
                    }
                }
                $transaction->allow_commit();
            } catch(Exception $e) {
                if (!empty($transaction) && !$transaction->is_disposed()) {
                    $transaction->rollback($e);
                }
            }

            if (!helper::is_online($record['tracking']->lastaccess)) {
                mtrace("User is offline, delete key: " . $key);
                $cache->delete($key);
            }
            mtrace("Cache key processed: " . $key);
        }
        mtrace("IntelliBoard Tracking Cache Export CRON completed!");
    }
}
