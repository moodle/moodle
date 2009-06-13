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
 * Experimenta mssql odbc database class using adodb backend
 *
 * TODO: delete before branching 2.0
 *
 * @package    moodlecore
 * @subpackage DML
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/dml/moodle_database.php');
require_once($CFG->libdir.'/dml/adodb_moodle_database.php');
require_once($CFG->libdir.'/dml/mssql_adodb_moodle_database.php');

/**
 * Experimenta mssql odbc database class using adodb backend
 */
class odbc_mssql_adodb_moodle_database extends mssql_adodb_moodle_database {

    /**
     * Detects if all needed PHP stuff installed.
     * Do not connect to connect to db if this test fails.
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('odbc')) {
            return get_string('odbcextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    /**
     * Returns database type
     * @return string db type mysql, mysqli, postgres7
     */
    protected function get_dbtype() {
        return 'odbc_mssql';
    }

    /**
     * Returns localised database description
     * Note: can be used before connect()
     * @return string
     */
    public function get_configuration_hints() {
        $str = get_string('databasesettingssub_odbc_mssql', 'install');
        $str .= "<p style='text-align:right'><a href=\"javascript:void(0)\" ";
        $str .= "onclick=\"return window.open('http://docs.moodle.org/en/Installing_MSSQL_for_PHP')\"";
        $str .= ">";
        $str .= '<img src="pix/docs.gif' . '" alt="Docs" class="iconhelp" />';
        $str .= get_string('moodledocslink', 'install') . '</a></p>';
        return $str;
    }

}
