<?PHP

function lesson_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004021600) {

       delete_records("log_display", "module", "lesson");

       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');");

    }

    return true;
}

?>
