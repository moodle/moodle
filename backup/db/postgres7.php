<?php  //$Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

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

    if ($oldversion < 2006042801) {
        table_column('backup_log', 'time', 'time', 'integer', '', '', '0');
        table_column('backup_log', 'laststarttime', 'laststarttime', 'integer', '', '', '0');
        table_column('backup_log', 'courseid', 'courseid', 'integer', '', '', '0');

        table_column('backup_courses', 'lastendtime', 'lastendtime', 'integer', '', '', '0');
        table_column('backup_courses', 'laststarttime', 'laststarttime', 'integer', '', '', '0');
        table_column('backup_courses', 'courseid', 'courseid', 'integer', '', '', '0');
        table_column('backup_courses', 'nextstarttime', 'nextstarttime', 'integer', '', '', '0');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    //Finally, return result
    return $result;

}

?>
