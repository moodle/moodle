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
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_qtype_ordering_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    $newversion = 2013062800;
    if ($oldversion < $newversion) {
        $select = 'qn.*, qo.id AS questionorderingid';
        $from   = '{question} qn LEFT JOIN {question_ordering} qo ON qn.id = qo.question';
        $where  = 'qn.qtype = ? AND qo.id IS NULL';
        $params = array('ordering');
        if ($questions = $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params)) {
            foreach ($questions as $question) {
                if ($answers = $DB->get_records('question_answers', array('question' => $question->id))) {
                    // add "options" for this ordering question
                    $question_ordering = (object)array(
                        'question'   => $question->id,
                        'logical'    => 1,
                        'studentsee' => min(6, count($answers)),
                        'correctfeedback' => '',
                        'partiallycorrectfeedback' => '',
                        'incorrectfeedback' => ''
                    );
                    $question_ordering->id = $DB->insert_record('question_ordering', $question_ordering);
                } else {
                    // this is a faulty ordering question - remove it
                    $DB->delete_records('question', array('id' => $question->id));
                    if ($dbman->table_exists('quiz_question_instances')) {
                        $DB->delete_records('quiz_question_instances', array('question' => $question->id));
                    }
                    if ($dbman->table_exists('reader_question_instances')) {
                        $DB->delete_records('reader_question_instances', array('question' => $question->id));
                    }
                }
            }
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    $newversion = 2015011915;
    if ($oldversion < $newversion) {

        // rename "ordering" table for Moodle >= 2.5
        $oldname = 'question_ordering';
        $newname = 'qtype_ordering_options';

        if ($dbman->table_exists($oldname)) {
            $oldtable = new xmldb_table($oldname);
            if ($dbman->table_exists($newname)) {
                $dbman->drop_table($oldtable);
            } else {
                $dbman->rename_table($oldtable, $newname);
            }
        }

        // remove index on question(id) field
        // (because we want to modify the field)
        $table = new xmldb_table('qtype_ordering_options');
        $fields = array('question', 'questionid');
        foreach ($fields as $field) {
            if ($dbman->field_exists($table, $field)) {
                $index = new xmldb_index('qtypordeopti_que_uix', XMLDB_INDEX_UNIQUE, array($field));
                if ($dbman->index_exists($table, $index)) {
                    $dbman->drop_index($table, $index);
                }
            }
        }

        // rename "question"   -> "questionid"
        // rename "logical"    -> "selecttype"
        // rename "studentsee" -> "selectcount"
        // add    "(xxx)feedbackformat" fields
        $table = new xmldb_table('qtype_ordering_options');
        $fields = array(
            'questionid'                     => new xmldb_field('question',                       XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'id'),
            'selecttype'                     => new xmldb_field('logical',                        XMLDB_TYPE_INTEGER,  '4', null, XMLDB_NOTNULL, null, '0', 'questionid'),
            'selectcount'                    => new xmldb_field('studentsee',                     XMLDB_TYPE_INTEGER,  '4', null, XMLDB_NOTNULL, null, '0', 'selecttype'),
            'correctfeedbackformat'          => new xmldb_field('correctfeedbackformat',          XMLDB_TYPE_INTEGER,  '2', null, XMLDB_NOTNULL, null, '0', 'correctfeedback'),
            'incorrectfeedbackformat'        => new xmldb_field('incorrectfeedbackformat',        XMLDB_TYPE_INTEGER,  '2', null, XMLDB_NOTNULL, null, '0', 'incorrectfeedback'),
            'partiallycorrectfeedbackformat' => new xmldb_field('partiallycorrectfeedbackformat', XMLDB_TYPE_INTEGER,  '2', null, XMLDB_NOTNULL, null, '0', 'partiallycorrectfeedback')
        );
        foreach ($fields as $newname => $field) {
            $oldexists = $dbman->field_exists($table, $field);
            $newexists = $dbman->field_exists($table, $newname);
            if ($field->getName()==$newname) {
                // same field name
            } else if ($oldexists) {
                if ($newexists) {
                    $dbman->drop_field($table, $field);
                } else {
                    $dbman->rename_field($table, $field, $newname);
                    $newexists = true;
                }
                $oldexists = false;
            }
            $field->setName($newname);
            if ($newexists) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
            }
        }

        // make sure there are no duplicate "questionid" fields in "qtype_ordering_options" table
        $select = 'questionid, COUNT(*) AS countduplicates, MAX(id) AS maxid';
        $from   = '{qtype_ordering_options}';
        $group  = 'questionid';
        $having = 'countduplicates > ?';
        $params = array(1);
        if ($records = $DB->get_records_sql("SELECT $select FROM $from GROUP BY $group HAVING $having", $params)) {
            foreach ($records as $record) {
                $select = 'id <> ? AND questionid = ?';
                $params = array($record->maxid, $record->questionid);
                $DB->delete_records_select('qtype_ordering_options', $select, $params);
            }
        }

        // restore index on questionid field
        $table = new xmldb_table('qtype_ordering_options');
        $index = new xmldb_index('qtypordeopti_que_uix', XMLDB_INDEX_UNIQUE, array('questionid'));
        if (! $dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    return true;
}


