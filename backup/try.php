<?PHP //$Id$
    require_once ("../config.php");
    require_once ("backup_scheduled.php");
    require_once ("lib.php");
    require_once ("backuplib.php");
    require_once ("$CFG->libdir/blocklib.php");

    require_login();

    if (!isadmin()) {
        error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
    }

    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();

    //Adjust some php variables to the execution of this script
    ini_set("max_execution_time","3000");
    ini_set("memory_limit","56M");

    echo "<pre>\n";

    $status = true;

    $courses = get_records("course");
    foreach ($courses as $course) {
        echo "Start course ".$course->fullname;
        $preferences = schedule_backup_course_configure($course);
        if ($preferences && $status) {
            $status = schedule_backup_course_execute($preferences);
        }
        if ($status && $preferences) {
            echo "End course ".$course->fullname." OK\n\n";
        } else {
            echo "End course ".$course->fullname." FAIL\n\n";
        }
    }
?>
