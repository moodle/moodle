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
class live_storage_repository extends storage_repository {

    /**
     * Save data.
     *
     * @param $trackdata
     * @return void
     * @throws \dml_transaction_exception
     */
    public function save_data($trackdata) {
        global $DB;
        try {
            $transaction = $DB->start_delegated_transaction();

            $tracklogs = (bool)SettingsHelper::get_setting('tracklogs');
            $trackdetails = (bool)SettingsHelper::get_setting('trackdetails');
            $trackparams = [
                'userid' => $trackdata->userid,
                'page' => $trackdata->page,
                'param' => $trackdata->param,
            ];
            $trackfields = 'id, visits, timespend, lastaccess';

            if ($tracking = $DB->get_record('local_intellidata_tracking', $trackparams, $trackfields)) {
                $this->fill_tracking($tracking, $trackdata);

                $DB->update_record('local_intellidata_tracking', $tracking);
            } else {
                $tracking = $this->get_default_tracking($trackdata);
                $tracking->id = $DB->insert_record('local_intellidata_tracking', $tracking, true);
            }

            if ($tracklogs) {
                $currentstamp = strtotime('today');
                $trlogparams = [
                    'trackid' => $tracking->id,
                    'timepoint' => $currentstamp,
                ];
                if ($log = $DB->get_record('local_intellidata_trlogs', $trlogparams)) {
                    $this->fill_log($log, $trackdata);
                    $DB->update_record('local_intellidata_trlogs', $log);
                } else {
                    $log = $this->get_default_log($trackdata, $tracking, $currentstamp);
                    $log->id = $DB->insert_record('local_intellidata_trlogs', $log, true);
                }

                if ($trackdetails) {
                    $currenthour = date('G');
                    $trdetparams = [
                        'logid' => $log->id,
                        'timepoint' => $currenthour,
                    ];

                    if ($detail = $DB->get_record('local_intellidata_trdetails', $trdetparams)) {
                        $this->fill_detail($detail, $trackdata);
                        $DB->update_record('local_intellidata_trdetails', $detail);
                    } else {
                        $detail = $this->get_default_log_detail($trackdata, $log, $currenthour);
                        $detail->id = $DB->insert_record('local_intellidata_trdetails', $detail, true);
                    }

                }
            }

            $transaction->allow_commit();
        } catch (\Exception $e) {
            if (!empty($transaction) && !$transaction->is_disposed()) {
                $transaction->rollback($e);
            }
        }
    }

    /**
     * Export data.
     *
     * @return void
     */
    public function export_data() {
        // Live data stored in database.
    }
}
