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

    if ($oldversion < 2004022200) {

		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxattempts` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxanswers");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `nextpagedefault` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxattempts");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxpages` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER nextpagedefault");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `qtype` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER lessonid");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `qoption` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER qtype");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_answers` ADD `grade` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER jumpto");

    }

    if ($oldversion < 2004032000) {           // Upgrade some old beta lessons
		execute_sql(" UPDATE `{$CFG->prefix}lesson_pages` SET qtype = 3 WHERE qtype = 0");
    }
    
    if ($oldversion < 2004032400) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `usemaxgrade` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER grade");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `minquestions` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER nextpagedefault");
    }
 
    if ($oldversion < 2004032700) {
		table_column("lesson_answers", "", "flags", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
    }
     
    return true;
}

?>
