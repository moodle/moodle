<?php



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

    if ($oldversion < 2004111200) { //DROP first
        execute_sql("ALTER TABLE {$CFG->prefix}label DROP INDEX course;",false);

        modify_database('','ALTER TABLE prefix_label ADD INDEX course (course);');
    }

    return true;

}



?>

