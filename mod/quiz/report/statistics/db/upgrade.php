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
 * Post-install script for the quiz statistics report.
 *
 * @package   quiz_statistics
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Quiz statistics report upgrade code.
 */
function xmldb_quiz_statistics_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2012061800) {

        // Changing type of field subqid on table quiz_question_response_stats to char.
        $table = new xmldb_table('quiz_question_response_stats');
        $field = new xmldb_field('subqid', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'questionid');

        // Launch change of type for field subqid.
        $dbman->change_field_type($table, $field);

        // Statistics savepoint reached.
        upgrade_plugin_savepoint(true, 2012061800, 'quiz', 'statistics');
    }

    if ($oldversion < 2012061801) {

        // Changing type of field aid on table quiz_question_response_stats to char.
        $table = new xmldb_table('quiz_question_response_stats');
        $field = new xmldb_field('aid', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'subqid');

        // Launch change of type for field aid.
        $dbman->change_field_type($table, $field);

        // Statistics savepoint reached.
        upgrade_plugin_savepoint(true, 2012061801, 'quiz', 'statistics');
    }

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    return true;
}

