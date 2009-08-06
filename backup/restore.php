<?php //$Id$
    //This script is used to configure and execute the restore proccess.

    //Define some globals for all the script

    //Units used
    require_once("../config.php");
    require_once("../lib/xmlize.php");
    require_once("../course/lib.php");
    require_once("lib.php");
    require_once("restorelib.php");
    require_once("bb/restore_bb.php");
    require_once("$CFG->libdir/wiki_to_markdown.php" );
    require_once("$CFG->libdir/adminlib.php");

    //Optional
    $id = optional_param( 'id' );
    $file = optional_param( 'file' );
    $cancel = optional_param( 'cancel' );
    $launch = optional_param( 'launch' );
    $to = optional_param( 'to' );
    $method = optional_param( 'method' );
    $backup_unique_code = optional_param('backup_unique_code',0,PARAM_INT);

    //Get and check course
    if (! $course = $DB->get_record("course", array("id"=>$id))) {
        print_error('invalidcourseid');
    }
    // To some reasons, course_startdateoffset value was lost during restoring
    // See MDL-17469
    if (!empty($course->startdate) && !empty($SESSION->course_header->course_startdate)) {
        $SESSION->restore->course_startdateoffset = $course->startdate - $SESSION->course_header->course_startdate;
    } else {
        $SESSION->restore->course_startdateoffset = 0;
    }

    //Check login
    require_login();

/// With method=manual, we come from the FileManager so we delete all the backup/restore/import session structures
    if ($method == 'manual') {
        if (isset($SESSION->course_header)) {
            unset ($SESSION->course_header);
        }
        if (isset($SESSION->info)) {
            unset ($SESSION->info);
        }
        if (isset($SESSION->backupprefs)) {
            unset ($SESSION->backupprefs);
        }
        if (isset($SESSION->restore)) {
            unset ($SESSION->restore);
        }
        if (isset($SESSION->import_preferences)) {
            unset ($SESSION->import_preferences);
        }
    }

    if (!$to && isset($SESSION->restore->restoreto) && isset($SESSION->restore->importing) && isset($SESSION->restore->course_id)) {
        $to = $SESSION->restore->course_id;
    }

    $loginurl = get_login_url();

    if (!empty($id)) {
        require_login($id);
        if (!has_capability('moodle/site:restore', get_context_instance(CONTEXT_COURSE, $id))) {
            if (empty($to)) {
                print_error("cannotuseadminadminorteacher", '', $loginurl);
            } else {
                if (!has_capability('moodle/site:restore', get_context_instance(CONTEXT_COURSE, $to))
                    && !has_capability('moodle/site:import',  get_context_instance(CONTEXT_COURSE, $to))) {
                    print_error("cannotuseadminadminorteacher", '', $loginurl);
                }
            }
        }
    } else {
        if (!has_capability('moodle/site:restore', get_context_instance(CONTEXT_SYSTEM))) {
            print_error("cannotuseadmin", '', $loginurl);
        }
    }

    //Check site
    if (!$site = get_site()) {
        print_error("cannotfindsite");
    }

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();

    //Get strings
    if (empty($to)) {
        $strcourserestore = get_string("courserestore");
    } else {
        $strcourserestore = get_string("importdata");
    }
    $stradministration = get_string("administration");

    //If no file has been selected from the FileManager, inform and end
    $navlinks = array();
    $navlinks[] = array('name' => $stradministration, 'link' => "$CFG->wwwroot/$CFG->admin/index.php", 'type' => 'misc');
    $navlinks[] = array('name' => $strcourserestore, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    if (!$file) {
        print_header("$site->shortname: $strcourserestore", $site->fullname, $navigation);
        echo $OUTPUT->heading(get_string("nofilesselected"));
        print_continue("$CFG->wwwroot/$CFG->admin/index.php");
        print_footer();
        exit;
    }

    //If cancel has been selected, inform and end
    if ($cancel) {
        print_header("$site->shortname: $strcourserestore", $site->fullname, $navigation);
        echo $OUTPUT->heading(get_string("restorecancelled"));
        print_continue("$CFG->wwwroot/course/view.php?id=".$id);
        print_footer();
        exit;
    }

    //We are here, so me have a file.

    //Print header
    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        $navlinks[] = array('name' => basename($file), 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header("$site->shortname: $strcourserestore", $site->fullname, $navigation);
    } else {
        $navlinks = array();
        $navlinks[] = array('name' => $course->shortname, 'link' => "$CFG->wwwroot/course/view.php?id=$course->id", 'type' => 'misc');
        $navlinks[] = array('name' => $strcourserestore, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);
        print_header("$course->shortname: $strcourserestore", $course->fullname, $navigation);
    }
    //Print form
    echo $OUTPUT->heading("$strcourserestore".((empty($to) ? ': '.basename($file) : '')));
    print_simple_box_start('center');

    //Adjust some php variables to the execution of this script
    @ini_set("max_execution_time","3000");
    if (empty($CFG->extramemorylimit)) {
        raise_memory_limit('128M');
    } else {
        raise_memory_limit($CFG->extramemorylimit);
    }

    //Call the form, depending the step we are

    if (!$launch) {
        include_once("restore_precheck.html");
    } else if ($launch == "form") {
        if (!empty($SESSION->restore->importing)) {
            // set up all the config stuff and skip asking the user about it.
            restore_setup_for_check($SESSION->restore,$backup_unique_code);
            include_once("restore_execute.html");
        } else {
            include_once("restore_form.html");
        }
    } else if ($launch == "check") {
        include_once("restore_check.html");
        //To avoid multiple restore executions...
        $SESSION->cancontinue = true;
    } else if ($launch == "execute") {
        //Prevent multiple restore executions...
        if (empty($SESSION->cancontinue)) {
            print_error('multiplerestorenotallow');
        }
        //Unset this for the future
        unset($SESSION->cancontinue);
        include_once("restore_execute.html");
    }
    print_simple_box_end();

    //Print footer
    print_footer();

?>
