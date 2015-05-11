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
 * Matching question type upgrade code.
 *
 * @package   qtype_randomsamatch
 * @copyright 2013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the random short answer matching question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_randomsamatch_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013110501) {

        // Define table question_randomsamatch to be renamed to qtype_randomsamatch_options.
        $table = new xmldb_table('question_randomsamatch');

        // Launch rename table for qtype_randomsamatch_options.
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'qtype_randomsamatch_options');
        }

        // Record that qtype_randomsamatch savepoint was reached.
        upgrade_plugin_savepoint(true, 2013110501, 'qtype', 'randomsamatch');
    }

    if ($oldversion < 2013110502) {

        // Define key question (foreign) to be dropped form qtype_randomsamatch_options.
        $table = new xmldb_table('qtype_randomsamatch_options');
        $field = new xmldb_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');

        if ($dbman->field_exists($table, $field)) {
            // Launch drop key question.
            $key = new xmldb_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));
            $dbman->drop_key($table, $key);

            // Launch rename field question.
            $dbman->rename_field($table, $field, 'questionid');

            $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE, array('questionid'), 'question', array('id'));
            // Launch add key questionid.
            $dbman->add_key($table, $key);
        }

        // Record that qtype_randomsamatch savepoint was reached.
        upgrade_plugin_savepoint(true, 2013110502, 'qtype', 'randomsamatch');
    }

    if ($oldversion < 2013110503) {

        // Add subcats field.
        $table = new xmldb_table('qtype_randomsamatch_options');

        // Define field subcats to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('subcats', XMLDB_TYPE_INTEGER, 2, null,
                XMLDB_NOTNULL, null, '1', 'choose');

        // Conditionally launch add field subcats.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add combined feedback fields.
        // Define field correctfeedback to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'subcats');

        // Conditionally launch add field correctfeedback.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now fill it with ''.
            $DB->set_field('qtype_randomsamatch_options', 'correctfeedback', '');

            // Now add the not null constraint.
            $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null,
                    XMLDB_NOTNULL, null, null, 'subcats');
            $dbman->change_field_notnull($table, $field);
        }

        // Define field correctfeedbackformat to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('correctfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'correctfeedback');

        // Conditionally launch add field correctfeedbackformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field partiallycorrectfeedback to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'correctfeedbackformat');

        // Conditionally launch add field partiallycorrectfeedback.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now fill it with ''.
            $DB->set_field('qtype_randomsamatch_options', 'partiallycorrectfeedback', '');

            // Now add the not null constraint.
            $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                    XMLDB_NOTNULL, null, null, 'correctfeedbackformat');
            $dbman->change_field_notnull($table, $field);
        }

        // Define field partiallycorrectfeedbackformat to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback');

        // Conditionally launch add field partiallycorrectfeedbackformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field incorrectfeedback to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                null, null, null, 'partiallycorrectfeedbackformat');

        // Conditionally launch add field incorrectfeedback.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);

            // Now fill it with ''.
            $DB->set_field('qtype_randomsamatch_options', 'incorrectfeedback', '');

            // Now add the not null constraint.
            $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null,
                    XMLDB_NOTNULL, null, null, 'partiallycorrectfeedbackformat');
            $dbman->change_field_notnull($table, $field);
        }

        // Define field incorrectfeedbackformat to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('incorrectfeedbackformat', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'incorrectfeedback');

        // Conditionally launch add field incorrectfeedbackformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field shownumcorrect to be added to qtype_randomsamatch_options.
        $field = new xmldb_field('shownumcorrect', XMLDB_TYPE_INTEGER, '2', null,
                XMLDB_NOTNULL, null, '0', 'incorrectfeedbackformat');

        // Conditionally launch add field shownumcorrect.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Record that qtype_randomsamatch savepoint was reached.
        upgrade_plugin_savepoint(true, 2013110503, 'qtype', 'randomsamatch');
    }

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
