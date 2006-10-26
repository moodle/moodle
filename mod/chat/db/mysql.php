<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function chat_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003072100) {
        modify_database ("", " INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('chat', 'report', 'chat', 'name'); ");
    }

    if ($oldversion < 2003072101) {
        table_column("chat", "messages", "keepdays", "integer", "10", "unsigned", "0", "not null");
    }

    if ($oldversion < 2003072102) {
        table_column("chat", "", "studentlogs", "integer", "4", "unsigned", "0", "not null", "keepdays");
    }

    if ($oldversion < 2003072500) {
        table_column("chat", "", "chattime", "integer", "10", "unsigned", "0", "not null", "studentlogs");
        table_column("chat", "", "schedule", "integer", "4", "", "0", "not null", "studentlogs");
    }

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

    if ($oldversion < 2004111200) {
        execute_sql("ALTER TABLE {$CFG->prefix}chat DROP INDEX `course`;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}chat_messages DROP INDEX  `chatid`;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}chat_messages DROP INDEX `userid`;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}chat_messages DROP INDEX `groupid`;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}chat_users DROP INDEX  `chatid`;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}chat_users DROP INDEX  `groupid`;",false);

        modify_database('','ALTER TABLE prefix_chat ADD INDEX `course` (`course`);');
        modify_database('','ALTER TABLE prefix_chat_messages ADD INDEX  `chatid` (`chatid`);');
        modify_database('','ALTER TABLE prefix_chat_messages ADD INDEX `userid` (`userid`);');
        modify_database('','ALTER TABLE prefix_chat_messages ADD INDEX `groupid` (`groupid`);');
        modify_database('','ALTER TABLE prefix_chat_users ADD INDEX  `chatid` (`chatid`);');
        modify_database('','ALTER TABLE prefix_chat_users ADD INDEX  `groupid` (`groupid`);');
    }

    if ($oldversion < 2005020300) {
        table_column('chat_users', '', 'course', 'integer', '10', 'unsigned', '0', 'not null', '');
        table_column('chat_users', '', 'lang'  , 'varchar', '10', ''        , '' , 'not null', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
