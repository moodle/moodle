<?PHP  //$Id$
//
// This file keeps track of upgrades to Moodle's
// backup/restore utility.
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

function backup_upgrade($oldversion=0) {

    global $CFG;

    $result = true;

    if ($oldversion < 2003050300 and $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_ids` (
                        `backup_code` INT(12) UNSIGNED NOT NULL, 
                        `table_name` VARCHAR(30) NOT NULL, 
                        `old_id` INT(10) UNSIGNED NOT NULL, 
                        `new_id` INT(10) UNSIGNED,
                         PRIMARY KEY (`backup_code`, `table_name`, `old_id`)
                     )
                     COMMENT = 'To store and convert ids in backup/restore'");
    }

    if ($oldversion < 2003050301 and $result) {
        $result = execute_sql("ALTER TABLE `{$CFG->prefix}backup_ids`
                         ADD `info` VARCHAR(30)");
    }

    if ($oldversion < 2003050400 and $result) {
        $result = execute_sql("ALTER TABLE `{$CFG->prefix}backup_ids`
                         MODIFY `info` VARCHAR(255)");
    } 

    if ($oldversion < 2003050401 and $result) {
        $result = execute_sql("CREATE  TABLE  `{$CFG->prefix}backup_files` (
                                  `backup_code` INT( 10  ) UNSIGNED NOT  NULL ,
                                  `file_type` VARCHAR( 10  )  NOT  NULL ,
                                  `path` VARCHAR( 255  )  NOT  NULL ,
                                  `old_id` INT( 10  ) UNSIGNED,
                                  `new_id` INT( 10  ) UNSIGNED,
                               PRIMARY  KEY (  `backup_code` ,  `file_type` ,  `path`  ) 
                               ) COMMENT  =  'To store and recode ids to user & course files.'");
    }

    if ($oldversion < 2003052000 and $result) {
        $result = execute_sql("ALTER TABLE `{$CFG->prefix}backup_ids`
                         MODIFY `info` TEXT");
    } 

    if ($oldversion < 2003061100 and $result) {
        $result = execute_sql("ALTER TABLE `{$CFG->prefix}backup_ids`
                         MODIFY `info` MEDIUMTEXT");
    } 

    if ($oldversion < 2003082600 and $result) {
        print_simple_box("This is the first non-alpha release of the Backup/Restore module.<p>Thanks for upgrading!","center", "50%", "$THEME->cellheading", "20", "noticebox");
    }

    if ($oldversion < 2003112700 and $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_config` (
                      `id` int(10) unsigned NOT NULL auto_increment,
                      `name` varchar(255) NOT NULL default '',
                      `value` varchar(255) NOT NULL default '',
                      PRIMARY KEY  (`id`),
                      UNIQUE KEY `name` (`name`)
                  ) TYPE=MyISAM COMMENT='To store backup configuration variables'");
    }

    if ($oldversion < 2003120800 and $result) {
        $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_courses` (
                      `id` int(10) unsigned NOT NULL auto_increment,
                      `courseid` int(10) unsigned NOT NULL default '0',
                      `laststarttime` int(10) unsigned NOT NULL default '0',
                      `lastendtime` int(10) unsigned NOT NULL default '0',
                      `laststatus` varchar(1) NOT NULL default '0',
                      `nextstarttime` int(10) unsigned NOT NULL default '0',
                      PRIMARY KEY  (`id`),
                      UNIQUE KEY `courseid` (`courseid`)
                  ) TYPE=MyISAM COMMENT='To store every course backup status'");

        if ($result) {
            $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_log` (    
                          `id` int(10) unsigned NOT NULL auto_increment, 
                          `courseid` int(10) unsigned NOT NULL default '0',
                          `time` int(10) unsigned NOT NULL default '0',
                          `laststarttime` int(10) unsigned NOT NULL default '0',
                          `info` varchar(255) NOT NULL default '',
                          PRIMARY KEY  (`id`)
                      ) TYPE=MyISAM COMMENT='To store every course backup log info'");
        }
    }

    //Finally, return result
    return $result;

}

?>
