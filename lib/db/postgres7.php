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
// This file is tailored to PostgreSQL 7

function main_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;


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
    
    //support user based course creating
    if ($oldversion < 2003032400) {
        execute_sql("CREATE TABLE {$CFG->prefix}user_coursecreators (
                                  id int8 SERIAL PRIMARY KEY,
                                  userid int8  NOT NULL default '0'
                                  )");
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

    if ($oldversion < 2003042700) {
        /// Changing to multiple indexes
        execute_sql(" CREATE INDEX {$CFG->prefix}log_coursemoduleaction_idx ON {$CFG->prefix}log (course,module,action) ");
        execute_sql(" CREATE INDEX {$CFG->prefix}log_courseuserid_idx ON {$CFG->prefix}log (course,userid) ");
    }

    if ($oldversion < 2003042801) {
        execute_sql("CREATE TABLE {$CFG->prefix}course_display (
                         id SERIAL PRIMARY KEY,
                         course integer NOT NULL default '0',
                         userid integer NOT NULL default '0',
                         display integer NOT NULL default '0'
                      )");

        execute_sql("CREATE INDEX {$CFG->prefix}course_display_courseuserid_idx ON {$CFG->prefix}course_display (course,userid)");
    }

    if ($oldversion < 2003050400) {
        table_column("course_sections", "", "visible", "integer", "1", "unsigned", "1", "", "");
    }
                                                            
    if ($oldversion < 2003050401) {
        table_column("user", "", "lang", "VARCHAR", "5", "", "$CFG->lang" ,"NOT NULL","");
    }

    if ($oldversion < 2003050900) {
        table_column("modules", "", "visible", "integer", "1", "unsigned", "1", "", "");
    }

    if ($oldversion < 2003050902) {
        if (get_records("modules", "name", "pgassignment")) {
            print_simple_box("Note: the pgassignment module will soon be deleted from CVS!  Go to the new 'Manage Modules' page and DELETE IT from your system", "center", "50%", "$THEME->cellheading", "20", "noticebox");
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
        execute_sql(" CREATE INDEX {$CFG->prefix}log_timecoursemoduleaction_idx ON {$CFG->prefix}log (time,course,module,action) ");
        execute_sql(" CREATE INDEX {$CFG->prefix}user_students_courseuserid_idx ON {$CFG->prefix}user_students (course,userid) ");
        execute_sql(" CREATE INDEX {$CFG->prefix}user_teachers_courseuserid_idx ON {$CFG->prefix}user_teachers (course,userid) ");
    }

    if ($oldversion < 2003072802) {
        table_column("course_categories", "", "description", "text", "", "", "");
        table_column("course_categories", "", "parent", "integer", "10", "unsigned");
        table_column("course_categories", "", "sortorder", "integer", "10", "unsigned");
        table_column("course_categories", "", "courseorder", "text", "", "", "");
        table_column("course_categories", "", "visible", "integer", "1", "unsigned", "1");
        table_column("course_categories", "", "timemodified", "integer", "10", "unsigned");
    }

    if ($oldversion < 2003080400) {
        notify("If the following command fails you may want to change the type manually, from TEXT to INTEGER.  Moodle should keep working even if you don't.");
        table_column("course_categories", "courseorder", "courseorder", "integer", "10", "unsigned");
        table_column("course", "", "sortorder", "integer", "10", "unsigned", "0", "", "category");
    }

    if ($oldversion < 2003081502) {
        execute_sql(" CREATE TABLE {$CFG->prefix}scale (
                         id SERIAL PRIMARY KEY,
                         courseid integer NOT NULL default '0',
                         userid integer NOT NULL default '0',
                         name varchar(255) NOT NULL default '',
                         scale text,
                         description text,
                         timemodified integer NOT NULL default '0'
                      )");
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

    if ($oldversion < 2003080700) {
        notify("Cleaning up categories and course ordering...");
        if ($categories = get_categories()) {
            foreach ($categories as $category) {
                fix_course_sortorder($category->id);
            }
        }
    }


    if ($oldversion < 2003082001) {
        table_column("course", "", "showgrades", "integer", "2", "unsigned", "1", "", "format");
    }

    if ($oldversion < 2003082101) {
        execute_sql(" CREATE INDEX {$CFG->prefix}course_category_idx ON {$CFG->prefix}course (category) ");
    }
    if ($oldversion < 2003082702) {
        execute_sql(" INSERT INTO {$CFG->prefix}log_display (module, action, mtable, field) VALUES ('course', 'user report', 'user', 'CONCAT(firstname,\" \",lastname)') ");
    }

    if ($oldversion < 2003091000) {
        # Old field that was never added!
        table_column("course", "", "showrecent", "integer", "10", "unsigned", "1", "", "numsections");
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
        execute_sql("CREATE TABLE {$CFG->prefix}groups (
                        id SERIAL PRIMARY KEY,
                        courseid integer NOT NULL default '0',
                        name varchar(255) NOT NULL default '',
                        description text,
                        lang varchar(10) NOT NULL default '',
                        picture integer NOT NULL default '0',
                        timecreated integer NOT NULL default '0',
                        timemodified integer NOT NULL default '0'
                     )");
    
        execute_sql("CREATE INDEX {$CFG->prefix}groups_idx ON {$CFG->prefix}groups (courseid) ");
    
        execute_sql("CREATE TABLE {$CFG->prefix}groups_members (
                        id SERIAL PRIMARY KEY,
                        groupid integer NOT NULL default '0',
                        userid integer NOT NULL default '0',
                        timeadded integer NOT NULL default '0'
                     )");
      
        execute_sql("CREATE INDEX {$CFG->prefix}groups_members_idx ON {$CFG->prefix}groups_members (groupid) ");
    }

    if ($oldversion < 2003122600) {
        table_column("course", "", "groupmode", "integer", "4", "unsigned", "0", "", "visible");
        table_column("course", "", "groupmodeforce", "integer", "4", "unsigned", "0", "", "groupmode");
    }

    if ($oldversion < 2004010900) {
        table_column("course_modules", "", "groupmode", "integer", "4", "unsigned", "0", "", "visible");
    }

    if ($oldversion < 2004011700) {
        modify_database("", "CREATE TABLE prefix_event (
                                id SERIAL PRIMARY KEY,
                                name varchar(255) NOT NULL default '',
                                description text,
                                courseid integer NOT NULL default '0',
                                groupid integer NOT NULL default '0',
                                userid integer NOT NULL default '0',
                                modulename varchar(20) NOT NULL default '',
                                instance integer NOT NULL default '0',
                                eventtype varchar(20) NOT NULL default '',
                                timestart integer NOT NULL default '0',
                                timeduration integer NOT NULL default '0',
                                timemodified integer NOT NULL default '0'
                             ); ");

        modify_database("", "CREATE INDEX prefix_event_courseid_idx ON prefix_event (courseid);");
        modify_database("", "CREATE INDEX prefix_event_userid_idx ON prefix_event (userid);");
    }


    if ($oldversion < 2004012800) {
        modify_database("", "CREATE TABLE prefix_user_preferences (
                                id SERIAL PRIMARY KEY,
                                userid integer NOT NULL default '0',
                                name varchar(50) NOT NULL default '',
                                value varchar(255) NOT NULL default ''
                             ); ");

        modify_database("", "CREATE INDEX prefix_user_preferences_useridname_idx ON prefix_user_preferences (userid,name);");
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
        modify_database("", "CREATE TABLE prefix_cache_text (
                                id SERIAL PRIMARY KEY,
                                md5key varchar(32) NOT NULL default '',
                                formattedtext text,
                                timemodified integer NOT NULL default '0'
                             );");
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
        modify_database("", "CREATE TABLE prefix_cache_filters (
                                id SERIAL PRIMARY KEY,
                                filter varchar(32) NOT NULL default '',
                                version integer NOT NULL default '0',
                                md5key varchar(32) NOT NULL default '',
                                rawtext text,
                                timemodified integer NOT NULL default '0'
                             );");

        modify_database("", "CREATE INDEX prefix_cache_filters_filtermd5key_idx ON prefix_cache_filters (filter,md5key);");
        modify_database("", "CREATE INDEX prefix_cache_text_md5key_idx ON prefix_cache_text (md5key);");
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
        #table_column("course", "", "hiddentopics", "integer", "1", "unsigned", "0", "not null", "visible");
        #See 'hiddensections' further down
    }

    if ($oldversion < 2004042702) {     /// Add a format field for the description 
        table_column("event", "", "format", "integer", "4", "unsigned", "0", "not null", "description");
    }

    if ($oldversion < 2004043001) {     /// Add hiddentopics field to control hidden topics behaviour
        table_column("course", "", "hiddensections", "integer", "2", "unsigned", "0", "not null", "visible");
    }
    
    if ($oldversion < 2004050400) {     /// add a visible field for events
        table_column("event", "", "visible", "smallint", "1", "", "1", "not null", "timeduration");
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
