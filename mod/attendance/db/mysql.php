<?PHP

function attendance_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091802) {
        execute_sql("ALTER TABLE `{$CFG->prefix}attendance` ADD `edited` TINYINT( 1 ) DEFAULT '0' NOT NULL;");
		execute_sql("UPDATE `{$CFG->prefix}attendance` set `edited` = 1;");
    }
    if ($oldversion < 2003092500) {
        execute_sql("ALTER TABLE `{$CFG->prefix}attendance` ADD `autoattend` TINYINT( 1 ) DEFAULT '0' NOT NULL;");
    }

    if ($oldversion < 2004050301) {
        modify_database("", "INSERT INTO {$CFG->prefix}log_display VALUES ('attendance', 'view', 'attendance', 'name');");
        modify_database("", "INSERT INTO {$CFG->prefix}log_display VALUES ('attendance', 'viewall', 'attendance', 'name');");
        modify_database("", "INSERT INTO {$CFG->prefix}log_display VALUES ('attendance', 'viewweek', 'attendance', 'name');");
    }



    return true;
}

?>
