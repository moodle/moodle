<?PHP // $Id$

function exercise_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2003111400) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_submissions` ADD INDEX (`userid`)");
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_submissions` DROP INDEX `title`");
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_assessments` ADD INDEX (`submissionid`)");
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_assessments` ADD INDEX (`userid`)");
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_grades` ADD INDEX (`assessmentid`)");
		}
    
    if ($oldversion < 2003121000) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}exercise_submissions` ADD `late` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0'");
		}
 		
    return true;
}


?>
