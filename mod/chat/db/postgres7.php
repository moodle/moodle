<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function chat_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2004022300) {
        table_column("chat_messages", "", "groupid", "integer", "10", "unsigned", "0", "not null", "userid");
        table_column("chat_users",    "", "groupid", "integer", "10", "unsigned", "0", "not null", "userid");
    }

    if ($oldversion < 2004042500) {
        include_once("$CFG->dirroot/mod/chat/lib.php");
        chat_refresh_events();
    }

    if ($oldversion < 2004043000) {
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('chat', 'talk', 'chat', 'name');");
    }

    if ($oldversion < 2004111200) { //drop them first to avoid collisions with upgrades from 1.4.2+
        execute_sql("DROP INDEX {$CFG->prefix}chat_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}chat_messages_chatid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}chat_messages_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}chat_messages_groupid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}chat_messages_timemodifiedchatid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}chat_users_chatid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}chat_users_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}chat_users_groupid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}chat_users_lastping_idx;",false);

        modify_database('','CREATE INDEX prefix_chat_course_idx ON prefix_chat(course);');
        modify_database('','CREATE INDEX prefix_chat_messages_chatid_idx ON prefix_chat_messages (chatid);');
        modify_database('','CREATE INDEX prefix_chat_messages_userid_idx ON prefix_chat_messages (userid);');
        modify_database('','CREATE INDEX prefix_chat_messages_groupid_idx ON prefix_chat_messages (groupid);');
        modify_database('','CREATE INDEX prefix_chat_messages_timemodifiedchatid_idx ON prefix_chat_messages(timestamp,chatid);');
        modify_database('','CREATE INDEX prefix_chat_users_chatid_idx ON prefix_chat_users (chatid);');
        modify_database('','CREATE INDEX prefix_chat_users_userid_idx ON prefix_chat_users (userid);');
        modify_database('','CREATE INDEX prefix_chat_users_groupid_idx ON prefix_chat_users (groupid);');
        modify_database('','CREATE INDEX prefix_chat_users_lastping_idx ON prefix_chat_users (lastping);');
    }

    if ($oldversion < 2005020300) {
        table_column('chat_users', '', 'course', 'integer', '10', 'unsigned', '0', 'not null', '');
        table_column('chat_users', '', 'lang'  , 'varchar', '10', ''        , '' , 'not null', '');
    }

    if ($oldversion < 2005031001) { // Mass cleanup of bad upgrade scripts
        modify_database('','ALTER TABLE prefix_chat_users ALTER course SET NOT NULL');
        modify_database('','ALTER TABLE prefix_chat_users ALTER lang SET NOT NULL');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
