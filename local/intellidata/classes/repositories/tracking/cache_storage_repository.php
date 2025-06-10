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

use core_message\helper;
use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\SettingsHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class cache_storage_repository extends storage_repository {

    /**
     * Save data to storage.
     *
     * @param $trackdata
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function save_data($trackdata) {
        global $DB;

        $cache = \cache::make('local_intellidata', 'tracking');
        $userkey = 'user_' . $trackdata->userid . '_page_' . $trackdata->page . '_param_' . $trackdata->param;
        if ($cache->has($userkey)) {
            $cacherecord = $cache->get($userkey);
        } else {
            $cacherecord = ['tracking' => null, 'logs' => [], 'details' => []];
        }

        if ($cacherecord['tracking'] != null) {
            $data = $cacherecord['tracking'];
        } else if (!$data = $DB->get_record('local_intellidata_tracking',
            ['userid' => $trackdata->userid, 'page' => $trackdata->page, 'param' => $trackdata->param],
            'id, visits, timespend, lastaccess')) {
            $data = $this->get_default_tracking($trackdata);
            $data->id = $DB->insert_record('local_intellidata_tracking', $data, true);
        }

        $this->fill_tracking($data, $trackdata);

        $tracklogs = SettingsHelper::get_setting('tracklogs');
        $trackdetails = SettingsHelper::get_setting('trackdetails');
        $currentstamp = strtotime('today');

        if ($tracklogs) {
            if (isset($cacherecord['logs'][$currentstamp])) {
                $log = $cacherecord['logs'][$currentstamp];
            } else if (!$log = $DB->get_record('local_intellidata_trlogs',
                ['trackid' => $data->id, 'timepoint' => $currentstamp])) {
                $log = $this->get_default_log($trackdata, $data, $currentstamp);
                $log->id = $DB->insert_record('local_intellidata_trlogs', $log, true);
            }

            $this->fill_log($log, $trackdata);

            $cacherecord['logs'][$currentstamp] = $log;

            if ($trackdetails) {
                $currenthour = date('G');

                if (isset($cacherecord['details'][$currentstamp][$currenthour])) {
                    $detail = $cacherecord['details'][$currentstamp][$currenthour];
                } else if (!(isset($log->id) && $detail = $DB->get_record('local_intellidata_trdetails',
                        ['logid' => $log->id, 'timepoint' => $currenthour]))) {
                    $detail = $this->get_default_log_detail($trackdata, $log, $currenthour);
                    $detail->id = $DB->insert_record('local_intellidata_trdetails', $detail, true);
                }

                $this->fill_detail($detail, $trackdata);

                $cacherecord['details'][$currentstamp][$currenthour] = $detail;
            }
        }

        $cacherecord['tracking'] = $data;

        if (!$cache->set($userkey, $cacherecord)) {
            // Something wrong.
            DebugHelper::error_log("IntelliData compress tracking: error save track to cache,
                key:{$userkey}, data:" . json_encode($cacherecord));
        }
    }

    /**
     * Export data to storage.
     *
     * @throws \coding_exception
     * @throws \dml_transaction_exception
     */
    public function export_data() {
        global $DB;

        mtrace("IntelliData Tracking Cache Export started!");
        $cache = \cache::make('local_intellidata', 'tracking');

        $keys = $cache->get_all_keys();
        foreach ($keys as $key) {
            mtrace("Cache key started: " . $key);
            $record = $cache->get($key);

            try {
                $transaction = $DB->start_delegated_transaction();
                $DB->update_record('local_intellidata_tracking', $record['tracking']);

                foreach ($record['logs'] as $log) {
                    $DB->update_record('local_intellidata_trlogs', $log);
                }

                foreach ($record['details'] as $logs) {
                    foreach ($logs as $detail) {
                        $DB->update_record('local_intellidata_trdetails', $detail);
                    }
                }
                $transaction->allow_commit();
            } catch (\Exception $e) {
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
        mtrace("IntelliData Tracking Cache Export CRON completed!");
    }
}
