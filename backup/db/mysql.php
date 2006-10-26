<?php  //$Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.
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
        print_simple_box("This is the first non-alpha release of the Backup/Restore module.<p>Thanks for upgrading!","center", "50%", '', "20", "noticebox");
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

    if ($oldversion < 2006011600 and $result) {
        $result = execute_sql("DROP TABLE {$CFG->prefix}backup_files");
        if ($result) {
            $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_files` (
                          `id` int(10) unsigned NOT NULL auto_increment,
                          `backup_code` int(10) unsigned NOT NULL default '0',
                          `file_type` varchar(10) NOT NULL default '',
                          `path` varchar(255) NOT NULL default '',
                          `old_id` int(10) unsigned NOT NULL default '0',
                          `new_id` int(10) unsigned NOT NULL default '0',
                          PRIMARY KEY  (`id`),
                          UNIQUE KEY `{$CFG->prefix}backup_files_uk` (`backup_code`,`file_type`,`path`)
                      ) TYPE=MyISAM COMMENT='To store and recode ids to user and course files.'");
        }
        if ($result) {
            $result = execute_sql("DROP TABLE {$CFG->prefix}backup_ids");
        }
        if ($result) {
            $result = execute_sql("CREATE TABLE `{$CFG->prefix}backup_ids` (
                          `id` int(10) unsigned NOT NULL auto_increment,
                          `backup_code` int(12) unsigned NOT NULL default '0',
                          `table_name` varchar(30) NOT NULL default '',
                          `old_id` int(10) unsigned NOT NULL default '0',
                          `new_id` int(10) unsigned NOT NULL default '0',
                          `info` mediumtext,
                          PRIMARY KEY  (`id`),
                          UNIQUE KEY `{$CFG->prefix}backup_ids_uk` (`backup_code` ,`table_name`,`old_id`)
                      ) TYPE=MyISAM COMMENT='To store and convert ids in backup/restore'");
        }
    }


    // code to drop the prefix in tables
    if ($oldversion < 2006042100) {
        // see bug 5205, silent drops, so should not panic anyone
        $result = execute_sql("ALTER TABLE {$CFG->prefix}backup_files DROP INDEX backup_files_uk", false);
        $result = execute_sql("ALTER TABLE {$CFG->prefix}backup_files DROP INDEX {$CFG->prefix}backup_files_uk", false);
        $result = execute_sql("ALTER TABLE {$CFG->prefix}backup_ids DROP INDEX backup_ids_uk", false);
        $result = execute_sql("ALTER TABLE {$CFG->prefix}backup_ids DROP INDEX {$CFG->prefix}backup_ids_uk", false);
        $result = execute_sql("ALTER TABLE {$CFG->prefix}backup_files ADD UNIQUE INDEX backup_files_uk(backup_code,file_type(10),path(255))");
        $result = execute_sql("ALTER TABLE {$CFG->prefix}backup_ids ADD UNIQUE INDEX backup_ids_uk(backup_code,table_name(30),old_id)");
    }

    // chaing default nulls to not null default 0
    
    if ($oldversion < 2006042800) {

        execute_sql("UPDATE {$CFG->prefix}backup_files SET old_id='0' WHERE old_id IS NULL");
        table_column('backup_files','old_id','old_id','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}backup_files SET new_id='0' WHERE new_id IS NULL");
        table_column('backup_files','new_id','new_id','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}backup_ids SET new_id='0' WHERE new_id IS NULL");
        table_column('backup_ids','new_id','new_id','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}backup_ids SET info='' WHERE info IS NULL");
        table_column('backup_ids','info','info','mediumtext','','','','not null');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    //Finally, return result
    return $result;

}

?>
