<?PHP // $Id$

function exercise_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003121000) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_submissions` ADD `late` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'");
		}

    if ($oldversion < 2004062300) {
		table_column("exercise", "", "gradinggrade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
		table_column("exercise", "", "assessmentcomps", "INTEGER", "4", "UNSIGNED", "2", "NOT NULL", "usemaximum");
    }

    if ($oldversion < 2004090200) {
        table_column("exercise", "", "usepassword", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL");
        table_column("exercise", "", "password", "VARCHAR", "32", "", "", "NOT NULL");
    }


    return true;
}


?>
