<?PHP  //$Id$
//
// This file keeps track of upgrades to Moodle's
// blocks system.
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
// Versions are defined by backup_version.php
//
// This file is tailored to MySQL

function blocks_upgrade($oldversion=0) {

global $CFG;
    
    $result = true;
    
    if ($oldversion < 2004041000 and $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}blocks` (
                        `id` int(10) unsigned NOT NULL auto_increment,
                        `name` varchar(40) NOT NULL default '',
                        `version` int(10) NOT NULL default '0',
                        `cron` int(10) unsigned NOT NULL default '0',
                        `lastcron` int(10) unsigned NOT NULL default '0',
                        `visible` tinyint(1) NOT NULL default '1',
                        PRIMARY KEY (`id`)
                     ) 
                     COMMENT = 'To register and update all the available blocks'");
    }

    //Finally, return result
    return $result;
}
?>
