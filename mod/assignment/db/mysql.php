<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function assignment_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2002080500) {

        execute_sql("
        CREATE TABLE `assignment` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `course` int(10) unsigned NOT NULL default '0',
          `name` varchar(255) NOT NULL default '',
          `description` text NOT NULL,
          `type` int(10) unsigned NOT NULL default '1',
          `maxbytes` int(10) unsigned NOT NULL default '100000',
          `timedue` int(10) unsigned NOT NULL default '0',
          `grade` int(10) NOT NULL default '0',
          `timemodified` int(10) unsigned NOT NULL default '0',
          PRIMARY KEY  (`id`)
        ) COMMENT='Defines assignments'
        ");
        
        execute_sql("
        CREATE TABLE `assignment_submissions` (
          `id` int(10) unsigned NOT NULL default '0',
          `assignment` int(10) unsigned NOT NULL default '0',
          `user` int(10) unsigned NOT NULL default '0',
          `timecreated` int(10) unsigned NOT NULL default '0',
          `timemodified` int(10) unsigned NOT NULL default '0',
          `numfiles` int(10) unsigned NOT NULL default '0',
          `grade` int(11) NOT NULL default '0',
          `comment` text NOT NULL,
          `teacher` int(10) unsigned NOT NULL default '0',
          `timemarked` int(10) unsigned NOT NULL default '0',
          `mailed` tinyint(1) unsigned NOT NULL default '0',
          PRIMARY KEY  (`id`)
        ) COMMENT='Info about submitted assignments'
        ");
        
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'view', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'add', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'update', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'view submissions', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'upload', 'assignment', 'name') ");
    }

    if ($oldversion < 2002080701) {
        execute_sql(" ALTER TABLE `assignment_submissions` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ");
    }

    if ($oldversion < 2002082806) {
        // assignment file area was moved, so rename all the directories in existing courses

        notify("Moving location of assignment files...");

        $basedir = opendir("$CFG->dataroot");
        while (false !== ($dir = readdir($basedir))) {
            if ($dir == "." || $dir == ".." || $dir == "users") {
                continue;
            }
            if (filetype("$CFG->dataroot/$dir") != "dir") {
                continue;
            }
            $coursedir = "$CFG->dataroot/$dir";

            if (! $coursemoddata = make_mod_upload_directory($dir)) {
                echo "Error: could not create mod upload directory: $coursemoddata";
                continue;
            }

            if (file_exists("$coursedir/assignment")) {
                if (! rename("$coursedir/assignment", "$coursemoddata/assignment")) {
                    echo "Error: could not move $coursedir/assignment to $coursemoddata/assignment\n";
                }
            }
        }
    }

    if ($oldversion < 2002101600) {
        execute_sql(" ALTER TABLE `assignment` ADD `format` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `description` ");
    }
    if ($oldversion < 2002110302) {
        execute_sql(" UPDATE `assignment` SET `type` = '1'");
    }
    if ($oldversion < 2002111500) {
        execute_sql(" ALTER TABLE `assignment` ADD `resubmit` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL AFTER `format` ");
    }
    if ($oldversion < 2002122300) {
        execute_sql("ALTER TABLE `assignment_submissions` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
    }
    if ($oldversion < 2004021700) {
        set_field("log_display", "action", "view submission", "module", "assignment", "action", "view submissions");
    }
    if ($oldversion < 2004040100) {
        include_once("$CFG->dirroot/mod/assignment/lib.php");
        assignment_refresh_events();
    }

    if ($oldversion < 2004111200) { 
        execute_sql("ALTER TABLE {$CFG->prefix}assignment DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}assignment_submissions DROP INDEX assignment;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}assignment_submissions DROP INDEX userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}assignment_submissions DROP INDEX mailed;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}assignment_submissions DROP INDEX timemarked;",false);

        modify_database('','ALTER TABLE prefix_assignment ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_assignment_submissions ADD INDEX assignment(assignment);');
        modify_database('','ALTER TABLE prefix_assignment_submissions ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_assignment_submissions ADD INDEX mailed (mailed);');
        modify_database('','ALTER TABLE prefix_assignment_submissions ADD INDEX timemarked (timemarked);');
    }

    if ($oldversion < 2005010500) {  // New field for sending out mail to teachers
        table_column('assignment', '', 'emailteachers', 'integer', '2', 'unsigned', 0, 'not null', 'resubmit');
    }

    if ($oldversion < 2005041100) { // replace wiki-like with markdown
        include_once( "$CFG->dirroot/lib/wiki_to_markdown.php" );
        $wtm = new WikiToMarkdown();
        $wtm->update( 'assignment','description','format' );
    }

    if ($oldversion < 2005041400) {  // Add new fields for the new refactored version of assignment
        table_column('assignment', '', 'timeavailable', 'integer', '10', 'unsigned', 0, 'not null', 'timedue');
        table_column('assignment', '', 'assignmenttype', 'varchar', '50', '', '', 'not null', 'format');
        execute_sql("UPDATE {$CFG->prefix}assignment SET assignmenttype = 'offline' WHERE type = '0';");
        execute_sql("UPDATE {$CFG->prefix}assignment SET assignmenttype = 'uploadsingle' WHERE type = '1';");
        execute_sql('ALTER TABLE '.$CFG->prefix.'assignment DROP type;');
    }

    if ($oldversion < 2005041501) {  // Add two new fields for general data handling, 
                                     // so most assignment types won't need new fields and backups stay simple
        table_column('assignment_submissions', '', 'data2', 'MEDIUMTEXT', '', '', '', 'not null', 'numfiles');
        table_column('assignment_submissions', '', 'data1', 'MEDIUMTEXT', '', '', '', 'not null', 'numfiles');
    }

    if ($oldversion < 2005041600) {  // Add five new fields for general assignment parameters
                                     // so most assignment types won't need new fields and backups stay simple
        table_column('assignment', '', 'var5', 'integer', '10', '', 0, 'null', 'emailteachers');
        table_column('assignment', '', 'var4', 'integer', '10', '', 0, 'null', 'emailteachers');
        table_column('assignment', '', 'var3', 'integer', '10', '', 0, 'null', 'emailteachers');
        table_column('assignment', '', 'var2', 'integer', '10', '', 0, 'null', 'emailteachers');
        table_column('assignment', '', 'var1', 'integer', '10', '', 0, 'null', 'emailteachers');
    }

    if ($oldversion < 2005041700) {  // Allow comments to have a formatting
        table_column('assignment_submissions', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'comment');
    }

    if ($oldversion < 2005041800) {  // Prevent late submissions?  (default no)
        table_column('assignment', '', 'preventlate', 'integer', '2', 'unsigned', '0', 'not null', 'resubmit');
    }

    if ($oldversion < 2005060100) {
        include_once("$CFG->dirroot/mod/assignment/lib.php");
        assignment_refresh_events();
    }

    if ($oldversion < 2006092100) {
        table_column('assignment_submissions', 'comment', 'submissioncomment', 'text', '', '', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
