<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function journal_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    $result = true;

    if ($oldversion < 2003081705) {
        $defaultscale = NULL;
        $defaultscale->courseid = 0;
        $defaultscale->userid = 0;
        $defaultscale->timemodified = time();
        $defaultscale->name  = get_string("journalrating2", "journal");
        $defaultscale->scale = get_string("journalrating1", "journal").",".
                               get_string("journalrating2", "journal").",".
                               get_string("journalrating3", "journal");

        if ($defaultscale->id = insert_record("scale", $defaultscale)) {
            execute_sql("UPDATE {$CFG->prefix}journal SET assessed = '-$defaultscale->id'", false);
        } else {
            notify("An error occurred while inserting the default journal scale");
            $result = false;
        }
    }

    if ($oldversion < 2004011400) {
        table_column("journal", "", "introformat", "integer", "2", "", "1", "not null", "intro");
    }

    if ($oldversion < 2004111200) {
        execute_sql("DROP INDEX {$CFG->prefix}journal_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}journal_entries_journal_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}journal_entries_userid_idx;",false);

        modify_database('','CREATE INDEX prefix_journal_course_idx ON prefix_journal (course);');
        modify_database('','CREATE INDEX prefix_journal_entries_journal_idx ON prefix_journal_entries (journal);');
        modify_database('','CREATE INDEX prefix_journal_entries_userid_idx ON prefix_journal_entries (userid);');
    }

    if ($oldversion < 2005041100) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        // journal intro
        $wtm->update( 'journal','intro','introformat' );
        // journal entries
        $sql = "select course from {$CFG->prefix}journal, {$CFG->prefix}journal_entries ";
        $sql .= "where {$CFG->prefix}journal.id = {$CFG->prefix}journal_entries.journal ";
        $sql .= "and {$CFG->prefix}journal_entries.id = ";
        $wtm->update( 'journal_entries', 'text', 'format', $sql );
    }

    if ($oldversion < 2006092100) {
        table_column('journal_entries', 'comment', 'entrycomment', 'text', '', '', '', 'null');
    } 

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return $result;
}
