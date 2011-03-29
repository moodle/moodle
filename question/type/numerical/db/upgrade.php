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
 * Numerical question type upgrade code.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the numerical question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_numerical_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

//===== 1.9.0 upgrade line ======//
    if ($oldversion < 2009100100 ) { //New version in version.php

    /// Define table question_numerical_options to be created
        $table = new xmldb_table('question_numerical_options');

    /// Adding fields to table question_numerical_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('instructions', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('showunits', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('unitsleft', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('unitgradingtype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('unitpenalty', XMLDB_TYPE_NUMBER, '12, 7', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0.1');

    /// Adding keys to table question_numerical_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));
    /// Conditionally launch create table for question_calculated_options
        if (!$dbman->table_exists($table)) {
            // $dbman->create_table doesnt return a result, we just have to trust it
            $dbman->create_table($table);
        }//else
        upgrade_plugin_savepoint(true, 2009100100, 'qtype', 'numerical');
    }

    if ($oldversion < 2009100101) {

        // Define field instructionsformat to be added to question_numerical_options
        $table = new xmldb_table('question_numerical_options');
        $field = new xmldb_field('instructionsformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'instructions');

        // Conditionally launch add field instructionsformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // In the past, question_match_sub.questiontext assumed to contain
        // content of the same form as question.questiontextformat. If we are
        // using the HTML editor, then convert FORMAT_MOODLE content to FORMAT_HTML.
        $rs = $DB->get_recordset_sql('
                SELECT qno.*, q.oldquestiontextformat
                FROM {question_numerical_options} qno
                JOIN {question} q ON qno.question = q.id');
        foreach ($rs as $record) {
            if ($CFG->texteditors !== 'textarea' && $record->oldquestiontextformat == FORMAT_MOODLE) {
                $record->instructions = text_to_html($record->questiontext, false, false, true);
                $record->instructionsformat = FORMAT_HTML;
            } else {
                $record->instructionsformat = $record->oldquestiontextformat;
            }
            $DB->update_record('question_numerical_options', $record);
        }
        $rs->close();

        // numerical savepoint reached
        upgrade_plugin_savepoint(true, 2009100101, 'qtype', 'numerical');
    }

    return true;
}
