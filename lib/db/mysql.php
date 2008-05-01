<?PHP  //$Id$
// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.
//
//
// This file is tailored to MySQL

function main_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

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
        execute_sql(" ALTER TABLE `user` CHANGE `personality` `secret` VARCHAR( 15 ) NOT NULL DEFAULT ''  ");
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
        execute_sql(" INSERT INTO log_display (module, action, mtable, field) VALUES ('resource', 'view', 'resource', 'name') ");
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
                        notify("Could not add a new course module to the course '" . format_string($course->fullname) . "'");
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
                        notify("Could not add a new course module to the course '" . format_string($course->fullname) . "'");
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
                    notify("Could not cache module information for course '" . format_string($course->fullname) . "'!");
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
                    notify("Could not cache module information for course '" . format_string($course->fullname) . "'!");
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
            execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('user', 'view', 'user', 'CONCAT(firstname,' ',lastname)') ");
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
                echo "$CFG->dataroot/$file<br />";
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
                    notify("Could not cache module information for course '" . format_string($course->fullname) . "'!");
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
        echo "Checking userdatabase:<br />";
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
            print_simple_box("Note: the pgassignment module has been removed (it will be replaced later by the workshop module).  Go to the new 'Manage Modules' page and DELETE IT from your system", "center", "50%", "$THEME->cellheading", "20", "noticebox");
        }
    }

    if ($oldversion < 2003051600) {
        print_simple_box("Thanks for upgrading!<p>There are many changes since the last release.  Please read the release notes carefully.  If you are using CUSTOM themes you will need to edit them.  You will also need to check your site's config.php file.", "center", "50%", "$THEME->cellheading", "20", "noticebox");
    }

    if ($oldversion < 2003052300) {
        table_column("user", "", "autosubscribe", "integer", "1", "unsigned", "1", "", "htmleditor");
    }

    if ($oldversion < 2003072100) {
        table_column("course", "", "visible", "integer", "1", "unsigned", "1", "", "marker");
    }

    if ($oldversion < 2003072101) {
        table_column("course_sections", "sequence", "sequence", "text", "", "", "", "", "");
    }

    if ($oldversion < 2003072800) {
        print_simple_box("The following database index improves performance, but can be quite large - if you are upgrading and you have problems with a limited quota you may want to delete this index later from the '{$CFG->prefix}log' table in your database", "center", "50%", "$THEME->cellheading", "20", "noticebox");
        flush();
        execute_sql(" ALTER TABLE `{$CFG->prefix}log` ADD INDEX timecoursemoduleaction (time,course,module,action) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}user_students` ADD INDEX courseuserid (course,userid) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}user_teachers` ADD INDEX courseuserid (course,userid) ");
    }

    if ($oldversion < 2003072803) {
        table_column("course_categories", "", "description", "text", "", "", "");
        table_column("course_categories", "", "parent", "integer", "10", "unsigned");
        table_column("course_categories", "", "sortorder", "integer", "10", "unsigned");
        table_column("course_categories", "", "courseorder", "text", "", "", "");
        table_column("course_categories", "", "visible", "integer", "1", "unsigned", "1");
        table_column("course_categories", "", "timemodified", "integer", "10", "unsigned");
    }

    if ($oldversion < 2003080400) {
        table_column("course_categories", "courseorder", "courseorder", "integer", "10", "unsigned");
        table_column("course", "", "sortorder", "integer", "10", "unsigned", "0", "", "category");
    }

    if ($oldversion < 2003080700) {
        notify("Cleaning up categories and course ordering...");
        fix_course_sortorder();
    }

    if ($oldversion < 2003081001) {
        table_column("course", "format", "format", "varchar", "10", "", "topics");
    }

    if ($oldversion < 2003081500) {
//        print_simple_box("Some important changes have been made to how course creators work.  Formerly, they could create new courses and assign teachers, and teachers could edit courses.  Now, ordinary teachers can no longer edit courses - they <b>need to be a teacher of a course AND a course creator</b>.  A new site-wide configuration variable allows you to choose whether to allow course creators to create new courses as well (by default this is off).  <p>The following update will automatically convert all your existing teachers into course creators, to maintain backward compatibility.  Make sure you look at your upgraded site carefully and understand these new changes.", "center", "50%", "$THEME->cellheading", "20", "noticebox");

//        $count = 0;
//        $errorcount = 0;
//        if ($teachers = get_records("user_teachers")) {
//            foreach ($teachers as $teacher) {
//                if (! record_exists("user_coursecreators", "userid", $teacher->userid)) {
//                    $creator = NULL;
//                    $creator->userid = $teacher->userid;
//                    if (!insert_record("user_coursecreators", $creator)) {
//                        $errorcount++;
//                    } else {
//                        $count++;
//                    }
//                }
//            }
//        }
//        print_simple_box("$count teachers were upgraded to course creators (with $errorcount errors)", "center", "50%", "$THEME->cellheading", "20", "noticebox");

    }

    if ($oldversion < 2003081501) {
        execute_sql(" CREATE TABLE `{$CFG->prefix}scale` (
                         `id` int(10) unsigned NOT NULL auto_increment,
                         `courseid` int(10) unsigned NOT NULL default '0',
                         `userid` int(10) unsigned NOT NULL default '0',
                         `name` varchar(255) NOT NULL default '',
                         `scale` text NOT NULL,
                         `description` text NOT NULL,
                         `timemodified` int(10) unsigned NOT NULL default '0',
                         PRIMARY KEY  (id)
                       ) TYPE=MyISAM COMMENT='Defines grading scales'");

    }

    if ($oldversion < 2003081503) {
        table_column("forum", "", "scale", "integer", "10", "unsigned", "0", "", "assessed");
        get_scales_menu(0);    // Just to force the default scale to be created
    }

    if ($oldversion < 2003081600) {
        table_column("user_teachers", "", "editall", "integer", "1", "unsigned", "1", "", "role");
        table_column("user_teachers", "", "timemodified", "integer", "10", "unsigned", "0", "", "editall");
    }

    if ($oldversion < 2003081900) {
        table_column("course_categories", "courseorder", "coursecount", "integer", "10", "unsigned", "0");
    }

    if ($oldversion < 2003082001) {
        table_column("course", "", "showgrades", "integer", "2", "unsigned", "1", "", "format");
    }

    if ($oldversion < 2003082101) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}course` ADD INDEX category (category) ");
    }
    if ($oldversion < 2003082702) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'user report', 'user', 'CONCAT(firstname,\" \",lastname)') ");
    }

    if ($oldversion < 2003091400) {
        table_column("course_modules", "", "indent", "integer", "5", "unsigned", "0", "", "score");
    }

    if ($oldversion < 2003092900) {
        table_column("course", "", "maxbytes", "integer", "10", "unsigned", "0", "", "marker");
    }

    if ($oldversion < 2003102700) {
        table_column("user_students", "", "timeaccess", "integer", "10", "unsigned", "0", "", "time");
        table_column("user_teachers", "", "timeaccess", "integer", "10", "unsigned", "0", "", "timemodified");

        $db->debug = false;
        $CFG->debug = 0;
        notify("Calculating access times.  Please wait - this may take a long time on big sites...", "green");
        flush();

        if ($courses = get_records_select("course", "category > 0")) {
            foreach ($courses as $course) {
                notify("Processing " . format_string($course->fullname) . " ...", "green");
                flush();
                if ($users = get_records_select("user_teachers", "course = '$course->id'",
                                                "id", "id, userid, timeaccess")) {
                    foreach ($users as $user) {
                        $loginfo = get_record_sql("SELECT id, time FROM {$CFG->prefix}log                                                                                  WHERE course = '$course->id' and userid = '$user->userid'                                                               ORDER by time DESC");
                        if (empty($loginfo->time)) {
                            $loginfo->time = 0;
                        }
                        execute_sql("UPDATE {$CFG->prefix}user_teachers                                                                                      SET timeaccess = '$loginfo->time'
                                     WHERE userid = '$user->userid' AND course = '$course->id'", false);

                    }
                }

                if ($users = get_records_select("user_students", "course = '$course->id'",
                                                "id", "id, userid, timeaccess")) {
                    foreach ($users as $user) {
                        $loginfo = get_record_sql("SELECT id, time FROM {$CFG->prefix}log
                                                   WHERE course = '$course->id' and userid = '$user->userid'
                                                   ORDER by time DESC");
                        if (empty($loginfo->time)) {
                            $loginfo->time = 0;
                        }
                        execute_sql("UPDATE {$CFG->prefix}user_students
                                     SET timeaccess = '$loginfo->time'
                                     WHERE userid = '$user->userid' AND course = '$course->id'", false);

                    }
                }
            }
        }
        notify("All courses complete.", "green");
        $db->debug = true;
    }

    if ($oldversion < 2003103100) {
        table_column("course", "", "showreports", "integer", "4", "unsigned", "0", "", "maxbytes");
    }

    if ($oldversion < 2003121600) {
        modify_database("", "CREATE TABLE `prefix_groups` (
                                `id` int(10) unsigned NOT NULL auto_increment,
                                `courseid` int(10) unsigned NOT NULL default '0',
                                `name` varchar(254) NOT NULL default '',
                                `description` text NOT NULL,
                                `lang` varchar(10) NOT NULL default 'en',
                                `picture` int(10) unsigned NOT NULL default '0',
                                `timecreated` int(10) unsigned NOT NULL default '0',
                                `timemodified` int(10) unsigned NOT NULL default '0',
                                PRIMARY KEY  (`id`),
                                KEY `courseid` (`courseid`)
                              ) TYPE=MyISAM COMMENT='Each record is a group in a course.'; ");

        modify_database("", "CREATE TABLE `prefix_groups_members` (
                                `id` int(10) unsigned NOT NULL auto_increment,
                                `groupid` int(10) unsigned NOT NULL default '0',
                                `userid` int(10) unsigned NOT NULL default '0',
                                `timeadded` int(10) unsigned NOT NULL default '0',
                                PRIMARY KEY  (`id`),
                                KEY `groupid` (`groupid`)
                              ) TYPE=MyISAM COMMENT='Lists memberships of users in groups'; ");
    }

    if ($oldversion < 2003121800) {
        table_column("course", "modinfo", "modinfo", "longtext", "", "", "");
    }

    if ($oldversion < 2003122600) {
        table_column("course", "", "groupmode", "integer", "4", "unsigned", "0", "", "showreports");
        table_column("course", "", "groupmodeforce", "integer", "4", "unsigned", "0", "", "groupmode");
    }

    if ($oldversion < 2004010900) {
        table_column("course_modules", "", "groupmode", "integer", "4", "unsigned", "0", "", "visible");
    }

    if ($oldversion < 2004011700) {
        modify_database("", "CREATE TABLE `prefix_event` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `name` varchar(255) NOT NULL default '',
                              `description` text NOT NULL,
                              `courseid` int(10) unsigned NOT NULL default '0',
                              `groupid` int(10) unsigned NOT NULL default '0',
                              `userid` int(10) unsigned NOT NULL default '0',
                              `modulename` varchar(20) NOT NULL default '',
                              `instance` int(10) unsigned NOT NULL default '0',
                              `eventtype` varchar(20) NOT NULL default '',
                              `timestart` int(10) unsigned NOT NULL default '0',
                              `timeduration` int(10) unsigned NOT NULL default '0',
                              `timemodified` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY  (`id`),
                              UNIQUE KEY `id` (`id`),
                              KEY `courseid` (`courseid`),
                              KEY `userid` (`userid`)
                            ) TYPE=MyISAM COMMENT='For everything with a time associated to it'; ");
    }

    if ($oldversion < 2004012800) {
        modify_database("", "CREATE TABLE `prefix_user_preferences` (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `userid` int(10) unsigned NOT NULL default '0',
                              `name` varchar(50) NOT NULL default '',
                              `value` varchar(255) NOT NULL default '',
                              PRIMARY KEY  (`id`),
                              UNIQUE KEY `id` (`id`),
                              KEY `useridname` (userid,name)
                            ) TYPE=MyISAM COMMENT='Allows modules to store arbitrary user preferences'; ");
    }

    if ($oldversion < 2004012900) {
        table_column("config", "value", "value", "text", "", "", "");
    }

    if ($oldversion < 2004013101) {
        table_column("log", "", "cmid", "integer", "10", "unsigned", "0", "", "module");
        set_config("upgrade", "logs");
    }

    if ($oldversion < 2004020900) {
        table_column("course", "", "lang", "varchar", "5", "", "", "", "groupmodeforce");
    }

    if ($oldversion < 2004020903) {
        modify_database("", "CREATE TABLE `prefix_cache_text` (
                                `id` int(10) unsigned NOT NULL auto_increment,
                                `md5key` varchar(32) NOT NULL default '',
                                `formattedtext` longtext NOT NULL,
                                `timemodified` int(10) unsigned NOT NULL default '0',
                                PRIMARY KEY  (`id`),
                                KEY `md5key` (`md5key`)
                             ) TYPE=MyISAM COMMENT='For storing temporary copies of processed texts';");
    }

    if ($oldversion < 2004021000) {
        $textfilters = array();
        for ($i=1; $i<=10; $i++) {
            $variable = "textfilter$i";
            if (!empty($CFG->$variable)) {   /// No more filters
                if (is_readable("$CFG->dirroot/".$CFG->$variable)) {
                    $textfilters[] = $CFG->$variable;
                }
            }
        }
        $textfilters = implode(',', $textfilters);
        if (empty($textfilters)) {
            $textfilters = 'mod/glossary/dynalink.php';
        }
        set_config('textfilters', $textfilters);
    }

    if ($oldversion < 2004021201) {
        modify_database("", "CREATE TABLE `prefix_cache_filters` (
                                `id` int(10) unsigned NOT NULL auto_increment,
                                `filter` varchar(32) NOT NULL default '',
                                `version` int(10) unsigned NOT NULL default '0',
                                `md5key` varchar(32) NOT NULL default '',
                                `rawtext` text NOT NULL,
                                `timemodified` int(10) unsigned NOT NULL default '0',
                                PRIMARY KEY  (`id`),
                                KEY `filtermd5key` (filter,md5key)
                              ) TYPE=MyISAM COMMENT='For keeping information about cached data';");
    }

    if ($oldversion < 2004021500) {
        table_column("groups", "", "hidepicture", "integer", "2", "unsigned", "0", "", "picture");
    }

    if ($oldversion < 2004021700) {
        if (!empty($CFG->textfilters)) {
            $CFG->textfilters = str_replace("tex_filter.php", "filter.php", $CFG->textfilters);
            $CFG->textfilters = str_replace("multilang.php", "filter.php", $CFG->textfilters);
            $CFG->textfilters = str_replace("censor.php", "filter.php", $CFG->textfilters);
            $CFG->textfilters = str_replace("mediaplugin.php", "filter.php", $CFG->textfilters);
            $CFG->textfilters = str_replace("algebra_filter.php", "filter.php", $CFG->textfilters);
            $CFG->textfilters = str_replace("dynalink.php", "filter.php", $CFG->textfilters);
            set_config("textfilters", $CFG->textfilters);
        }
    }

    if ($oldversion < 2004022000) {
        table_column("user", "", "emailstop", "integer", "1", "unsigned", "0", "not null", "email");
    }

    if ($oldversion < 2004022200) {     /// Final renaming I hope.  :-)
        if (!empty($CFG->textfilters)) {
            $CFG->textfilters = str_replace("/filter.php", "", $CFG->textfilters);
            $CFG->textfilters = str_replace("mod/glossary/dynalink.php", "mod/glossary", $CFG->textfilters);
            $textfilters = explode(',', $CFG->textfilters);
            foreach ($textfilters as $key => $textfilter) {
                $textfilters[$key] = trim($textfilter);
            }
            set_config("textfilters", implode(',',$textfilters));
        }
    }

    if ($oldversion < 2004030702) {     /// Because of the renaming of Czech language pack
        execute_sql("UPDATE {$CFG->prefix}user SET lang = 'cs' WHERE lang = 'cz'");
        execute_sql("UPDATE {$CFG->prefix}course SET lang = 'cs' WHERE lang = 'cz'");
    }

    if ($oldversion < 2004041800) {     /// Integrate Block System from contrib
        table_column("course", "", "blockinfo", "varchar", "255", "", "", "not null", "modinfo");
    }

    if ($oldversion < 2004042600) {     /// Rebuild course caches for resource icons
        //include_once("$CFG->dirroot/course/lib.php");
        //rebuild_course_cache();
    }

    if ($oldversion < 2004042700) {     /// Increase size of lang fields
        table_column("user",   "lang", "lang", "varchar", "10", "", "en");
        table_column("groups", "lang", "lang", "varchar", "10", "", "");
        table_column("course", "lang", "lang", "varchar", "10", "", "");
    }

    if ($oldversion < 2004042701) {     /// Add hiddentopics field to control hidden topics behaviour
        table_column("course", "", "hiddentopics", "integer", "1", "unsigned", "0", "not null", "visible");
    }

    if ($oldversion < 2004042702) {     /// add a format field for the description
        table_column("event", "", "format", "integer", "4", "unsigned", "0", "not null", "description");
    }

    if ($oldversion < 2004042900) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}course` DROP `showrecent` ");
    }

    if ($oldversion < 2004043001) {     /// Change hiddentopics to hiddensections
        table_column("course", "hiddentopics", "hiddensections", "integer", "2", "unsigned", "0", "not null");
    }

    if ($oldversion < 2004050400) {     /// add a visible field for events
        table_column("event", "", "visible", "tinyint", "1", "", "1", "not null", "timeduration");
        if ($events = get_records('event')) {
            foreach($events as $event) {
                if ($moduleid = get_field('modules', 'id', 'name', $event->modulename)) {
                    if (get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $event->instance) == 0) {
                        set_field('event', 'visible', 0, 'id', $event->id);
                    }
                }
            }
        }
    }

    if ($oldversion < 2004052800) {     /// First version tagged "1.4 development", version.php 1.227
        set_config('siteblocksadded', true);   /// This will be used later by the block upgrade
    }

    if ($oldversion < 2004053000) {     /// set defaults for site course
        $site = get_site();
        set_field('course', 'numsections', 0, 'id', $site->id);
        set_field('course', 'groupmodeforce', 1, 'id', $site->id);
        set_field('course', 'teacher', get_string('administrator'), 'id', $site->id);
        set_field('course', 'teachers', get_string('administrators'), 'id', $site->id);
        set_field('course', 'student', get_string('user'), 'id', $site->id);
        set_field('course', 'students', get_string('users'), 'id', $site->id);
    }

    if ($oldversion < 2004060100) {
        set_config('digestmailtime', 0);
        table_column('user', "", 'maildigest', 'tinyint', '1', '', '0', 'not null', 'mailformat');
    }

    if ($oldversion < 2004062400) {
        table_column('user_teachers', "", 'timeend', 'int', '10', 'unsigned', '0', 'not null', 'editall');
        table_column('user_teachers', "", 'timestart', 'int', '10', 'unsigned', '0', 'not null', 'editall');
    }

    if ($oldversion < 2004062401) {
        table_column('course', '', 'idnumber', 'varchar', '100', '', '', 'not null', 'shortname');
        execute_sql('UPDATE '.$CFG->prefix.'course SET idnumber = shortname');   // By default
    }

    if ($oldversion < 2004062600) {
        table_column('course', '', 'cost', 'varchar', '10', '', '', 'not null', 'lang');
    }

    if ($oldversion < 2004072900) {
        table_column('course', '', 'enrolperiod', 'int', '10', 'unsigned', '0', 'not null', 'startdate');
    }

    if ($oldversion < 2004072901) {  // Fixing error in schema
        if ($record = get_record('log_display', 'module', 'course', 'action', 'update')) {
            delete_records('log_display', 'module', 'course', 'action', 'update');
            insert_record('log_display', $record, false);
        }
    }

    if ($oldversion < 2004081200) {  // Fixing version errors in some blocks
        set_field('blocks', 'version', 2004081200, 'name', 'admin');
        set_field('blocks', 'version', 2004081200, 'name', 'calendar_month');
        set_field('blocks', 'version', 2004081200, 'name', 'course_list');
    }

    if ($oldversion < 2004081500) {  // Adding new "auth" field to user table to allow more flexibility
        table_column('user', '', 'auth', 'varchar', '20', '', 'manual', 'not null', 'id');

        execute_sql("UPDATE {$CFG->prefix}user SET auth = 'manual'");  // Set everyone to 'manual' to be sure

        if ($admins = get_admins()) {   // Set all the NON-admins to whatever the current auth module is
            $adminlist = array();
            foreach ($admins as $user) {
                $adminlist[] = $user->id; 
            }
            $adminlist = implode(',', $adminlist);
            execute_sql("UPDATE {$CFG->prefix}user SET auth = '$CFG->auth' WHERE id NOT IN ($adminlist)");
        }
    }
    
    if ($oldversion < 2004082200) { // Making admins teachers on site course
        $site = get_site();
        $admins = get_admins();
        foreach ($admins as $admin) {
            add_teacher($admin->id, $site->id);
        }
    }

    if ($oldversion < 2004082600) {
        //update auth-fields for external users
        // following code would not work in 1.8
/*        include_once ($CFG->dirroot."/auth/".$CFG->auth."/lib.php");
        if (function_exists('auth_get_userlist')) {
            $externalusers = auth_get_userlist();
            if (!empty($externalusers)){
                $externalusers = '\''. implode('\',\'',$externalusers).'\'';
                execute_sql("UPDATE {$CFG->prefix}user SET auth = '$CFG->auth' WHERE username  IN ($externalusers)");
            }
        }*/
    }

    if ($oldversion < 2004082900) {  // Make sure guest is "manual" too.
        set_field('user', 'auth', 'manual', 'username', 'guest');
    }
    
    /* Commented out unused guid-field code
    if ($oldversion < 2004090300) { // Add guid-field used in user syncronization
        table_column('user', '', 'guid', 'varchar', '128', '', '', '', 'auth');
        execute_sql("ALTER TABLE {$CFG->prefix}user ADD INDEX authguid (auth, guid)");
    }
    */

    if ($oldversion < 2004091900) { // modify idnumber to hold longer values
        table_column('user', 'idnumber', 'idnumber', 'varchar', '64', '', '', '', '');
        execute_sql("ALTER TABLE {$CFG->prefix}user DROP INDEX user_idnumber",false); // added in case of conflicts with upgrade from 14stable
        execute_sql("ALTER TABLE {$CFG->prefix}user DROP INDEX user_auth",false); // added in case of conflicts with upgrade from 14stable

        execute_sql("ALTER TABLE {$CFG->prefix}user ADD INDEX idnumber (idnumber)");
        execute_sql("ALTER TABLE {$CFG->prefix}user ADD INDEX auth (auth)");
    }

    if ($oldversion < 2004093001) { // add new table for sessions storage
        execute_sql(" CREATE TABLE `{$CFG->prefix}sessions` (
                          `sesskey` char(32) NOT null,
                          `expiry` int(11) unsigned NOT null,
                          `expireref` varchar(64),
                          `data` text NOT null,
                          PRIMARY KEY (`sesskey`), 
                          KEY (`expiry`) 
                      ) TYPE=MyISAM COMMENT='Optional database session storage, not used by default';");
    }

    if ($oldversion < 2004111500) {  // Update any users/courses using wrongly-named lang pack
        execute_sql("UPDATE {$CFG->prefix}user SET lang = 'mi_nt' WHERE lang = 'ma_nt'");
        execute_sql("UPDATE {$CFG->prefix}course SET lang = 'mi_nt' WHERE lang = 'ma_nt'");
    }

    if ($oldversion < 2004111700) { // add indexes. - drop them first silently to avoid conflicts when upgrading.
        execute_sql(" ALTER TABLE `{$CFG->prefix}course` DROP INDEX idnumber;",false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}course` DROP INDEX shortname;",false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}user_students` DROP INDEX userid;",false);
        execute_sql(" ALTER TABLE `{$CFG->prefix}user_teachers` DROP INDEX userid;",false);

        execute_sql(" ALTER TABLE `{$CFG->prefix}course` ADD INDEX idnumber (idnumber);");
        execute_sql(" ALTER TABLE `{$CFG->prefix}course` ADD INDEX shortname (shortname);");
        execute_sql(" ALTER TABLE `{$CFG->prefix}user_students` ADD INDEX userid (userid);");
        execute_sql(" ALTER TABLE `{$CFG->prefix}user_teachers` ADD INDEX userid (userid);");
    }

    if ($oldversion < 2004111700) {// add an index to event for timestart and timeduration. - drop them first silently to avoid conflicts when upgrading.
        execute_sql("ALTER TABLE {$CFG->prefix}event DROP INDEX timestart;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}event DROP INDEX timeduration;",false); 

        modify_database('','ALTER TABLE prefix_event ADD INDEX timestart (timestart);');
        modify_database('','ALTER TABLE prefix_event ADD INDEX timeduration (timeduration);');
    }

    if ($oldversion < 2004111700) { //add indexes on modules and course_modules. - drop them first silently to avoid conflicts when upgrading.
        execute_sql("ALTER TABLE {$CFG->prefix}course_modules drop key visible;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}course_modules drop key course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}course_modules drop key module;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}course_modules drop key instance;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}course_modules drop key deleted;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}modules drop key name;",false);

        modify_database('','ALTER TABLE prefix_course_modules add key visible(visible);');
        modify_database('','ALTER TABLE prefix_course_modules add key course(course);');
        modify_database('','ALTER TABLE prefix_course_modules add key module(module);');
        modify_database('','ALTER TABLE prefix_course_modules add key instance (instance);');
        modify_database('','ALTER TABLE prefix_course_modules add key deleted (deleted);');
        modify_database('','ALTER TABLE prefix_modules add key name(name);');
    }

    if ($oldversion < 2004111700) { // add an index on the groups_members table. - drop them first silently to avoid conflicts when upgrading.
        execute_sql("ALTER TABLE {$CFG->prefix}groups_members DROP INDEX userid;",false);

        modify_database('','ALTER TABLE prefix_groups_members ADD INDEX userid (userid);');
    }

    if ($oldversion < 2004111700) { // add an index on user students timeaccess (used for sorting)- drop them first silently to avoid conflicts when upgrading
        execute_sql("ALTER TABLE {$CFG->prefix}user_students DROP INDEX timeaccess;",false);

        modify_database('','ALTER TABLE prefix_user_students ADD INDEX timeaccess (timeaccess);');
    }

    if ($oldversion < 2004111700) {  // add indexes on faux-foreign keys. - drop them first silently to avoid conflicts when upgrading.
        execute_sql("ALTER TABLE {$CFG->prefix}scale DROP INDEX courseid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}user_admins DROP INDEX userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}user_coursecreators DROP INDEX userid;",false);

        modify_database('','ALTER TABLE prefix_scale ADD INDEX courseid (courseid);');
        modify_database('','ALTER TABLE prefix_user_admins ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_user_coursecreators ADD INDEX userid (userid);');
    }

    if ($oldversion < 2004111700) { // replace index on course
        fix_course_sortorder(0,0,1);
        execute_sql("ALTER TABLE `{$CFG->prefix}course` DROP KEY category",false);

        execute_sql("ALTER TABLE `{$CFG->prefix}course` DROP KEY category_sortorder;",false);
        modify_database('', "ALTER TABLE `prefix_course` ADD UNIQUE KEY category_sortorder(category,sortorder)"); 

        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_deleted_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_confirmed_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_firstname_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_lastname_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_city_idx;",false); 
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_country_idx;",false); 
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_lastaccess_idx;",false);

        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_deleted_idx  (deleted)");
        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_confirmed_idx (confirmed)");
        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_firstname_idx (firstname)");
        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_lastname_idx (lastname)");
        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_city_idx (city)");
        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_country_idx (country)");
        modify_database("", "ALTER TABLE `prefix_user` ADD INDEX prefix_user_lastaccess_idx (lastaccess)");
     }
 
    if ($oldversion < 2004111700) { // one more index for email (for sorting)
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_email_idx;",false);
        modify_database('','ALTER TABLE `prefix_user` ADD INDEX prefix_user_email_idx (email);');
    }

    if ($oldversion < 2004112200) { // new 'enrol' field for enrolment tables
        table_column('user_students', '', 'enrol', 'varchar', '20', '', '', 'not null');
        table_column('user_teachers', '', 'enrol', 'varchar', '20', '', '', 'not null');
        execute_sql("ALTER TABLE `{$CFG->prefix}user_students` ADD INDEX enrol (enrol);");
        execute_sql("ALTER TABLE `{$CFG->prefix}user_teachers` ADD INDEX enrol (enrol);");
    }
    
    if ($oldversion < 2004112400) {
        /// Delete duplicate enrolments 
        /// and then tell the database course,userid is a unique combination
        if ($users = get_records_select("user_students", "userid > 0 GROUP BY course, userid ".
                                        "HAVING count(*) > 1", "", "max(id) as id, userid, course ,count(*)")) {
            foreach ($users as $user) {
                delete_records_select("user_students", "userid = '$user->userid' ".
                                     "AND course = '$user->course' AND id <> '$user->id'");
            }
        }
        flush();
        
        modify_database('','ALTER TABLE prefix_user_students DROP INDEX courseuserid;');
        modify_database('','ALTER TABLE prefix_user_students ADD UNIQUE INDEX courseuserid(course,userid);');        

        /// Delete duplicate teacher enrolments 
        /// and then tell the database course,userid is a unique combination
        if ($users = get_records_select("user_teachers", "userid > 0 GROUP BY course, userid ".
                                        "HAVING count(*) > 1", "", "max(id) as id, userid, course ,count(*)")) {
            foreach ($users as $user) {
                delete_records_select("user_teachers", "userid = '$user->userid' ".
                                     "AND course = '$user->course' AND id <> '$user->id'");
            }
        }
        flush();
        modify_database('','ALTER TABLE prefix_user_teachers DROP INDEX courseuserid;');
        modify_database('','ALTER TABLE prefix_user_teachers ADD UNIQUE INDEX courseuserid(course,userid);');        
    } 

    if ($oldversion < 2004112900) {
        table_column('user', '', 'policyagreed', 'integer', '1', 'unsigned', '0', 'not null', 'confirmed');
    }

    if ($oldversion < 2004121400) {
        table_column('groups', '', 'password', 'varchar', '50', '', '', 'not null', 'description');
    }

    if ($oldversion < 2004121500) {
        modify_database('',"CREATE TABLE prefix_dst_preset (
            id int(10) NOT NULL auto_increment,
            name char(48) default '' NOT NULL,
            
            apply_offset tinyint(3) default '0' NOT NULL,
            
            activate_index tinyint(1) default '1' NOT NULL,
            activate_day tinyint(1) default '1' NOT NULL,
            activate_month tinyint(2) default '1' NOT NULL,
            activate_time char(5) default '03:00' NOT NULL,
            
            deactivate_index tinyint(1) default '1' NOT NULL,
            deactivate_day tinyint(1) default '1' NOT NULL,
            deactivate_month tinyint(2) default '2' NOT NULL,
            deactivate_time char(5) default '03:00' NOT NULL,
            
            last_change int(10) default '0' NOT NULL,
            next_change int(10) default '0' NOT NULL,
            current_offset tinyint(3) default '0' NOT NULL,
            
            PRIMARY KEY (id))");
    }       

    if ($oldversion < 2004122800) {
        execute_sql("DROP TABLE {$CFG->prefix}message", false);
        execute_sql("DROP TABLE {$CFG->prefix}message_read", false);
        execute_sql("DROP TABLE {$CFG->prefix}message_contacts", false);

        modify_database('',"CREATE TABLE `prefix_message` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `useridfrom` int(10) NOT NULL default '0',
                               `useridto` int(10) NOT NULL default '0',
                               `message` text NOT NULL,
                               `timecreated` int(10) NOT NULL default '0',
                               `messagetype` varchar(50) NOT NULL default '',
                               PRIMARY KEY  (`id`),
                               KEY `useridfrom` (`useridfrom`),
                               KEY `useridto` (`useridto`)
                             ) TYPE=MyISAM COMMENT='Stores all unread messages';");

        modify_database('',"CREATE TABLE `prefix_message_read` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `useridfrom` int(10) NOT NULL default '0',
                               `useridto` int(10) NOT NULL default '0',
                               `message` text NOT NULL,
                               `timecreated` int(10) NOT NULL default '0',
                               `timeread` int(10) NOT NULL default '0',
                               `messagetype` varchar(50) NOT NULL default '',
                               `mailed` tinyint(1) NOT NULL default '0',
                               PRIMARY KEY  (`id`),
                               KEY `useridfrom` (`useridfrom`),
                               KEY `useridto` (`useridto`)
                             ) TYPE=MyISAM COMMENT='Stores all messages that have been read';");

        modify_database('',"CREATE TABLE `prefix_message_contacts` (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `userid` int(10) unsigned NOT NULL default '0',
                               `contactid` int(10) unsigned NOT NULL default '0',
                               `blocked` tinyint(1) unsigned NOT NULL default '0',
                               PRIMARY KEY  (`id`),
                               UNIQUE KEY `usercontact` (`userid`,`contactid`)
                             ) TYPE=MyISAM COMMENT='Maintains lists of relationships between users';");

        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'write', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'read', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
    }

    if ($oldversion < 2004122801) {
        table_column('message', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'message');
        table_column('message_read', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'message');
    }

    if ($oldversion < 2005010100) {
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'add contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'remove contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'block contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('message', 'unblock contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
    }

    if ($oldversion < 2005011000) {     // Create a .htaccess file in dataroot, just in case
        if (!file_exists($CFG->dataroot.'/.htaccess')) {
            if ($handle = fopen($CFG->dataroot.'/.htaccess', 'w')) {   // For safety
                @fwrite($handle, "deny from all\r\nAllowOverride None\r\n");
                @fclose($handle); 
                notify("Created a default .htaccess file in $CFG->dataroot");
            }
        }
    }
    

    if ($oldversion < 2005012500) { 
        /*
        // add new table for meta courses.
        modify_database("","CREATE TABLE `prefix_meta_course` (
            `id` int(1) unsigned NOT NULL auto_increment,
            `parent_course` int(10) NOT NULL default 0,
            `child_course` int(10) NOT NULL default 0,
            PRIMARY KEY (`id`),
            KEY `parent_course` (parent_course),
            KEY `child_course` (child_course)
        );");
        // add flag to course field
        table_column('course','','meta_course','integer','1','','0','not null');
        */ // taking this OUT for upgrade from 1.4 to 1.5 (those tracking head will have already seen it)
    }

    if ($oldversion < 2005012501) { 
        execute_sql("DROP TABLE {$CFG->prefix}meta_course",false); // drop silently
        execute_sql("ALTER TABLE {$CFG->prefix}course DROP COLUMN meta_course",false); // drop silently
        
        // add new table for meta courses.
        modify_database("","CREATE TABLE `prefix_course_meta` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `parent_course` int(10) NOT NULL default 0,
            `child_course` int(10) NOT NULL default 0,
            PRIMARY KEY (`id`),
            KEY `parent_course` (parent_course),
            KEY `child_course` (child_course)
        );");
        // add flag to course field
        table_column('course','','metacourse','integer','1','','0','not null');
    }

    if ($oldversion < 2005012800) {
        // fix a typo (int 1 becomes int 10) 
        table_column('course_meta','id','id','integer','10','','0','not null');
    }

    if ($oldversion < 2005020100) {
        fix_course_sortorder(0, 1, 1);
    }   


    if ($oldversion < 2005020101) {
        // hopefully this is the LAST TIME we need to do this ;)
        if ($rows = count_records("course_meta")) {
            // we need to upgrade
            modify_database("","CREATE TABLE `prefix_course_meta_tmp` (
            `parent_course` int(10) NOT NULL default 0,
            `child_course` int(10) NOT NULL default 0);");
            
            execute_sql("INSERT INTO {$CFG->prefix}course_meta_tmp (parent_course,child_course) 
               SELECT {$CFG->prefix}course_meta.parent_course, {$CFG->prefix}course_meta.child_course
               FROM {$CFG->prefix}course_meta");
            $insertafter = true;
        }

        execute_sql("DROP TABLE {$CFG->prefix}course_meta");

        modify_database("","CREATE TABLE `prefix_course_meta` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `parent_course` int(10) unsigned NOT NULL default 0,
            `child_course` int(10) unsigned NOT NULL default 0,
            PRIMARY KEY (`id`),
            KEY `parent_course` (parent_course),
            KEY `child_course` (child_course));");

        if (!empty($insertafter)) {
            execute_sql("INSERT INTO {$CFG->prefix}course_meta (parent_course,child_course) 
               SELECT {$CFG->prefix}course_meta_tmp.parent_course, {$CFG->prefix}course_meta_tmp.child_course
               FROM {$CFG->prefix}course_meta_tmp");

            execute_sql("DROP TABLE {$CFG->prefix}course_meta_tmp");
        }
    }

    if ($oldversion < 2005020800) {     // Expand module column to max 20 chars
        table_column('log','module','module','varchar','20','','','not null');
    }

    if ($oldversion < 2005021000) {     // New fields for theme choices
        table_column('course', '', 'theme', 'varchar', '50', '', '', '', 'lang');
        table_column('groups', '', 'theme', 'varchar', '50', '', '', '', 'lang');
        table_column('user',   '', 'theme', 'varchar', '50', '', '', '', 'lang');

        set_config('theme', 'standardwhite');         // Reset to a known good theme 
    }
    
    if ($oldversion < 2005021600) {     // course.idnumber should be varchar(100)
        table_column('course', 'idnumber', 'idnumber', 'varchar', '100', '', '', '', '');
    }

    if ($oldversion < 2005021700) {
        table_column('user', '', 'dstpreset', 'int', '10', '', '0', 'not null', 'timezone');
    }

    if ($oldversion < 2005021800) {     // For database debugging, not for normal use
        modify_database(""," CREATE TABLE `adodb_logsql` (
                               `created` datetime NOT NULL,
                               `sql0` varchar(250) NOT NULL,
                               `sql1` text NOT NULL,
                               `params` text NOT NULL,
                               `tracer` text NOT NULL,
                               `timer` decimal(16,6) NOT NULL
                              );");
    }

    if ($oldversion < 2005022400) {
        // Add more visible digits to the fields
        table_column('dst_preset', 'activate_index', 'activate_index', 'tinyint', '2', '', '0', 'not null');
        table_column('dst_preset', 'activate_day', 'activate_day', 'tinyint', '2', '', '0', 'not null');
        // Add family and year fields
        table_column('dst_preset', '', 'family', 'varchar', '100', '', '', 'not null', 'name');
        table_column('dst_preset', '', 'year', 'int', '10', '', '0', 'not null', 'family');
    }

    if ($oldversion < 2005030501) {
        table_column('user', '', 'msn', 'varchar', '50', '', '', '', 'icq');
        table_column('user', '', 'aim', 'varchar', '50', '', '', '', 'icq');
        table_column('user', '', 'yahoo', 'varchar', '50', '', '', '', 'icq');
        table_column('user', '', 'skype', 'varchar', '50', '', '', '', 'icq');
    }

    if ($oldversion < 2005032300) {
        table_column('user', 'dstpreset', 'timezonename', 'varchar', '100');
        execute_sql('UPDATE `'.$CFG->prefix.'user` SET timezonename = \'\'');
    }

    if ($oldversion < 2005032600) {
        execute_sql('DROP TABLE '.$CFG->prefix.'dst_preset', false);
        modify_database('',"CREATE TABLE `prefix_timezone` (
                              `id` int(10) NOT NULL auto_increment,
                              `name` varchar(100) NOT NULL default '',
                              `year` int(11) NOT NULL default '0',
                              `rule` varchar(20) NOT NULL default '',
                              `gmtoff` int(11) NOT NULL default '0',
                              `dstoff` int(11) NOT NULL default '0',
                              `dst_month` tinyint(2) NOT NULL default '0',
                              `dst_startday` tinyint(3) NOT NULL default '0',
                              `dst_weekday` tinyint(3) NOT NULL default '0',
                              `dst_skipweeks` tinyint(3) NOT NULL default '0',
                              `dst_time` varchar(5) NOT NULL default '00:00',
                              `std_month` tinyint(2) NOT NULL default '0',
                              `std_startday` tinyint(3) NOT NULL default '0',
                              `std_weekday` tinyint(3) NOT NULL default '0',
                              `std_skipweeks` tinyint(3) NOT NULL default '0',
                              `std_time` varchar(5) NOT NULL default '00:00',
                              PRIMARY KEY (`id`)
                            ) TYPE=MyISAM COMMENT='Rules for calculating local wall clock time for users';");
    }

    if ($oldversion < 2005032800) {
        execute_sql("CREATE TABLE `{$CFG->prefix}grade_category` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(64) NOT NULL default '',
            `courseid` int(10) unsigned NOT NULL default '0',
            `drop_x_lowest` int(10) unsigned NOT NULL default '0',
            `bonus_points` int(10) unsigned NOT NULL default '0',
            `hidden` int(10) unsigned NOT NULL default '0',
            `weight` decimal(4,2) NOT NULL default '0.00',
            PRIMARY KEY  (`id`),
            KEY `courseid` (`courseid`)
          ) TYPE=MyISAM ;");

        execute_sql("CREATE TABLE `{$CFG->prefix}grade_exceptions` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `courseid` int(10) unsigned NOT NULL default '0',
            `grade_itemid` int(10) unsigned NOT NULL default '0',
            `userid` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`id`),
            KEY `courseid` (`courseid`)
          ) TYPE=MyISAM ;");


        execute_sql("CREATE TABLE `{$CFG->prefix}grade_item` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `courseid` int(10) unsigned NOT NULL default '0',
            `category` int(10) unsigned NOT NULL default '0',
            `modid` int(10) unsigned NOT NULL default '0',
            `cminstance` int(10) unsigned NOT NULL default '0',
            `scale_grade` float(11,10) default '1.0000000000',
            `extra_credit` int(10) unsigned NOT NULL default '0',
            `sort_order` int(10) unsigned NOT NULL default '0',
            PRIMARY KEY  (`id`),
            KEY `courseid` (`courseid`)
          ) TYPE=MyISAM ;");


        execute_sql("CREATE TABLE `{$CFG->prefix}grade_letter` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `courseid` int(10) unsigned NOT NULL default '0',
            `letter` varchar(8) NOT NULL default 'NA',
            `grade_high` decimal(4,2) NOT NULL default '100.00',
            `grade_low` decimal(4,2) NOT NULL default '0.00',
            PRIMARY KEY  (`id`),
            KEY `courseid` (`courseid`)
          ) TYPE=MyISAM ;");
          

        execute_sql("CREATE TABLE `{$CFG->prefix}grade_preferences` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `courseid` int(10) unsigned NOT NULL default '0',
            `preference` int(10) NOT NULL default '0',
            `value` int(10) NOT NULL default '0',
            PRIMARY KEY  (`id`),
            UNIQUE KEY `courseidpreference` (`courseid`,`preference`)
          ) TYPE=MyISAM ;");
          
    }

    if ($oldversion < 2005033100) {   // Get rid of defunct field from course modules table
         delete_records('course_modules', 'deleted', 1);  // Delete old records we don't need any more
         execute_sql('ALTER TABLE `'.$CFG->prefix.'course_modules` DROP INDEX `deleted`');  // Old index
         execute_sql('ALTER TABLE `'.$CFG->prefix.'course_modules` DROP `deleted`');    // Old field
    }

    if ($oldversion < 2005040800) {
        table_column('user', 'timezone', 'timezone', 'varchar', '100', '', '99');
        execute_sql(" ALTER TABLE `{$CFG->prefix}user` DROP `timezonename` ");
    }
    
    if ($oldversion < 2005041101) {
        require_once($CFG->libdir.'/filelib.php');
        if (is_readable($CFG->dirroot.'/lib/timezones.txt')) {  // Distribution file
            if ($timezones = get_records_csv($CFG->dirroot.'/lib/timezones.txt', 'timezone')) {
                $db->debug = false;
                update_timezone_records($timezones);
                notify(count($timezones).' timezones installed');
                $db->debug = true;
            }
        }
    }

    if ($oldversion < 2005041900) {  // Copy all Dialogue entries into Messages, and hide Dialogue module

        if ($entries = get_records_sql('SELECT e.id, e.userid, c.recipientid, e.text, e.timecreated
                                          FROM '.$CFG->prefix.'dialogue_conversations c,
                                               '.$CFG->prefix.'dialogue_entries e
                                         WHERE e.conversationid = c.id')) {
            foreach ($entries as $entry) {
                $message = new object;
                $message->useridfrom    = $entry->userid;
                $message->useridto      = $entry->recipientid;
                $message->message       = addslashes($entry->text);
                $message->format        = FORMAT_HTML;
                $message->timecreated   = $entry->timecreated;
                $message->messagetype   = 'direct';
            
                insert_record('message_read', $message);
            }
        }

        set_field('modules', 'visible', 0, 'name', 'dialogue');

        notify('The Dialogue module has been disabled, and all the old Messages from it copied into the new standard Message feature.  If you really want Dialogue back, you can enable it using the "eye" icon here:  Admin >> Modules >> Dialogue');

    }

    if ($oldversion < 2005042100) {
        $result = table_column('event', '', 'repeatid', 'int', '10', 'unsigned', '0', 'not null', 'userid') && $result;
    }

    if ($oldversion < 2005042400) {  // Add user tracking prefs field.
        table_column('user', '', 'trackforums', 'int', '4', 'unsigned', '0', 'not null', 'autosubscribe');
    }

    if ($oldversion < 2005053000 ) { // Add config_plugins table
        
        // this table was created on the MOODLE_15_STABLE branch
        // so it may already exist.
        $result = execute_sql("CREATE TABLE IF NOT EXISTS `{$CFG->prefix}config_plugins` (
                                  `id`         int(10) unsigned NOT NULL auto_increment,
                                  `plugin`     varchar(100) NOT NULL default 'core',
                                  `name`       varchar(100) NOT NULL default '',
                                  `value`      text NOT NULL default '',
                                  PRIMARY KEY  (`id`),
                                           UNIQUE KEY `plugin_name` (`plugin`, `name`)
                                  ) TYPE=MyISAM 
                                  COMMENT='Moodle modules and plugins configuration variables';");
    }

    if ($oldversion < 2005060200) {  // migrate some config items to config_plugins table

        // NOTE: this block is in both postgres AND mysql upgrade
        // files. If you edit either, update the otherone. 
        $user_fields = array("firstname", "lastname", "email", 
                             "phone1", "phone2", "department", 
                             "address", "city", "country", 
                             "description", "idnumber", "lang");
        if (!empty($CFG->auth)) { // if we have no auth, just pass
            foreach ($user_fields as $field) {
                $suffixes = array('', '_editlock', '_updateremote', '_updatelocal');
                foreach ($suffixes as $suffix) {
                    $key = 'auth_user_' . $field . $suffix;
                    if (isset($CFG->$key)) {
                        
                        // translate keys & values
                        // to the new convention
                        // this should support upgrading 
                        // even 1.5dev installs
                        $newkey = $key;
                        $newval = $CFG->$key;
                        if ($suffix === '') {
                            $newkey = 'field_map_' . $field;
                        } elseif ($suffix === '_editlock') {
                            $newkey = 'field_lock_' . $field;
                            $newval = ($newval==1) ? 'locked' : 'unlocked'; // translate 0/1 to locked/unlocked
                        } elseif ($suffix === '_updateremote') {
                            $newkey = 'field_updateremote_' . $field;                            
                        } elseif ($suffix === '_updatelocal') {
                            $newkey = 'field_updatelocal_' . $field;
                            $newval = ($newval==1) ? 'onlogin' : 'oncreate'; // translate 0/1 to locked/unlocked
                        }

                        if (!(set_config($newkey, addslashes($newval), 'auth/'.$CFG->auth)
                            && delete_records('config', 'name', $key))) {
                            notify("Error updating Auth configuration $key to {$CFG->auth} $newkey .");
                            $result = false;
                        }
                    } // end if isset key
                } // end foreach suffix
            } // end foreach field
        }
    }

    if ($oldversion < 2005060201) {  // Close down the Attendance module, we are removing it from CVS.
        if (!file_exists($CFG->dirroot.'/mod/attendance/lib.php')) {
            if (count_records('attendance')) {   // We have some data, so should keep it

                set_field('modules', 'visible', 0, 'name', 'attendance');
                notify('The Attendance module has been discontinued.  If you really want to 
                        continue using it, you should download it individually from 
                        http://download.moodle.org/modules and install it, then 
                        reactivate it from Admin >> Configuration >> Modules.  
                        None of your existing data has been deleted, so all existing 
                        Attendance activities should re-appear.');

            } else {  // No data, so do a complete delete

                execute_sql('DROP TABLE '.$CFG->prefix.'attendance', false);
                delete_records('modules', 'name', 'attendance');
                notify("The Attendance module has been discontinued and removed from your site.  
                        You weren't using it anyway.  ;-)");
            }
        }
    }

    if ($oldversion < 2005071700) {  // Close down the Dialogue module, we are removing it from CVS.
        if (!file_exists($CFG->dirroot.'/mod/dialogue/lib.php')) {
            if (count_records('dialogue')) {   // We have some data, so should keep it

                set_field('modules', 'visible', 0, 'name', 'dialogue');
                notify('The Dialogue module has been discontinued.  If you really want to 
                        continue using it, you should download it individually from 
                        http://download.moodle.org/modules and install it, then 
                        reactivate it from Admin >> Configuration >> Modules.  
                        None of your existing data has been deleted, so all existing 
                        Dialogue activities should re-appear.');

            } else {  // No data, so do a complete delete

                execute_sql('DROP TABLE '.$CFG->prefix.'dialogue', false);
                delete_records('modules', 'name', 'dialogue');
                notify("The Dialogue module has been discontinued and removed from your site.  
                        You weren't using it anyway.  ;-)");
            }
        }
    }

    if ($oldversion < 2005072000) {  // Add a couple fields to mdl_event to work towards iCal import/export
        table_column('event', '', 'uuid', 'char', '36', '', '', 'not null', 'visible');
        table_column('event', '', 'sequence', 'integer', '10', 'unsigned', '1', 'not null', 'uuid');
    }

    if ($oldversion < 2005072100) { // run the online assignment cleanup code
        include($CFG->dirroot.'/'.$CFG->admin.'/oacleanup.php');
        if (function_exists('online_assignment_cleanup')) {
            online_assignment_cleanup();
        }
    }

    if ($oldversion < 2005072200) { // fix the mistakenly-added currency stuff from enrol/authorize
        execute_sql("DROP TABLE {$CFG->prefix}currencies", false); // drop silently
        execute_sql("ALTER TABLE {$CFG->prefix}course DROP currency", false);
        $defaultcurrency = empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency;
        table_column('course', '', 'currency', 'char', '3', '', $defaultcurrency, 'not null', 'cost');
    }

    if ($oldversion < 2005081600) { //set up the course requests table
        modify_database('',"CREATE TABLE `prefix_course_request`  (
          `id` int(10) unsigned NOT NULL auto_increment,
          `fullname` varchar(254) NOT NULL default '',
          `shortname` varchar(15) NOT NULL default '',
          `summary` text NOT NULL,
          `reason` text NOT NULL,
          `requester` int(10) NOT NULL default 0,
          PRIMARY KEY (`id`),
          KEY `shortname` (`shortname`)
        ) TYPE=MyISAM;");
        
        table_column('course','','requested');
    }

    if ($oldversion < 2005081601) {
        modify_database('',"CREATE TABLE `prefix_course_allowed_modules` (
         `id` int(10) unsigned NOT NULL auto_increment,
         `course` int(10) unsigned NOT NULL default 0,
         `module` int(10) unsigned NOT NULL default 0,
         PRIMARY KEY (`id`),
         KEY `course` (`course`),
         KEY `module` (`module`)
      ) TYPE=MyISAM;");
        
        table_column('course','','restrictmodules','int','1','','0','not null');
    }

    if ($oldversion < 2005081700) {
        table_column('course_categories','','depth','integer');
        table_column('course_categories','','path','varchar','255');
    }

    if ($oldversion < 2005090100) {
        modify_database("","CREATE TABLE `prefix_stats_daily` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `courseid` int(10) unsigned NOT NULL default 0,
          `timeend` int(10) unsigned NOT NULL default 0,
          `students` int(10) unsigned NOT NULL default 0,
          `teachers` int(10) unsigned NOT NULL default 0,
          `activestudents` int(10) unsigned NOT NULL default 0,
          `activeteachers` int(10) unsigned NOT NULL default 0,
          `studentreads` int(10) unsigned NOT NULL default 0,
          `studentwrites` int(10) unsigned NOT NULL default 0,
          `teacherreads` int(10) unsigned NOT NULL default 0,
          `teacherwrites` int(10) unsigned NOT NULL default 0,
          `logins` int(10) unsigned NOT NULL default 0,
          `uniquelogins` int(10) unsigned NOT NULL default 0,
          PRIMARY KEY (`id`),
          KEY `courseid` (`courseid`),
          KEY `timeend` (`timeend`)
       );");

        modify_database("","CREATE TABLE prefix_stats_weekly (
          `id` int(10) unsigned NOT NULL auto_increment,
          `courseid` int(10) unsigned NOT NULL default 0,
          `timeend` int(10) unsigned NOT NULL default 0,
          `students` int(10) unsigned NOT NULL default 0,
          `teachers` int(10) unsigned NOT NULL default 0,
          `activestudents` int(10) unsigned NOT NULL default 0,
          `activeteachers` int(10) unsigned NOT NULL default 0,
          `studentreads` int(10) unsigned NOT NULL default 0,
          `studentwrites` int(10) unsigned NOT NULL default 0,
          `teacherreads` int(10) unsigned NOT NULL default 0,
          `teacherwrites` int(10) unsigned NOT NULL default 0,
          `logins` int(10) unsigned NOT NULL default 0,
          `uniquelogins` int(10) unsigned NOT NULL default 0,
          PRIMARY KEY (`id`),
          KEY `courseid` (`courseid`),
          KEY `timeend` (`timeend`)
       );");

        modify_database("","CREATE TABLE prefix_stats_monthly (
          `id` int(10) unsigned NOT NULL auto_increment,
          `courseid` int(10) unsigned NOT NULL default 0,
          `timeend` int(10) unsigned NOT NULL default 0,
          `students` int(10) unsigned NOT NULL default 0,
          `teachers` int(10) unsigned NOT NULL default 0,
          `activestudents` int(10) unsigned NOT NULL default 0,
          `activeteachers` int(10) unsigned NOT NULL default 0,
          `studentreads` int(10) unsigned NOT NULL default 0,
          `studentwrites` int(10) unsigned NOT NULL default 0,
          `teacherreads` int(10) unsigned NOT NULL default 0,
          `teacherwrites` int(10) unsigned NOT NULL default 0,
          `logins` int(10) unsigned NOT NULL default 0,
          `uniquelogins` int(10) unsigned NOT NULL default 0,
          PRIMARY KEY (`id`),
          KEY `courseid` (`courseid`),
          KEY `timeend` (`timeend`)
       );");

        modify_database("","CREATE TABLE prefix_stats_user_daily (
          `id` int(10) unsigned NOT NULL auto_increment,
          `courseid` int(10) unsigned NOT NULL default 0,
          `userid` int(10) unsigned NOT NULL default 0,
          `roleid` int(10) unsigned NOT NULL default 0,
          `timeend` int(10) unsigned NOT NULL default 0,
          `statsreads` int(10) unsigned NOT NULL default 0,
          `statswrites` int(10) unsigned NOT NULL default 0,
          `stattype` varchar(30) NOT NULL default '',
          PRIMARY KEY (`id`),
          KEY `courseid` (`courseid`),
          KEY `userid` (`userid`),
          KEY `roleid` (`roleid`),
          KEY `timeend` (`timeend`)
       );");

        modify_database("","CREATE TABLE prefix_stats_user_weekly (
          `id` int(10) unsigned NOT NULL auto_increment,
          `courseid` int(10) unsigned NOT NULL default 0,
          `userid` int(10) unsigned NOT NULL default 0,
          `roleid` int(10) unsigned NOT NULL default 0,
          `timeend` int(10) unsigned NOT NULL default 0,
          `statsreads` int(10) unsigned NOT NULL default 0,
          `statswrites` int(10) unsigned NOT NULL default 0,
          `stattype` varchar(30) NOT NULL default '',
          PRIMARY KEY (`id`),
          KEY `courseid` (`courseid`),
          KEY `userid` (`userid`),
          KEY `roleid` (`roleid`),
          KEY `timeend` (`timeend`)
       );");

        modify_database("","CREATE TABLE prefix_stats_user_monthly (
          `id` int(10) unsigned NOT NULL auto_increment,
          `courseid` int(10) unsigned NOT NULL default 0,
          `userid` int(10) unsigned NOT NULL default 0,
          `roleid` int(10) unsigned NOT NULL default 0,
          `timeend` int(10) unsigned NOT NULL default 0,
          `statsreads` int(10) unsigned NOT NULL default 0,
          `statswrites` int(10) unsigned NOT NULL default 0,
          `stattype` varchar(30) NOT NULL default '',
          PRIMARY KEY (`id`),
          KEY `courseid` (`courseid`),
          KEY `userid` (`userid`),
          KEY `roleid` (`roleid`),
          KEY `timeend` (`timeend`)
       );");
         
    }

    if ($oldversion < 2005100300) {
        table_column('course','','expirynotify','tinyint','1');
        table_column('course','','expirythreshold','int','10');
        table_column('course','','notifystudents','tinyint','1');
        $new = new stdClass();
        $new->name = 'lastexpirynotify';
        $new->value = 0;
        insert_record('config', $new);
    }

    if ($oldversion < 2005100400) {
        table_column('course','','enrollable','tinyint','1','unsigned','1');
        table_column('course','','enrolstartdate','int');
        table_column('course','','enrolenddate','int');
    }

    if ($oldversion < 2005101200) { // add enrolment key to course_request.
        table_column('course_request','','password','varchar',50);
    }

    if ($oldversion < 2006030800) { # add extra indexes to log (see bug #4112)
        modify_database('',"ALTER TABLE prefix_log ADD INDEX userid (userid);");
        modify_database('',"ALTER TABLE prefix_log ADD INDEX info (info);");
    }

    if ($oldversion < 2006030900) {
        table_column('course','','enrol','varchar','20','','');

        if ($CFG->enrol == 'internal' || $CFG->enrol == 'manual') {
            set_config('enrol_plugins_enabled', 'manual');
            set_config('enrol', 'manual');
        } else {
            set_config('enrol_plugins_enabled', 'manual,'.$CFG->enrol);
        }

        require_once("$CFG->dirroot/enrol/enrol.class.php");
        $defaultenrol = enrolment_factory::factory($CFG->enrol);
        if (!method_exists($defaultenrol, 'print_entry')) { // switch enrollable to off for all courses in this case
            modify_database('', 'UPDATE prefix_course SET enrollable = 0');
        }

        execute_sql("UPDATE {$CFG->prefix}user_students SET enrol='manual' WHERE enrol='' OR enrol='internal'");
        execute_sql("UPDATE {$CFG->prefix}user_teachers SET enrol='manual' WHERE enrol=''");

    }
    
    if ($oldversion < 2006031000) {

        modify_database("","CREATE TABLE prefix_post (
          `id` int(10) unsigned NOT NULL auto_increment,
          `userid` int(10) unsigned NOT NULL default '0',
          `courseid` int(10) unsigned NOT NULL default'0',
          `groupid` int(10) unsigned NOT NULL default'0',
          `moduleid` int(10) unsigned NOT NULL default'0',
          `coursemoduleid` int(10) unsigned NOT NULL default'0',
          `subject` varchar(128) NOT NULL default '',
          `summary` longtext,
          `content` longtext,
          `uniquehash` varchar(128) NOT NULL default '',
          `rating` int(10) unsigned NOT NULL default'0',
          `format` int(10) unsigned NOT NULL default'0',
          `publishstate` enum('draft','site','public') NOT NULL default 'draft',
          `lastmodified` int(10) unsigned NOT NULL default '0',
          `created` int(10) unsigned NOT NULL default '0',
          PRIMARY KEY  (`id`),
          UNIQUE KEY `id_user_idx` (`id`, `userid`),
          KEY `post_lastmodified_idx` (`lastmodified`),
          KEY `post_subject_idx` (`subject`)
        ) TYPE=MyISAM  COMMENT='New moodle post table. Holds data posts such as forum entries or blog entries.';");

        modify_database("","CREATE TABLE prefix_tags (
          `id` int(10) unsigned NOT NULL auto_increment,
          `type` varchar(255) NOT NULL default 'official',
          `userid` int(10) unsigned NOT NULL default'0',
          `text` varchar(255) NOT NULL default '',
          PRIMARY KEY  (`id`)
        ) TYPE=MyISAM COMMENT ='tags structure for moodle.';");

        modify_database("","CREATE TABLE prefix_blog_tag_instance (
          `id` int(10) unsigned NOT NULL auto_increment,
          `entryid` int(10) unsigned NOT NULL default'0',
          `tagid` int(10) unsigned NOT NULL default'0',
          `groupid` int(10) unsigned NOT NULL default'0',
          `courseid` int(10) unsigned NOT NULL default'0',
          `userid` int(10) unsigned NOT NULL default'0',
          PRIMARY KEY  (`id`)
          ) TYPE=MyISAM COMMENT ='tag instance for blogs.';");
    }

    if ($oldversion < 2006031400) {
        require_once("$CFG->dirroot/enrol/enrol.class.php");
        $defaultenrol = enrolment_factory::factory($CFG->enrol);
        if (!method_exists($defaultenrol, 'print_entry')) {
            set_config('enrol', 'manual');
        }
    }
    
    if ($oldversion < 2006031600) {
        execute_sql(" ALTER TABLE `{$CFG->prefix}grade_category` CHANGE `weight` `weight` decimal(5,2) default '0.00';");
    }

    if ($oldversion < 2006032000) {
        table_column('post','','module','varchar','20','','','not null', 'id');
        modify_database('',"ALTER TABLE prefix_post ADD INDEX post_module_idx (module);");
        modify_database('',"UPDATE prefix_post SET module = 'blog';");
    }

    if ($oldversion < 2006032001) {
        table_column('blog_tag_instance','','timemodified','integer','10','unsigned','0','not null', 'userid');
        modify_database('',"ALTER TABLE prefix_blog_tag_instance ADD INDEX bti_entryid_idx (entryid);");
        modify_database('',"ALTER TABLE prefix_blog_tag_instance ADD INDEX bti_tagid_idx (tagid);");
        modify_database('',"UPDATE prefix_blog_tag_instance SET timemodified = '".time()."';");
    }

    if ($oldversion < 2006040500) { // Add an index to course_sections that was never upgraded (bug 5100)
        execute_sql(" CREATE INDEX coursesection ON {$CFG->prefix}course_sections (course,section) ", false);
    }

    /// change all the int(11) to int(10) for blogs and tags

    if ($oldversion < 2006041000) {
        table_column('post','id','id','integer','10','unsigned','0','not null');
        table_column('post','userid','userid','integer','10','unsigned','0','not null');
        table_column('post','courseid','courseid','integer','10','unsigned','0','not null');
        table_column('post','groupid','groupid','integer','10','unsigned','0','not null');
        table_column('post','moduleid','moduleid','integer','10','unsigned','0','not null');
        table_column('post','coursemoduleid','coursemoduleid','integer','10','unsigned','0','not null');
        table_column('post','rating','rating','integer','10','unsigned','0','not null');
        table_column('post','format','format','integer','10','unsigned','0','not null');
        table_column('tags','id','id','integer','10','unsigned','0','not null');
        table_column('tags','userid','userid','integer','10','unsigned','0','not null');
        table_column('blog_tag_instance','id','id','integer','10','unsigned','0','not null');
        table_column('blog_tag_instance','entryid','entryid','integer','10','unsigned','0','not null');
        table_column('blog_tag_instance','tagid','tagid','integer','10','unsigned','0','not null');
        table_column('blog_tag_instance','groupid','groupid','integer','10','unsigned','0','not null');
        table_column('blog_tag_instance','courseid','courseid','integer','10','unsigned','0','not null');
        table_column('blog_tag_instance','userid','userid','integer','10','unsigned','0','not null');
    }

    if ($oldversion < 2006041001) {
        table_column('cache_text','formattedtext','formattedtext','longblob','','','','not null');
    }
    
    if ($oldversion < 2006041100) {
        table_column('course_modules','','visibleold','integer','1','unsigned','1','not null', 'visible');
    }
    
    if ($oldversion < 2006041801) { // forgot auto_increments for ids
        modify_database('',"ALTER TABLE prefix_post CHANGE id id INT UNSIGNED NOT NULL AUTO_INCREMENT");
        modify_database('',"ALTER TABLE prefix_tags CHANGE id id INT UNSIGNED NOT NULL AUTO_INCREMENT");
        modify_database('',"ALTER TABLE prefix_blog_tag_instance CHANGE id id INT UNSIGNED NOT NULL AUTO_INCREMENT");
    }
    
    // changed user->firstname, user->lastname, course->shortname to varchar(100)
    
    if ($oldversion < 2006041900) {
        table_column('course','shortname','shortname','varchar','100','','','not null');
        table_column('user','firstname','firstname','varchar','100','','','not null');
        table_column('user','lastname','lastname','varchar','100','','','not null');
    }
    
    if ($oldversion < 2006042400) {
        // Look through table log_display and get rid of duplicates.
        $rs = get_recordset_sql('SELECT DISTINCT * FROM '.$CFG->prefix.'log_display');
        
        // Drop the log_display table and create it back with an id field.
        execute_sql("DROP TABLE {$CFG->prefix}log_display", false);
        
        modify_database('', "CREATE TABLE prefix_log_display (
                               `id` int(10) unsigned NOT NULL auto_increment,
                               `module` varchar(30),
                               `action` varchar(40),
                               `mtable` varchar(30),
                               `field` varchar(50),
                               PRIMARY KEY (`id`)
                               ) TYPE=MyISAM");
        
        // Add index to ensure that module and action combination is unique.
        modify_database('', "ALTER TABLE prefix_log_display ADD UNIQUE `moduleaction`(`module` , `action`)");
        
        // Insert the records back in, sans duplicates.
        if ($rs) {
            while (!$rs->EOF) {
                $sql = "INSERT INTO {$CFG->prefix}log_display ".
                            "VALUES('', '".$rs->fields['module']."', ".
                            "'".$rs->fields['action']."', ".
                            "'".$rs->fields['mtable']."', ".
                            "'".$rs->fields['field']."')";
                
                execute_sql($sql, false);
                $rs->MoveNext();
            }
            rs_close($rs);
        }
    }
    
    // change tags->type to varchar(20), adding 2 indexes for tags table.
    if ($oldversion < 2006042401) {
        table_column('tags','type','type','varchar','20','','','not null');
        modify_database('',"ALTER TABLE prefix_tags ADD INDEX tags_typeuserid_idx (type(20), userid)");
        modify_database('',"ALTER TABLE prefix_tags ADD INDEX tags_text_idx(text(255))");
    }
    
    /***************************************************
     * The following is an effort to change all the    *
     * default NULLs to NOT NULL defaut '' in all      *
     * mysql tables, to prevent 5303 and be consistent *
     ***************************************************/

    if ($oldversion < 2006042800) {

        execute_sql("UPDATE {$CFG->prefix}grade_category SET name='' WHERE name IS NULL");
        table_column('grade_category','name','name','varchar','64','','','not null');

        execute_sql("UPDATE {$CFG->prefix}grade_category SET weight='0' WHERE weight IS NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}grade_category change weight weight decimal(5,2) NOT NULL default 0.00");
        execute_sql("UPDATE {$CFG->prefix}grade_item SET courseid='0' WHERE courseid IS NULL");
        table_column('grade_item','courseid','courseid','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}grade_item SET category='0' WHERE category IS NULL");
        table_column('grade_item','category','category','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}grade_item SET modid='0' WHERE modid IS NULL");
        table_column('grade_item','modid','modid','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}grade_item SET cminstance='0' WHERE cminstance IS NULL");
        table_column('grade_item','cminstance','cminstance','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}grade_item SET scale_grade='0' WHERE scale_grade IS NULL");
        execute_sql("ALTER TABLE {$CFG->prefix}grade_item change scale_grade scale_grade float(11,10) NOT NULL default 1.0000000000");
        
        execute_sql("UPDATE {$CFG->prefix}grade_preferences SET courseid='0' WHERE courseid IS NULL");
        table_column('grade_preferences','courseid','courseid','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET idnumber='' WHERE idnumber IS NULL");
        table_column('user','idnumber','idnumber','varchar','64','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET icq='' WHERE icq IS NULL");
        table_column('user','icq','icq','varchar','15','','','not null');
        
        execute_sql("UPDATE {$CFG->prefix}user SET skype='' WHERE skype IS NULL");
        table_column('user','skype','skype','varchar','50','','','not null');
        
        execute_sql("UPDATE {$CFG->prefix}user SET yahoo='' WHERE yahoo IS NULL");
        table_column('user','yahoo','yahoo','varchar','50','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET aim='' WHERE aim IS NULL");
        table_column('user','aim','aim','varchar','50','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET msn='' WHERE msn IS NULL");
        table_column('user','msn','msn','varchar','50','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET phone1='' WHERE phone1 IS NULL");
        table_column('user','phone1','phone1','varchar','20','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET phone2='' WHERE phone2 IS NULL");
        table_column('user','phone2','phone2','varchar','20','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET institution='' WHERE institution IS NULL");
        table_column('user','institution','institution','varchar','40','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET department='' WHERE department IS NULL");
        table_column('user','department','department','varchar','30','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET address='' WHERE address IS NULL");
        table_column('user','address','address','varchar','70','','','not null');
        
        execute_sql("UPDATE {$CFG->prefix}user SET city='' WHERE city IS NULL");
        table_column('user','city','city','varchar','20','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET country='' WHERE country IS NULL");
        table_column('user','country','country','char','2','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET lang='' WHERE lang IS NULL");
        table_column('user','lang','lang','varchar','10','','en','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET lastIP='' WHERE lastIP IS NULL");
        table_column('user','lastIP','lastIP','varchar','15','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET secret='' WHERE secret IS NULL");
        table_column('user','secret','secret','varchar','15','','','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET picture='0' WHERE picture IS NULL");
        table_column('user','picture','picture','tinyint','1','','0','not null');

        execute_sql("UPDATE {$CFG->prefix}user SET url='' WHERE url IS NULL");
        table_column('user','url','url','varchar','255','','','not null');
    }
    
    if ($oldversion < 2006050400) {

        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_deleted_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_confirmed_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_firstname_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_lastname_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_city_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_country_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_lastaccess_idx;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` DROP INDEX {$CFG->prefix}user_email_idx;",false);

        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_deleted (deleted)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_confirmed (confirmed)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_firstname (firstname)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_lastname (lastname)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_city (city)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_country (country)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_lastaccess (lastaccess)",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}user` ADD INDEX user_email (email)",false);
    }
    
    if ($oldversion < 2006050500) {
        table_column('log', 'action', 'action', 'varchar', '40', '', '', 'not null');
    }

    if ($oldversion < 2006050501) {
        table_column('sessions', 'data', 'data', 'mediumtext', '', '', '', 'not null');
    }
    
    // renaming of reads and writes for stats_user_xyz
    if ($oldversion < 2006052400) { // change this later

        // we are using this because we want silent updates

        execute_sql("ALTER TABLE `{$CFG->prefix}stats_user_daily` CHANGE `reads` statsreads int(10) unsigned  NOT NULL default 0", false);
        execute_sql("ALTER TABLE `{$CFG->prefix}stats_user_daily` CHANGE `writes` statswrites int(10) unsigned  NOT NULL default 0", false);
        execute_sql("ALTER TABLE `{$CFG->prefix}stats_user_weekly` CHANGE `reads` statsreads int(10) unsigned  NOT NULL default 0", false);
        execute_sql("ALTER TABLE `{$CFG->prefix}stats_user_weekly` CHANGE `writes` statswrites int(10) unsigned  NOT NULL default 0", false);
        execute_sql("ALTER TABLE `{$CFG->prefix}stats_user_monthly` CHANGE `reads` statsreads int(10) unsigned  NOT NULL default 0", false);
        execute_sql("ALTER TABLE `{$CFG->prefix}stats_user_monthly` CHANGE `writes` statswrites int(10) unsigned  NOT NULL default 0", false);
  
    }

    // Adding some missing log actions
    if ($oldversion < 2006060400) {
        // But only if they doesn't exist (because this was introduced after branch and we could be duplicating!)
        if (!record_exists('log_display', 'module', 'course', 'action', 'report log')) {
            execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'report log', 'course', 'fullname')");
        }
        if (!record_exists('log_display', 'module', 'course', 'action', 'report live')) {
            execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'report live', 'course', 'fullname')");
        }
        if (!record_exists('log_display', 'module', 'course', 'action', 'report outline')) {
            execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'report outline', 'course', 'fullname')");
        }
        if (!record_exists('log_display', 'module', 'course', 'action', 'report participation')) {
            execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'report participation', 'course', 'fullname')");
        }
        if (!record_exists('log_display', 'module', 'course', 'action', 'report stats')) {
            execute_sql("INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'report stats', 'course', 'fullname')");
        }
    }

    //Renaming lastIP to lastip (all fields lowercase)
    if ($oldversion < 2006060900) {
        //Only if it exists
        $fields = $db->MetaColumnNames($CFG->prefix.'user');
        if (in_array('lastIP',$fields)) {
            table_column("user", "lastIP", "lastip", "varchar", "15", "", "", "not null", "currentlogin");
        }
    }

    // Change in MySQL 5.0.3 concerning how decimals are stored. Mimic from 16_STABLE
    // this isn't dangerous because it's a simple type change, but be careful with
    // versions and duplicate work in order to provide smooth upgrade paths.
    if ($oldversion < 2006071800) {
        table_column('grade_letter', 'grade_high', 'grade_high', 'decimal(5,2)', '', '', '100.00', 'not null', '');
        table_column('grade_letter', 'grade_low', 'grade_low', 'decimal(5,2)', '', '', '0.00', 'not null', '');
    }
    
    if ($oldversion < 2006080400) {
        execute_sql("CREATE TABLE {$CFG->prefix}role (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `name` varchar(255) NOT NULL default '',
                              `shortname` varchar(100) NOT NULL default '',
                              `description` text NOT NULL default '',
                              `sortorder` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY  (`id`)
                            )", true);

        execute_sql("CREATE TABLE {$CFG->prefix}context (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `level` int(10) unsigned NOT NULL default '0',
                              `instanceid` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY  (`id`)
                            )", true);

        execute_sql("CREATE TABLE {$CFG->prefix}role_assignments (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `roleid` int(10) unsigned NOT NULL default '0',
                              `contextid` int(10) unsigned NOT NULL default '0',
                              `userid` int(10) unsigned NOT NULL default '0',
                              `hidden` int(1) unsigned NOT NULL default '0',
                              `timestart` int(10) unsigned NOT NULL default '0',
                              `timeend` int(10) unsigned NOT NULL default '0',
                              `timemodified` int(10) unsigned NOT NULL default '0',
                              `modifierid` int(10) unsigned NOT NULL default '0',
                              `enrol` varchar(20) NOT NULL default '',
                              `sortorder` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY  (`id`)
                            )", true);

        execute_sql("CREATE TABLE {$CFG->prefix}role_capabilities (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `contextid` int(10) unsigned NOT NULL default '0',
                              `roleid` int(10) unsigned NOT NULL default '0',
                              `capability` varchar(255) NOT NULL default '',
                              `permission` int(10) unsigned NOT NULL default '0',
                              `timemodified` int(10) unsigned NOT NULL default '0',
                              `modifierid` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY (`id`)
                            )", true);
                            
        execute_sql("CREATE TABLE {$CFG->prefix}role_deny_grant (
                              `id` int(10) unsigned NOT NULL auto_increment,
                              `roleid` int(10) unsigned NOT NULL default '0',
                              `unviewableroleid` int(10) unsigned NOT NULL default '0',
                              PRIMARY KEY (`id`)
                            )", true);
                            
        execute_sql("CREATE TABLE {$CFG->prefix}capabilities ( 
                              `id` int(10) unsigned NOT NULL auto_increment, 
                              `name` varchar(255) NOT NULL default '', 
                              `captype` varchar(50) NOT NULL default '', 
                              `contextlevel` int(10) unsigned NOT NULL default '0', 
                              `component` varchar(100) NOT NULL default '', 
                              PRIMARY KEY (`id`) 
                            )", true);     
                            
        execute_sql("CREATE TABLE {$CFG->prefix}role_names ( 
                              `id` int(10) unsigned NOT NULL auto_increment, 
                              `roleid` int(10) unsigned NOT NULL default '0',
                              `contextid` int(10) unsigned NOT NULL default '0', 
                              `text` text NOT NULL default '',
                              PRIMARY KEY (`id`) 
                            )", true);                      
                        
    }
    
    if ($oldversion < 2006081000) {
      
        execute_sql("ALTER TABLE `{$CFG->prefix}role` ADD INDEX `sortorder` (`sortorder`)",true);
        
        execute_sql("ALTER TABLE `{$CFG->prefix}context` ADD INDEX `instanceid` (`instanceid`)",true);
        execute_sql("ALTER TABLE `{$CFG->prefix}context` ADD UNIQUE INDEX `level-instanceid` (`level`, `instanceid`)",true);

        execute_sql("ALTER TABLE `{$CFG->prefix}role_assignments` ADD INDEX `roleid` (`roleid`)",true);
        execute_sql("ALTER TABLE `{$CFG->prefix}role_assignments` ADD INDEX `contextid` (`contextid`)",true);  
        execute_sql("ALTER TABLE `{$CFG->prefix}role_assignments` ADD INDEX `userid` (`userid`)",true);
        execute_sql("ALTER TABLE `{$CFG->prefix}role_assignments` ADD UNIQUE INDEX `contextid-roleid-userid` (`contextid`, `roleid`, `userid`)",true);
        execute_sql("ALTER TABLE `{$CFG->prefix}role_assignments` ADD INDEX `sortorder` (`sortorder`)",true);

        execute_sql("ALTER TABLE `{$CFG->prefix}role_capabilities` ADD INDEX `roleid` (`roleid`)",true);
        execute_sql("ALTER TABLE `{$CFG->prefix}role_capabilities` ADD INDEX `contextid` (`contextid`)",true); 
        execute_sql("ALTER TABLE `{$CFG->prefix}role_capabilities` ADD INDEX `modifierid` (`modifierid`)",true);                
        // MDL-10640  adding missing index from upgrade
        execute_sql("ALTER TABLE `{$CFG->prefix}role_capabilities` ADD INDEX `capability` (`capability`)",true);   
        execute_sql("ALTER TABLE `{$CFG->prefix}role_capabilities` ADD UNIQUE INDEX `roleid-contextid-capability` (`roleid`, `contextid`, `capability`)",true);         
                        
        execute_sql("ALTER TABLE `{$CFG->prefix}role_deny_grant` ADD INDEX `roleid` (`roleid`)",true);
        execute_sql("ALTER TABLE `{$CFG->prefix}role_deny_grant` ADD INDEX `unviewableroleid` (`unviewableroleid`)",true);    
        execute_sql("ALTER TABLE `{$CFG->prefix}role_deny_grant` ADD UNIQUE INDEX `roleid-unviewableroleid` (`roleid`, `unviewableroleid`)",true);         
       
        execute_sql("ALTER TABLE `{$CFG->prefix}capabilities` ADD UNIQUE INDEX `name` (`name`)",true); 
                             
        execute_sql("ALTER TABLE `{$CFG->prefix}role_names` ADD INDEX `roleid` (`roleid`)",true);                         
        execute_sql("ALTER TABLE `{$CFG->prefix}role_names` ADD INDEX `contextid` (`contextid`)",true); 
        execute_sql("ALTER TABLE `{$CFG->prefix}role_names` ADD UNIQUE INDEX `roleid-contextid` (`roleid`, `contextid`)",true);                 
    }
    
    if ($oldversion < 2006081600) {
        execute_sql("ALTER TABLE `{$CFG->prefix}role_capabilities` CHANGE permission permission int(10) NOT NULL default '0'",true);   
    }
    
    // drop role_deny_grant table, and create 2 new ones
    if ($oldversion < 2006081700) {
        execute_sql("DROP TABLE `{$CFG->prefix}role_deny_grant`", true);
        
        execute_sql("CREATE TABLE {$CFG->prefix}role_allow_assign (
                    `id` int(10) unsigned NOT NULL auto_increment,
                    `roleid` int(10) unsigned NOT NULL default '0',
                    `allowassign` int(10) unsigned NOT NULL default '0',
                    KEY `roleid` (`roleid`),
                    KEY `allowassign` (`allowassign`),
                    UNIQUE KEY `roleid-allowassign` (`roleid`, `allowassign`),
                    PRIMARY KEY (`id`)
                    )", true); 
                            
        execute_sql("CREATE TABLE {$CFG->prefix}role_allow_override (
                    `id` int(10) unsigned NOT NULL auto_increment,
                    `roleid` int(10) unsigned NOT NULL default '0',
                    `allowoverride` int(10) unsigned NOT NULL default '0',
                    KEY `roleid` (`roleid`),
                    KEY `allowoverride` (`allowoverride`),
                    UNIQUE KEY `roleid-allowoverride` (`roleid`, `allowoverride`),
                    PRIMARY KEY (`id`)
                    )", true); 
        
    }
    
    if ($oldversion < 2006082100) {
        execute_sql("ALTER TABLE `{$CFG->prefix}context` DROP INDEX `level-instanceid`;",false);
        table_column('context', 'level', 'aggregatelevel', 'int', '10', 'unsigned', '0', 'not null', '');
        execute_sql("ALTER TABLE `{$CFG->prefix}context` ADD UNIQUE INDEX `aggregatelevel-instanceid` (`aggregatelevel`, `instanceid`)",false);
    }
    
    if ($oldversion < 2006082200) {
        table_column('timezone', 'rule', 'tzrule', 'varchar', '20', '', '', 'not null', '');
    }

    if ($oldversion < 2006082800) {
        table_column('user', '', 'ajax', 'integer', '1', 'unsigned', '1', 'not null', 'htmleditor');
    }

    if ($oldversion < 2006082900) {
        execute_sql("DROP TABLE {$CFG->prefix}sessions", true);
        execute_sql("
            CREATE TABLE {$CFG->prefix}sessions2 (
                sesskey VARCHAR(64) NOT NULL default '',
                expiry DATETIME NOT NULL,
                expireref VARCHAR(250),
                created DATETIME NOT NULL,
                modified DATETIME NOT NULL,
                sessdata TEXT,
                CONSTRAINT  PRIMARY KEY (sesskey)
            ) COMMENT='Optional database session storage in new format, not used by default';", true);

        execute_sql("
            CREATE INDEX {$CFG->prefix}sess_exp_ix ON {$CFG->prefix}sessions2 (expiry);", true);
        execute_sql("
            CREATE INDEX {$CFG->prefix}sess_exp2_ix ON {$CFG->prefix}sessions2 (expireref);", true);
    }

    if ($oldversion < 2006083001) {
        table_column('sessions2', 'sessdata', 'sessdata', 'LONGTEXT', '', '', '', '', '');
    }
    
    if ($oldversion < 2006083002) {
        table_column('capabilities', '', 'riskbitmask', 'INTEGER', '10', 'unsigned', '0', 'not null', '');
    }

    if ($oldversion < 2006083100) {
        execute_sql("ALTER TABLE {$CFG->prefix}course CHANGE modinfo modinfo longtext NULL AFTER showgrades");
    }

    if ($oldversion < 2006083101) {
        execute_sql("ALTER TABLE {$CFG->prefix}course_categories CHANGE description description text NULL AFTER name");
    }

    if ($oldversion < 2006083102) {
        execute_sql("ALTER TABLE {$CFG->prefix}user CHANGE description description text NULL AFTER url");
    }

    if ($oldversion < 2006090200) {
        execute_sql("ALTER TABLE {$CFG->prefix}course_sections CHANGE summary summary text NULL AFTER section");
        execute_sql("ALTER TABLE {$CFG->prefix}course_sections CHANGE sequence sequence text NULL AFTER section");
    }


    // table to keep track of course page access times, used in online participants block, and participants list
    if ($oldversion < 2006091200) {
        execute_sql("CREATE TABLE {$CFG->prefix}user_lastaccess ( 
                    `id` int(10) unsigned NOT NULL auto_increment, 
                    `userid` int(10) unsigned NOT NULL default '0',
                    `courseid` int(10) unsigned NOT NULL default '0', 
                    `timeaccess` int(10) unsigned NOT NULL default '0', 
                    KEY `userid` (`userid`),
                    KEY `courseid` (`courseid`),
                    UNIQUE KEY `userid-courseid` (`userid`, `courseid`),
                    PRIMARY KEY (`id`) 
                    )TYPE=MYISAM COMMENT ='time user last accessed any page in a course';", true);
    }

    if (!empty($CFG->rolesactive) and $oldversion < 2006091212) {   // Reload the guest roles completely with new defaults
        if ($guestroles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
            delete_records('capabilities');
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);
            foreach ($guestroles as $guestrole) {
                delete_records('role_capabilities', 'roleid', $guestrole->id);
                assign_capability('moodle/legacy:guest', CAP_ALLOW, $guestrole->id, $sitecontext->id);
            }
        }
    }

    if ($oldversion < 2006091700) {
        table_column('course','','defaultrole','integer','10', 'unsigned', '0', 'not null');
    }

    if ($oldversion < 2006091800) {
        delete_records('config', 'name', 'showsiteparticipantslist');
        delete_records('config', 'name', 'requestedteachername');
        delete_records('config', 'name', 'requestedteachersname');
        delete_records('config', 'name', 'requestedstudentname');
        delete_records('config', 'name', 'requestedstudentsname');
    }

    if (!empty($CFG->rolesactive) and $oldversion < 2006091901) {
        if ($roles = get_records('role')) {
            $first = array_shift($roles);
            if (!empty($first->shortname)) {
                // shortnames already exist
            } else {
                table_column('role', '', 'shortname', 'varchar', '100', '', '', 'not null', 'name');
                $legacy_names = array('admin', 'coursecreator', 'editingteacher', 'teacher', 'student', 'guest');
                foreach ($legacy_names as $name) {
                    if ($roles = get_roles_with_capability('moodle/legacy:'.$name, CAP_ALLOW)) {
                        $i = '';
                        foreach ($roles as $role) {
                            if (empty($role->shortname)) {
                                $updated = new object();
                                $updated->id = $role->id;
                                $updated->shortname = $name.$i;
                                update_record('role', $updated);
                                $i++;
                            }
                        }
                    }
                }
            }
        }
    }

    /// Tables for customisable user profile fields
    if ($oldversion < 2006092000) {
        execute_sql("CREATE TABLE {$CFG->prefix}user_info_field (
                        id BIGINT(10) NOT NULL auto_increment,
                        name VARCHAR(255) NOT NULL default '',
                        datatype VARCHAR(255) NOT NULL default '',
                        categoryid BIGINT(10) unsigned NOT NULL default 0,
                        sortorder BIGINT(10) unsigned NOT NULL default 0,
                        required TINYINT(2) unsigned NOT NULL default 0,
                        locked TINYINT(2) unsigned NOT NULL default 0,
                        visible SMALLINT(4) unsigned NOT NULL default 0,
                        defaultdata LONGTEXT,
                        CONSTRAINT  PRIMARY KEY (id));", true);

        execute_sql("ALTER TABLE {$CFG->prefix}user_info_field COMMENT='Customisable user profile fields';", true);

        execute_sql("CREATE TABLE {$CFG->prefix}user_info_category (
                        id BIGINT(10) NOT NULL auto_increment,
                        name VARCHAR(255) NOT NULL default '',
                        sortorder BIGINT(10) unsigned NOT NULL default 0,
                        CONSTRAINT  PRIMARY KEY (id));", true);

        execute_sql("ALTER TABLE {$CFG->prefix}user_info_category COMMENT='Customisable fields categories';", true);

        execute_sql("CREATE TABLE {$CFG->prefix}user_info_data (
                        id BIGINT(10) NOT NULL auto_increment,
                        userid BIGINT(10) unsigned NOT NULL default 0,
                        fieldid BIGINT(10) unsigned NOT NULL default 0,
                        data LONGTEXT NOT NULL,
                        CONSTRAINT  PRIMARY KEY (id));", true);

        execute_sql("ALTER TABLE {$CFG->prefix}user_info_data COMMENT='Data for the customisable user fields';", true);


    }

    if ($oldversion < 2006092200) {
       table_column('context', 'aggregatelevel', 'contextlevel', 'int', '10', 'unsigned', '0', 'not null', '');
/*        execute_sql("ALTER TABLE `{$CFG->prefix}context` DROP INDEX `aggregatelevel-instanceid`;",false);
        execute_sql("ALTER TABLE `{$CFG->prefix}context` ADD UNIQUE INDEX `contextlevel-instanceid` (`contextlevel`, `instanceid`)",false);   // see 2006092409 below  */
    }

    if ($oldversion < 2006092201) {
        execute_sql('TRUNCATE TABLE '.$CFG->prefix.'cache_text', true);
        table_column('cache_text','formattedtext','formattedtext','longtext','','','','not null');
    }

    if ($oldversion < 2006092302) {
        // fix sortorder first if needed
        if ($roles = get_all_roles()) {
            $i = 0;
            foreach ($roles as $rolex) {
                if ($rolex->sortorder != $i) {
                    $r = new object();
                    $r->id = $rolex->id;
                    $r->sortorder = $i;
                    update_record('role', $r);
                }
                $i++;
            }
        }
/*        execute_sql("ALTER TABLE {$CFG->prefix}role DROP INDEX {$CFG->prefix}role_sor_ix;", false);
        execute_sql("ALTER TABLE {$CFG->prefix}role ADD UNIQUE INDEX {$CFG->prefix}role_sor_uix (sortorder)", false);*/
    }

    if ($oldversion < 2006092400) {
        table_column('user', '', 'trustbitmask', 'INTEGER', '10', 'unsigned', '0', 'not null', '');
    }

    if ($oldversion < 2006092409) {
        // ok, once more and now correctly!
        execute_sql("DROP INDEX `aggregatelevel-instanceid` ON {$CFG->prefix}context ;", false);
        execute_sql("DROP INDEX `contextlevel-instanceid` ON {$CFG->prefix}context ;", false);
        execute_sql("CREATE UNIQUE INDEX {$CFG->prefix}cont_conins_uix ON {$CFG->prefix}context (contextlevel, instanceid);", false);

        execute_sql("DROP INDEX {$CFG->prefix}role_sor_ix ON {$CFG->prefix}role ;", false);
        execute_sql("DROP INDEX {$CFG->prefix}role_sor_uix ON {$CFG->prefix}role ;", false);
        execute_sql("CREATE UNIQUE INDEX {$CFG->prefix}role_sor_uix ON {$CFG->prefix}role (sortorder);", false);
    }

    if ($oldversion < 2006092601) {
        table_column('log_display', 'field', 'field', 'varchar', '200', '', '', 'not null', '');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return $result;
}

?>
