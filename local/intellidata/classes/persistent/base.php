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

namespace local_intellidata\persistent;

use stdClass;

/**
 * Class base
 *
 * @copyright  2021 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
abstract class base extends \core\persistent {

    /** The table name. */
    const TABLE = null;

    /**
     * Create an instance of this class.
     *
     * @param int $id If set, this is the id of an existing record, used to load the data.
     * @param null $record If set will be passed to {@link self::from_record()}.
     * @throws \coding_exception
     */
    public function __construct($id = 0, $record = null) {
        if ($record) {
            $record = $this->clean_record($record);
        }
        parent::__construct($id, $record);
    }

    /**
     * Clean record.
     *
     * @param $record
     * @return stdClass
     * @throws \coding_exception
     */
    protected function clean_record($record) {
        $properties = static::properties_definition();
        $data = new stdClass();
        foreach ($record as $key => $value) {
            if (isset($properties[$key])) {
                $data->{$key} = $value;
            } else if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $data;
    }

    /**
     * Check if item exists
     *
     * @return bool
     * @throws \coding_exception
     */
    public function exists() {
        return ((int) $this->get('id'));
    }

    /**
     * Select column.
     *
     * @param string $fieldkey
     * @param string $fieldvalue
     * @param string $where
     * @param array $params
     * @param string $sort
     * @return array
     * @throws \coding_exception
     */
    public static function select_column(string $fieldkey, string $fieldvalue, string $where = '',
                                         array $params = [], string $sort = '') {
        $result = [];
        $instances = static::get_records_select($where, $params,  $sort, "$fieldkey, $fieldvalue");
        foreach ($instances as $instance) {
            $result[$instance->get($fieldkey)] = $instance->get($fieldvalue);
        }
        return $result;
    }

    /**
     * Bulk insert records to DB.
     *
     * @param $records
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function insert_records($records) {
        global $DB;

        try {
            $transaction = $DB->start_delegated_transaction();
            $DB->insert_records(static::TABLE, $records);
            $transaction->allow_commit();
        } catch (\Exception $e) {
            if (!empty($transaction) && !$transaction->is_disposed()) {
                $transaction->rollback($e);
            }
        }
    }

}
