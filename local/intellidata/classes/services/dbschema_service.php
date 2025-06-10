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
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\services;

use local_intellidata\repositories\optional_tables_repository;
use local_intellidata\repositories\system_tables_repository;
use local_intellidata\helpers\DBManagerHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class dbschema_service {

    /**  @var \moodle_database|null */
    protected $mdb = null;
    /** @var array */
    protected $tables  = [];

    /**
     * Table updated fields.
     */
    const TABLE_UPDATED_FIELDS = [
        'timemodified',
        'timeupdated',
    ];

    /**
     * Table fields params.
     */
    const TABLE_FIELDS_PARAMS = [
        'name',
        'type',
        'max_length',
        'not_null',
        'primary_key',
        'auto_increment',
        'has_default',
        'default_value',
        'unique',
    ];

    /**
     * DB schema service construct.
     */
    public function __construct() {
        global $DB;

        $this->mdb = $DB;
    }

    /**
     * Get tables.
     *
     * @param bool $export
     * @return array
     */
    public function get_tables($export = false) {

        // Tables list from database.
        $dbtables = $this->get_tableslist();

        // Tables list with keys from install.xml.
        $xmltables = DBManagerHelper::get_install_xml_tables();

        // Get datatypes config.
        $configservice = new config_service(datatypes_service::get_all_datatypes());
        $datatypes = $configservice->get_datatypes();

        $exportres = [];
        foreach ($dbtables as $tablename) {
            $table = [
                'name' => $tablename,
                'fields' => $this->get_table_fields($tablename),
                'config' => $datatypes[datatypes_service::generate_optional_datatype($tablename)] ?? [],
            ];

            // Merge with install.xml keys and set plugintype/pluginname.
            $this->tables[$tablename] = (isset($xmltables[$tablename]))
                ? array_merge($table, $xmltables[$tablename])
                : $table;

            if ($export) {
                $exportres[datatypes_service::generate_optional_datatype($tablename)] = $this->tables[$tablename];
            }
        }

        return $export ? $exportres : $this->tables;
    }

    /**
     * Get tables list.
     *
     * @return array
     */
    public function get_tableslist() {
        $tables = $this->get_all_tableslist();

        // Exclude not existing DB tables.
        $tables = optional_tables_repository::exclude_tables($tables);

        // Exclude system tables.
        $tables = system_tables_repository::exclude_tables($tables);

        return $tables;
    }

    /**
     * Get all tables list.
     *
     * @return array
     */
    public function get_all_tableslist() {
        return $this->mdb->get_tables(false);
    }

    /**
     * Get table columns.
     *
     * @return array
     */
    public function get_table_columns($table) {
        return $this->mdb->get_columns($table);
    }

    /**
     * Column exists.
     *
     * @param $table
     * @param $column
     * @return bool
     */
    public function column_exists($table, $column) {

        $tablecolumns = $this->get_table_columns($table);

        if (isset($tablecolumns[$column])) {
            return true;
        }

        return false;
    }

    /**
     * Table exists.
     *
     * @return array
     */
    public function table_exists($table) {
        return count($this->mdb->get_columns($table)) ? true : false;
    }

    /**
     * Get updated field name.
     *
     * @return array
     */
    public function get_updated_fieldname($table) {
        $dbcolumns = $this->get_table_columns($table);
        $updatefield = null;
        $allowedfieldtypes = ['int', 'bigint'];

        if (count($dbcolumns)) {
            foreach ($dbcolumns as $columnname => $column) {

                // Allow only specific field names.
                if (!in_array($columnname, self::TABLE_UPDATED_FIELDS)) {
                    continue;
                }

                // Allow only specific field types.
                if (!in_array($column->type, $allowedfieldtypes)) {
                    continue;
                }

                $updatefield = $columnname;
                break;
            }
        }

        return $updatefield;
    }

    /**
     * Get available updates field names.
     *
     * @return array
     */
    public function get_available_updates_fieldnames($table) {
        $dbcolumns = $this->get_table_columns($table);
        $fields = [];
        $allowedfieldtypes = ['int', 'bigint'];

        if (count($dbcolumns)) {
            foreach ($dbcolumns as $columnname => $column) {

                // Exclude ID field.
                if ($columnname == 'id') {
                    continue;
                }

                // Allow only specific field types.
                if (!in_array($column->type, $allowedfieldtypes) &&
                    !(($column->max_length == 10) && $column->type == 'NUMBER')) {
                    continue;
                }

                $fields[$columnname] = $columnname;
            }
        }

        return $fields;
    }

    /**
     * Get table fields.
     *
     * @param $tablename
     * @return array
     */
    protected function get_table_fields($tablename) {
        $tablecolumns = $this->mdb->get_columns($tablename, false);
        $fields = [];

        foreach ($tablecolumns as $column) {

            if (!$column instanceof \database_column_info) {
                $column = new \database_column_info($column);
            }

            $fields[$column->name] = $this->get_table_field($column);
        }

        $fields = $this->apply_default_fields($fields);

        return $fields;
    }

    /**
     * Get table field.
     *
     * @param $column
     * @return array
     */
    protected function get_table_field($column) {
        $field = [];

        foreach (self::TABLE_FIELDS_PARAMS as $paramname) {
            if (isset($column->{$paramname})) {
                $field[$paramname] = $column->{$paramname};
            }
        }

        return $field;
    }

    /**
     * Apply default fields.
     *
     * @param $fields
     * @return mixed
     */
    protected function apply_default_fields($fields) {

        $fields['recordtimecreated'] = [
            'name' => 'recordtimecreated',
            'type' => 'float',
            'max_length' => '19',
            'not_null' => false,
            'primary_key' => false,
            'auto_increment' => false,
            'has_default' => true,
            'default_value' => '0',
        ];

        $fields['recordusermodified'] = [
            'name' => 'recordusermodified',
            'type' => 'bigint',
            'max_length' => '19',
            'not_null' => false,
            'primary_key' => false,
            'auto_increment' => false,
            'has_default' => true,
            'default_value' => '0',
        ];

        $fields['crud'] = [
            'name' => 'crud',
            'type' => 'varchar',
            'max_length' => '50',
            'not_null' => false,
            'primary_key' => false,
            'auto_increment' => false,
            'has_default' => true,
            'default_value' => 'c',
        ];

        return $fields;
    }

    /**
     * Export tables.
     *
     * @return array
     */
    public function export() {
        return $this->get_tables(true);
    }
}
