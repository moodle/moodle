<?PHP // $Id$

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
        modify_database("", "INSERT INTO prefix_log_display VALUES ('chat', 'talk', 'chat', 'name');");
    }

    if ($oldversion < 2004060401) {
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

    return true;
}


?>

