<?php
    //This script is used to configure and execute the backup proccess.

    //Define some globals for all the script

    require_once ("../config.php");
    require_once ("lib.php");
    require_once ("backuplib.php");
    require_once ("$CFG->libdir/adminlib.php");

    $id = optional_param('id', 0, PARAM_INT);       // course id
    $to = optional_param('to', 0, PARAM_INT); // id of course to import into afterwards.
    $cancel = optional_param('cancel', '', PARAM_RAW);
    $launch = optional_param('launch', '', PARAM_ACTION);

    $url = new moodle_url('/backup/backup.php');
    if ($id !== 0) {
        $url->param('id', $id);
    }
    if ($to !== 0) {
        $url->param('to', $to);
    }
    if ($launch !== '') {
        $url->param('launch', $launch);
    }
    $PAGE->set_url($url);

    $loginurl = get_login_url();

    if (!empty($id)) {
        require_login($id);
        if (!has_capability('moodle/backup:backupcourse', get_context_instance(CONTEXT_COURSE, $id))) {
            print_error('cannotuseadminadminorteacher', 'error', $loginurl);
        }
    } else {
        require_login();
        if (!has_capability('moodle/backup:backupcourse', get_context_instance(CONTEXT_SYSTEM))) {
            print_error('cannotuseadmin', 'error', $loginurl);
        }
    }

    if (!empty($to)) {
        if (!has_capability('moodle/backup:backupcourse', get_context_instance(CONTEXT_COURSE, $to))) {
            print_error('cannotuseadminadminorteacher', 'error', $loginurl);
        }
    }

    //Check site
    $site = get_site();

    //Check necessary functions exists. Thanks to gregb@crowncollege.edu
    backup_required_functions();

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
    $PAGE->set_title("$site->shortname: $strcoursebackup");
    $PAGE->set_heading($site->fullname);
    if (!$id) {
        $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
        $PAGE->navbar->add($strcoursebackup);
        echo $OUTPUT->header();
        if ($courses = get_courses('all','c.shortname','c.id,c.shortname,c.fullname')) {
            echo $OUTPUT->heading(get_string("choosecourse"));
            echo $OUTPUT->box_start();
            foreach ($courses as $course) {
                echo '<a href="backup.php?id='.$course->id.'">'.format_string($course->fullname).' ('.format_string($course->shortname).')</a><br />'."\n";
            }
            echo $OUTPUT->box_end();
        } else {
            echo $OUTPUT->heading(get_string("nocoursesyet"));
            echo $OUTPUT->continue_button("$CFG->wwwroot/$CFG->admin/index.php");
        }
        echo $OUTPUT->footer();
        exit;
    }

    //Get and check course
    if (! $course = $DB->get_record("course", array("id"=>$id))) {
        print_error('unknowncourseidnumber','error');
    }

    //Print header
    if (has_capability('moodle/backup:backupcourse', get_context_instance(CONTEXT_SYSTEM))) {
        $PAGE->navbar->add($stradministration, new moodle_url('/admin/index.php'));
        $PAGE->navbar->add($strcoursebackup, new moodle_url('/backup/backup.php'));
        $PAGE->navbar->add("$course->fullname ($course->shortname)");
        echo $OUTPUT->header();
    } else {
        $PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', array('id'=>$course->id)));
        $PAGE->navbar->add($strcoursebackup);
        echo $OUTPUT->header();
    }

    //Print form
    echo $OUTPUT->heading(format_string("$strcoursebackup: $course->fullname ($course->shortname)"));
    echo $OUTPUT->box_start();

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
    echo $OUTPUT->box_end();

    //Print footer
    echo $OUTPUT->footer();
