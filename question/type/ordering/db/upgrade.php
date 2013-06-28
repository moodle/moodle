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
        $from   = '{question} qn LEFT JOIN {question_ordering} qo ON qn.id=qo.question';
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
                    $DB->delete_records('quiz_question_instances', array('question' => $question->id));
                    $DB->delete_records('reader_question_instances', array('question' => $question->id));
                }
            }
        }
        upgrade_plugin_savepoint(true, $newversion, 'qtype', 'ordering');
    }

    return true;
}


