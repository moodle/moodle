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
                @chown("$CFG->dataroot/$file", $CFG->directorypermissions);
            }
        }
    }

	
    return true;
}

?>
