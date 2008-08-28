<?php  //$Id$

/**
 * General database import classes
 * @author Andrei Bautu
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package dbtransfer
 */

/**
 * Base class for database import operations. This class implements
 * basic callbacks for import operations and defines the @see import_database
 * method as a common method for all importers. In general, subclasses will
 * override import_database and call other methods in appropriate moments.
 * Between a single pair of calls to @see begin_database_import and
 * @see finish_database_import, multiple non-overlapping pairs of calls may
 * be made to @see begin_table_import and @see finish_database_import for
 * different tables.
 * Between one pair of calls to @see begin_table_import and
 * @see finish_database_import multiple calls may be made to
 * @see import_table_data for the same table.
 * This class can be used directly, if the standard control flow (defined above)
 * is respected.
 */
class database_importer {
    /** Connection to the target database (a @see moodle_database object). */
    protected $mdb;
    /** Database manager of the target database (a @see database_manager object). */
    protected $manager;
    /** Target database schema in XMLDB format (a @see xmldb_structure object). */
    protected $schema;
    /**
     * Boolean flag - whether or not to check that XML database schema matches
     * the RDBMS database schema before importing (used by
     * @see begin_database_import).
     */
    protected $check_schema;

    /**
     * Object constructor.
     *
     * @param moodle_database $mdb Connection to the target database (a
     * @see moodle_database object). Use null to use the curent $DB connection.
     * @param xmldb_structure $schema Target database schema in XMLDB format (a
     * @see xmldb_structure object). Use null to load the schema from the
     * system's install.xml files.
     * @param boolean $check_schema - whether or not to check that XML database
     * schema matches the RDBMS database schema before importing (inside
     * @see begin_database_import).
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
     * Callback function. Should be called only once database per import
     * operation, before any database changes are made. It will check the database
     * schema if @see check_schema is true
     *
     * @exception import_exception if any checking (e.g. database schema, Moodle
     * version) fails
     *
     * @param float $version the version of the system which generated the data
     * @param string $timestamp the timestamp of the data (in ISO 8601) format.
     * @return void
     */
    public function begin_database_import($version, $timestamp) {
        global $CFG;

        if (round($version, 2) !== round($CFG->version, 2)) { // version might be in decimal format too
            //TODO put message in error lang
            throw new import_exception('Current Moodle version does not match exported Moodle version.');
        }
        if ($this->check_schema && $this->manager->check_database_schema($this->schema)) {
            //TODO put message in error lang
            throw new import_exception('XMLDB schema does not match database schema.');
        }
        $this->mdb->begin_sql();
    }

    /**
     * Callback function. Should be called only once per table import operation,
     * before any table changes are made. It will delete all table data.
     *
     * @exception import_exception an unknown table import is attempted
     * @exception ddl_table_missing_exception if the table is missing
     *
     * @param string $tablename - the name of the table that will be imported
     * @param string $schemaHash - the hash of the xmldb_table schema of the table
     * @return void
     */
    public function begin_table_import($tablename, $schemaHash) {
        $table = $this->schema->getTable($tablename);
        if (is_null($table)) {
            //TODO put message in error lang
            throw new import_exception('Unknown table in import data');
        }
        if ($schemaHash != $table->getHash()) {
            throw new import_exception('XMLDB schema does not match database schema.');
        }
        // this should not happen, unless someone drops tables after import started
        if (!$this->manager->table_exists($table)) {
            // in the future, missing tables will be recreated with
            //$this->manager->create_table($table);
            throw new ddl_table_missing_exception($tablename);
        }
        $this->mdb->delete_records($tablename);
    }

    /**
     * Callback function. Should be called only once per table import operation,
     * after all table changes are made. It will reset table sequences if any.
     * @param string $tablename
     * @return void
     */
    public function finish_table_import($tablename) {
        $table  = $this->schema->getTable($tablename);
        $fields = $table->getFields();
        foreach ($fields as $field) {
            if ($field->getSequence()) {
                //TODO make sure that there aren't tables using two sequences (probably there is none)
                $this->mdb->reset_sequence($tablename);
                return;
            }
        }
    }

    /**
     * Callback function. Should be called only once database per import
     * operation, after all database changes are made. It will commit changes.
     * @return void
     */
    public function finish_database_import() {
        $this->mdb->commit_sql();
    }

    /**
     * Callback function. Should be called only once per record import operation, only
     * between @see begin_table_import and @see finish_table_import calls.
     * It will insert table data.
     *
     * @exception dml_exception if data insert operation failed
     *
     * @param string $tablename - the name of the table in which data will be
     * imported
     * @param object $data - data object (fields and values will be inserted
     * into table)
     * @return void
     */
    public function import_table_data($tablename, $data) {
        $this->mdb->import_record($tablename, $data);
    }

    /**
     * All subclases must call this
     * @return void
     */
    public function init_database() {
        if (!$this->mdb->get_tables()) {
            // not tables yet, time to create all tables
            $this->manager->install_from_xmldb_structure($this->schema);
        }
    }
}

