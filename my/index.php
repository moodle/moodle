<?php  // $Id$

    // this is the 'my moodle' page

    require_once(dirname(__FILE__) . '/../config.php');
    require_once($CFG->dirroot.'/course/lib.php');

    require_login();

    $strmymoodle = get_string('mymoodle','my');

    if (isguest()) {
        print_header($strmymoodle);
        notice_yesno(get_string('noguest', 'my') . '<br /><br />' .
                get_string('liketologin'), get_login_url(), $CFG->wwwroot);
        echo $OUTPUT->footer();
        die;
    }

    $edit = optional_param('edit', -1, PARAM_BOOL);
    $blockaction = optional_param('blockaction', '', PARAM_ALPHA);

    $PAGE->set_context(get_context_instance(CONTEXT_USER, $USER->id));
    $PAGE->set_url('my/index.php');
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

    $form = new html_form();
    $form->url = new moodle_url("$CFG->wwwroot/my/index.php", array('edit' => $edit));
    $form->button->text = $string;
    $button = $OUTPUT->button($form);

    $header = $SITE->shortname . ': ' . $strmymoodle;
    $navigation = build_navigation($strmymoodle);
    $loggedinas = user_login_string();

    if (empty($CFG->langmenu)) {
        $langmenu = '';
    } else {
        $currlang = current_language();
        $langs = get_list_of_languages();
        $select = moodle_select::make_popup_form($CFG->wwwroot .'/my/index.php', 'lang', $langs, 'chooselang', $currlang);
        $select->nothinglabel = false;
        $select->set_label(get_accesshide(get_string('language')));
        $langmenu = $OUTPUT->select($select);
    }

    print_header($strmymoodle, $header, $navigation, '', '', true, $button, $loggedinas . $langmenu);

/// The main overview in the middle of the page

    // limits the number of courses showing up
    $courses_limit = 21;
    if (!empty($CFG->mycoursesperpage)) {
        $courses_limit = $CFG->mycoursesperpage;
    }
    $courses = get_my_courses($USER->id, 'visible DESC,sortorder ASC', '*', false, $courses_limit);
    $site = get_site();
    $course = $site; //just in case we need the old global $course hack

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
    if (count($courses) > 20) {
        echo '<br />...';
    }

    echo $OUTPUT->footer();

?>
