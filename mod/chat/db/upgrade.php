<?php

// This file keeps track of upgrades to
// the chat module
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

function xmldb_chat_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    //===== 1.9.0 upgrade line ======//

    if ($oldversion < 2010050100) {

    /// Changing precision of field ip on table chat_users to (45)
        $table = new xmldb_table('chat_users');
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'version');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// chat savepoint reached
        upgrade_mod_savepoint(true, 2010050100, 'chat');
    }

    if ($oldversion < 2010050101) {

    /// Define field introformat to be added to chat
        $table = new xmldb_table('chat');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');

    /// Launch add field introformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // conditionally migrate to html format in intro
        if ($CFG->texteditors !== 'textarea') {
            $rs = $DB->get_recordset('chat', array('introformat'=>FORMAT_MOODLE), '', 'id,intro,introformat');
            foreach ($rs as $ch) {
                $ch->intro       = text_to_html($ch->intro, false, false, true);
                $ch->introformat = FORMAT_HTML;
                $DB->update_record('chat', $ch);
                upgrade_set_timeout();
            }
            $rs->close();
        }

    /// chat savepoint reached
        upgrade_mod_savepoint(true, 2010050101, 'chat');
    }

    /// Creating chat_messages_current to hold latest chat messages
    if ($oldversion < 2010050102) {

    /// Define table chat_messages_current to be created
        $table = new xmldb_table('chat_messages_current');

    /// Adding fields to table chat_messages_current
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('chatid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('system', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('message', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timestamp', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table chat_messages_current
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('chatid', XMLDB_KEY_FOREIGN, array('chatid'), 'chat', array('id'));

    /// Adding indexes to table chat_messages_current
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $table->add_index('groupid', XMLDB_INDEX_NOTUNIQUE, array('groupid'));
        $table->add_index('timestamp-chatid', XMLDB_INDEX_NOTUNIQUE, array('timestamp', 'chatid'));

    /// create table for chat_messages_current
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// chat savepoint reached
        upgrade_mod_savepoint(true, 2010050102, 'chat');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}


