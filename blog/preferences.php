<?php  // $Id$
       // preferences.php - user prefs for blog modeled on calendar

    require_once('../config.php');
    require_once($CFG->dirroot.'/blog/lib.php');

    require_login();
    global $USER;

    // detemine where the user is coming from in case we need to send them back there

    if (!$referrer = optional_param('referrer','', PARAM_URL)) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = $_SERVER['HTTP_REFERER'];
        } else {
            $referrer = $CFG->wwwroot;
        }
    }

    //ensure that the logged in user is not using the guest account
    if (isguest()) {
        error(get_string('noguestpost', 'blog'), $referrer);
    }
    
    if (!(isloggedin() && !isguest())) {
        error(get_string('noguestpost', 'blog'), $referrer);
    }
    $userid = $USER->id;

/// If data submitted, then process and store.

    if ($post = data_submitted()) {

        $pagesize = optional_param('pagesize', 10, PARAM_INT);
        if ($pagesize < 1 ) {
            error ('invalid page size');
        }
        set_user_preference('blogpagesize', $pagesize);
        redirect($referrer, get_string('changessaved'), 1);
        exit;
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
