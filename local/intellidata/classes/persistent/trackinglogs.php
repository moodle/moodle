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
 * Class storage
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\persistent;

use local_intellidata\persistent\base;

/**
 * Class storage
 *
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class trackinglogs extends base {

    /** The table name. */
    const TABLE = 'local_intellidata_trlogs';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'trackid' => [
                'type' => PARAM_INT,
                'description' => 'Track ID.',
                'default' => 0,
            ],
            'visits' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Visits.',
            ],
            'timespend' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Time spent.',
            ],
            'timepoint' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Time point.',
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Timemodified timestamp.',
            ],
        ];
    }

    /**
     * Get existing tracking logs from DB.
     *
     * @param $select
     * @param null $params
     * @return array
     * @throws \dml_exception
     */
    public static function get_logs_records($select, $params = null) {

        $dbdata = self::get_records_select($select, $params);

        $records = [];
        if (count($dbdata)) {
            foreach ($dbdata as $data) {
                $data = $data->to_record();
                $records[$data->trackid . '_' . $data->timepoint] = $data;
            }
        }

        return $records;
    }

    /**
     * Update record.
     *
     * @param $records
     * @throws \dml_transaction_exception
     */
    public static function update_record($log, $logrecord) {
        global $DB;

        if (isset($logrecord->visits)) {
            $log->visits += $logrecord->visits;
        }
        $log->timespend += $logrecord->timespend;
        $log->timemodified = time();

        $DB->update_record(self::TABLE, $log);
    }
}
