<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function workshop_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG, $db;

    if ($oldversion < 2003050400) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop` CHANGE `graded` `agreeassessments` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL");
        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop` CHANGE `showgrades` `hidegrades` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL");
        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop_assessments` ADD `timeagreed` INT(10) UNSIGNED DEFAULT '0' NOT NULL AFTER `timecreated`");
        execute_sql("
        CREATE TABLE `{$CFG->prefix}workshop_comments` (
          `id` int(10) unsigned NOT NULL auto_increment,
          # workshopid not necessary just makes deleting instance easier
          `workshopid` int(10) unsigned NOT NULL default '0',
          `assessmentid` int(10) unsigned NOT NULL default '0',
          `userid` int(10) unsigned NOT NULL default '0',
          `timecreated` int(10) unsigned NOT NULL default '0',
          `mailed` tinyint(2) unsigned NOT NULL default '0',
          `comments` text NOT NULL,
          PRIMARY KEY  (`id`)
        ) COMMENT='Defines comments'
        ");
    }

    if ($oldversion < 2003051400) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop` ADD `showleaguetable` TINYINT(3) UNSIGNED NOT NULL  DEFAULT '0' AFTER `gradingweight`");
        execute_sql("
        CREATE TABLE `{$CFG->prefix}workshop_rubrics` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `workshopid` int(10) unsigned NOT NULL default '0',
          `elementid` int(10) unsigned NOT NULL default '0',
          `rubricno` tinyint(3) unsigned NOT NULL default '0',
          `description` text NOT NULL,
          PRIMARY KEY  (`id`)
        ) COMMENT='Info about the rubrics marking scheme'
        ");
    }

    if ($oldversion < 2003082200) {

        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop_rubrics` CHANGE `elementid` `elementno` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
    }

    if ($oldversion < 2003092500) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop` ADD `overallocation` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `nsassessments`");
    }

    if ($oldversion < 2003100200) {

        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop_assessments` ADD `resubmission` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `mailed`");
    }

    if ($oldversion < 2003100800) {
        // tidy up log_display entries
        execute_sql("DELETE FROM `{$CFG->prefix}log_display` WHERE `module` = 'workshop'");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES('workshop', 'assessments', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'close', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'display', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'resubmit', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'set up', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'submissions', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'view', 'workshop', 'name')");
        execute_sql("INSERT INTO `{$CFG->prefix}log_display` VALUES ('workshop', 'update', 'workshop', 'name')");
    }

    if ($oldversion < 2003113000) {
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` ADD `teacherloading` tinyint(3) unsigned
                NOT NULL default '5'");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` ADD `assessmentstodrop` tinyint(3) unsigned
                NOT NULL default '0'");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_assessments` ADD `donotuse` tinyint(3) unsigned
                NOT NULL default '0' AFTER `resubmission`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_grades` ADD INDEX (`assessmentid`)");
    }

    if ($oldversion < 2004052100) {
        include_once("$CFG->dirroot/mod/workshop/lib.php");
        workshop_refresh_events();
    }

    if ($oldversion < 2004081100) {
        table_column("workshop", "", "gradinggrade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
        table_column("workshop", "", "assessmentcomps", "INTEGER", "4", "UNSIGNED", "2", "NOT NULL", "ntassessments");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `gradingweight`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `mergegrades`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `peerweight`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `includeteachersgrade`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `biasweight`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `reliabilityweight`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `teacherloading`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop` DROP COLUMN `assessmentstodrop`");
    }

    if ($oldversion < 2004092400) {
        table_column("workshop", "", "nattachments", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "nelements");
        table_column("workshop_submissions", "", "description", "TEXT", "", "", "", "", "mailed");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_submissions` ADD INDEX (`userid`)");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_assessments` ADD INDEX (`submissionid`)");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_assessments` ADD INDEX (`userid`)");
    }

    if ($oldversion < 2004092700) {
        table_column("workshop", "", "wtype", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "description");
        table_column("workshop", "", "usepassword", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL");
        table_column("workshop", "", "password", "VARCHAR", "32", "", "", "NOT NULL");
        table_column("workshop_submissions", "", "late", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL");

        // update wkey value
        if ($workshops = get_records("workshop")) {
            foreach ($workshops as $workshop) {
                $wtype = 0; // 3 phases, no grading grades
                if ($workshop->includeself or $workshop->ntassessments) $wtype = 1; // 3 phases with grading grades
                if ($workshop->nsassessments) $wtype = 2; // 5 phases with grading grades
                set_field("workshop", "wtype", $wtype, "id", $workshop->id);
            }
        }
    }

    if ($oldversion < 2004102800) {
        table_column("workshop", "", "releasegrades", "INTEGER", "10", "UNSIGNED", "0", "NOT NULL", "deadline");
        execute_sql("
        CREATE TABLE `{$CFG->prefix}workshop_stockcomments` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `workshopid` int(10) unsigned NOT NULL default '0',
          `elementno` int(10) unsigned NOT NULL default '0',
          `comments` text NOT NULL,
          PRIMARY KEY  (`id`)
        ) COMMENT='Defines stockcomments, the teacher comment bank'
        ");
    }

    if ($oldversion < 2004111000) {
        table_column("workshop_elements", "", "stddev", "FLOAT", "", "", "0", "NOT NULL");
        table_column("workshop_elements", "", "totalassessments", "INTEGER", "10", "", "0", "NOT NULL");
        execute_sql(" ALTER TABLE `{$CFG->prefix}workshop_elements` CHANGE `weight` `weight` INT(4) UNSIGNED NOT NULL DEFAULT '11'");
        table_column("workshop_submissions", "", "nassessments", "INTEGER", "10", "", "0", "NOT NULL");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_submissions` DROP COLUMN `teachergrade`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_submissions` DROP COLUMN `peergrade`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_submissions` DROP COLUMN `biasgrade`");
        execute_sql("ALTER TABLE `{$CFG->prefix}workshop_submissions` DROP COLUMN `reliabilitygrade`");
    }

    if ($oldversion < 2004111200) {
        execute_sql("ALTER TABLE {$CFG->prefix}workshop DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_assessments DROP INDEX userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_assessments DROP INDEX workshopid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_assessments DROP INDEX submissionid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_assessments DROP INDEX mailed;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_comments DROP INDEX workshopid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_comments DROP INDEX assessmentid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_comments DROP INDEX userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_comments DROP INDEX mailed;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_elements DROP INDEX workshopid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_grades DROP INDEX workshopid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_grades DROP INDEX assessmentid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP INDEX workshopid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP INDEX userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}workshop_submissions DROP INDEX mailed;",false);

        modify_database('','ALTER TABLE prefix_workshop ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_workshop_assessments ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_workshop_assessments ADD INDEX workshopid (workshopid);');
        modify_database('','ALTER TABLE prefix_workshop_assessments ADD INDEX submissionid (submissionid);');
        modify_database('','ALTER TABLE prefix_workshop_assessments ADD INDEX mailed (mailed);');
        modify_database('','ALTER TABLE prefix_workshop_comments ADD INDEX workshopid (workshopid);');
        modify_database('','ALTER TABLE prefix_workshop_comments ADD INDEX assessmentid (assessmentid);');
        modify_database('','ALTER TABLE prefix_workshop_comments ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_workshop_comments ADD INDEX mailed (mailed);');
        modify_database('','ALTER TABLE prefix_workshop_elements ADD INDEX workshopid (workshopid);');
        modify_database('','ALTER TABLE prefix_workshop_grades ADD INDEX workshopid (workshopid);');
        modify_database('','ALTER TABLE prefix_workshop_grades ADD INDEX assessmentid (assessmentid);');
        modify_database('','ALTER TABLE prefix_workshop_submissions ADD INDEX workshopid (workshopid);');
        modify_database('','ALTER TABLE prefix_workshop_submissions ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_workshop_submissions ADD INDEX mailed (mailed);');
    }

    if ($oldversion < 2004120402) {
        table_column('workshop', '', 'submissionstart', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL', 'maxbytes');
        table_column('workshop', '', 'assessmentstart', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL', 'submissionstart');
        table_column('workshop', 'deadline', 'submissionend', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL');
        table_column('workshop', '', 'assessmentend', 'INTEGER', '10', 'UNSIGNED', '0', 'NOT NULL', 'submissionend');

        $workshops = get_records('workshop');
        if(!empty($workshops)) {
            foreach ($workshops as $workshop) {
                $early = (time() < $workshop->submissionend) ? 0 : $workshop->submissionend;
                $late = (time() > $workshop->submissionend) ? 0 : $workshop->submissionend;
                set_field('workshop', 'submissionstart', ($workshop->phase > 1) ? $early : $late, 'id', $workshop->id);
                set_field('workshop', 'assessmentstart', ($workshop->phase > 2) ? $early : $late, 'id', $workshop->id);
                set_field('workshop', 'submissionend', ($workshop->phase > 3) ? $early : $late, 'id', $workshop->id);
                set_field('workshop', 'assessmentend', ($workshop->phase > 4) ? $early : $late, 'id', $workshop->id);
            }
        }
        execute_sql('ALTER TABLE  '. $CFG->prefix .'workshop DROP COLUMN phase');

        execute_sql("UPDATE {$CFG->prefix}event SET eventtype = 'submissionend' WHERE eventtype = 'deadline' AND modulename = 'workshop'", false);
    }

    if ($oldversion < 2004120900) {
        table_column('workshop_assessments', '', 'teachergraded', 'INTEGER', '4', 'UNSIGNED', '0', 'NOT NULL', 'gradinggrade');
    }

    if ($oldversion < 2005041200) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $wtm->update( 'workshop','description','format' );
    }

    if ($oldversion < 2006090500) {
        $columns = $db->MetaColumns($CFG->prefix.'workshop_assessments');
        $columns = array_change_key_case($columns, CASE_LOWER);
        if (!isset($columns['teachergraded'])) {
            table_column('workshop_assessments', '', 'teachergraded', 'INTEGER', '4', 'UNSIGNED', '0', 'NOT NULL', 'gradinggrade');
        }
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
