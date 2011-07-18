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
 * Database enrolment plugin upgrade.
 *
 * @package    enrol
 * @subpackage database
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
function xmldb_enrol_database_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // fix leftovers after incorrect 2.x upgrade in install.php
    if ($oldversion < 2010073101) {
        unset_config('enrol_db_localrolefield');
        unset_config('enrol_db_remoterolefield');
        unset_config('enrol_db_disableunenrol');

        upgrade_plugin_savepoint(true, 2010073101, 'enrol', 'database');
    }


    return true;
}
