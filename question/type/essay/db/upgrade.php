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
 * Essay question type upgrade code.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the essay question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_essay_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {

        // Define field maxbytes to be added to qtype_essay_options.
        $table = new xmldb_table('qtype_essay_options');
        $field = new xmldb_field('maxbytes', XMLDB_TYPE_INTEGER, '10', null,
            XMLDB_NOTNULL, null, '0', 'responsetemplateformat');

        // Conditionally launch add field maxbytes.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2021052501, 'qtype', 'essay');
    }

    if ($oldversion < 2021052502) {

        // Define field minwordlimit to be added to qtype_essay_options.
        $table = new xmldb_table('qtype_essay_options');
        $field = new xmldb_field('minwordlimit', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'responsefieldlines');

        // Conditionally launch add field minwordlimit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field maxwordlimit to be added to qtype_essay_options.
        $table = new xmldb_table('qtype_essay_options');
        $field = new xmldb_field('maxwordlimit', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'minwordlimit');

        // Conditionally launch add field maxwordlimit.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Essay savepoint reached.
        upgrade_plugin_savepoint(true, 2021052502, 'qtype', 'essay');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
