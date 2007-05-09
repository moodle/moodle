<?php
/**
 * Functions required for setting up the database to use the new groups.
 *
 * TODO: replace with, postrges7.sql, mysql.php, install.xml
 *
 * @copyright &copy; 2006 The Open University
 * @author J.White AT open.ac.uk
 * @author N.D.Freear AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
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

    if ('mysql' == $CFG->dbfamily) {

        $createcoursegrouptablesql = "CREATE TABLE IF NOT EXISTS `{$CFG->prefix}groups_courses_groups` (
                            `id` int(10) unsigned NOT NULL auto_increment,
                            `courseid` int(10) unsigned NOT NULL default '0',
                            `groupid` int(11) NOT NULL,
                            PRIMARY KEY  (`id`),
                            UNIQUE KEY `id` (`id`),
                            KEY `courseid` (`courseid`)
                          ) ";

        $creategroupstablesql = "CREATE TABLE IF NOT EXISTS `{$CFG->prefix}groups_groups` (
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
                            PRIMARY KEY  (`id`),
                            UNIQUE KEY `id` (`id`)
                          ) ";

        $creategroupsuserstablesql = "CREATE TABLE IF NOT EXISTS `{$CFG->prefix}groups_groups_users` (
                            `id` int(10) unsigned NOT NULL auto_increment,
                            `groupid` int(10) unsigned NOT NULL default '0',
                            `userid` int(10) unsigned NOT NULL default '0',
                            `timeadded` int(10) unsigned NOT NULL default '0',
                            PRIMARY KEY  (`id`),
                            UNIQUE KEY `id` (`id`),
                            KEY `groupid` (`groupid`),
                            KEY `userid` (`userid`)
                          ) ";
    
        $createcoursesgroupingtablesql = "CREATE TABLE IF NOT EXISTS `{$CFG->prefix}groups_courses_groupings` (
                            `id` int(10) unsigned NOT NULL auto_increment,
                            `courseid` int(10) unsigned NOT NULL default '0',
                            `groupingid` mediumint(9) NOT NULL,
                            PRIMARY KEY  (`id`),
                            UNIQUE KEY `id` (`id`),
                            KEY `courseid` (`courseid`)
                          ) ";

        $creategroupingstablesql = "CREATE TABLE `{$CFG->prefix}groups_groupings` (
                            `id` int(10) unsigned NOT NULL auto_increment,
                            `name` varchar(254) collate latin1_general_ci NOT NULL,
                            `description` text collate latin1_general_ci NOT NULL default '',
                            `timecreated` int(10) unsigned NOT NULL default 0,
                            `viewowngroup` binary(1) NOT NULL default 1,
                            `viewallgroupsmembers` binary(1) NOT NULL default 0,
                            `viewallgroupsactivities` binary(1) NOT NULL default 0,
                            `teachersgroupmark` binary(1) NOT NULL default 0,
                            `teachersgroupview` binary(1) NOT NULL default 0,
                            `teachersoverride` binary(1) NOT NULL default 0,
                            PRIMARY KEY  (`id`),
                            UNIQUE KEY `id` (`id`)
                          ) ";

        $creategroupingsgroupstablesql = "CREATE TABLE IF NOT EXISTS `{$CFG->prefix}groups_groupings_groups` (
                            `id` int(10) unsigned NOT NULL auto_increment,
                            `groupingid` int(10) unsigned default '0',
                            `groupid` int(10) NOT NULL,
                            `timeadded` int(10) unsigned NOT NULL default '0',
                            PRIMARY KEY  (`id`),
                            UNIQUE KEY `id` (`id`),
                            KEY `courseid` (`groupingid`)
                          ) ";

    } else { //postgres7

        $createcoursegrouptablesql = "CREATE TABLE {$CFG->prefix}groups_courses_groups (
                            id SERIAL PRIMARY KEY,
                            courseid integer NOT NULL default '0',
                            groupid integer NOT NULL default '0'
                          );
                          CREATE INDEX {$CFG->prefix}groups_courses_groups_courseid_idx ON {$CFG->prefix}groups_courses_groups (courseid);
                          ";
                          //?? CONSTRAINT {$CFG->prefix}groups_courses_groups_id_courseid_uk UNIQUE (id, courseid)                                    

        $creategroupstablesql = "CREATE TABLE {$CFG->prefix}groups_groups (
                            id SERIAL PRIMARY KEY,
                            name varchar(255) NOT NULL,
                            description text NOT NULL default '',
                            enrolmentkey varchar(50) NOT NULL default '',
                            lang varchar(10) NOT NULL default 'en',
                            theme varchar(50) NOT NULL default '',
                            picture integer NOT NULL default '0',
                            hidepicture integer NOT NULL default '0',
                            timecreated integer NOT NULL default '0',
                            timemodified integer NOT NULL default '0'
                          ) ";

        $creategroupsuserstablesql = "CREATE TABLE {$CFG->prefix}groups_groups_users (
                            id SERIAL PRIMARY KEY,
                            groupid integer NOT NULL default '0',
                            userid integer NOT NULL default '0',
                            timeadded integer NOT NULL default '0'
                          );
                          CREATE INDEX {$CFG->prefix}groups_groups_users_groupid_idx ON {$CFG->prefix}groups_groups_users (groupid);
                          CREATE INDEX {$CFG->prefix}groups_groups_users_userid_idx ON {$CFG->prefix}groups_groups_users (userid);
                          COMMENT ON TABLE {$CFG->prefix}groups_groups_users IS 'New groupings (OU).';
                          ";
    
        $createcoursesgroupingtablesql = "CREATE TABLE {$CFG->prefix}groups_courses_groupings (
                            id SERIAL PRIMARY KEY,
                            courseid integer NOT NULL default '0',
                            groupingid integer NOT NULL
                          );
                          CREATE INDEX {$CFG->prefix}groups_courses_groupings_courseid_idx ON {$CFG->prefix}groups_courses_groupings (courseid);
                          COMMENT ON TABLE {$CFG->prefix}groups_courses_groupings IS 'New groupings (OU).';
                          ";
                                      
        $creategroupingstablesql = "CREATE TABLE {$CFG->prefix}groups_groupings (
                            id SERIAL PRIMARY KEY,
                            name varchar(254) NOT NULL default,
                            description text NOT NULL default '',
                            timecreated integer NOT NULL default 0,
                            viewowngroup integer NOT NULL default 1,
                            viewallgroupsmembers integer NOT NULL default 0,
                            viewallgroupsactivities integer NOT NULL default 0,
                            teachersgroupmark integer NOT NULL default 0,
                            teachersgroupview integer NOT NULL default 0,
                            teachersoverride integer NOT NULL default 0
                          ) ";

        $creategroupingsgroupstablesql = "CREATE TABLE {$CFG->prefix}groups_groupings_groups (
                            id SERIAL PRIMARY KEY,
                            groupingid integer default '0',
                            groupid integer NOT NULL,
                            timeadded integer NOT NULL default '0'
                          );
                          CREATE INDEX {$CFG->prefix}groups_groupings_groups_groupingid_idx ON {$CFG->prefix}groups_groupings_groups (groupingid);
                          ";
    }

    modify_database('', $createcoursegrouptablesql);
    modify_database('', $creategroupstablesql);
    modify_database('', $creategroupsuserstablesql);
    modify_database('', $createcoursesgroupingtablesql);
    modify_database('', $creategroupingstablesql);
    modify_database('', $creategroupingsgroupstablesql);
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
        $record = new Object();
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

        $courserecord = new Object();
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
                $record = new Object();
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
