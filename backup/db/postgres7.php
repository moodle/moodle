<?php  //$Id$
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

function backup_upgrade($oldversion=0) {

    global $CFG;

    $result = true;

    if ($oldversion < 2006011600 and $result) {
        $result = execute_sql("DROP TABLE {$CFG->prefix}backup_files");
        if ($result) {
            $result = execute_sql("CREATE TABLE {$CFG->prefix}backup_files (
                          id SERIAL PRIMARY KEY,
                          backup_code integer NOT NULL default '0',
                          file_type varchar(10) NOT NULL default '',
                          path varchar(255) NOT NULL default '',
                          old_id integer default NULL,
                          new_id integer default NULL,
                          CONSTRAINT {$CFG->prefix}backup_files_uk UNIQUE (backup_code, file_type, path))");
        }
        if ($result) {
            $result = execute_sql("DROP TABLE {$CFG->prefix}backup_ids");
        }
        if ($result) {
            $result = execute_sql("CREATE TABLE {$CFG->prefix}backup_ids (
                          id SERIAL PRIMARY KEY,
                          backup_code integer NOT NULL default '0',
                          table_name varchar(30) NOT NULL default '',
                          old_id integer NOT NULL default '0',
                          new_id integer default NULL,
                          info text,
                          CONSTRAINT {$CFG->prefix}backup_ids_uk UNIQUE (backup_code, table_name, old_id))");
        }
    }

    //Finally, return result
    return $result;

}

?>
