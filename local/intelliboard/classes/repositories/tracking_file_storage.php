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


class tracking_file_storage {

    public function saveData($ajaxRequest, $intelliboardTime, $intelliboardPage, $intelliboardParam){
        global $DB, $USER;

        $trackingstorage = new tracking_storage_repository($USER->id);
        $last_access = get_user_preferences('localintelliboardtracking_lastaccess', 0, $USER);

        if ($last_access <= (time() - $intelliboardTime)) {
            $data = new \stdClass();
            if (!$ajaxRequest) {
                $userDetails = (object)local_intelliboard_user_details();
                $courseid = 0;
                if ($intelliboardPage == "module") {
                    $courseid = $DB->get_field_sql("SELECT c.id FROM {course} c, {course_modules} cm WHERE c.id = cm.course AND cm.id = $intelliboardParam");
                } elseif ($intelliboardPage == "course") {
                    $courseid = $intelliboardParam;
                }
                $data->courseid = $courseid;
                $data->useragent = $userDetails->useragent;
                $data->useros = $userDetails->useros;
                $data->userlang = $userDetails->userlang;
                $data->userip = $userDetails->userip;
                $data->firstaccess = time();
                $data->visits = 1;
            }

            $data->userid = $USER->id;
            $data->page = $intelliboardPage;
            $data->param = $intelliboardParam;
            $data->timespend = $intelliboardTime;
            $data->lastaccess = time();
            $data->table = 'tracking';
            $data->ajaxrequest = $ajaxRequest;

            $trackingstorage->save_data(json_encode($data));

            set_user_preference('localintelliboardtracking_lastaccess', time(), $USER);

            $tracklogs = get_config('local_intelliboard', 'tracklogs');
            $trackdetails = get_config('local_intelliboard', 'trackdetails');

            $currentstamp = strtotime('today');
            if ($tracklogs) {
                $log = new \stdClass();
                $log->visits = (!$ajaxRequest) ? 1 : 0;
                $log->timespend = $intelliboardTime;
                $log->timepoint = $currentstamp;
                $log->table = 'logs';
                $log->ajaxrequest = $ajaxRequest;
                $log->userid = $USER->id;
                $log->page = $intelliboardPage;
                $log->param = $intelliboardParam;
                $trackingstorage->save_data(json_encode($log));


                if ($trackdetails) {
                    $currenthour = date('G');
                    $detail = new \stdClass();
                    $detail->visits = (!$ajaxRequest) ? 1 : 0;
                    $detail->timespend = $intelliboardTime;
                    $detail->timepoint = $currenthour;
                    $detail->currentstamp = $currentstamp;
                    $detail->table = 'details';
                    $detail->ajaxrequest = $ajaxRequest;
                    $detail->userid = $USER->id;
                    $detail->page = $intelliboardPage;
                    $detail->param = $intelliboardParam;
                    $trackingstorage->save_data(json_encode($detail));
                }
            }
        }
    }

