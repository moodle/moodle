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

    if ($oldversion < 2004062301) {
        modify_database('','CREATE INDEX prefix_exercise_course_idx ON prefix_exercise (course);');
        modify_database('','CREATE INDEX prefix_exercise_submissions_exerciseid_idx ON prefix_exercise_submissions (exerciseid);');
        modify_database('','CREATE INDEX prefix_exercise_assessments_exerciseid_idx ON prefix_exercise_assessments (exerciseid);');
        modify_database('','CREATE INDEX prefix_exercise_rubrics_exerciseid_idx ON prefix_exercise_rubrics (exerciseid);');
        modify_database('','CREATE INDEX prefix_exercise_grades_exerciseid_idx ON prefix_exercise_grades (exerciseid);');
    }
        
    return true;
}


?>
