<?PHP // $Id$

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
        
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'view', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'add', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'update', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'view submissions', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'upload', 'assignment', 'name') ");
    }

    if ($oldversion < 2002080701) {
        execute_sql(" ALTER TABLE `assignment_submissions` CHANGE `id` `id` SERIAL PRIMARY KEY ");
    }

    if ($oldversion < 2002082806) {
        // assignment file area was moved, so rename all the directories in existing courses

        notify("Moving location of assignment files...");

        $basedir = opendir("$CFG->dataroot");
        while ($dir = readdir($basedir)) {
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

    return true;
}


?>
