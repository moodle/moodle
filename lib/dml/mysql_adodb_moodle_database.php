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
 * Legacy MySQL database class using adodb backend.
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
require_once($CFG->libdir.'/dml/mysqli_adodb_moodle_database.php');

/**
 * Legacy MySQL database class using adodb backend
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
