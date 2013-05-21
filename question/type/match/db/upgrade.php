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
 * @package   qtype_match
 * @copyright 1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Upgrade code for the matching question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_match_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013012100) {

        // Define table question_match to be renamed to qtype_match_options.
        $table = new xmldb_table('question_match');

        // Launch rename table for qtype_match_options.
        $dbman->rename_table($table, 'qtype_match_options');

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012100, 'qtype', 'match');
    }

    if ($oldversion < 2013012101) {

        // Define key question (foreign) to be dropped form qtype_match_options.
        $table = new xmldb_table('qtype_match_options');
        $key = new xmldb_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

        // Launch drop key question.
        $dbman->drop_key($table, $key);

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012101, 'qtype', 'match');
    }

    if ($oldversion < 2013012102) {

        // Rename field question on table qtype_match_options to questionid.
        $table = new xmldb_table('qtype_match_options');
        $field = new xmldb_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');

        // Launch rename field question.
        $dbman->rename_field($table, $field, 'questionid');

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012102, 'qtype', 'match');
    }

    if ($oldversion < 2013012103) {

        // Define key questionid (foreign-unique) to be added to qtype_match_options.
        $table = new xmldb_table('qtype_match_options');
        $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN_UNIQUE, array('questionid'), 'question', array('id'));

        // Launch add key questionid.
        $dbman->add_key($table, $key);

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012103, 'qtype', 'match');
    }

    if ($oldversion < 2013012104) {

        // Define field subquestions to be dropped from qtype_match_options.
        $table = new xmldb_table('qtype_match_options');
        $field = new xmldb_field('subquestions');

        // Conditionally launch drop field subquestions.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012104, 'qtype', 'match');
    }

    if ($oldversion < 2013012105) {

        // Define table question_match_sub to be renamed to qtype_match_subquestions.
        $table = new xmldb_table('question_match_sub');

        // Launch rename table for qtype_match_subquestions.
        $dbman->rename_table($table, 'qtype_match_subquestions');

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012105, 'qtype', 'match');
    }

    if ($oldversion < 2013012106) {

        // Define key question (foreign) to be dropped form qtype_match_subquestions.
        $table = new xmldb_table('qtype_match_subquestions');
        $key = new xmldb_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

        // Launch drop key question.
        $dbman->drop_key($table, $key);

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012106, 'qtype', 'match');
    }

    if ($oldversion < 2013012107) {

        // Rename field question on table qtype_match_subquestions to questionid.
        $table = new xmldb_table('qtype_match_subquestions');
        $field = new xmldb_field('question', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'id');

        // Launch rename field question.
        $dbman->rename_field($table, $field, 'questionid');

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012107, 'qtype', 'match');
    }

    if ($oldversion < 2013012108) {

        // Define key questionid (foreign) to be added to qtype_match_subquestions.
        $table = new xmldb_table('qtype_match_subquestions');
        $key = new xmldb_key('questionid', XMLDB_KEY_FOREIGN, array('questionid'), 'question', array('id'));

        // Launch add key questionid.
        $dbman->add_key($table, $key);

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012108, 'qtype', 'match');
    }

    if ($oldversion < 2013012109) {

        // Define field code to be dropped from qtype_match_subquestions.
        // The field code has not been needed since the new question engine in
        // Moodle 2.1. It should be safe to drop it now.
        $table = new xmldb_table('qtype_match_subquestions');
        $field = new xmldb_field('code');

        // Conditionally launch drop field code.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Record that qtype_match savepoint was reached.
        upgrade_plugin_savepoint(true, 2013012109, 'qtype', 'match');
    }

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    return true;
}
