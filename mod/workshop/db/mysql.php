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
    return true;
}


?>

