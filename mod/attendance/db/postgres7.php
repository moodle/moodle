<?php

function attendance_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

//table_column($table, $oldfield, $field, $type="integer", $size="10",
//                      $signed="unsigned", $default="0", $null="not null", $after="") 


    if ($oldversion < 2003091802) {
        table_column("attendance", "", "edited", "integer", 2, "unsigned", "0", "not null");
   		  execute_sql("UPDATE {$CFG->prefix}attendance set edited = 1;");
    }
    if ($oldversion < 2003092500) {
        table_column("attendance", "", "autoattend", "integer", 2, "unsigned", "0", "not null");
    }

    if ($oldversion < 2004050301) {

        modify_database("", "INSERT INTO {$CFG->prefix}log_display VALUES ('attendance', 'view', 'attendance', 'name');");

        modify_database("", "INSERT INTO {$CFG->prefix}log_display VALUES ('attendance', 'viewall', 'attendance', 'name');");

        modify_database("", "INSERT INTO {$CFG->prefix}log_display VALUES ('attendance', 'viewweek', 'attendance', 'name');");

    }



    return true;
}

?>
