<?PHP  //$Id$
//
// This file keeps track of upgrades to Moodle.
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
// Versions are defined by /version.php
//
// This file is tailored to MySQL

function main_upgrade($oldversion=0) {

    global $CFG;

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
        execute_sql(" DELETE FROM `modules` WHERE `name` = 'chat' ");
    }
    if ($oldversion < 2002080200) {
        execute_sql(" ALTER TABLE `modules` DROP `fullname`  ");
        execute_sql(" ALTER TABLE `modules` DROP `search`  ");
    }
    if ($oldversion < 2002080300) {
        execute_sql(" ALTER TABLE `log_display` CHANGE `table` `mtable` VARCHAR( 20 ) NOT NULL ");
        execute_sql(" ALTER TABLE `user_teachers` CHANGE `authority` `authority` TINYINT( 3 ) DEFAULT '3' NOT NULL ");
    }
    if ($oldversion < 2002082100) {
        execute_sql(" ALTER TABLE `course` CHANGE `guest` `guest` TINYINT(2) UNSIGNED DEFAULT '0' NOT NULL ");
    }
    if ($oldversion < 2002082101) {
        execute_sql(" ALTER TABLE `user` ADD `maildisplay` TINYINT(2) UNSIGNED DEFAULT '2' NOT NULL AFTER `mailformat` ");
    }
    if ($oldversion < 2002090100) {
        execute_sql(" ALTER TABLE `course_sections` CHANGE `summary` `summary` TEXT NOT NULL ");
    }
    if ($oldversion < 2002090701) {
        execute_sql(" ALTER TABLE `user_teachers` CHANGE `authority` `authority` TINYINT( 10 ) DEFAULT '3' NOT NULL ");
        execute_sql(" ALTER TABLE `user_teachers` ADD `role` VARCHAR(40) NOT NULL AFTER `authority` ");
    }
    if ($oldversion < 2002090800) {
        execute_sql(" ALTER TABLE `course` ADD `teachers` VARCHAR( 100 ) DEFAULT 'Teachers' NOT NULL AFTER `teacher` ");
        execute_sql(" ALTER TABLE `course` ADD `students` VARCHAR( 100 ) DEFAULT 'Students' NOT NULL AFTER `student` ");
    }
    if ($oldversion < 2002091000) {
        execute_sql(" ALTER TABLE `user` CHANGE `personality` `secret` VARCHAR( 15 ) DEFAULT NULL  ");
    }
    if ($oldversion < 2002091400) {
        execute_sql(" ALTER TABLE `user` ADD `lang` VARCHAR( 3 ) DEFAULT 'en' NOT NULL AFTER `country`  ");
    }
    if ($oldversion < 2002091900) {
        notify("Most Moodle configuration variables have been moved to the database and can now be edited via the admin page.");
        notify("Although it is not vital that you do so, you might want to edit <U>config.php</U> and remove all the unused settings (except the database, URL and directory definitions).  See <U>config-dist.php</U> for an example of how your new slim config.php should look.");
    }
    if ($oldversion < 2002092000) {
        execute_sql(" ALTER TABLE `user` CHANGE `lang` `lang` VARCHAR(5) DEFAULT 'en' NOT NULL  ");
    }
    if ($oldversion < 2002092100) {
        execute_sql(" ALTER TABLE `user` ADD `deleted` TINYINT(1) UNSIGNED DEFAULT '0' NOT NULL AFTER `confirmed` ");
    }
    if ($oldversion < 2002101001) {
        execute_sql(" ALTER TABLE `user` ADD `htmleditor` TINYINT(1) UNSIGNED DEFAULT '1' NOT NULL AFTER `maildisplay` ");
    }
    if ($oldversion < 2002101701) {
        execute_sql(" ALTER TABLE `reading` RENAME `resource` ");  // Small line with big consequences!
        execute_sql(" DELETE FROM `log_display` WHERE module = 'reading'"); 
        execute_sql(" INSERT INTO log_display VALUES ('resource', 'view', 'resource', 'name') ");
        execute_sql(" UPDATE log SET module = 'resource' WHERE module = 'reading' ");
        execute_sql(" UPDATE modules SET name = 'resource' WHERE name = 'reading' ");
    }

    if ($oldversion < 2002102503) {
        require_once("$CFG->dirroot/mod/forum/lib.php");
        require_once("$CFG->dirroot/course/lib.php");

        if (! $module = get_record("modules", "name", "forum")) {
            notify("Could not find forum module!!");
            return false;
        }

        // First upgrade the site forums
        if ($site = get_site()) {
            print_heading("Making News forums editable for main site (moving to section 1)...");
            if ($news = forum_get_course_forum($site->id, "news")) {
                $mod->course = $site->id;
                $mod->module = $module->id;
                $mod->instance = $news->id;
                $mod->section = 1;
                if (! $mod->coursemodule = add_course_module($mod) ) {
                    notify("Could not add a new course module to the site");
                    return false;
                }
                if (! $sectionid = add_mod_to_section($mod) ) {
                    notify("Could not add the new course module to that section");
                    return false;
                }
                if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                    notify("Could not update the course module with the correct section");
                    return false;
                }
            }
        }


        // Now upgrade the courses.
        if ($courses = get_records_sql("SELECT * FROM course WHERE category > 0")) {
            print_heading("Making News and Social forums editable for each course (moving to section 0)...");
            foreach ($courses as $course) {
                if ($course->format == "social") {  // we won't touch them
                    continue;
                }
                if ($news = forum_get_course_forum($course->id, "news")) {
                    $mod->course = $course->id;
                    $mod->module = $module->id;
                    $mod->instance = $news->id;
                    $mod->section = 0;
                    if (! $mod->coursemodule = add_course_module($mod) ) {
                        notify("Could not add a new course module to the course '$course->fullname'");
                        return false;
                    }
                    if (! $sectionid = add_mod_to_section($mod) ) {
                        notify("Could not add the new course module to that section");
                        return false;
                    }
                    if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                        notify("Could not update the course module with the correct section");
                        return false;
                    }
                }
                if ($social = forum_get_course_forum($course->id, "social")) {
                    $mod->course = $course->id;
                    $mod->module = $module->id;
                    $mod->instance = $social->id;
                    $mod->section = 0;
                    if (! $mod->coursemodule = add_course_module($mod) ) {
                        notify("Could not add a new course module to the course '$course->fullname'");
                        return false;
                    }
                    if (! $sectionid = add_mod_to_section($mod) ) {
                        notify("Could not add the new course module to that section");
                        return false;
                    }
                    if (! set_field("course_modules", "section", $sectionid, "id", $mod->coursemodule)) {
                        notify("Could not update the course module with the correct section");
                        return false;
                    }
                }
            }
        }
    }

    if ($oldversion < 2002111003) {
        execute_sql(" ALTER TABLE `course` ADD `modinfo` TEXT NOT NULL AFTER `format` ");
        if ($courses = get_records_sql("SELECT * FROM course")) {
            require_once("$CFG->dirroot/course/lib.php");
            foreach ($courses as $course) {
                
                $modinfo = serialize(get_array_of_activities($course->id));

                if (!set_field("course", "modinfo", $modinfo, "id", $course->id)) {
                    notify("Could not cache module information for course '$course->fullname'!");
                }
            }
        }
    }


    return true;
}

?>
