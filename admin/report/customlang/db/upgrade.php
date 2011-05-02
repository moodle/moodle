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
 * Language customization report upgrades
 *
 * @package    report
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_report_customlang_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();
    $result = true;

    /**
     * Use proper plugin prefix for tables
     */
    if ($oldversion < 2010111200) {
        if ($dbman->table_exists('customlang')) {
            $dbman->rename_table(new xmldb_table('customlang'), 'report_customlang');
        }
        if ($dbman->table_exists('customlang_components')) {
            $dbman->rename_table(new xmldb_table('customlang_components'), 'report_customlang_components');
        }
        upgrade_plugin_savepoint(true, 2010111200, 'report', 'customlang');
    }

    /**
     * Regenerate the foreign key after the tables rename
     */
    if ($oldversion < 2010111500) {
        $table = new xmldb_table('report_customlang');
        $oldkey = new xmldb_key('fk_component', XMLDB_KEY_FOREIGN, array('componentid'), 'customlang_components', array('id'));
        $newkey = new xmldb_key('fk_component', XMLDB_KEY_FOREIGN, array('componentid'), 'report_customlang_components', array('id'));

        $dbman->drop_key($table, $oldkey);
        $dbman->add_key($table, $newkey);

        upgrade_plugin_savepoint(true, 2010111500, 'report', 'customlang');
    }

    /**
     * Change the version field from integer to varchar
     */
    if ($oldversion < 2011041900) {
        $table = new xmldb_table('report_customlang_components');
        $field = new xmldb_field('version', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'name');

        $dbman->change_field_type($table, $field);

        upgrade_plugin_savepoint(true, 2011041900, 'report', 'customlang');
    }


    return $result;
}
