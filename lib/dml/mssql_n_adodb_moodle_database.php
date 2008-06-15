<?php  //$Id$

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/mssql_adodb_moodle_database.php');

/**
 * MSSQL_N database class using adodb backend
 * @package dml
 */
class mssql_n_adodb_moodle_database extends mssql_adodb_moodle_database {

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'mssql_n';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        $str = get_string('databasesettingssub_mssql_n', 'install');
        $str .= "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
        $str .= "onclick=\"return window.open('http://docs.moodle.org/en/Installing_MSSQL_for_PHP')\"";
        $str .= ">";
        $str .= '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
        $str .= get_string('moodledocslink', 'install') . '</a></p>';
        return $str;
    }

}
