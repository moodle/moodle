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

    global $CFG, $THEME;

    $result = true;

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
        execute_sql(" ALTER TABLE `course` ADD `modinfo` TEXT NOT NULL AFTER `format` ");
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

    if ($oldversion < 2002111100) {
        print_simple_box_start("CENTER", "", "#FFCCCC");
        echo "<FONT SIZE=+1>";
        echo "<P>Changes have been made to all built-in themes, to add the new popup navigation menu.";
        echo "<P>If you have customised themes, you will need to edit theme/xxxx/header.html as follows:";
        echo "<UL><LI>Change anywhere it says <B>$"."button</B> to say <B>$"."menu</B>";
        echo "<LI>Add <B>$"."button</B> elsewhere (eg at the end of the navigation bar)</UL>";
        echo "<P>See the standard themes for examples, eg: theme/standard/header.html";
        print_simple_box_end();
    }

    if ($oldversion < 2002111200) {
        execute_sql(" ALTER TABLE `course` ADD `showrecent` TINYINT(5) UNSIGNED DEFAULT '1' NOT NULL AFTER `numsections` ");
    }

    if ($oldversion < 2002111400) {
    // Rebuild all course caches, because some may not be done in new installs (eg site page)
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

    if ($oldversion < 2002112000) {
        set_config("guestloginbutton", 1);
    }

    if ($oldversion < 2002122300) {
        execute_sql("ALTER TABLE `log` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `user_admins` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `user_students` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `user_teachers` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `user_students` CHANGE `start` `timestart` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `user_students` CHANGE `end` `timeend` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
    }

    if ($oldversion < 2002122700) {
        if (! record_exists("log_display", "module", "user", "action", "view")) {
            execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('user', 'view', 'user', 'CONCAT(firstname,' ',lastname)') ");
        }
    }
    if ($oldversion < 2003010101) {
        delete_records("log_display", "module", "user");
        $new->module = "user";
        $new->action = "view";
        $new->mtable = "user";
        $new->field  = "CONCAT(firstname,\" \",lastname)";
        insert_record("log_display", $new);

        delete_records("log_display", "module", "course");
        $new->module = "course";
        $new->action = "view";
        $new->mtable = "course";
        $new->field  = "fullname";
        insert_record("log_display", $new);
        $new->action = "update";
        insert_record("log_display", $new);
        $new->action = "enrol";
        insert_record("log_display", $new);
    }

    if ($oldversion < 2003012200) {
        // execute_sql(" ALTER TABLE `log_display` CHANGE `module` `module` VARCHAR( 20 ) NOT NULL ");
        // Commented out - see below where it's done properly
    }

	if ($oldversion < 2003032500) {
	    modify_database("", "CREATE TABLE `prefix_user_coursecreators` (
                             `id` int(10) unsigned NOT NULL auto_increment,
                             `userid` int(10) unsigned NOT NULL default '0',
                             PRIMARY KEY  (`id`),
                             UNIQUE KEY `id` (`id`)
                             ) TYPE=MyISAM COMMENT='One record per course creator';");
	}
	if ($oldversion < 2003032602) {
        // Redoing it because of no prefix last time
        execute_sql(" ALTER TABLE `{$CFG->prefix}log_display` CHANGE `module` `module` VARCHAR( 20 ) NOT NULL ");
        // Add some indexes for speed
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX(course) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX(userid) ");
    }
    
	if ($oldversion < 2003041400) {
        table_column("course_modules", "", "visible", "integer", "1", "unsigned", "1", "not null", "score");
    }

	if ($oldversion < 2003042104) {  // Try to update permissions of all files
        if ($files = get_directory_list($CFG->dataroot)) {
            echo "Attempting to update permissions for all files... ignore any errors.";
            foreach ($files as $file) {
                echo "$CFG->dataroot/$file<br>";
                @chmod("$CFG->dataroot/$file", $CFG->directorypermissions);
            }
        }
    }

    if ($oldversion < 2003042400) {
    // Rebuild all course caches, because of changes to do with visible variable
        if ($courses = get_records_sql("SELECT * FROM {$CFG->prefix}course")) {
            require_once("$CFG->dirroot/course/lib.php");
            foreach ($courses as $course) {
                $modinfo = serialize(get_array_of_activities($course->id));

                if (!set_field("course", "modinfo", $modinfo, "id", $course->id)) {
                    notify("Could not cache module information for course '$course->fullname'!");
                }
            }
        }
    }

    if ($oldversion < 2003042500) {                 
    //  Convert all usernames to lowercase.  
        $users = get_records_sql("SELECT id, username FROM {$CFG->prefix}user"); 
        $cerrors = "";
        $rarray = array();

        foreach ($users as $user) {      // Check for possible conflicts
            $lcname = trim(moodle_strtolower($user->username));
            if (in_array($lcname, $rarray)) {
                $cerrors .= $user->id."->".$lcname.'<br/>' ; 
            } else {
                array_push($rarray,$lcname);
            }
        }

        if ($cerrors != '') {
            notify("Error: Cannot convert usernames to lowercase. 
                    Following usernames would overlap (id->username):<br/> $cerrors . 
                    Please resolve overlapping errors."); 
            $result = false;
        }

        $cerrors = "";
        echo "Checking userdatabase:<br>";
        foreach ($users as $user) {
            $lcname = trim(moodle_strtolower($user->username));
            if ($lcname != $user->username) {
                $convert = set_field("user" , "username" , $lcname, "id", $user->id);
                if (!$convert) {
                    if ($cerrors){
                       $cerrors .= ", ";
                    }   
                    $cerrors .= $item;
                } else {
                    echo ".";
                }   
            }
        }
        if ($cerrors != '') {
            notify("There were errors when converting following usernames to lowercase. 
                   '$cerrors' . Sorry, but you will need to fix your database by hand.");
            $result = false;
        }
    }

    if ($oldversion < 2003042600) {
        /// Some more indexes - we need all the help we can get on the logs
        //execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX(module) ");
        //execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX(action) ");
    }

    if ($oldversion < 2003042700) {
        /// Changing to multiple indexes
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` DROP INDEX module ", false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` DROP INDEX action ", false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` DROP INDEX course ", false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` DROP INDEX userid ", false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX coursemoduleaction (course,module,action) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX courseuserid (course,userid) ");
    }

    if ($oldversion < 2003042801) {
        execute_sql("CREATE TABLE `{$CFG->prefix}course_display` (
                        `id` int(10) unsigned NOT NULL auto_increment,
                        `course` int(10) unsigned NOT NULL default '0',
                        `userid` int(10) unsigned NOT NULL default '0',
                        `display` int(10) NOT NULL default '0',
                        PRIMARY KEY  (`id`),
                        UNIQUE KEY `id` (`id`),
                        KEY `courseuserid` (course,userid)
                     ) TYPE=MyISAM COMMENT='Stores info about how to display the course'");
    }

    if ($oldversion < 2003050400) {
        table_column("course_sections", "", "visible", "integer", "1", "unsigned", "1", "", "");
    }

    if ($oldversion < 2003050900) {
        table_column("modules", "", "visible", "integer", "1", "unsigned", "1", "", "");
    }

    if ($oldversion < 2003050902) {
        if (get_records("modules", "name", "pgassignment")) {
            print_simple_box("Note: the pgassignment module will soon be deleted from CVS!  Go to the new 'Manage Modules' page and DELETE IT from your system", "center", "50%", "$THEME->cellheading", "20", "noticebox");
        }
    }

    return $result;

}

?>