    public function exportData(){
        global $DB;

        mtrace("IntelliBoard Tracking Files Export CRON started!");
        $trackingstorage = new tracking_storage_repository();
        $files = $trackingstorage->get_files();

        foreach ($files as $filename) {
            list($userid, $extension) = explode('.', $filename);

            if (!is_numeric($userid) || $extension != $trackingstorage::STORAGE_FILE_TYPE) {
                mtrace("Incorrect file " . $filename);
                $trackingstorage->delete_file($filename);
                continue; // something wrong
            }

            $tempfilepath = $trackingstorage->rename_file($filename);

            if (!$tempfilepath){
                mtrace("Error rename file " . $filename);
                continue; // something wrong
            }

            $data = [];
            $handle = @fopen($tempfilepath, "r");
            if ($handle) {
                while (($buffer = fgets($handle)) !== false) {
                    $record = json_decode($buffer);

                    if($record->table == 'tracking') {
                        if (isset($data[$record->userid][$record->page][$record->param][$record->table])) {
                            $item = &$data[$record->userid][$record->page][$record->param][$record->table];
                            if (isset($record->visits)) {
                                @$item['visits'] += $record->visits;
                            }
                            $item['timespend'] += $record->timespend;
                            $item['ajaxrequest'] = min($item['ajaxrequest'], $record->ajaxrequest);

                        } else {
                            $data[$record->userid][$record->page][$record->param][$record->table] = (array)$record;
                        }
                    } else if($record->table == 'logs') {
                        if (isset($data[$record->userid][$record->page][$record->param][$record->table][$record->timepoint])) {
                            $item = &$data[$record->userid][$record->page][$record->param][$record->table][$record->timepoint];
                            if (isset($record->visits)) {
                                @$item['visits'] += $record->visits;
                            }
                            $item['timespend'] += $record->timespend;
                            $item['ajaxrequest'] = min($item['ajaxrequest'], $record->ajaxrequest);

                        } else {
                            $data[$record->userid][$record->page][$record->param][$record->table][$record->timepoint] = (array)$record;
                        }
                    } else if($record->table == 'details') {
                        if (isset($data[$record->userid][$record->page][$record->param][$record->table][$record->currentstamp][$record->timepoint])) {
                            $item = &$data[$record->userid][$record->page][$record->param][$record->table][$record->currentstamp][$record->timepoint];
                            if (isset($record->visits)) {
                                @$item['visits'] += $record->visits;
                            }
                            $item['timespend'] += $record->timespend;
                            $item['ajaxrequest'] = min($item['ajaxrequest'], $record->ajaxrequest);

                        } else {
                            $data[$record->userid][$record->page][$record->param][$record->table][$record->currentstamp][$record->timepoint] = (array)$record;
                        }
                    }
                }
                if (!feof($handle)) {
                    mtrace("Error reading file " . $filename);
                }
                fclose($handle);
            }

            try {
                $transaction = $DB->start_delegated_transaction();

                foreach ($data as $user) {
                    foreach ($user as $page) {
                        foreach ($page as $param) {
                            $tr_record = (object)$param['tracking'];

                            if ($tracking = $DB->get_record('local_intelliboard_tracking', array('userid' => $tr_record->userid, 'page' => $tr_record->page, 'param' => $tr_record->param), 'id, visits, timespend, lastaccess')) {
                                if ($tracking->lastaccess < strtotime('today') || $tr_record->ajaxrequest == 0) {
                                    $tracking->lastaccess = $tr_record->lastaccess;
                                }
                                if (isset($tr_record->visits)) {
                                    $tracking->visits += $tr_record->visits;
                                }
                                $tracking->timespend += $tr_record->timespend;
                                $DB->update_record('local_intelliboard_tracking', $tracking);
                            } else {
                                $tracking = new \stdClass();
                                $tracking->id = $DB->insert_record('local_intelliboard_tracking', $tr_record, true);
                            }

                            $log_records = $param['logs'];
                            foreach ($log_records as $log_record) {
                                $log_record = (object)$log_record;
                                if ($log = $DB->get_record('local_intelliboard_logs', array('trackid' => $tracking->id, 'timepoint' => $log_record->timepoint))) {
                                    if (isset($log_record->visits)) {
                                        $log->visits += $log_record->visits;
                                    }
                                    $log->timespend += $log_record->timespend;
                                    $DB->update_record('local_intelliboard_logs', $log);
                                } else {
                                    $log = new \stdClass();
                                    $log->trackid = $tracking->id;
                                    $log->visits = $log_record->visits;
                                    $log->timespend = $log_record->timespend;
                                    $log->timepoint = $log_record->timepoint;
                                    $log->id = $DB->insert_record('local_intelliboard_logs', $log, true);
                                }

                                $detail_records = $param['details'][$log_record->timepoint];
                                foreach ($detail_records as $detail_record) {
                                    $detail_record = (object)$detail_record;
                                    if ($detail = $DB->get_record('local_intelliboard_details', array('logid' => $log->id, 'timepoint' => $detail_record->timepoint))) {
                                        if (isset($detail_record->visits)) {
                                            $detail->visits += $detail_record->visits;
                                        }
                                        $detail->timespend += $detail_record->timespend;
                                        $DB->update_record('local_intelliboard_details', $detail);
                                    } else {
                                        $detail = new \stdClass();
                                        $detail->logid = $log->id;
                                        $detail->visits = $detail_record->visits;
                                        $detail->timespend = $detail_record->timespend;
                                        $detail->timepoint = $detail_record->timepoint;
                                        $detail->id = $DB->insert_record('local_intelliboard_details', $detail, true);
                                    }
                                }
                            }
                        }
                    }
                }

                $transaction->allow_commit();
            } catch(Exception $e) {
                if (!empty($transaction) && !$transaction->is_disposed()) {
                    $transaction->rollback($e);
                }
            }

            $trackingstorage->delete_filepath($tempfilepath);
            mtrace("Successfull imported for user: " . $userid);
        }

        mtrace("IntelliBoard Tracking Files Export CRON completed!");
    }
}
