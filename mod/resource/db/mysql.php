<?PHP // $Id$

function resource_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003082000) {
        table_column("resource", "course", "course", "integer", "10", "unsigned", "0");
    }

    if ($oldversion < 2004013101) {
        modify_database("", "INSERT INTO prefix_log_display VALUES ('resource', 'update', 'resource', 'name');");
        modify_database("", "INSERT INTO prefix_log_display VALUES ('resource', 'add', 'resource', 'name');");
    }

    return true;
}


?>
