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
        fix_course_sortorder();
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
        table_column('user', "", 'maildigest', 'smallint', '1', '', '0', 'not null', 'mailformat');
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

    /* Just commenteed unused fields out
    if ($oldversion < 2004090300) { // Add guid-field used in user syncronization
            table_column('user', '', 'guid', 'varchar', '128', '', '', '', 'auth');
            execute_sql("CREATE INDEX {$CFG->prefix}user_auth_guid_idx ON {$CFG->prefix}user (auth, guid)"); 
    }
    */

    if ($oldversion < 2004091900) {  //Modify idnumber to hold longer keys 
        set_field('user', 'auth', 'manual', 'username', 'guest');
        table_column('user', 'idnumber', 'idnumber', 'varchar', '64', '', '', '', '');
        execute_sql("DROP INDEX {$CFG->prefix}user_idnumber_idx ;",false);// added in case of conflicts with upgrade from 14stable
        execute_sql("DROP INDEX {$CFG->prefix}user_auth_idx ;",false);// added in case of conflicts with upgrade from 14stable
        execute_sql("CREATE INDEX {$CFG->prefix}user_idnumber_idx ON {$CFG->prefix}user (idnumber)"); 
        execute_sql("CREATE INDEX {$CFG->prefix}user_auth_idx ON {$CFG->prefix}user (auth)"); 
    }

    if ($oldversion < 2004092000) { //redoing this just to be sure that column type is text (postgres type changes didnt work when this was done first time)
        table_column("config", "value", "value", "text", "", "", "");
    }

    if ($oldversion < 2004093001) { // add new table for sessions storage
        execute_sql(" CREATE TABLE {$CFG->prefix}sessions (
                          sesskey char(32) PRIMARY KEY,
                          expiry integer NOT null,
                          expireref varchar(64),
                          data text NOT null
                      );");

        execute_sql(" CREATE INDEX {$CFG->prefix}sessions_expiry_idx ON {$CFG->prefix}sessions (expiry)");
    }

    if ($oldversion < 2004111500) {  // Update any users/courses using wrongly-named lang pack
        execute_sql("UPDATE {$CFG->prefix}user SET lang = 'mi_nt' WHERE lang = 'ma_nt'");
        execute_sql("UPDATE {$CFG->prefix}course SET lang = 'mi_nt' WHERE lang = 'ma_nt'");
    }

    if ($oldversion < 2004111700) { // add indexes- drop them first silently to avoid conflicts when upgrading.
        execute_sql("DROP INDEX {$CFG->prefix}course_idnumber_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}course_shortname_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_students_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_teachers_userid_idx;",false);

        modify_database("","CREATE INDEX {$CFG->prefix}course_idnumber_idx ON {$CFG->prefix}course (idnumber);" );
        modify_database("","CREATE INDEX {$CFG->prefix}course_shortname_idx ON {$CFG->prefix}course (shortname);" );
        modify_database("","CREATE INDEX {$CFG->prefix}user_students_userid_idx ON {$CFG->prefix}user_students (userid);");
        modify_database("","CREATE INDEX {$CFG->prefix}user_teachers_userid_idx ON {$CFG->prefix}user_teachers (userid);");
    }
 
    if ($oldversion < 2004111700) { // add an index to event for timestart and timeduration- drop them first silently to avoid conflicts when upgrading.
        execute_sql("DROP INDEX {$CFG->prefix}event_timestart_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}event_timeduration_idx;",false);

        modify_database('','CREATE INDEX prefix_event_timestart_idx ON prefix_event (timestart);');
        modify_database('','CREATE INDEX prefix_event_timeduration_idx ON prefix_event (timeduration);');
    }

    if ($oldversion < 2004117000) { // add an index on the groups_members table- drop them first silently to avoid conflicts when upgrading.
        execute_sql("DROP INDEX {$CFG->prefix}groups_members_userid_idx;",false);

        modify_database('','CREATE INDEX prefix_groups_members_userid_idx ON prefix_groups_members (userid);');
    }
    
    if ($oldversion < 2004111700) { //add indexes on modules and course_modules- drop them first silently to avoid conflicts when upgrading.
        execute_sql("DROP INDEX {$CFG->prefix}course_modules_visible_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}course_modules_course_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}course_modules_module_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}course_modules_instance_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}course_modules_deleted_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}modules_name_idx;",false);

        modify_database('','CREATE INDEX prefix_course_modules_visible_idx ON prefix_course_modules (visible);');
        modify_database('','CREATE INDEX prefix_course_modules_course_idx ON prefix_course_modules (course);');
        modify_database('','CREATE INDEX prefix_course_modules_module_idx ON prefix_course_modules (module);');
        modify_database('','CREATE INDEX prefix_course_modules_instance_idx ON prefix_course_modules (instance);');
        modify_database('','CREATE INDEX prefix_course_modules_deleted_idx ON prefix_course_modules (deleted);');
        modify_database('','CREATE INDEX prefix_modules_name_idx ON prefix_modules (name);');
    }
    
    if ($oldversion < 2004111700) { // add an index on user students timeaccess (used for sorting)- drop them first silently to avoid conflicts when upgrading
        execute_sql("DROP INDEX {$CFG->prefix}user_students_timeaccess_idx;",false);

        modify_database('','CREATE INDEX prefix_user_students_timeaccess_idx ON prefix_user_students (timeaccess);');
    }
    
    if ($oldversion < 2004111700) { //add indexes on faux foreign keys  - drop them first silently to avoid conflicts when upgrading.
        execute_sql("DROP INDEX {$CFG->prefix}course_sections_coursesection_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}scale_courseid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}user_admins_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_coursecreators_userid_idx;",false); 

        modify_database('','CREATE INDEX prefix_course_sections_coursesection_idx ON prefix_course_sections (course,section);');
        modify_database('','CREATE INDEX prefix_scale_courseid_idx ON prefix_scale (courseid);');
        modify_database('','CREATE INDEX prefix_user_admins_userid_idx ON prefix_user_admins (userid);');
        modify_database('','CREATE INDEX prefix_user_coursecreators_userid_idx ON prefix_user_coursecreators (userid);');
    }
   
    if ($oldversion < 2004111700) { // make new indexes on user table.
        fix_course_sortorder(0,0,1);

        execute_sql("DROP INDEX {$CFG->prefix}course_category_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}course_category_sortorder_uk;",false);
        modify_database('', "CREATE UNIQUE INDEX prefix_course_category_sortorder_uk ON prefix_course(category,sortorder)"); 

        execute_sql("DROP INDEX {$CFG->prefix}user_deleted_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_confirmed_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_firstname_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_lastname_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}user_city_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}user_country_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}user_lastaccess_idx;",false);

        modify_database("","CREATE INDEX prefix_user_deleted_idx ON prefix_user (deleted)");
        modify_database("","CREATE INDEX prefix_user_confirmed_idx ON prefix_user (confirmed)");
        modify_database("","CREATE INDEX prefix_user_firstname_idx ON prefix_user (firstname)");
        modify_database("","CREATE INDEX prefix_user_lastname_idx ON prefix_user (lastname)");
        modify_database("","CREATE INDEX prefix_user_city_idx ON prefix_user (city)");
        modify_database("","CREATE INDEX prefix_user_country_idx ON prefix_user (country)");
        modify_database("","CREATE INDEX prefix_user_lastaccess_idx ON prefix_user (lastaccess)");
    }

    if ($oldversion < 2004111700) { // one more index for email (for sorting)
        execute_sql("DROP INDEX {$CFG->prefix}user_email_idx;",false);

        modify_database('','CREATE INDEX prefix_user_email_idx ON prefix_user (email);');
     }

    if ($oldversion < 2004112200) { // new 'enrol' field for enrolment tables
        table_column('user_students', '', 'enrol', 'varchar', '20', '', '', 'not null');
        table_column('user_teachers', '', 'enrol', 'varchar', '20', '', '', 'not null');
        modify_database("","CREATE INDEX {$CFG->prefix}user_students_enrol_idx ON {$CFG->prefix}user_students (enrol);");
        modify_database("","CREATE INDEX {$CFG->prefix}user_teachers_enrol_idx ON {$CFG->prefix}user_teachers (enrol);");
    } 

    if ($oldversion < 2004112300) { // update log display to use correct postgres friendly sql
        execute_sql("UPDATE {$CFG->prefix}log_display SET field='firstname||\' \'||lastname' WHERE module='user' AND action='view' AND mtable='user'");
        execute_sql("UPDATE {$CFG->prefix}log_display SET field='firstname||\' \'||lastname' WHERE module='course' AND action='user report' AND mtable='user'");
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

        // drop some indexes quietly -- they may or may not exist depending on what version 
        // the user upgrades from 
        execute_sql("DROP INDEX {$CFG->prefix}user_students_courseuserid_idx ", false);
        execute_sql("DROP INDEX {$CFG->prefix}user_students_courseuserid_uk  ", false);        
        modify_database('','CREATE UNIQUE INDEX prefix_user_students_courseuserid_uk ON prefix_user_students (course,userid);');        

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

        // drop some indexes quietly -- they may or may not exist depending on what version 
        // the user upgrades from 
        execute_sql("DROP INDEX {$CFG->prefix}user_teachers_courseuserid_idx ", false);
        execute_sql("DROP INDEX {$CFG->prefix}user_teachers_courseuserid_uk  ", false);
        modify_database('','CREATE UNIQUE INDEX prefix_user_teachers_courseuserid_uk ON prefix_user_teachers (course,userid);');        
    } 
    
    if ($oldversion < 2004112401) {
        // some postgres databases may have a non-unique index mislabeled unique.
        fix_course_sortorder(0,0,1);
        execute_sql("DROP INDEX {$CFG->prefix}course_category_sortorder_uk  ", false);
        execute_sql("DROP INDEX {$CFG->prefix}course_category_idx  ", false);
        modify_database('', "CREATE UNIQUE INDEX prefix_course_category_sortorder_uk ON prefix_course(category,sortorder);");
        
        // odd! username was missing its unique index!
        // first silently drop it just in case...
        execute_sql("ALTER TABLE {$CFG->prefix}user DROP CONSTRAINT {$CFG->prefix}user_username_uk;", false);   
        execute_sql("DROP INDEX {$CFG->prefix}user_username_uk", false);
        modify_database('', "CREATE UNIQUE INDEX prefix_user_username_uk ON prefix_user (username);");
        
    } 

    if ($oldversion < 2004112900) {
        table_column('user', '', 'policyagreed', 'integer', '1', 'unsigned', '0', 'not null', 'confirmed');
    }

    if ($oldversion < 2004121400) {
        table_column('groups', '', 'password', 'varchar', '50', '', '', 'not null', 'description');
    }

    if ($oldversion < 2004121600) {
        modify_database('',"CREATE TABLE prefix_dst_preset (
                                id SERIAL PRIMARY KEY,
                                name varchar(48) NOT NULL default '',
                                apply_offset integer NOT NULL default '0',
                                activate_index integer NOT NULL default '1',
                                activate_day integer NOT NULL default '1',
                                activate_month integer NOT NULL default '1',
                                activate_time char(5) NOT NULL default '03:00',
                                deactivate_index integer NOT NULL default '1',
                                deactivate_day integer NOT NULL default '1',
                                deactivate_month integer NOT NULL default '2',
                                deactivate_time char(5) NOT NULL default '03:00',
                                last_change integer NOT NULL default '0',
                                next_change integer NOT NULL default '0',
                                current_offset integer NOT NULL default '0'
                             );");
    }

    if ($oldversion < 2004122800) {
        execute_sql("DROP TABLE {$CFG->prefix}message", false);
        execute_sql("DROP TABLE {$CFG->prefix}message_read", false);
        execute_sql("DROP TABLE {$CFG->prefix}message_contacts", false);

        execute_sql("DROP INDEX {$CFG->prefix}message_useridfrom_idx", false);
        execute_sql("DROP INDEX {$CFG->prefix}message_useridto_idx", false);
        execute_sql("DROP INDEX {$CFG->prefix}message_read_useridfrom_idx", false);
        execute_sql("DROP INDEX {$CFG->prefix}message_read_useridto_idx", false);
        execute_sql("DROP INDEX {$CFG->prefix}message_contacts_useridcontactid_idx", false);

        modify_database('',"CREATE TABLE prefix_message (
                               id SERIAL PRIMARY KEY,
                               useridfrom integer NOT NULL default '0',
                               useridto integer NOT NULL default '0',
                               message text,
                               timecreated integer NOT NULL default '0',
                               messagetype varchar(50) NOT NULL default ''
                            );

                            CREATE INDEX prefix_message_useridfrom_idx ON prefix_message (useridfrom);
                            CREATE INDEX prefix_message_useridto_idx ON prefix_message (useridto);

                            CREATE TABLE prefix_message_read (
                               id SERIAL PRIMARY KEY,
                               useridfrom integer NOT NULL default '0',
                               useridto integer NOT NULL default '0',
                               message text,
                               timecreated integer NOT NULL default '0',
                               timeread integer NOT NULL default '0',
                               messagetype varchar(50) NOT NULL default '',
                               mailed integer NOT NULL default '0'
                            );

                            CREATE INDEX prefix_message_read_useridfrom_idx ON prefix_message_read (useridfrom);
                            CREATE INDEX prefix_message_read_useridto_idx ON prefix_message_read (useridto);
                            ");
      
        modify_database('',"CREATE TABLE prefix_message_contacts (
                               id SERIAL PRIMARY KEY,
                               userid integer NOT NULL default '0',
                               contactid integer NOT NULL default '0',
                               blocked integer NOT NULL default '0'
                            );

                            CREATE INDEX prefix_message_contacts_useridcontactid_idx ON prefix_message_contacts (userid,contactid);
                            ");

        modify_database('',"INSERT INTO prefix_log_display VALUES ('message', 'write', 'user', 'firstname||\' \'||lastname');
                            INSERT INTO prefix_log_display VALUES ('message', 'read', 'user', 'firstname||\' \'||lastname');
                            ");
    }

    if ($oldversion < 2004122801) {
        table_column('message', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'message');
        table_column('message_read', '', 'format', 'integer', '4', 'unsigned', '0', 'not null', 'message');
    }
       
                                
    if ($oldversion < 2005010100) {
        modify_database('',"INSERT INTO prefix_log_display VALUES ('message', 'add contact', 'user', 'firstname||\' \'||lastname');
                            INSERT INTO prefix_log_display VALUES ('message', 'remove contact', 'user', 'firstname||\' \'||lastname');
                            INSERT INTO prefix_log_display VALUES ('message', 'block contact', 'user', 'firstname||\' \'||lastname');
                            INSERT INTO prefix_log_display VALUES ('message', 'unblock contact', 'user', 'firstname||\' \'||lastname');
                            ");
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

    if ($oldversion < 2005012500) { // add new table for meta courses.
        /*
        modify_database("","CREATE TABLE prefix_meta_course (
	         id SERIAL primary key,
	         parent_course integer NOT NULL,
	         child_course integer NOT NULL
             );");

        modify_database("","CREATE INDEX prefix_meta_course_parent_idx ON prefix_meta_course (parent_course);");
        modify_database("","CREATE INDEX prefix_meta_course_child_idx ON prefix_meta_course (child_course);");
        table_column('course','','meta_course','integer','1','','0','not null');
        */ // taking this OUT for upgrade from 1.4 to 1.5 (those tracking head will have already seen it)
    }

    if ($oldversion < 2005012501) { //fix table names for consistency
        execute_sql("DROP TABLE {$CFG->prefix}meta_course",false); // drop silently
        execute_sql("ALTER TABLE {$CFG->prefix}course DROP COLUMN meta_course",false); // drop silently
        
        modify_database("","CREATE TABLE prefix_course_meta (
	         id SERIAL primary key,
	         parent_course integer NOT NULL,
	         child_course integer NOT NULL
             );");

        modify_database("","CREATE INDEX prefix_course_meta_parent_idx ON prefix_course_meta (parent_course);");
        modify_database("","CREATE INDEX prefix_course_meta_child_idx ON prefix_course_meta (child_course);");
        table_column('course','','metacourse','integer','1','','0','not null');
    }

    if ($oldversion < 2005020100) {
        fix_course_sortorder(0, 1, 1);
    } 

    if ($oldversion < 2005021000) {     // New fields for theme choices
        table_column('course', '', 'theme', 'varchar', '50', '', '', '', 'lang');
        table_column('groups', '', 'theme', 'varchar', '50', '', '', '', 'lang');
        table_column('user',   '', 'theme', 'varchar', '50', '', '', '', 'lang');

        set_config('theme', 'standardwhite');         // Reset to a known good theme 
    }

    if ($oldversion < 2005021700) {
        table_column('user', '', 'dstpreset', 'int', '10', '', '0', 'not null', 'timezone');
    }

    if ($oldversion < 2005021800) {
        modify_database("","CREATE TABLE adodb_logsql (
                              created timestamp NOT NULL,
                              sql0 varchar(250) NOT NULL,
                              sql1 text NOT NULL,
                              params text NOT NULL,
                              tracer text NOT NULL,
                              timer decimal(16,6) NOT NULL
                           );");
    }

    if ($oldversion < 2005022400) {
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
        execute_sql('UPDATE '.$CFG->prefix.'user SET timezonename = \'\'');
    }


    if ($oldversion < 2005032600) {
        execute_sql('DROP TABLE '.$CFG->prefix.'dst_preset', false);
        modify_database('',"CREATE TABLE prefix_timezone (
                              id SERIAL PRIMARY KEY,
                              name varchar(100) NOT NULL default '',
                              year integer NOT NULL default '0',
                              rule varchar(20) NOT NULL default '',
                              gmtoff integer NOT NULL default '0',
                              dstoff integer NOT NULL default '0',
                              dst_month integer NOT NULL default '0',
                              dst_startday integer NOT NULL default '0',
                              dst_weekday integer NOT NULL default '0',
                              dst_skipweeks integer NOT NULL default '0',
                              dst_time varchar(5) NOT NULL default '00:00',
                              std_month integer NOT NULL default '0',
                              std_startday integer NOT NULL default '0',
                              std_weekday integer NOT NULL default '0',
                              std_skipweeks integer NOT NULL default '0',
                              std_time varchar(5) NOT NULL default '00:00'
                            );");
    }

    if ($oldversion < 2005032800) {
        modify_database('',"CREATE TABLE prefix_grade_category (
                              id SERIAL PRIMARY KEY,
                              name varchar(64) default NULL,
                              courseid integer NOT NULL default '0',
                              drop_x_lowest integer NOT NULL default '0',
                              bonus_points integer NOT NULL default '0',
                              hidden integer NOT NULL default '0',
                              weight decimal(4,2) default '0.00'
                            );");
        
        modify_database('',"CREATE INDEX prefix_grade_category_courseid_idx ON prefix_grade_category (courseid);");
        
        modify_database('',"CREATE TABLE prefix_grade_exceptions (
                              id SERIAL PRIMARY KEY,
                              courseid integer  NOT NULL default '0',
                              grade_itemid integer  NOT NULL default '0',
                              userid integer  NOT NULL default '0'
                            );");
        
        modify_database('',"CREATE INDEX prefix_grade_exceptions_courseid_idx ON prefix_grade_exceptions (courseid);");
        
        
        modify_database('',"CREATE TABLE prefix_grade_item (
                              id SERIAL PRIMARY KEY,
                              courseid integer default NULL,
                              category integer default NULL,
                              modid integer default NULL,
                              cminstance integer default NULL,
                              scale_grade float(11) default '1.0000000000',
                              extra_credit integer NOT NULL default '0',
                              sort_order integer  NOT NULL default '0'
                            );");
        
        modify_database('',"CREATE INDEX prefix_grade_item_courseid_idx ON prefix_grade_item (courseid);");
        
        modify_database('',"CREATE TABLE prefix_grade_letter (
                              id SERIAL PRIMARY KEY,
                              courseid integer NOT NULL default '0',
                              letter varchar(8) NOT NULL default 'NA',
                              grade_high decimal(4,2) NOT NULL default '100.00',
                              grade_low decimal(4,2) NOT NULL default '0.00'
                            );");

        modify_database('',"CREATE INDEX prefix_grade_letter_courseid_idx ON prefix_grade_letter (courseid);");
        
        modify_database('',"CREATE TABLE prefix_grade_preferences (
                              id SERIAL PRIMARY KEY,
                              courseid integer default NULL,
                              preference integer NOT NULL default '0',
                              value integer NOT NULL default '0'
                            );");
        
        modify_database('',"CREATE UNIQUE INDEX prefix_grade_prefs_courseidpref_uk ON prefix_grade_preferences (courseid,preference);");
    }

    if ($oldversion < 2005033100) {   // Get rid of defunct field from course modules table
         delete_records('course_modules', 'deleted', 1);  // Delete old records we don't need any more
         execute_sql('DROP INDEX '.$CFG->prefix.'course_modules_deleted_idx;');  // Old index
         execute_sql('ALTER TABLE '.$CFG->prefix.'course_modules DROP deleted;');    // Old field
    }

    if ($oldversion < 2005040800) {
        table_column('user', 'timezone', 'timezone', 'varchar', '100', '', '99');
        execute_sql(" ALTER TABLE {$CFG->prefix}user DROP timezonename ");
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
                $message = NULL;
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

    if ($oldversion < 2005051500) {  // Add user tracking prefs field.
        table_column('grade_category', 'weight', 'weight', 'numeric(5,2)', '', '', '0.00', '', '');
    }

    if ($oldversion < 2005052301 ) { // Add config_plugins table
        
        execute_sql("CREATE TABLE {$CFG->prefix}config_plugins (
                        id     SERIAL PRIMARY KEY,
                        plugin varchar(100) NOT NULL default 'core',
                        name   varchar(100) NOT NULL default '',
                        value  text NOT NULL default '',
                        CONSTRAINT {$CFG->prefix}config_plugins_plugin_name_uk UNIQUE (plugin, name)
                     );");

    }

    if ($oldversion < 2005052302) {  // migrate some config items to config_plugins table

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

    if ($oldversion < 2005060221) { // run the online assignment cleanup code
        include($CFG->dirroot.'/'.$CFG->admin.'/oacleanup.php');
        if (function_exists('online_assignment_cleanup')) {
            online_assignment_cleanup();
        }
    }

    return $result;
}

?>
