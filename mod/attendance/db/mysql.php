<?PHP

function attendance_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2003091801) {

        execute_sql("ALTER TABLE `attendance` ADD `edited` TINYINT( 1 ) DEFAULT '0' NOT NULL;");
				execute_sql("UPDATE `attendance` set `edited` = 1;");
    }

    return true;
}

?>
