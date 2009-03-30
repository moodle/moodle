<?PHP //$Id$
    require_once ("../config.php");
    require_once ("backup_scheduled.php");
    require_once ("lib.php");
    require_once ("backuplib.php");
    require_once ("$CFG->libdir/blocklib.php");

    require_login();

    require_capability('moodle/site:restore', get_context_instance(CONTEXT_SYSTEM));

    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();

    //Adjust some php variables to the execution of this script
    @ini_set("max_execution_time","3000");
    if (empty($CFG->extramemorylimit)) {
        raise_memory_limit('128M');
    } else {
        raise_memory_limit($CFG->extramemorylimit);
    }

    echo "<pre>\n";

    $status = true;

    $courses = get_records("course");
    foreach ($courses as $course) {
        echo "Start course ". format_string($course->fullname);
        $preferences = schedule_backup_course_configure($course);
        if ($preferences && $status) {
            $status = schedule_backup_course_execute($preferences);
        }
        if ($status && $preferences) {
            echo "End course ". format_string($course->fullname)." OK\n\n";
        } else {
            echo "End course ". format_string($course->fullname)." FAIL\n\n";
        }
    }
?>
