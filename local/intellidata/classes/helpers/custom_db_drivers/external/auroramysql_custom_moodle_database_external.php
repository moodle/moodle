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
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

use local_intellidata\helpers\DebugHelper;
use local_intellidata\services\new_export_service;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../../../lib/dml/auroramysql_native_moodle_database.php');

/**
 * Custom mysqli class representing moodle database interface.
 *
 * @package    local_intellidata
 * @copyright  2023 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class auroramysql_custom_moodle_database_external extends \auroramysql_native_moodle_database {

    /**
     * Insert new record into database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool $returnit return it of inserted record
     * @param bool $bulk true means repeated inserts expected
     * @param bool $customsequence true if 'id' included in $params, disables $returnid
     * @return bool|int true or new id
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function insert_record_raw($table, $params, $returnid = true, $bulk = false, $customsequence = false) {
        $id = parent::insert_record_raw($table, $params, true, $bulk, $customsequence);

        try {
            $exportservice = new new_export_service();
            $data = (object)$params;
            $data->id = $id;
            $exportservice->insert_record_event($table, $data);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        if (!$returnid) {
            return true;
        } else {
            return (int)$id;
        }
    }

    /**
     * Insert multiple records into database as fast as possible.
     *
     * Order of inserts is maintained, but the operation is not atomic,
     * use transactions if necessary.
     *
     * This method is intended for inserting of large number of small objects,
     * do not use for huge objects with text or binary fields.
     *
     * @param string $table The database table to be inserted into
     * @param array|Traversable $dataobjects list of objects to be inserted, must be compatible with foreach
     * @return void does not return new record ids
     *
     * @throws coding_exception if data objects have different structure
     * @throws dml_exception A DML specific exception is thrown for any errors.
     * @since Moodle 2.7
     *
     */
    public function insert_records($table, $dataobjects) {
        parent::insert_records($table, $dataobjects);

        try {
            $exportservice = new new_export_service();
            $exportservice->insert_records_event($table, $dataobjects);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }
    }

    /**
     * Update record in database, as fast as possible, no safety checks, lobs not supported.
     * @param string $table name
     * @param mixed $params data record as object or array
     * @param bool true means repeated updates expected
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function update_record_raw($table, $params, $bulk = false) {
        $status = parent::update_record_raw($table, $params, $bulk);

        try {
            $exportservice = new new_export_service();
            $data = (object)$params;
            $exportservice->update_record_event($table, $data);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        return $status;
    }

    /**
     * Set a single field in every table record which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $newfield the field to set.
     * @param string $newvalue the value to set the field to.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call.
     * @param array $params array of sql parameters
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function set_field_select($table, $newfield, $newvalue, $select, array $params = null) {
        $status = parent::set_field_select($table, $newfield, $newvalue, $select, $params);

        if ($select) {
            $select = "WHERE $select";
        }

        if (is_null($params)) {
            $params = [];
        }

        list($select, $params, $type) = $this->fix_sql_params($select, $params);

        try {
            (new new_export_service())->set_field_select_event($table, $select, $params);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        return $status;
    }

    /**
     * Delete the records from a table where all the given conditions met.
     * If conditions not specified, table is truncated.
     *
     * @param string $table the table to delete from.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return bool true.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete_records($table, array $conditions = null) {
        $status = parent::delete_records($table, $conditions);
        try {
            $exportservice = new new_export_service();
            $exportservice->delete_record_event($table, $conditions);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        return $status;
    }

    /**
     * Delete the records from a table where one field match one list of values.
     *
     * @param string $table the table to delete from.
     * @param string $field The field to search
     * @param array $values array of values
     * @return bool true.
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete_records_list($table, $field, array $values) {

        try {
            $exportservice = new new_export_service();
            $exportservice->delete_records_event($table, $field, $values);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        return parent::delete_records_list($table, $field, $values);
    }

    /**
     * Deletes records using a subquery, which is done with a strange DELETE...JOIN syntax in MySQL
     * because it performs very badly with normal subqueries.
     *
     * @param string $table Table to delete from
     * @param string $field Field in table to match
     * @param string $alias Name of single column in subquery e.g. 'id'
     * @param string $subquery Query that will return values of the field to delete
     * @param array $params Parameters for query
     * @throws dml_exception If there is any error
     */
    public function delete_records_subquery(string $table, string $field, string $alias,
                                            string $subquery, array $params = []): void {
        try {
            $exportservice = new new_export_service();
            $exportservice->delete_records_select_event($table, $field . ' IN (' . $subquery . ')', $params);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        parent::delete_records_subquery($table, $field, $alias, $subquery, $params);
    }

    /**
     * Delete one or more records from a table which match a particular WHERE clause.
     *
     * @param string $table The database table to be checked against.
     * @param string $select A fragment of SQL to be used in a where clause in the SQL call (used to define the selection criteria).
     * @param array $params array of sql parameters
     * @return bool true
     * @throws dml_exception A DML specific exception is thrown for any errors.
     */
    public function delete_records_select($table, $select, array $params = null) {
        try {
            $exportservice = new new_export_service();
            $exportservice->delete_records_select_event($table, $select, $params);
        } catch (Exception $e) {
            DebugHelper::error_log($e->getMessage());
        }

        return parent::delete_records_select($table, $select, $params);
    }
}
