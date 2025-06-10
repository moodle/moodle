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
 * OU multi-response question type upgrade code.
 *
 * @package   qtype_oumultiresponse
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the OU multi-response question type.
 *
 * @param int $oldversion the version we are upgrading from.
 * @return bool true
 */
function xmldb_qtype_oumultiresponse_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020031600) {

        // For OUMR sub-questions of combined questions, ensure that the setting in the database
        // matches what the behaviour was (no added answer numbers) before the recent change.
        // This upgrade step really belongs with the commit of 2020-01-02
        // Combined: add numbering list option for Single Choice and Multiple Response #373286
        // but we did not think of it until now.

        // The config check is because we have already manually applied this fix to some OU server,
        // so we needed a way to stop it running again.
        if (!get_config('qtype_oumultiresponse', 'combined2020031600upgradealreadyrun')) {
            $DB->execute("
                    UPDATE {question_oumultiresponse}

                       SET answernumbering = 'none'

                     WHERE questionid IN (
                            SELECT child.id

                              FROM {question} combined
                              JOIN {question} child ON child.parent = combined.id

                             WHERE combined.qtype = 'combined'
                               AND child.qtype = 'oumultiresponse'
                           )
                ");
        }

        upgrade_plugin_savepoint(true, 2020031600, 'qtype', 'oumultiresponse');
    }

    // Add a new checkbox for the question author to decide
    // whether the Standard instruction ('Select one or more:') is displayed.
    $newversion = 2020041600;
    if ($oldversion < $newversion) {

        // Define field id to be added to question_oumultiresponse.
        $table = new xmldb_table('question_oumultiresponse');
        $field = new xmldb_field('showstandardinstruction', XMLDB_TYPE_INTEGER, '2',
            null, XMLDB_NOTNULL, null, '1', 'shownumcorrect');

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Oumultiresponse savepoint reached.
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'oumultiresponse');
    }
    return true;
}
