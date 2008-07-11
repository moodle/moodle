<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function label_upgrade($oldversion) {

/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091400) {
        table_column("label", "", "course", "integer", "10", "unsigned", "0", "not null", "id");
    }

    if ($oldversion < 2004021900) {
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('label', 'add', 'label', 'name');");
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('label', 'update', 'label', 'name');");
    }

    if ($oldversion < 2004111200) { //DROP first
        execute_sql("ALTER TABLE {$CFG->prefix}label DROP INDEX course;",false);
        modify_database('','ALTER TABLE prefix_label ADD INDEX course (course);');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;

}

?>
