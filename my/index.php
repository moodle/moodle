<?php

    // this is the 'my moodle' page

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->dirroot.'/course/lib.php');

    require_login();

    $strmymoodle = get_string('mymoodle','my');

    if (isguestuser()) {
        $PAGE->set_title($strmymoodle);
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('noguest', 'my') . '<br /><br />' . get_string('liketologin'), get_login_url(), $CFG->wwwroot);
        echo $OUTPUT->footer();
        die;
    }

    $edit = optional_param('edit', -1, PARAM_BOOL);
    $blockaction = optional_param('blockaction', '', PARAM_ALPHA);

    $PAGE->set_context(get_context_instance(CONTEXT_USER, $USER->id));
    $PAGE->set_url('/my/index.php');
    $PAGE->set_pagelayout('mydashboard');
    $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');

    if (($edit != -1) and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    if (!empty($USER->editing)) {
        $string = get_string('updatemymoodleoff');
        $edit = '0';
    } else {
        $string = get_string('updatemymoodleon');
        $edit = '1';
    }

    $url = new moodle_url("$CFG->wwwroot/my/index.php", array('edit' => $edit));
    $button = $OUTPUT->single_button($url, $string);

    $header = $SITE->shortname . ': ' . $strmymoodle;

    $PAGE->set_title($strmymoodle);
    $PAGE->set_heading($header);
    $PAGE->set_button($button);
    echo $OUTPUT->header();

/// The main overview in the middle of the page

    // limits the number of courses showing up
    $courses_limit = 21;
    if (isset($CFG->mycoursesperpage)) {
        $courses_limit = $CFG->mycoursesperpage;
    }

    $morecourses = false;
    if ($courses_limit > 0) {
        $courses_limit = $courses_limit + 1;
    }

    $courses = get_my_courses($USER->id, 'visible DESC,sortorder ASC', '*', false, $courses_limit);
    $site = get_site();
    $course = $site; //just in case we need the old global $course hack

    if (($courses_limit > 0) && (count($courses) >= $courses_limit)) {
        //remove the 'marker' course that we retrieve just to see if we have more than $courses_limit
        array_pop($courses);
        $morecourses = true;
    }


    if (array_key_exists($site->id,$courses)) {
        unset($courses[$site->id]);
    }

    foreach ($courses as $c) {
        if (isset($USER->lastcourseaccess[$c->id])) {
            $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
        } else {
            $courses[$c->id]->lastaccess = 0;
        }
    }

    if (empty($courses)) {
        echo $OUTPUT->box(get_string('nocourses','my'));
    } else {
        print_overview($courses);
    }

    // if more than 20 courses
    if ($morecourses) {
        echo '<br />...';
    }

    echo $OUTPUT->footer();

