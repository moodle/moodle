<?php

// This file keeps track of upgrades to
// the calculated qtype plugin
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_qtype_calculated_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

    // MDL-16505.
    if ($result && $oldversion < 2008091700 ) { //New version in version.php
        if (get_config('qtype_datasetdependent', 'version')) {
            $result = $result && unset_config('version', 'qtype_datasetdependent');
        }
        upgrade_plugin_savepoint($result, 2008091700, 'qtype', 'calculated');
    }
    if ($result && $oldversion < 2009082000 ) { //New version in version.php

// this should be changed if merged to 1.9
//    let if ($dbman->table_exists()) replace the normal $oldversion test
//    as in any case the question question_calculated_options should be created

    /// Define table question_calculated_options to be created
        $table = new xmldb_table('question_calculated_options');

    /// Adding fields to table question_calculated_options
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('question', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('synchronize', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table question_calculated_options
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('question', XMLDB_KEY_FOREIGN, array('question'), 'question', array('id'));

    /// Conditionally launch create table for question_calculated_options
        if (!$dbman->table_exists($table)) {
            // $dbman->create_table doesnt return a result, we just have to trust it
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint($result, 2009082000 , 'qtype', 'calculated');
    }
    if ( $result && $oldversion < 2009092000 ) { //New version in version.php

    /// Define field multichoice to be added to question_calculated_options
    ///ALTER TABLE `moodle`.`mdl_question_calculated_options` DROP COLUMN `multichoice`;
    //    $table = new xmldb_table('question_calculated_options');
    //    $field = new xmldb_field('multichoice', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'synchronize');

    /// Conditionally launch add field multichoice
     //   if (!$dbman->field_exists($table, $field)) {
    //        $dbman->add_field($table, $field);
    //    }
    /// Define field single to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('single', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'synchronize');

    /// Conditionally launch add field single
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field shuffleanswers to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('shuffleanswers', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'single');

    /// Conditionally launch add field shuffleanswers
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    /// Define field correctfeedback to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('correctfeedback', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'shuffleanswers');

    /// Conditionally launch add field correctfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field partiallycorrectfeedback to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('partiallycorrectfeedback', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'correctfeedback');

    /// Conditionally launch add field partiallycorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    /// Define field incorrectfeedback to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('incorrectfeedback', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'partiallycorrectfeedback');

    /// Conditionally launch add field incorrectfeedback
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    /// Define field answernumbering to be added to question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('answernumbering', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'abc', 'incorrectfeedback');

    /// Conditionally launch add field answernumbering
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_plugin_savepoint($result, 2009092000, 'qtype', 'calculated');
    }

   if ($result && $oldversion < 2010020800) {

    /// Define field multiplechoice to be dropped from question_calculated_options
        $table = new xmldb_table('question_calculated_options');
        $field = new xmldb_field('multichoice');
        
    /// Conditionally launch drop field multiplechoice
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// calculated savepoint reached
        upgrade_plugin_savepoint($result, 2010020800, 'qtype', 'calculated');
    }
/// calculated savepoint reached
/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of database_manager methods
///     upgrade_plugin_savepoint($result, YYYYMMDD00, 'qtype', 'calculated');
/// }

    return $result;
}


