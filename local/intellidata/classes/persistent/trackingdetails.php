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
class trackingdetails extends base {

    /** The table name. */
    const TABLE = 'local_intellidata_trdetails';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'logid' => [
                'type' => PARAM_INT,
                'description' => 'Log ID.',
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
    public static function get_details_records($select, $params = null) {

        $dbdata = self::get_records_select($select, $params);

        $records = [];
        if (count($dbdata)) {
            foreach ($dbdata as $data) {
                $data = $data->to_record();
                $records[$data->logid . '_' . $data->timepoint] = $data;
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
    public static function update_record($detail, $detailrecord) {
        global $DB;

        if (isset($detailrecord->visits)) {
            $detail->visits += $detailrecord->visits;
        }
        $detail->timespend += $detailrecord->timespend;
        $detail->timemodified = time();

        $DB->update_record(self::TABLE, $detail);
    }
}
