<?PHP  //$Id$
//
// This file keeps track of upgrades to Moodle.
// 
// Sometimes, changes between versions involve 
// alterations to database structures and other 
// major things that may break installations.  
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it 
// will tell you what you need to do.
//
// Versions are defined by /version.php
//
// This file is tailored to MySQL

function upgrade_moodle($oldversion=0) {

    if ($oldversion == 0) {
        execute_sql("
          CREATE TABLE `config` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(255) NOT NULL default '',
            `value` varchar(255) NOT NULL default '',
            PRIMARY KEY  (`id`),
            UNIQUE KEY `name` (`name`)
          ) COMMENT='Moodle configuration variables';");
        notify("Created a new table 'config' to hold configuration data");
    }
    if ($oldversion < 2002073100) {
        execute_sql(" DELETE FROM `modules` WHERE `name` = 'chat' ");
    }
    if ($oldversion < 2002080200) {
        execute_sql(" ALTER TABLE `modules` DROP `fullname`  ");
        execute_sql(" ALTER TABLE `modules` DROP `search`  ");
    }
    if ($oldversion < 2002080300) {
        execute_sql(" ALTER TABLE `log_display` CHANGE `table` `mtable` VARCHAR( 20 ) NOT NULL ");
        execute_sql(" ALTER TABLE `user_teachers` CHANGE `authority` `authority` TINYINT( 3 ) DEFAULT '3' NOT NULL ");
    }
    if ($oldversion < 2002082100) {
        execute_sql(" ALTER TABLE `course` CHANGE `guest` `guest` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL ");
    }
    if ($oldversion < 2002082101) {
        execute_sql(" ALTER TABLE `user` ADD `maildisplay` TINYINT(2) UNSIGNED DEFAULT '2' NOT NULL AFTER `mailformat` ");
    }
    if ($oldversion < 2002090100) {
        execute_sql(" ALTER TABLE `course_sections` CHANGE `summary` `summary` TEXT NOT NULL ");
    }
    if ($oldversion < 2002090701) {
        execute_sql(" ALTER TABLE `user_teachers` CHANGE `authority` `authority` TINYINT( 10 ) DEFAULT '3' NOT NULL ");
        execute_sql(" ALTER TABLE `user_teachers` ADD `role` VARCHAR(40) NOT NULL AFTER `authority` ");
    }
    if ($oldversion < 2002090800) {
        execute_sql(" ALTER TABLE `course` ADD `teachers` VARCHAR( 100 ) DEFAULT 'Teachers' NOT NULL AFTER `teacher` ");
        execute_sql(" ALTER TABLE `course` ADD `students` VARCHAR( 100 ) DEFAULT 'Students' NOT NULL AFTER `student` ");
    }
    if ($oldversion < 2002091000) {
        execute_sql(" ALTER TABLE `user` CHANGE `personality` `secret` VARCHAR( 15 ) DEFAULT NULL  ");
    }
    if ($oldversion < 2002091400) {
        execute_sql(" ALTER TABLE `user` ADD `lang` VARCHAR( 3 ) DEFAULT 'en' NOT NULL AFTER `country`  ");
    }
    if ($oldversion < 2002091900) {
        notify("Most Moodle configuration variables have been moved to the database and can now be edited via the admin page.");
        notify("Although it is not vital that you do so, you might want to edit <U>config.php</U> and remove all the unused settings (except the database, URL and directory definitions).  See <U>config-dist.php</U> for an example of how your new slim config.php should look.");
    }
    if ($oldversion < 2002092000) {
        execute_sql(" ALTER TABLE `user` CHANGE `lang` `lang` VARCHAR(5) DEFAULT 'en' NOT NULL  ");
    }
    if ($oldversion < 2002092100) {
        execute_sql(" ALTER TABLE `user` ADD `deleted` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `confirmed` ");
    }

    return true;
}

?>
