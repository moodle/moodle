<?php

/***************************************************************************
 * Functions required for setting up the database to use the new groups
 **************************************************************************/
//require_once('../../config.php');
require_once($CFG->libdir.'/datalib.php');


// @@@ TO DO Needs lots of sorting out so proper install/upgrade and also 
// so used new db stuff. In practice we probably don't actually want to rename 
// the tables (the group_member table in particular as this is basically 
// unchanged)that already exist on the whole if we can help it so this and the 
// the other dblib files should really be sorted out to do this. 

// Database changes
// New tables - the SQL for creating the tables is below (though should be 
// foreign keys!) - however it might be more sensible to modify the existing 
// tables instead as much as we can so that we don't need to copy data over and 
// that any existing code that does assume the existence of those tables
// might still work.
// Another caveat - the code below doesn't contain the new fields in the
// groupings table - viewowngroup, viewallgroupsmemebers, viewallgroupsactivities,
// teachersgroupmark, teachersgroupview, teachersoverride, teacherdeletetable.
// Other changes:
// * course currently contains groupmode and groupmodeforce - we need to change
// this to groupingid which is either null or a forced groupingid - need to
// copy over existing data sensibly. 
// * course_modules needs groupingid (think it previously had groupmode)


// Change database tables - course table need to remove two fields add groupingid field
// Move everything over
// Course module instance need to add groupingid field
// Module table - add group support field. 
// Add deletable by teacher field. 


/**
 * Creates the database tables required
 */
function groups_create_database_tables() {
	global $CFG;
	$table_prefix = $CFG->prefix;

	$createcoursegrouptablesql = "CREATE TABLE IF NOT EXISTS  `{$table_prefix}groups_courses_groups`
								 (`id` int(10) unsigned NOT NULL auto_increment,
  					               `courseid` int(10) unsigned NOT NULL default '0',
  							       `groupid` int(11) NOT NULL,
  							        PRIMARY KEY  (`id`),
  						            UNIQUE KEY `id` (`id`),  KEY `courseid` (`courseid`))";
  						            
	$creategroupstablesql = "CREATE TABLE IF NOT EXISTS `{$table_prefix}groups_groups` (
  							`id` int(10) unsigned NOT NULL auto_increment,
  							`name` varchar(254) collate latin1_general_ci NOT NULL default '',
  							`description` text collate latin1_general_ci NOT NULL,
  							`enrolmentkey` varchar(50) collate latin1_general_ci NOT NULL default '',
  							`lang` varchar(10) collate latin1_general_ci NOT NULL default 'en',
  							`theme` varchar(50) collate latin1_general_ci NOT NULL default '',
  							`picture` int(10) unsigned NOT NULL default '0',
  							`hidepicture` int(2) unsigned NOT NULL default '0',
  							`timecreated` int(10) unsigned NOT NULL default '0',
  							`timemodified` int(10) unsigned NOT NULL default '0',
  							PRIMARY KEY  (`id`), UNIQUE KEY `id` (`id`))";


	$creategroupsuserstablesql = "CREATE TABLE IF NOT EXISTS `{$table_prefix}groups_groups_users` (
								  `id` int(10) unsigned NOT NULL auto_increment,
								  `groupid` int(10) unsigned NOT NULL default '0',
								  `userid` int(10) unsigned NOT NULL default '0',
								  `timeadded` int(10) unsigned NOT NULL default '0',
								  PRIMARY KEY  (`id`), UNIQUE KEY `id` (`id`),
								  KEY `groupid` (`groupid`), KEY `userid` (`userid`))  ";
	
	$createcoursesgroupingtablesql = "CREATE TABLE IF NOT EXISTS `{$table_prefix}groups_courses_groupings` (
									  `id` int(10) unsigned NOT NULL auto_increment,
									  `courseid` int(10) unsigned NOT NULL default '0',
									  `groupingid` mediumint(9) NOT NULL,
									  PRIMARY KEY  (`id`),
									  UNIQUE KEY `id` (`id`),
									  KEY `courseid` (`courseid`)
									)";
									  
	$creategroupingstablesql = "CREATE TABLE `{$table_prefix}groups_groupings` (
								  `id` int(10) unsigned NOT NULL auto_increment,
								  `name` varchar(254) collate latin1_general_ci NOT NULL default '',
								  `description` text collate latin1_general_ci NOT NULL,
								  `timecreated` int(10) unsigned NOT NULL default '0',
								  PRIMARY KEY  (`id`),
								  UNIQUE KEY `id` (`id`)
								)  ";
																	   
	$creategroupingsgroupstablesql = "CREATE TABLE IF NOT EXISTS `{$table_prefix}groups_groupings_groups` (
									  `id` int(10) unsigned NOT NULL auto_increment,
									  `groupingid` int(10) unsigned default '0',
									  `groupid` int(10) NOT NULL,
									  `timecreated` int(10) unsigned NOT NULL default '0',								  `viewowngroup` binary(1) NOT NULL,
									  `viewallgroupsmembers` binary(1) NOT NULL,
									  `viewallgroupsactivities` binary(1) NOT NULL,
									  `teachersgroupmark` binary(1) NOT NULL,
									  `teachersgroupview` binary(1) NOT NULL,
									  `teachersoverride` binary(1) NOT NULL, .
									  PRIMARY KEY  (`id`),
									  UNIQUE KEY `id` (`id`),
									  KEY `courseid` (`groupingid`)
									)  ";								  
	
	modify_database('',$createcoursegrouptablesql );
	modify_database('',$creategroupstablesql );
	modify_database('',$creategroupsuserstablesql);
	modify_database('',$createcoursesgroupingtablesql);
	modify_database('',$creategroupingstablesql);
	modify_database('',$creategroupingsgroupstablesql );
                       
}


/**
 * Copies any old style moodle group to a new style moodle group - we'll need this for any upgrade code
 * @param int $groupid The 'old moodle groups' id of the group to copy
 * @param int $courseid The course id 
 * @param boolean True if the operation was successful, false otherwise. 
 */
function groups_db_copy_moodle_group_to_imsgroup($groupid, $courseid) {
	
	$success = true;
	
	$groupsettings = get_record('groups', 'id ', $groupid, '');
	
	// Only copy the group if the group exists. 
	if ($groupsettings != false) {
		$record->name = $groupsettings->name;
		$record->description = $groupsettings->description;
		$record->password = $groupsettings->password;
		$record->lang = $groupsettings->lang;
		$record->theme = $groupsettings->theme;
		$record->picture = $groupsettings->picture;
		$record->hidepicture = $groupsettings->hidepicture;
		$record->timecreated = $groupsettings->timecreated;
		$record->timemodified = $groupsettings->timemodified;
		$newgroupid = insert_record('groups_groups', $record);
	    if (!$newgroupid) {
	    	$success = false;
	    }
		
		$courserecord->courseid = $groupsettings->courseid;
		$courserecord->groupid = $newgroupid;
		
		$added = insert_record('groups_courses_groups', $courserecord);
			            
	     if (!$added) {
	     	$success = false;
	     }  
	     
	     // Copy over the group members
		$groupmembers = get_records('groups_users', 'groupid', $groupid);
		if ($groupmembers != false) {
			foreach($groupmembers as $member) {
				$record->groupid = $newgroupid;
				$record->userid = $member->userid;
				$useradded = insert_record('groups_groups_users', $record);
				if (!$useradded) {
					$success = false;
				}
			}
		}

	} 
	
	if (!$success) {
		notify('Copy operations from Moodle groups to IMS Groups failed');
	}
     
    return $success;
}

?>