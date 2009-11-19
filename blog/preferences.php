<?php  // $Id$
       // preferences.php - user prefs for blog modeled on calendar

    require_once('../config.php');
    require_once($CFG->dirroot.'/blog/lib.php');

    $courseid = optional_param('courseid', SITEID, PARAM_INT);

    if ($courseid == SITEID) {
        require_login();
        $context = get_context_instance(CONTEXT_SYSTEM);
    } else {
        require_login($courseid);
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    }

    if (empty($CFG->bloglevel)) {
        error('Blogging is disabled!');
    }

    require_capability('moodle/blog:view', $context);

/// If data submitted, then process and store.

    if (data_submitted() and confirm_sesskey()) {
        $pagesize = required_param('pagesize', PARAM_INT);

        if ($pagesize < 1) {
            error('invalid page size');
        }
        set_user_preference('blogpagesize', $pagesize);

        // now try to guess where to go from here ;-)
        if ($courseid == SITEID) {
            redirect($CFG->wwwroot.'/blog/index.php');
        } else {
            redirect($CFG->wwwroot.'/blog/index.php?filtertype=course&amp;filterselect='.$courseid);
        }
    }

    $site = get_site();

    $strpreferences = get_string('preferences');
    $strblogs       = get_string('blogs', 'blog');
    $navlinks = array(array('name' => $strblogs, 'link' => "$CFG->wwwroot/blog/", 'type' => 'misc'));
    $navlinks[] = array('name' => $strpreferences, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);

    print_header("$site->shortname: $strblogs : $strpreferences", $strblogs, $navigation);
    print_heading($strpreferences);

    print_simple_box_start('center', '', '');
    require('./preferences.html');
    print_simple_box_end();

    print_footer();
?>
