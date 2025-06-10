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
class tracking extends base {

    /** The table name. */
    const TABLE = 'local_intellidata_tracking';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'courseid' => [
                'type' => PARAM_INT,
                'description' => 'Course ID.',
                'default' => 0,
            ],
            'page' => [
                'type' => PARAM_TEXT,
                'description' => 'Page identifier.',
            ],
            'param' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Page param.',
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
            'firstaccess' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'First access timestamp.',
            ],
            'lastaccess' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Last access timestamp.',
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'default' => 0,
                'description' => 'Timemodified timestamp.',
            ],
            'useragent' => [
                'type' => PARAM_TEXT,
                'description' => 'User Agent.',
            ],
            'ip' => [
                'type' => PARAM_TEXT,
                'description' => 'IP adress.',
            ],
        ];
    }

    /**
     * Get existing tracking records from DB.
     *
     * @param $select
     * @param null $params
     * @return array
     * @throws \dml_exception
     */
    public static function get_tracking_records($select, $params = null) {

        $dbdata = self::get_records_select($select, $params);

        $records = [];
        if (count($dbdata)) {
            foreach ($dbdata as $data) {
                $data = $data->to_record();
                $records[$data->page . '_' . $data->param] = $data;
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
    public static function update_record($tracking, $trrecord) {
        global $DB;

        if (isset($trrecord->lastaccess) &&
            ($tracking->lastaccess < strtotime('today') || $trrecord->ajaxrequest == 0)) {
            $tracking->lastaccess = $trrecord->lastaccess;
        }
        if (isset($trrecord->visits)) {
            $tracking->visits += $trrecord->visits;
        }
        $tracking->timespend += $trrecord->timespend;
        $tracking->useragent = $trrecord->useragent;
        $tracking->ip = $trrecord->ip;
        $tracking->timemodified = time();

        $DB->update_record(self::TABLE, $tracking);
    }
}
