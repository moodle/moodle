<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/mssql_adodb_moodle_database.php');

/**
 * MSSQL_N database class using adodb backend
 * @package dmlib
 */
class mssql_n_adodb_moodle_database extends mssql_adodb_moodle_database {
    function __construct ($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix) {
        parent::__construct($dbhost, $dbuser, $dbpass, $dbname, false, $prefix);
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'mssql_n';
    }

}
