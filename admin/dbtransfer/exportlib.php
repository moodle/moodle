<?php  //$Id$

/**
 * General database import classes
 * @author Andrei Bautu
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package dbtransfer
 */

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
    /** Connection to the source database (a @see moodle_database object). */
    protected $mdb;
    /** Database manager of the source database (a @see database_manager object). */
    protected $manager;
    /** Source database schema in XMLDB format (a @see xmldb_structure object). */
    protected $schema;
    /**
     * Boolean flag - whether or not to check that XML database schema matches
     * the RDBMS database schema before exporting (used by
     * @see export_database).
     */
    protected $check_schema;

    /**
     * Object constructor.
     *
     * @param moodle_database $mdb Connection to the source database (a
     * @see moodle_database object).
     * @param xmldb_structure $schema Source database schema in XMLDB format (a
     * @see xmldb_structure object). Use null to load the schema from the
     * system's install.xml files.
     * @param boolean $check_schema - whether or not to check that XML database
     * schema matches the RDBMS database schema before exporting (used by
     * @see export_database).
     */
    public function __construct(moodle_database $mdb, xmldb_structure $schema=null, $check_schema=true) {
        if (is_null($schema)) {
            $schema = database_manager::get_install_xml_schema();
        }
        $this->mdb          = $mdb;
        $this->manager      = $mdb->get_manager();
        $this->schema       = $schema;
        $this->check_schema = $check_schema;
    }

    /**
     * Callback function. Should be called only once database per export
     * operation, before any other export operations. Subclasses should export
     * basic database information (version and timestamp).
     *
     * @param float $version the version of the system which generating the data
     * @param string $timestamp the timestamp of the data (in ISO 8601) format.
     * @param string $description a user description of the data.
     * @return void
     */
    public function begin_database_export($version, $timestamp, $description) {
    }

    /**
     * Callback function. Should be called only once per table export operation,
     * before any other table export operations. Subclasses should export
     * basic database information (name and schema's hash).
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     * @return void
     */
    public function begin_table_export(xmldb_table $table) {
    }

    /**
     * Callback function. Should be called only once per table export operation,
     * after all other table export operations.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     */
    public function finish_table_export(xmldb_table $table) {
    }

    /**
     * Callback function. Should be called only once database per export
     * operation, after all database export operations.
     */
    public function finish_database_export() {
    }

    /**
     * Callback function. Should be called only once per record export operation,
     * only between @see begin_table_export and @see finish_table_export calls.
     * It will insert table data. Subclasses should export basic record
     * information (data values).
     *
     * @param xmldb_table $table - XMLDB object of the table from which data was retrived
     * @param object $data - data object (fields and values from record)
     * @return void
     */
    public abstract function export_table_data(xmldb_table $table, $data);

    /**
     * Generic method to export the database. It checks the schema (if
     * @see $check_schema is true), queries the database and calls
     * appropiate callbacks.
     *
     * @exception export_exception if any checking (e.g. database schema) fails
     *
     * @param string $description a user description of the data.
     */
    public function export_database($description=null) {
        global $CFG;

        if ($this->check_schema && $this->manager->check_database_schema($this->schema)) {
            //TODO put message in error lang
            throw new export_exception('XMLDB schema does not match database schema.');
        }
        $tables = $this->schema->getTables();
        $this->begin_database_export($CFG->version, date('c'), $description);
        foreach ($tables as $table) {
            $rs = $this->mdb->get_recordset_sql('SELECT * FROM {'.$table->getName().'}');
            //TODO remove this when dml will have exceptions
            if (!$rs) {
                //TODO put message in error lang
                throw new export_exception('An error occured while reading the database.');
            }
            $this->begin_table_export($table);
            foreach ($rs as $row) {
                $this->export_table_data($table, $row);
            }
            $this->finish_table_export($table);
        }
        $this->finish_database_export();
    }

}

/**
 * XML format exporter class.
 * Provides logic for writing XML tags and data inside appropiate callbacks.
 * Subclasses should define XML data sinks.
 */
abstract class xml_database_exporter extends database_exporter {
    /**
     * Generic output method. Subclasses should implement it with code specific
     * to the target XML sink.
     */
    protected abstract function output($text);

    /**
     * Callback function. Outputs open XML PI and moodle_database opening tag.
     *
     * @param float $version the version of the system which generating the data
     * @param string $timestamp the timestamp of the data (in ISO 8601) format.
     * @param string $description a user description of the data.
     * @return void
     */
    public function begin_database_export($version, $timestamp, $description) {
        $this->output('<?xml version="1.0" encoding="utf-8"?>');
        //TODO add xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" and schema information
        $this->output('<moodle_database version="'.$version.'" timestamp="'.$timestamp.'"'.(empty ($description) ? '' : ' comment="'.htmlspecialchars($description, ENT_QUOTES).'"').'>');
    }

    /**
     * Callback function. Outputs table opening tag.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     * @return void
     */
    public function begin_table_export(xmldb_table $table) {
        $this->output('<table name="'.$table->getName().'" schemaHash="'.$table->getHash().'">');
    }

    /**
     * Callback function. Outputs table closing tag.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     */
    public function finish_table_export(xmldb_table $table) {
        $this->output('</table>');
    }

