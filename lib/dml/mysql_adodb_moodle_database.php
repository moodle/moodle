<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/mysqli_adodb_moodle_database.php');

/**
 * Legacy MySQL database class using adodb backend
 * @package dmlib
 */
class mysql_adodb_moodle_database extends mysqli_adodb_moodle_database {

    /**
     * Detects if all needed PHP stuff installed.
     * Do not try to connect to db if this test fails.
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('mysql')) {
            return get_string('mysqlextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'mysql';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        return get_string('databasesettingssub_mysql', 'install');
    }

}
