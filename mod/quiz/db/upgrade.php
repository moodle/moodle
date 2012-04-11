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
 * Upgrade script for the quiz module.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2006 Eloy Lafuente (stronk7)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Quiz module upgrade function.
 * @param string $oldversion the version we are upgrading from.
 */
function xmldb_quiz_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    if ($oldversion < 2011120700) {

        // Define field lastcron to be dropped from quiz_reports
        $table = new xmldb_table('quiz_reports');
        $field = new xmldb_field('lastcron');

        // Conditionally launch drop field lastcron
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011120700, 'quiz');
    }

    if ($oldversion < 2011120701) {

        // Define field cron to be dropped from quiz_reports
        $table = new xmldb_table('quiz_reports');
        $field = new xmldb_field('cron');

        // Conditionally launch drop field cron
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // quiz savepoint reached
        upgrade_mod_savepoint(true, 2011120701, 'quiz');
    }

    if ($oldversion < 2011120703) {
        // Track page of quiz attempts
        $table = new xmldb_table('quiz_attempts');

        $field = new xmldb_field('currentpage', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2011120703, 'quiz');
    }

    if ($oldversion < 2012030901) {
        // Configuration option for navigation method
        $table = new xmldb_table('quiz');

        $field = new xmldb_field('navmethod', XMLDB_TYPE_CHAR, '16', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 'free');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2012030901, 'quiz');
    }

    return true;
}

