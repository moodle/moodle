<?PHP // $Id$

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
        
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'view', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'add', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'update', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'view submissions', 'assignment', 'name') ");
        execute_sql(" INSERT INTO log_display VALUES ('assignment', 'upload', 'assignment', 'name') ");
    }

    if ($oldversion < 2002080701) {
        execute_sql(" ALTER TABLE `assignment_submissions` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ");
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

    return true;
}


?>
