<?PHP  // $Id$
       // backup.php - allows admin to edit all configuration variables for scheduled backups

    require_once("../config.php");
    require_once("../backup/lib.php");
    require_once("../backup/backup_scheduled.php");

    require_login();

    if (!isadmin()) {
        error("Only an admin can use this page");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    //Initialise error variables
    $error = false;
    $sche_destination_error = "";

    /// If data submitted, then process and store.

    if ($config = data_submitted()) {

        //First of all we check that everything is correct
        //Check for trailing slash and backslash in backup_sche_destination
        if (!empty($backup_sche_destination) and 
            (substr($backup_sche_destination,-1) == "/" or substr($backup_sche_destination,-1) == "\\")) {
            $error = true;
            $sche_destination_error = get_string("pathslasherror");
        //Now check that backup_sche_destination dir exists
        } else if (!empty($backup_sche_destination) and
            !is_dir($backup_sche_destination)) {
            $error = true;
            $sche_destination_error = get_string("pathnotexists");
        }

        //We need to do some weekdays conversions prior to continue
        $i = 0;
        $temp = "";
        $a_config = (array)$config;
        while ($i<7) {
            $tocheck = "dayofweek_".$i;
            if (isset($a_config[$tocheck])) {
                $temp .= "1";
            } else {
                $temp .= "0"; 
            }
            unset($a_config[$tocheck]);
            $i++;
        }
        $a_config['backup_sche_weekdays'] = $temp;
        $config = (object)$a_config;
        //weekdays conversions done. Continue

        print_header();
        foreach ($config as $name => $value) {
            backup_set_config($name, $value);
        }

        //And now, we execute schedule_backup_next_execution() for each course in the server to have the next
        //execution time updated automatically everytime it's changed.
        $status = true;
        //get admin
        $admin = get_admin();
        if (!$admin) {
            $status = false;
        }
        //get backup config
        if (! $backup_config =  backup_get_config()) {
            $status = false;
        }
        if ($status) {
            //get courses
            if ($courses = get_records("course")) {
                //For each course, we check (insert, update) the backup_course table
                //with needed data
                foreach ($courses as $course) {
                    //We check if the course exists in backup_course
                    $backup_course = get_record("backup_courses","courseid",$course->id);
                    //If it doesn't exist, create 
                    if (!$backup_course) {
                        $temp_backup_course->courseid = $course->id;
                        $newid = insert_record("backup_courses",$temp_backup_course);
                        //And get it from db
                        $backup_course = get_record("backup_courses","id",$newid);
                    }
                    //Now, calculate next execution of the course
                    $nextstarttime = schedule_backup_next_execution ($backup_course,$backup_config,time(),$admin->timezone);
                    //Save it to db
                    set_field("backup_courses","nextstarttime",$nextstarttime,"courseid",$backup_course->courseid);
                }
            }
        }

        if (!$error) {
            redirect("$CFG->wwwroot/$CFG->admin/index.php", get_string("changessaved"), 1);
            exit;
        }
    }

/// Otherwise print the form.

    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strbackup = get_string("backup");

    print_header("$site->shortname: $strconfiguration: $strbackup", $site->fullname,
                  "<a href=\"index.php\">$stradmin</a> -> ".
                  "<a href=\"configure.php\">$strconfiguration</a> -> ".
                  $strbackup);

    echo "<p align=right><a href=\"../backup/log.php\">".get_string("logs")."</a></p>";

    print_heading($strbackup);

    print_simple_box("<center>".get_string("adminhelpbackup")."</center>", "center", "50%");
    echo "<br />";

    print_simple_box_start("center", "", "$THEME->cellheading");

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        print_simple_box("<font color=\"red\">You need to add XML support to your PHP installation</font>", "center", "70%", "$THEME->cellheading", "20", "noticebox");
    } 
    include ("$CFG->dirroot/backup/config.html");

    print_simple_box_end();

    print_footer();

?>