/**
 * XML format importer class (uses SAX for speed and low memory footprint).
 * Provides logic for parsing XML data and calling appropiate callbacks.
 * Subclasses should define XML data sources.
 */
abstract class xml_database_importer extends database_importer {
    /**
     * Creates and setups a SAX parser. Subclasses should use this method to
     * create the XML parser.
     *
     * @return resource XML parser resource.
     */
    protected function get_parser() {
        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($parser, 'cdata');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        return $parser;
    }

    /**
     * Callback function. Called by the XML parser for opening tags processing.
     *
     * @param resource $parser XML parser resource.
     * @param string $tag name of opening tag
     * @param array $attributes set of opening tag XML attributes
     * @return void
     */
    protected function tag_open($parser, $tag, $attributes) {
        switch ($tag) {
            case 'moodle_database' :
                $this->begin_database_import($attributes['version'], $attributes['timestamp']);
                break;
            case 'table' :
                $this->current_table = $attributes['name'];
                $this->begin_table_import($this->current_table, $attributes['schemaHash']);
                break;
            case 'record' :
                $this->current_row = new object();
                break;
            case 'field' :
                $this->current_field = $attributes['name'];
                $this->current_data = @$attributes['value'] == 'null' ? null : '';
                break;
            default :
                //TODO put message in error lang
                throw new import_exception('XML content not valid for import operation.');
        }
    }

    /**
     * Callback function. Called by the XML parser for closing tags processing.
     *
     * @param resource $parser XML parser resource.
     * @param string $tag name of opening tag
     * @return void
     */
    protected function tag_close($parser, $tag) {
        switch ($tag) {
            case 'moodle_database' :
                $this->finish_database_import();
                break;
            case 'table' :
                $this->finish_table_import($this->current_table);
                unset ($this->current_table);
                break;
            case 'record' :
                $this->import_table_data($this->current_table, $this->current_row);
                unset ($this->current_row);
                break;
            case 'field' :
                $field = $this->current_field;
                unset ($this->current_field);
                $this->current_row-> $field = $this->current_data;
                unset ($this->current_data);
                break;
            default :
                //TODO put message in error lang
                throw new import_exception('XML content not valid for import operation.');
        }
    }

    /**
     * Callback function. Called by the XML parser for character data processing.
     *
     * @param resource $parser XML parser resource.
     * @param string $data character data to be processed
     * @return void
     */
    protected function cdata($parser, $cdata) {
        if (isset($this->current_field)) {
            $this->current_data .= $cdata;
        }
    }

    /**
     * Common import method
     * @return void
     */
    public abstract function import_database();
}

/**
 * XML format importer class from file storage.
 */
class file_xml_database_importer extends xml_database_importer {
    /** Path to the XML data file. */
    protected $filepath;

    /**
     * Object constructor.
     *
     * @param string $filepath - path to the XML data file. Use null for PHP
     * input stream.
     * @param moodle_database $mdb Connection to the target database
     * @see xml_database_importer::__construct()
     * @param xmldb_structure $schema Target database schema in XMLDB format
     * @see xml_database_importer::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_importer::__construct()
     */
    public function __construct($filepath, moodle_database $mdb, xmldb_structure $schema=null) {
        parent::__construct($mdb, $schema);
        if (is_null($filepath)) {
            $filepath = 'php://input';
        }
        $this->filepath = $filepath;
    }

    /**
     * Common import method: it opens the file storage, creates the parser, feeds
     * the XML parser with data, releases the parser and closes the file storage.
     * @return void
     */
    public function import_database() {
        $this->init_database();
        $file = fopen($this->filepath, 'r');
        $parser = $this->get_parser();
        while (($data = fread($file, 65536)) && xml_parse($parser, $data, feof($file)));
        xml_parser_free($parser);
        fclose($file);
    }
}

/**
 * XML format importer class from memory storage (i.e. string).
 */
class string_xml_database_importer extends xml_database_importer {
    /** String with XML data. */
    protected $data;

    /**
     * Object constructor.
     *
     * @param string data - string with XML data
     * @param moodle_database $mdb Connection to the target database
     * @see xml_database_importer::__construct()
     * @param xmldb_structure $schema Target database schema in XMLDB format
     * @see xml_database_importer::__construct()
     * @param boolean $check_schema - whether or not to check that XML database
     * @see xml_database_importer::__construct()
     */
    public function __construct($data, moodle_database $mdb, xmldb_structure $schema=null) {
        parent::__construct($mdb, $schema);
        $this->data = $data;
    }

    /**
     * Common import method: it creates the parser, feeds the XML parser with
     * data, releases the parser.
     * @return void
     */
    public function import_database() {
        $this->init_database();
        $parser = $this->get_parser();
        xml_parse($parser, $this->data, true);
        xml_parser_free($parser);
    }
}
