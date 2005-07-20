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
            insert_record('log_display', $record, false, 'module');
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
        include_once ($CFG->dirroot."/auth/".$CFG->auth."/lib.php");
        if (function_exists('auth_get_userlist')) {
            $externalusers = auth_get_userlist();
            if (!empty($externalusers)){
                $externalusers = '\''. implode('\',\'',$externalusers).'\'';
                execute_sql("UPDATE {$CFG->prefix}user SET auth = '$CFG->auth' WHERE username  IN ($externalusers)");
            }
        }
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

        modify_database('', "INSERT INTO prefix_log_display VALUES ('message', 'write', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('message', 'read', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
    }

    if ($oldversion < 2004122801) {
        table_column('message', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'message');
        table_column('message_read', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'message');
    }

    if ($oldversion < 2005010100) {
        modify_database('', "INSERT INTO prefix_log_display VALUES ('message', 'add contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('message', 'remove contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('message', 'block contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
        modify_database('', "INSERT INTO prefix_log_display VALUES ('message', 'unblock contact', 'user', 'CONCAT(firstname,\" \",lastname)'); ");
    }

    if ($oldversion < 2005011000) {     // Create a .htaccess file in dataroot, just in case
        if (!file_exists($CFG->dataroot.'/.htaccess')) {
            if ($handle = fopen($CFG->dataroot.'/.htaccess', 'w')) {   // For safety
                @fwrite($handle, "deny from all\r\n");
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
            `name` varchar(64) default NULL,
            `courseid` int(10) unsigned NOT NULL default '0',
            `drop_x_lowest` int(10) unsigned NOT NULL default '0',
            `bonus_points` int(10) unsigned NOT NULL default '0',
            `hidden` int(10) unsigned NOT NULL default '0',
            `weight` decimal(4,2) default '0.00',
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
            `courseid` int(10) unsigned default NULL,
            `category` int(10) unsigned default NULL,
            `modid` int(10) unsigned default NULL,
            `cminstance` int(10) unsigned default NULL,
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
            `courseid` int(10) unsigned default NULL,
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

    return $result;
}

?>
