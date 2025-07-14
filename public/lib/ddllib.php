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
 * This library includes all the required functions used to handle the DB
 * structure (DDL) independently of the underlying RDBMS in use.
 *
 * This library includes all the required functions used to handle the DB
 * structure (DDL) independently of the underlying RDBMS in use. All the functions
 * rely on the XMLDBDriver classes to be able to generate the correct SQL
 * syntax needed by each DB.
 *
 * To define any structure to be created we'll use the schema defined
 * by the XMLDB classes, for tables, fields, indexes, keys and other
 * statements instead of direct handling of SQL sentences.
 *
 * This library should be used, exclusively, by the installation and
 * upgrade process of Moodle.
 *
 * For further documentation, visit {@link http://docs.moodle.org/en/DDL_functions}
 *
 * @package    core
 * @subpackage ddl
 * @copyright  2001-3001 Eloy Lafuente (stronk7) http://contiento.com
 *             2008 Petr Skoda                   http://skodak.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Add required library
require_once($CFG->libdir.'/xmlize.php');

// Add required XMLDB constants
require_once($CFG->libdir.'/xmldb/xmldb_constants.php');

// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_object.php');
// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_file.php');
// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_structure.php');
// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_table.php');
// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_field.php');
// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_key.php');
// Add required XMLDB DB classes
require_once($CFG->libdir.'/xmldb/xmldb_index.php');

require_once($CFG->libdir.'/ddl/sql_generator.php');
require_once($CFG->libdir.'/ddl/database_manager.php');



/**
 * DDL exception class, use instead of throw new \moodle_exception() and "return false;" in ddl code.
 */
class ddl_exception extends moodle_exception {
    /**
     * @param string $errorcode
     * @param string $debuginfo
     */
    function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * Table does not exist problem exception
 */
class ddl_table_missing_exception extends ddl_exception {
    /**
     * @param string $tablename
     * @param string $debuginfo
     */
    function __construct($tablename, $debuginfo=null) {
        parent::__construct('ddltablenotexist', $tablename, $debuginfo);
    }
}

/**
 * Table does not exist problem exception
 */
class ddl_field_missing_exception extends ddl_exception {
    /**
     * @param string $fieldname
     * @param string $tablename
     * @param string $debuginfo
     */
    function __construct($fieldname, $tablename, $debuginfo=null) {
        $a = new stdClass();
        $a->fieldname = $fieldname;
        $a->tablename = $tablename;
        parent::__construct('ddlfieldnotexist', $a, $debuginfo);
    }
}

/**
 * Error during changing db structure
 */
class ddl_change_structure_exception extends ddl_exception {
    /** @var string */
    public $error;
    public $sql;
    /**
     * @param string $error
     * @param string $sql
     */
    function __construct($error, $sql=null) {
        $this->error = $error;
        $this->sql   = $sql;
        $errorinfo   = $error."\n".$sql;
        parent::__construct('ddlexecuteerror', NULL, $errorinfo);
    }
}

/**
 * Error changing db structure, caused by some dependency found
 * like trying to modify one field having related indexes.
 */
class ddl_dependency_exception extends ddl_exception {

    function __construct($targettype, $targetname, $offendingtype, $offendingname, $debuginfo=null) {
        $a = new stdClass();
        $a->targettype = $targettype;
        $a->targetname = $targetname;
        $a->offendingtype = $offendingtype;
        $a->offendingname = $offendingname;

        parent::__construct('ddldependencyerror', $a, $debuginfo);
    }
}
