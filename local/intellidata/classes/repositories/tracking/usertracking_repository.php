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
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories\tracking;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\persistent\tracking;
use local_intellidata\persistent\trackinglogs;
use local_intellidata\persistent\trackingdetails;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class usertracking_repository {

    /** @var int The User ID. */
    private $userid = null;

    /** @var array The File Data. */
    private $userdata = null;

    /** @var null|\stdClass Tracking records. */
    private $trackingrecords = null;

    /** @var null|\stdClass Logs records. */
    private $logsrecords = null;

    /** @var null|\stdClass Details records. */
    private $detailsrecords = null;

    /**
     * Set data method.
     *
     * @param $userid
     * @param $userdata
     */
    public function set_data($userid, $userdata) {
        $this->userid = $userid;
        $this->userdata = $userdata;

        $this->trackingrecords = null;
        $this->logsrecords = null;
        $this->detailsrecords = null;
    }

    /**
     * Save tracking records.
     *
     * @return array
     * @throws \dml_exception
     */
    public function save_tracking_records($userid, $userdata) {

        // Reset data for new user.
        $this->set_data($userid, $userdata);

        // Extract tracking records from tracking storage.
        $this->trackingrecords = TrackingHelper::extract_tracking_records($this->userdata);

        // Retrieve existing tracking logs for update.
        $exitstingrecords = $this->get_tracking_records();

        // Insert new records.
        $isnewrecords = $this->insert_tracking_records($exitstingrecords);

        // Update existing records.
        if (count($exitstingrecords)) {
            $this->update_tracking_records($exitstingrecords);
        }

        // Get all records inserted + updated.
        $this->trackingrecords = ($isnewrecords) ? $this->get_tracking_records() : $exitstingrecords;

        // Process tracking logs.
        return $this->save_tracking_logs();
    }

    /**
     * Get existing tracking records from DB.
     *
     * @return array
     * @throws \dml_exception
     */
    private function get_tracking_records() {

        $sqlwhere = "userid = :userid";
        $wheres = [];
        $sqlparams = [];

        // Combine records for one user.
        $i = 0;
        foreach ($this->trackingrecords as $tracking) {
            $wheres[] = "(page = :page$i AND param = :param$i)";
            $sqlparams['page'.$i] = $tracking->page;
            $sqlparams['param'.$i] = $tracking->param;
            $i++;
        }

        if (count($wheres)) {
            $sqlwhere .= " AND (" . implode(' OR ', $wheres) . ")";
        }
        $sqlparams['userid'] = $this->userid;

        return tracking::get_tracking_records($sqlwhere, $sqlparams);
    }

    /**
     * Insert new tracking records.
     *
     * @param $exitstingrecords
     * @return int|void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function insert_tracking_records($exitstingrecords) {

        // Get existing records from DB.
        $newrecords = $this->get_new_tracking_records($exitstingrecords);

        if (count($newrecords)) {
            tracking::insert_records($newrecords);
        }

        return count($newrecords);
    }

    /**
     * Update existing tracking records.
     *
     * @param $exitstingrecords
     * @throws \dml_transaction_exception
     */
    private function update_tracking_records($exitstingrecords) {
        global $DB;

        try {
            $transaction = $DB->start_delegated_transaction();

            foreach ($this->trackingrecords as $trrecord) {
                if (isset($exitstingrecords[$trrecord->page . '_' . $trrecord->param])) {
                    tracking::update_record(
                        $exitstingrecords[$trrecord->page . '_' . $trrecord->param],
                        $trrecord
                    );
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
     * Exclude new tracking records.
     *
     * @param $exitstingrecords
     * @return array
     */
    private function get_new_tracking_records($exitstingrecords) {

        $recordstoinsert = [];
        foreach ($this->trackingrecords as $tracking) {
            if (!isset($exitstingrecords[$tracking->page . '_' . $tracking->param])) {
                $tracking->timemodified = time();
                $recordstoinsert[] = $tracking;
            }
        }

        return $recordstoinsert;
    }

    /**
     * Save tracking logs.
     *
     * @return array
     * @throws \dml_exception
     */
    private function save_tracking_logs() {

        // Extract tracking logs from tracking storage.
        $this->logsrecords = TrackingHelper::extract_tracking_logs($this->userdata, $this->trackingrecords);

        // Retrieve existing tracking logs for update.
        $exitstingrecords = $this->get_tracking_logs();

        // Insert new records.
        $isnewrecords = $this->insert_logs_records($exitstingrecords);

        // Update existing records.
        if (count($exitstingrecords)) {
            $this->update_logs_records($exitstingrecords);
        }

        // Get all records inserted + updated.
        $this->logsrecords = ($isnewrecords) ? $this->get_tracking_logs() : $exitstingrecords;

        // Process tracking details.
        return $this->save_tracking_details();
    }

    /**
     * Get existing tracking records from DB.
     *
     * @return array
     * @throws \dml_exception
     */
    private function get_tracking_logs() {

        $wheres = [];
        $sqlparams = [];

        // Combine records for one user.
        $i = 0;
        foreach ($this->logsrecords as $log) {
            $wheres[] = "(trackid = :trackid$i AND timepoint = :timepoint$i)";
            $sqlparams['trackid'.$i] = $log->trackid;
            $sqlparams['timepoint'.$i] = $log->timepoint;
            $i++;
        }

        if (!count($wheres)) {
            return [];
        }

        $sqlwhere = "(" . implode(' OR ', $wheres) . ")";
        return trackinglogs::get_logs_records($sqlwhere, $sqlparams);
    }

    /**
     * Insert new tracking records.
     *
     * @param $exitstingrecords
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function insert_logs_records($exitstingrecords) {

        // Get existing records from DB.
        $newrecords = $this->get_new_tracking_logs($exitstingrecords);

        if (count($newrecords)) {
            trackinglogs::insert_records($newrecords);
        }

        return count($newrecords);
    }

    /**
     * Update existing tracking records.
     *
     * @param $exitstingrecords
     * @throws \dml_transaction_exception
     */
    private function update_logs_records($exitstingrecords) {
        global $DB;

        try {
            $transaction = $DB->start_delegated_transaction();

            foreach ($this->logsrecords as $logrecord) {
                if (isset($exitstingrecords[$logrecord->trackid . '_' . $logrecord->timepoint])) {
                    trackinglogs::update_record(
                        $exitstingrecords[$logrecord->trackid . '_' . $logrecord->timepoint],
                        $logrecord
                    );
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
     * Exclude new tracking records.
     *
     * @param $exitstingrecords
     * @return array
     */
    private function get_new_tracking_logs($exitstingrecords) {

        $recordstoinsert = [];
        foreach ($this->logsrecords as $record) {
            if (!isset($exitstingrecords[$record->trackid . '_' . $record->timepoint])) {
                $record->timemodified = time();
                $recordstoinsert[] = $record;
            }
        }

        return $recordstoinsert;
    }

    /**
     * Save tracking logs.
     *
     * @return array
     * @throws \dml_exception
     */
    private function save_tracking_details() {

        // Extract tracking logs from tracking storage.
        $this->detailsrecords = TrackingHelper::extract_tracking_details(
            $this->userdata,
            $this->trackingrecords,
            $this->logsrecords
        );

        // Retrieve existing tracking logs for update.
        $exitstingrecords = $this->get_tracking_details();

        // Insert new records.
        $this->insert_details_records($exitstingrecords);

        // Update existing records.
        if (count($exitstingrecords)) {
            $this->update_details_records($exitstingrecords);
        }

        return true;
    }

    /**
     * Get existing tracking records from DB.
     *
     * @return array
     * @throws \dml_exception
     */
    private function get_tracking_details() {

        $wheres = [];
        $sqlparams = [];

        $i = 0;
        foreach ($this->detailsrecords as $record) {
            $wheres[] = "(logid = :logid$i AND timepoint = :timepoint$i)";
            $sqlparams['logid'.$i] = $record->logid;
            $sqlparams['timepoint'.$i] = $record->timepoint;
            $i++;
        }

        if (!count($wheres)) {
            return [];
        }

        $sqlwhere = implode(' OR ', $wheres);

        return trackingdetails::get_details_records($sqlwhere, $sqlparams);
    }

    /**
     * Insert new details records.
     *
     * @param $exitstingrecords
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function insert_details_records($exitstingrecords) {

        // Get existing records from DB.
        $newrecords = $this->get_new_tracking_details($exitstingrecords);

        if (count($newrecords)) {
            trackingdetails::insert_records($newrecords);
        }

        return count($newrecords);
    }

    /**
     * Update existing details records.
     *
     * @param $exitstingrecords
     * @throws \dml_transaction_exception
     */
    private function update_details_records($exitstingrecords) {
        global $DB;

        try {
            $transaction = $DB->start_delegated_transaction();

            foreach ($this->detailsrecords as $record) {
                if (isset($exitstingrecords[$record->logid . '_' . $record->timepoint])) {
                    trackingdetails::update_record(
                        $exitstingrecords[$record->logid . '_' . $record->timepoint],
                        $record
                    );
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
     * Exclude new details records.
     *
     * @param $exitstingrecords
     * @return array
     */
    private function get_new_tracking_details($exitstingrecords) {

        $recordstoinsert = [];
        foreach ($this->detailsrecords as $record) {
            if (!isset($exitstingrecords[$record->logid . '_' . $record->timepoint])) {
                $record->timemodified = time();
                $recordstoinsert[] = $record;
            }
        }

        return $recordstoinsert;
    }
}
