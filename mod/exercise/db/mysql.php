<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

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

    if ($oldversion < 2004062300) {
        table_column("exercise", "", "gradinggrade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
        table_column("exercise", "", "assessmentcomps", "INTEGER", "4", "UNSIGNED", "2", "NOT NULL", "usemaximum");
        execute_sql("ALTER TABLE `{$CFG->prefix}exercise` DROP COLUMN `teacherweight`");
        execute_sql("ALTER TABLE `{$CFG->prefix}exercise` DROP COLUMN `gradingweight`");
    }

    if ($oldversion < 2004090200) {
        table_column("exercise", "", "usepassword", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL");
        table_column("exercise", "", "password", "VARCHAR", "32", "", "", "NOT NULL");
    }

    if ($oldversion < 2004091000) {
        table_column("exercise_assessments","generalcomment","generalcomment","text","","","","NOT NULL");
        table_column("exercise_assessments","teachercomment","teachercomment","text","","","","NOT NULL");
    }

    if ($oldversion < 2004100800) {
        include_once("$CFG->dirroot/mod/exercise/lib.php");
        exercise_refresh_events();
    }

    if ($oldversion < 2004111200) {
        execute_sql("ALTER TABLE {$CFG->prefix}exercise DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}exercise_submissions DROP INDEX exerciseid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}exercise_assessments DROP INDEX exerciseid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}exercise_elements DROP INDEX exerciseid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}exercise_rubrics DROP INDEX exerciseid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}exercise_grades DROP INDEX exerciseid;",false);

        modify_database('','ALTER TABLE prefix_exercise ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_exercise_submissions ADD INDEX exerciseid (exerciseid);');
        modify_database('','ALTER TABLE prefix_exercise_assessments ADD INDEX exerciseid (exerciseid);');
        modify_database('','ALTER TABLE prefix_exercise_elements ADD INDEX exerciseid (exerciseid);');
        modify_database('','ALTER TABLE prefix_exercise_rubrics ADD INDEX exerciseid (exerciseid);');
        modify_database('','ALTER TABLE prefix_exercise_grades ADD INDEX exerciseid (exerciseid);');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
