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
        if ($categories = get_categories()) {
            foreach ($categories as $category) {
                fix_course_sortorder($category->id);
            }
        }
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
                notify("Processing $course->fullname ...", "green");
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
        include_once("$CFG->dirroot/course/lib.php");
        rebuild_course_cache();
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

    return $result;

}

?>
