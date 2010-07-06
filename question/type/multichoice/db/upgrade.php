<?php

// This file keeps track of upgrades to
// the multichoice qtype plugin
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

function xmldb_qtype_multichoice_upgrade($oldversion) {
    global $CFG, $DB, $QTYPES;

    $dbman = $DB->get_manager();

    // This upgrade actually belongs to the random question type,
    // but that does not have a DB upgrade script. Therefore, multichoice
    // is doing it.
    // Rename random questions to give them more helpful names.
    if ($oldversion < 2008021800) {
        require_once($CFG->libdir . '/questionlib.php');
        // Get all categories containing random questions.
        $categories = $DB->get_recordset_sql("
                SELECT qc.id, qc.name
                FROM {question_categories} qc
                JOIN {question} q ON q.category = qc.id
                WHERE q.qtype = 'random'
                GROUP BY qc.id, qc.name");

        // Rename the random qusetions in those categories.
        $where = "qtype = 'random' AND category = ? AND " . $DB->sql_compare_text('questiontext') . " = ?";
        foreach ($categories as $cat) {
            $randomqname = $QTYPES[RANDOM]->question_name($cat, false);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '0'));

            $randomqname = $QTYPES[RANDOM]->question_name($cat, true);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '1'));
        }

        upgrade_plugin_savepoint(true, 2008021800, 'qtype', 'multichoice');
    }

    return true;
}


