<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/mssql_adodb_moodle_database.php');

/**
 * Experimenta mssql odbc database class using adodb backend
 * @package dmlib
 */
class odbc_mssql_adodb_moodle_database extends mssql_adodb_moodle_database {
    function __construct ($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix) {
        parent::__construct($dbhost, $dbuser, $dbpass, $dbname, false, $prefix);
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'odbc_mssql';
    }

    protected function configure_dbconnection() {
        if (!defined('ADODB_ASSOC_CASE')) {
            define ('ADODB_ASSOC_CASE', 0);
        }
        $this->db->SetFetchMode(ADODB_ASSOC_CASE);

        /// No need to set charset. It must be specified in the driver conf
        /// Allow quoted identifiers
            $this->db->Execute('SET QUOTED_IDENTIFIER ON');
        /// Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        /// instead of equal(=) and distinct(<>) simbols
            $this->db->Execute('SET ANSI_NULLS ON');
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO CHANGE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly

        return true;
    }

}
