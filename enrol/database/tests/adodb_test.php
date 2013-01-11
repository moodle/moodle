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
 * basic adodb connection test
 *
 * @package    enrol_database
 * @category   phpunit
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/enrol/database/lib.php');

class core_adodb_testcase extends advanced_testcase {
    public function test_read_table() {
        global $DB, $CFG;

        $this->resetAfterTest();

        set_config('dbencoding', 'utf-8', 'enrol_database');

        set_config('dbhost', $CFG->dbhost, 'enrol_database');
        set_config('dbuser', $CFG->dbuser, 'enrol_database');
        set_config('dbpass', $CFG->dbpass, 'enrol_database');
        set_config('dbname', $CFG->dbname, 'enrol_database');

        if (!empty($CFG->dboptions['dbport'])) {
            set_config('dbhost', $CFG->dbhost.':'.$CFG->dboptions['dbport'], 'enrol_database');
        }

        switch (get_class($DB)) {
            case 'mssql_native_moodle_database':
                set_config('dbtype', 'mssql_n', 'enrol_database');
                set_config('dbsybasequoting', '1', 'enrol_database');
                break;

            case 'mysqli_native_moodle_database':
                set_config('dbtype', 'mysqli', 'enrol_database');
                set_config('dbsetupsql', 'SET NAMES \'UTF-8\'', 'enrol_database');
                set_config('dbsybasequoting', '0', 'enrol_database');
                if (!empty($CFG->dboptions['dbsocket'])) {
                    set_config('dbtype', 'mysqli://'.rawurlencode($CFG->dbuser).':'.rawurlencode($CFG->dbpass).'@'.rawurlencode($CFG->dbhost).'/'.rawurlencode($CFG->dbname).'?socket='.rawurlencode($CFG->dboptions['dbsocket']), 'enrol_database');
                }
                break;

            case 'oci_native_moodle_database':
                set_config('dbtype', 'oci8po', 'enrol_database');
                set_config('dbsybasequoting', '1', 'enrol_database');
                break;

            case 'pgsql_native_moodle_database':
                set_config('dbtype', 'postgres7', 'enrol_database');
                $setupsql = "SET NAMES 'UTF-8'";
                if (!empty($CFG->dboptions['dbschema'])) {
                    $setupsql .= "; SET search_path = '".$CFG->dboptions['dbschema']."'";
                }
                set_config('dbsetupsql', $setupsql, 'enrol_database');
                set_config('dbsybasequoting', '0', 'enrol_database');
                if (!empty($CFG->dboptions['dbsocket']) and ($CFG->dbhost === 'localhost' or $CFG->dbhost === '127.0.0.1')) {
                    if (strpos($CFG->dboptions['dbsocket'], '/') !== false) {
                      set_config('dbhost', $CFG->dboptions['dbsocket'], 'enrol_database');
                    } else {
                      set_config('dbhost', '', 'enrol_database');
                    }
                }
                break;

            case 'sqlsrv_native_moodle_database':
                set_config('dbtype', 'mssqlnative', 'enrol_database');
                set_config('dbsybasequoting', '1', 'enrol_database');
                break;

            default:
                $this->markTestSkipped('Unknown database driver.');
                return;
        }

        $plugin = new enrol_database_tester();

        // can we connect?
        $extdb = $plugin->test_get_db_init();
        $this->assertNotEmpty($extdb);

        // let's fetch one row
        $sql = $plugin->test_db_get_sql($CFG->prefix.'user', array('id'=>2), array('id', 'username'));
        $rs = $extdb->Execute($sql);
        $this->assertEmpty($rs->EOF);
        $row = $rs->FetchRow();
        $row = array_change_key_case($row, CASE_LOWER);
        $this->assertEquals('2', $row['id']);
        $this->assertEquals('admin', $row['username']);
        $this->assertEmpty($rs->FetchRow());
        $rs->Close();

        $extdb->Close();
    }
}


class enrol_database_tester extends enrol_database_plugin {
    public function test_get_db_init() {
        return self::db_init();
    }

    public function test_db_get_sql($table, array $conditions, array $fields, $distinct = false, $sort = "") {
        return self::db_get_sql($table, $conditions, $fields, $distinct, $sort);
    }
}
