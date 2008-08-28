<?php  //$Id$

/**
 * Database transfer related code.
 * @author Andrei Bautu
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package dbtransfer
*/

require_once $CFG->libdir.'/xmldb/xmldb_structure.php';
require_once $CFG->libdir.'/ddl/database_manager.php';
require_once $CFG->dirroot.'/'.$CFG->admin.'/dbtransfer/exportlib.php';
require_once $CFG->dirroot.'/'.$CFG->admin.'/dbtransfer/importlib.php';

/**
 * Exception class for export operations.
 * @see moodle_exception
 * TODO subclass for specific purposes
 */
class export_exception extends moodle_exception {
    function __construct($errorcode, $a=null, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * Exception class for import operations.
 * @see moodle_exception
 * TODO subclass for specific purposes
 */
class import_exception extends moodle_exception {
    function __construct($errorcode, $a=null, $debuginfo=null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
