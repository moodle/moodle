<?php //$Id$
    //This script is used to configure and execute the backup proccess.

    //Define some globals for all the script

    require_once ("../config.php");
    require_once ("lib.php");
    require_once ("backuplib.php");
    require_once ("$CFG->libdir/blocklib.php");
    require_once ("$CFG->libdir/adminlib.php");

    $id = optional_param( 'id' );       // course id
    $to = optional_param( 'to' ); // id of course to import into afterwards.
    $cancel = optional_param( 'cancel' );
    $launch = optional_param( 'launch' );


    if (!empty($id)) {
        require_login($id);
        if (!has_capability('moodle/site:backup', get_context_instance(CONTEXT_COURSE, $id))) {
            error("You need to be a teacher or admin user to use this page.", "$CFG->wwwroot/login/index.php");
        }
    } else {
        require_login();
        if (!has_capability('moodle/site:backup', get_context_instance(CONTEXT_SYSTEM))) {
            error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
        }
    }

    if (!empty($to)) {
        if (!has_capability('moodle/site:backup', get_context_instance(CONTEXT_COURSE, $to))) {
            error("You need to be a teacher or admin user to use this page.", "$CFG->wwwroot/login/index.php");
        }
    }

    //Check site
    if (!$site = get_site()) {
        error("Site not found!");
    }

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();

    //Check backup_version
    if ($id) {
        $linkto = "backup.php?id=".$id.((!empty($to)) ? '&to='.$to : '');
    } else {
        $linkto = "backup.php";
    }
    upgrade_backup_db($linkto);

    //Get strings
    if (empty($to)) {
        $strcoursebackup = get_string("coursebackup");
    }
    else {
        $strcoursebackup = get_string('importdata');
    }
    $stradministration = get_string("administration");

    //If cancel has been selected, go back to course main page (bug 2817)
    if ($cancel) {
        if ($id) {
            $redirecto = $CFG->wwwroot . '/course/view.php?id=' . $id; //Course page
        } else {
            $redirecto = $CFG->wwwroot.'/';
        }
        redirect ($redirecto, get_string('backupcancelled')); //Site page
        exit;
    }

    //If no course has been selected, show a list of available courses

    $navlinks = array();
    if (!$id) {
        $navlinks[] = array('name' => $stradministration, 'link' => "$CFG->wwwroot/$CFG->admin/index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strcoursebackup, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$site->shortname: $strcoursebackup", $site->fullname, $navigation);

        if ($courses = get_courses('all','c.shortname','c.id,c.shortname,c.fullname')) {
            print_heading(get_string("choosecourse"));
            print_simple_box_start("center");
            foreach ($courses as $course) {
                echo '<a href="backup.php?id='.$course->id.'">'.format_string($course->fullname).' ('.format_string($course->shortname).')</a><br />'."\n";
            }
            print_simple_box_end();
        } else {
            print_heading(get_string("nocoursesyet"));
            print_continue("$CFG->wwwroot/$CFG->admin/index.php");
        }
        print_footer();
        exit;
    }

    //Get and check course
    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    //Print header
    if (has_capability('moodle/site:backup', get_context_instance(CONTEXT_SYSTEM))) {
        $navlinks[] = array('name' => $stradministration, 'link' => "$CFG->wwwroot/$CFG->admin/index.php", 'type' => 'misc');
        $navlinks[] = array('name' => $strcoursebackup, 'link' => 'backup.php', 'type' => 'misc');
        $navlinks[] = array('name' => "$course->fullname ($course->shortname)", 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$site->shortname: $strcoursebackup", $site->fullname, $navigation);
    } else {
        $navlinks[] = array('name' => $course->fullname, 'link' => "$CFG->wwwroot/course/view.php?id=$course->id", 'type' => 'misc');
        $navlinks[] = array('name' => $strcoursebackup, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header("$course->shortname: $strcoursebackup", $course->fullname, $navigation);
    }

    //Print form
    print_heading(format_string("$strcoursebackup: $course->fullname ($course->shortname)"));
    print_simple_box_start("center");

    //Adjust some php variables to the execution of this script
    @ini_set("max_execution_time","3000");
    raise_memory_limit("192M");

    //Call the form, depending the step we are
    if (!$launch or !data_submitted() or !confirm_sesskey()) {
        // if we're at the start, clear the cache of prefs
        if (isset($SESSION->backupprefs[$course->id])) {
            unset($SESSION->backupprefs[$course->id]);
        }
        include_once("backup_form.html");
    } else if ($launch == "check") {
        include_once("backup_check.html");
    } else if ($launch == "execute") {
        include_once("backup_execute.html");
    }
    print_simple_box_end();

    //Print footer
    print_footer();
?>
