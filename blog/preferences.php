<?php  // $Id$
       // preferences.php - user prefs for blog modeled on calendar

    require_once('../config.php');
    require_once($CFG->dirroot.'/blog/lib.php');

    require_login();

    if (empty($CFG->bloglevel)) {
        error('Blogging is disabled!');
    }

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

    // Ensure that the logged in user has the capability to view blog entries for now,
    // because there is only $pagesize which affects the viewing ;-)
    require_capability('moodle/blog:view', $sitecontext);

/// If data submitted, then process and store.

    if (data_submitted()) {

        $pagesize = optional_param('pagesize', 10, PARAM_INT);
        if ($pagesize < 1 ) {
            error ('invalid page size');
        }
        set_user_preference('blogpagesize', $pagesize);
         // the best guess is IMHO to redirect to blog page, so that user reviews the changed preferences - skodak
        redirect($CFG->wwwroot.'/blog/index.php');
    }

    $site = get_site();
    $pageMeta = '' . "\n";

    $strpreferences = get_string('preferences');
    $strblogs = get_string('blogs', 'blog');

    $navigation = "<a href='".$CFG->wwwroot."/blog/'>$strblogs</a> -> $strpreferences";

    print_header("$site->shortname: $strblogs : $strpreferences", $strblogs, $navigation, '', $pageMeta, true, '', '');

    print_heading($strpreferences);

    print_simple_box_start('center', '', '');

    include('./preferences.html');
    print_simple_box_end();

    print_footer();
?>
