<?PHP

function label_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091400) {
        table_column("label", "", "course", "integer", "10", "unsigned", "0", "not null", "id");
    }

    if ($oldversion < 2004021900) {
        modify_database("", "INSERT INTO prefix_log_display VALUES ('label', 'add', 'quiz', 'name');");
        modify_database("", "INSERT INTO prefix_log_display VALUES ('label', 'update', 'quiz', 'name');");
    }


    return true;
}

?>