    /**
     * Callback function. Outputs moodle_database closing tag.
     */
    public function finish_database_export() {
        $this->output('</moodle_database>');
    }

    /**
     * Callback function. Outputs record tag with field subtags and data.
     *
     * @param xmldb_table $table - XMLDB object of the table from which data was retrived
     * @param object $data - data object (fields and values from record)
     * @return void
     */
    public function export_table_data(xmldb_table $table, $data) {
        $this->output('<record>');
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $this->output('<field name="'.$key.'" value="null" />');
            } else {
                $this->output('<field name="'.$key.'">'.htmlspecialchars($value, ENT_NOQUOTES).'</field>');
            }
        }
        $this->output('</record>');
    }
}

/**
 * XML format exporter class to file storage.
 */
class file_xml_database_exporter extends xml_database_exporter {
    /** Path to the XML data file. */
    protected $filepath;
    /** File descriptor for the output file. */
    protected $file;

    /**
     * Object constructor.
     *
     * @param string $filepath - path to the XML data file. Use null for PHP
     * output stream.
     * @param moodle_database $mdb Connection to the source database
     * @see xml_database_exporter::__construct()
     * @param xmldb_structure $schema Source database schema in XMLDB format
     * @see xml_database_exporter::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_exporter::__construct()
     */
    function __construct($filepath, moodle_database $mdb, xmldb_structure $schema=null, $check_schema=true) {
        parent::__construct($mdb, $schema, $check_schema);
        if (is_null($filepath)) {
            $filepath = 'php://output';
        }
        $this->filepath = $filepath;
    }

    /**
     * Specific output method for the file XML sink.
     */
    protected function output($text) {
        fwrite($this->file, $text);
    }

    /**
     * Specific implementation for file exporting the database: it opens output stream, calls
     * superclass @see database_exporter::export_database() and closes output stream.
     *
     * @exception export_exception if any checking (e.g. database schema) fails
     *
     * @param string $description a user description of the data.
     */
    public function export_database($description=null) {
        $this->file = fopen($this->filepath, 'wb');
        parent::export_database($description);
        fclose($this->file);
    }
}

/**
 * XML format exporter class to memory storage (i.e. a string).
 */
class string_xml_database_exporter extends xml_database_exporter {
    /** String with XML data. */
    protected $data;

    /**
     * Specific output method for the memory XML sink.
     */
    protected function output($text) {
        $this->data .= $text;
    }

    /**
     * Returns the output of the exporters
     * @return string XML data from exporter
     */
    public function get_output() {
        return $this->data;
    }

    /**
     * Specific implementation for memory exporting the database: it clear the buffer
     * and calls superclass @see database_exporter::export_database().
     *
     * @exception export_exception if any checking (e.g. database schema) fails
     * @param string $description a user description of the data.
     * @return void
     */
    public function export_database($description=null) {
        $this->data = '';
        parent::export_database($description);
    }
}

class database_mover extends database_exporter {
    /** Importer object used to transfer data. */
    protected $importer;

    /**
     * Object constructor.
     *
     * @param moodle_database $mdb_target Connection to the target database (a
     * @see moodle_database object).
     * @param moodle_database $mdb Connection to the source database (a
     * @see moodle_database object).
     * @param xmldb_structure $schema Source database schema in XMLDB format (a
     * @see xmldb_structure object). Use null to load the schema from the
     * system's install.xml files.
     * @param boolean $check_schema - whether or not to check that XML database
     * schema matches the RDBMS database schema before exporting (used by
     * @see export_database).
     */
    public function __construct(moodle_database $mdb_target, moodle_database $mdb_source, xmldb_structure $schema=null, $check_schema=true) {
        parent::__construct($mdb_source, $schema, $check_schema);
        $this->importer = new database_importer($mdb_target, $schema, $check_schema);
    }

    /**
     * Callback function. Calls importer's begin_database_import callback method.
     *
     * @param float $version the version of the system which generating the data
     * @param string $timestamp the timestamp of the data (in ISO 8601) format.
     * @param string $description a user description of the data.
     * @return void
     */
    public function begin_database_export($version, $timestamp, $description) {
        $this->importer->init_database();
        $this->importer->begin_database_import($version, $timestamp, $description);
    }

    /**
     * Callback function. Calls importer's begin_table_import callback method.
     *
     * @param xmldb_table $table - XMLDB object for the exported table
     * @return void
     */
    public function begin_table_export(xmldb_table $table) {
        $this->importer->begin_table_import($table->getName(), $table->getHash());
    }

    /**
     * Callback function. Calls importer's import_table_data callback method.
     *
     * @param xmldb_table $table - XMLDB object of the table from which data
     * was retrived
     * @param object $data - data object (fields and values from record)
     * @return void
     */
    public function export_table_data(xmldb_table $table, $data) {
        $this->importer->import_table_data($table->getName(), $data);
    }

    /**
     * Callback function. Calls importer's finish_table_import callback method.
     * @param xmldb_table $table - XMLDB object for the exported table
     * @return void
     */
    public function finish_table_export(xmldb_table $table) {
        $this->importer->finish_table_import($table->getName());
    }

    /**
     * Callback function. Calls importer's finish_database_import callback method.
     * @return void
     */
    public function finish_database_export() {
        $this->importer->finish_database_import();
    }
}
