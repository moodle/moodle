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
    global $CFG;
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
	    execute_sql("CREATE TABLE $CFG->prefix_user_coursecreators (
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

    if ($oldversion < 2003042401) {                 
    //  Convert usernames to lowercase
        $users = get_records_sql("SELECT  id, username FROM {$CFG->prefix}user"); 
	    $cerrors = "";
   	    $rarray = array();
	    foreach ($users as $user) {
        	$lcname = trim(moodle_strtolower($user->username));
	        if (in_array($lcname, $rarray)) {
                $cerrors .= $user->id."->".$lcname.'<br/>' ; 
        	}else {
            	    array_push($rarray,$lcname);
        	}
        }
	    /// Do convert or give error message
        if ($cerrors != '') {
        	print "Error: Cannot convert usernames to lowercase. Following usernames would overlap (id->username):<br/> $cerrors . Please resolve overlapping errors."; 
            $result = false;
        }else {
            $cerrors = '';
            print "Checking userdatabase:<br>";
            foreach ($users as $user) {
            	$lcname = trim(moodle_strtolower($user->username));
            	$convert = set_field("user" , "username" , $lcname, "id", $user->id);
            	if (!$convert) {
                    if ($cerrors){
                       $cerrors .= ", ";
                    }   
                    $cerrors .= $item;
            	} else {
                    print ".";
                }   
            }
            if ($cerrors != '') {
                print "There was errors when converting following usernames to lowercase. '$cerrors' . Please maintain your database by hand.";
                $result=false;
            }
        }
    }
    return $result;
}
?>    
