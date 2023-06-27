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
 * This file keeps track of upgrades to the navigation block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 *
 * Definition of Edwiser Site monitor upgrade.
 *
 * @package    format_remuiformat
 * @copyright  2019 WisdmLabs <support@wisdmlabs.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     WisdmLabs
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Perform database upgrade
 * @param  int $oldversion Older plugin version
 * @return bool
 */
function xmldb_format_remuiformat_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2020061800) {
        // Define table format_remuiformat to be created.
        $table = new xmldb_table('format_remuiformat');

        // Adding fields to table format_remuiformat.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sectionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('activityid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('layouttype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table format_remuiformat.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

        // Adding indexes to table format_remuiformat.
        $table->add_index('formatoption', XMLDB_INDEX_UNIQUE, ['courseid', 'sectionid', 'activityid', 'layouttype']);

        // Conditionally launch create table for format_remuiformat.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Remuiformat savepoint reached.
        upgrade_plugin_savepoint(true, 2020061800, 'format', 'remuiformat');
    }

    if ($oldversion < 2021070800) {
        // Define table remuiformat_course_module_visits to be created.
        $table = new xmldb_table('remuiformat_course_visits');

        // Adding fields to table remuiformat_course_module_visits.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cm', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timevisited', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table remuiformat_course_module_visits.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table remuiformat_course_module_visits.
        $table->add_index('remuiformatvisits', XMLDB_INDEX_UNIQUE, ['course', 'user']);

        // Conditionally launch create table for remuiformat_course_module_visits.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Remuiformat savepoint reached.
        upgrade_plugin_savepoint(true, 2021070800, 'format', 'remuiformat');
    }

    return true;
}
