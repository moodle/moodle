<?PHP  //$Id$
// This file keeps track of upgrades to Moodle.
// 
// Sometimes, changes between versions involve 
// alterations to database structures and other 
// major things that may break installations.  
//
// This file specifies the current version of 
// Moodle installed, which can be compared against
// a previous version (see the "config" table).
//
// To do this, visit the "admin" page.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older databases to the current version.
// If there's something it cannot do itself, it 
// will tell you what you need to do.

$version = 2002073100;

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
        execute_sql("DELETE FROM `modules` WHERE `name` = 'chat' ");
    }

    return true;
}

?>
