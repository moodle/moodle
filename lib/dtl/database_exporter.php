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
 * General database export class
 *
 * @package    core_dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for database export operations. This class defines basic callbacks
 * for export operations and implements the @see export_database and
 * @export_table methods generic export processing. In general, subclasses will
 * override callback methods to provide specific output and (optionally)
 * @see export_database to add functionality.
 * Between a single pair of calls to @see begin_database_export and
 * @see finish_database_export, multiple non-overlapping pairs of calls may
 * be made to @see begin_table_export and @see finish_database_export for
 * different tables. Between one pair of calls to @see begin_table_export and
 * @see finish_database_export multiple calls may be made to
 * @see export_table_data for the same table.
 */
abstract class database_exporter {
    /** @var moodle_database Connection to the source database (a @see moodle_database object). */
    protected $mdb;
    /** @var database_manager Database manager of the source database (a @see database_manager object). */
    protected $manager;
    /** @var xmldb_structure Source database schema in XMLDB format (a @see xmldb_structure object). */
    protected $schema;
    /**
     * Boolean flag - whether or not to check that XML database schema matches
     * the RDBMS database schema before exporting (used by
     * @see export_database).
     * @var bool
     */
    protected $check_schema;

    /**
     * Object constructor.
     *
     * @param moodle_database $mdb Connection to the source database (a
     * @see moodle_database object).
     * @param boolean $check_schema - whether or not to check that XML database
     * schema matches the RDBMS database schema before exporting (used by
     * @see export_database).
     */
    public function __construct(moodle_database $mdb, $check_schema=true) {
        $this->mdb          = $mdb;
        $this->manager      = $mdb->get_manager();
        $this->schema       = $this->manager->get_install_xml_schema();
        $this->check_schema = $check_schema;
    }

    /**
     * Callback function. Should be called only once database per export
     * operation, before any other export operations. Subclasses should export
     * basic database information (version and timestamp).
     *
     * @param float $version the version of the system which generating the data
     * @param string $release moodle release info
     * @param string $timestamp the timestamp of the data (in ISO 8601) format.
     * @param string $description a user description of the data.
     * @return void
     */
    public abstract function begin_database_export($version, $release, $timestamp, $description);

    /**
     * Callback function. Should be called only once per table export operation,
     * before any other table export operations. Subclasses should export
     * basic database information (name and schema's hash).
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     * @return void
     */
    public abstract function begin_table_export(xmldb_table $table);

    /**
     * Callback function. Should be called only once per table export operation,
     * after all other table export operations.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     */
    public abstract function finish_table_export(xmldb_table $table);

    /**
     * Callback function. Should be called only once database per export
     * operation, after all database export operations.
     */
    public abstract function finish_database_export();

    /**
     * Callback function. Should be called only once per record export operation,
     * only between @see begin_table_export and @see finish_table_export calls.
     * It will insert table data. Subclasses should export basic record
     * information (data values).
     *
     * @param xmldb_table $table - XMLDB object of the table from which data was retrieved
     * @param object $data - data object (fields and values from record)
     * @return void
     */
    public abstract function export_table_data(xmldb_table $table, $data);

    /**
     * Generic method to export the database. It checks the schema (if
     * @see $check_schema is true), queries the database and calls
     * appropriate callbacks.
     *
     * @exception dbtransfer_exception if any checking (e.g. database schema) fails
     *
     * @param string $description a user description of the data.
     */
    public function export_database($description=null) {
        global $CFG;

        if ($this->check_schema and $errors = $this->manager->check_database_schema($this->schema)) {
            $details = '';
            foreach ($errors as $table=>$items) {
                $details .= '<div>'.get_string('tablex', 'dbtransfer', $table);
                $details .= '<ul>';
                foreach ($items as $item) {
                    $details .= '<li>'.$item.'</li>';
                }
                $details .= '</ul></div>';
            }
            throw new dbtransfer_exception('exportschemaexception', $details);
        }
        $tables = $this->schema->getTables();
        $this->begin_database_export($CFG->version, $CFG->release, date('c'), $description);
        foreach ($tables as $table) {
            $rs = $this->mdb->export_table_recordset($table->getName());
            if (!$rs) {
                throw new ddl_table_missing_exception($table->getName());
            }
            $this->begin_table_export($table);
            foreach ($rs as $row) {
                $this->export_table_data($table, $row);
            }
            $this->finish_table_export($table);
            $rs->close();
        }
        $this->finish_database_export();
    }
}
