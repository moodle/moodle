<?PHP // $Id$

function workshop_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

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

    
    return true;
}


?>
