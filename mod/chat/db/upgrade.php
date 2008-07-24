<?php  //$Id$

// This file keeps track of upgrades to
// the chat module
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

function xmldb_chat_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager();

    $result = true;

    if ($result && $oldversion < 2007101510) {

    /// Define field id to be added to chat_messages_current
        $table = new xmldb_table('chat_messages_current');

        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null, null);
        $table->addField($field);

        $field = new xmldb_field('chatid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'id');
        $table->addField($field);

        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'chatid');
        $table->addField($field);

        $field = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'userid');
        $table->addField($field);

        $field = new xmldb_field('system', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'groupid');
        $table->addField($field);

        $field = new xmldb_field('message', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null, 'system');
        $table->addField($field);

        $field = new xmldb_field('timestamp', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'message');
        $table->addField($field);

        $key = new xmldb_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKey($key);

        $key = new xmldb_key('chatid', XMLDB_KEY_FOREIGN, array('chatid'), 'chat', array('id'));
        $table->addKey($key);

        $result = $result && $dbman->create_table($table);

        $index = new xmldb_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        if (!$dbman->index_exists($table, $index)) {
            $result = $result && $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('groupid', XMLDB_INDEX_NOTUNIQUE, array('groupid'));
        if (!$dbman->index_exists($table, $index)) {
            $result = $result && $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('timestamp-chatid', XMLDB_INDEX_NOTUNIQUE, array('timestamp', 'chatid'));
        if (!$dbman->index_exists($table, $index)) {
            $result = $result && $dbman->add_index($table, $index);
        }
    }

    return $result;
}

?>
