<?php
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
    $id = optional_param('id', 0, PARAM_INT);
    $file = optional_param( 'file', 0, PARAM_PATH);
    $cancel = optional_param('cancel', '', PARAM_RAW);
    $launch = optional_param( 'launch', '', PARAM_ACTION);
    $to = optional_param('to', '', PARAM_INT);
    $method = optional_param('method', '', PARAM_ACTION);
    $backup_unique_code = optional_param('backup_unique_code',0,PARAM_INT);

    $url = new moodle_url('/backup/restore.php');
    if ($id !== 0) {
        $url->param('id', $id);
    }
    if ($file !== 0) {
        $url->param('file', $file);
    }
    if ($cancel !== '') {
        $url->param('cancel', $cancel);
    }
    if ($launch !== '') {
        $url->param('launch', $launch);
    }
    if ($to !== '') {
        $url->param('to', $to);
    }
    if ($method !== '') {
        $url->param('method', $method);
    }
    if ($backup_unique_code !== 0) {
        $url->param('backup_unique_code', $backup_unique_code);
    }
    $PAGE->set_url($url);

    $site = get_site();

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
        if (!has_capability('moodle/restore:restorecourse', get_context_instance(CONTEXT_COURSE, $id))) {
            if (empty($to)) {
                print_error("cannotuseadminadminorteacher", '', $loginurl);
            } else {
                if (!has_capability('moodle/restore:restorecourse', get_context_instance(CONTEXT_COURSE, $to))
                    && !has_capability('moodle/restore:restoretargetimport',  get_context_instance(CONTEXT_COURSE, $to))) {
                    print_error("cannotuseadminadminorteacher", '', $loginurl);
                }
            }
        }
    } else {
        if (!has_capability('moodle/restore:restorecourse', get_context_instance(CONTEXT_SYSTEM))) {
            print_error("cannotuseadmin", '', $loginurl);
        }
    }

    //Check site
    $site = get_site();

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
    $PAGE->set_title("$site->shortname: $strcourserestore");
    $PAGE->set_heading($site->fullname);
    if (!$file) {
        $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
        $PAGE->navbar->add($strcourserestore);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string("nofilesselected"));
        echo $OUTPUT->continue_button("$CFG->wwwroot/$CFG->admin/index.php");
        echo $OUTPUT->footer();
        exit;
    }

    //If cancel has been selected, inform and end
    if ($cancel) {
        $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
        $PAGE->navbar->add($strcourserestore);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string("restorecancelled"));
        echo $OUTPUT->continue_button("$CFG->wwwroot/course/view.php?id=".$id);
        echo $OUTPUT->footer();
        exit;
    }

    //We are here, so we have a file.

    //Get and check course
    if (! $course = $DB->get_record('course', array('id'=>$id))) {
        error("Course ID was incorrect (can't find it)");
    }

    //Print header
    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        $PAGE->navbar->add(basename($file));
        echo $OUTPUT->header();
    } else {
        $PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', array('id'=>$course->id)));
        $PAGE->navbar->add($strcourserestore);
        echo $OUTPUT->header();
    }
    //Print form
    echo $OUTPUT->heading("$strcourserestore".((empty($to) ? ': '.basename($file) : '')));
    echo $OUTPUT->box_start();

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
            require_sesskey();
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
        require_sesskey();
        include_once("restore_execute.html");
    }
    echo $OUTPUT->box_end();

    //Print footer
    echo $OUTPUT->footer();
