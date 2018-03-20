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
 * Quiz overview report upgrade script.
 *
 * @package   quiz_overview
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz overview report upgrade function.
 * @param number $oldversion
 */
function xmldb_quiz_overview_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018021800) {

        // Define key questionusageid-slot (foreign-unique) to be added to quiz_overview_regrades.
        $table = new xmldb_table('quiz_overview_regrades');
        $key = new xmldb_key('questionusageid-slot', XMLDB_KEY_FOREIGN_UNIQUE, array('questionusageid', 'slot'), 'question_attempts', array('questionusageid', 'slot'));

        // Launch add key questionusageid-slot.
        $dbman->add_key($table, $key);

        // Overview savepoint reached.
        upgrade_plugin_savepoint(true, 2018021800, 'quiz', 'overview');
    }

    return true;
}
