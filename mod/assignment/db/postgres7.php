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
          `id` SERIAL PRIMARY KEY,
          `course` integer NOT NULL default '0',
          `name` varchar(255) NOT NULL default '',
          `description` text NOT NULL,
          `type` integer NOT NULL default '1',
          `maxbytes` integer NOT NULL default '100000',
          `timedue` integer NOT NULL default '0',
          `grade` integer NOT NULL default '0',
          `timemodified` integer NOT NULL default '0'
        )
        ");
        
        execute_sql("
        CREATE TABLE `assignment_submissions` (
          `id` integer NOT NULL PRIMARY KEY default '0',
          `assignment` integer NOT NULL default '0',
          `user` integer NOT NULL default '0',
          `timecreated` integer NOT NULL default '0',
          `timemodified` integer NOT NULL default '0',
          `numfiles` integer NOT NULL default '0',
          `grade` integer NOT NULL default '0',
          `comment` text NOT NULL,
          `teacher` integer NOT NULL default '0',
          `timemarked` integer NOT NULL default '0',
          `mailed` integer NOT NULL default '0'
        )
        ");
        
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'view', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'add', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'update', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'view submissions', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('assignment', 'upload', 'assignment', 'name') ");
    }

    if ($oldversion < 2002080701) {
        execute_sql(" ALTER TABLE `assignment_submissions` CHANGE `id` `id` SERIAL PRIMARY KEY ");
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
        execute_sql(" ALTER TABLE `assignment` ADD `format` INTEGER DEFAULT '0' NOT NULL AFTER `description` ");
    }
    if ($oldversion < 2002110302) {
        execute_sql(" UPDATE `assignment` SET `type` = '1'");
    }
    if ($oldversion < 2003091000) {
        # Old field that was never added!
        table_column("assignment", "", "resubmit", "integer", "2", "unsigned", "0", "", "format");
    }

    if ($oldversion < 2004021700) {
        set_field("log_display", "action", "view submission", "module", "assignment", "action", "view submissions");
    }

    if ($oldversion < 2004040100) {
        include_once("$CFG->dirroot/mod/assignment/lib.php");
        assignment_refresh_events();
    }

    if ($oldversion < 2004111200) { 
        execute_sql("DROP INDEX {$CFG->prefix}assignment_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}assignment_submissions_assignment_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}assignment_submissions_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}assignment_submissions_mailed_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}assignment_submissions_timemarked_idx;",false);

        modify_database('','CREATE INDEX prefix_assignment_course_idx ON prefix_assignment (course);');
        modify_database('','CREATE INDEX prefix_assignment_submissions_assignment_idx ON prefix_assignment_submissions (assignment);');
        modify_database('','CREATE INDEX prefix_assignment_submissions_userid_idx ON prefix_assignment_submissions (userid);');
        modify_database('','CREATE INDEX prefix_assignment_submissions_mailed_idx ON prefix_assignment_submissions (mailed);');
        modify_database('','CREATE INDEX prefix_assignment_submissions_timemarked_idx ON prefix_assignment_submissions (timemarked);');
    }

    if ($oldversion < 2005010500) { 
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
        execute_sql("UPDATE {$CFG->prefix}assignment SET assignmenttype = 'offline' WHERE type = '0';",false);
        execute_sql("UPDATE {$CFG->prefix}assignment SET assignmenttype = 'uploadsingle' WHERE type = '1';",false);
        execute_sql('ALTER TABLE '.$CFG->prefix.'assignment DROP type;');
    }

    if ($oldversion < 2005041501) {  // Add two new fields for general data handling, 
                                     // so most assignment types won't need new fields and backups stay simple
        table_column('assignment_submissions', '', 'data2', 'TEXT', '', '', '', 'not null', 'numfiles');
        table_column('assignment_submissions', '', 'data1', 'TEXT', '', '', '', 'not null', 'numfiles');
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

    if ($oldversion < 2005060101) { // Mass cleanup of bad upgrade scripts
        modify_database('','ALTER TABLE prefix_assignment ALTER assignmenttype SET NOT NULL');
        modify_database('','ALTER TABLE prefix_assignment ALTER emailteachers SET NOT NULL');
        modify_database('','ALTER TABLE prefix_assignment ALTER preventlate SET NOT NULL');
        modify_database('','ALTER TABLE prefix_assignment ALTER timeavailable SET NOT NULL');
        modify_database('','ALTER TABLE prefix_assignment_submissions ALTER data1 SET NOT NULL');
        modify_database('','ALTER TABLE prefix_assignment_submissions ALTER data2 SET NOT NULL');
        modify_database('','ALTER TABLE prefix_assignment_submissions ALTER format SET NOT NULL');
    }

    if ($oldversion < 2006092100) {
        table_column('assignment_submissions', 'comment', 'submissioncomment', 'text', '', '', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
