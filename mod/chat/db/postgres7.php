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

    return true;
}


?>

