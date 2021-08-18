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
 * Database upgrade for MathType.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_filter_wiris_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016101701) {
         // Define table filter_wiris to be created.
        $table = new xmldb_table('filter_wiris_formulas');

        // Adding fields to table filter_wiris.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('md5', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'md5');

        // Adding keys to table filter_wiris.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('md5', XMLDB_KEY_UNIQUE, array('md5'));

        // Conditionally launch create table for filter_wiris.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Wiris savepoint reached.
        upgrade_plugin_savepoint(true, 2016101701, 'filter', 'wiris');

    }

    if ($oldversion < 2017030100) {

        // Define field jsoncontent to be added to filter_wiris_formulas.
        $table = new xmldb_table('filter_wiris_formulas');
        $field = new xmldb_field('jsoncontent', XMLDB_TYPE_TEXT, null, null, null, null, null, 'content');

        // Conditionally launch add field jsoncontent.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('alt', XMLDB_TYPE_TEXT, null, null, null, null, null, 'jsoncontent');

        // Conditionally launch add field alt.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Wiris savepoint reached.
        upgrade_plugin_savepoint(true, 2017030100, 'filter', 'wiris');
    }

    if ($oldversion < 2017050800) {

        // Define field timecreated to be added to filter_wiris_formulas.
        $table = new xmldb_table('filter_wiris_formulas');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'alt');

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Keys with utf8mb4 collation have a maxium of 191 characters
        // This collation is recommended in Moodle 3.3 and upcoming versions: https://tracker.moodle.org/browse/MDL-54901.

        // Define key md5 (unique) to be dropped form filter_wiris_formulas.
        $table = new xmldb_table('filter_wiris_formulas');
        $index = new xmldb_index('md5', XMLDB_INDEX_UNIQUE, array('md5'));

        $dbman->drop_index($table, $index);

        $table = new xmldb_table('filter_wiris_formulas');
        $field = new xmldb_field('md5', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, 'id');

        // Launch change of precision for field md5.
        $dbman->change_field_precision($table, $field);

        $key = new xmldb_key('md5', XMLDB_KEY_UNIQUE, array('md5'));

        // Launch add key md5.
        $dbman->add_key($table, $key);

        // Wiris savepoint reached.
        upgrade_plugin_savepoint(true, 2017050800, 'filter', 'wiris');
    }

    if ($oldversion < 2017102400) {
        unset_config('filter_wiris_editor_enable');
        unset_config('filter_wiris_chem_editor_enable');
        // Wiris savepoint reached.
        upgrade_plugin_savepoint(true, 2017102400, 'filter', 'wiris');
    }

    return true;
}
