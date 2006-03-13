<?php  // $Id$
       // preferences.php - user prefs for blog modeled on calendar

    require_once('../config.php');
    require_once($CFG->dirroot.'/blog/lib.php');

    require_login();
    global $USER;

    // detemine where the user is coming from in case we need to send them back there
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = $CFG->wwwroot;
    }

    //ensure that the logged in user is not using the guest account
    if (isguest()) {
        error(get_string('noguestpost', 'blog'), $referrer);
    }
    
    if (!blog_isLoggedIn() ) {
        error(get_string('noguestpost', 'blog'), $referrer);
    }
    $userid = $USER->id;
    $bloginfo =& new BlogInfo($userid);

/// If data submitted, then process and store.

	if ($post = data_submitted()) {
        print_header();

        set_user_preference('blogpagesize', optional_param('pagesize'));
        
        redirect($referrer, get_string('changessaved'), 1);
        exit;
    }
    
    $site = get_site();
    $pageMeta = '<script language="javascript" type="text/javascript" src="'. $CFG->wwwroot .'/blog/blog.js"></script>' . "\n";

    $strpreferences = get_string('preferences');

    $navigation = '<a href="'. $bloginfo->get_blog_url() .'">'. $bloginfo->get_blog_title() . '</a> -> '. $strpreferences;

    print_header("$site->shortname: ". $bloginfo->get_blog_title() .": $strpreferences", $bloginfo->get_blog_title(), $navigation, '', $pageMeta, true, '', '');

    print_heading($strpreferences);

    print_simple_box_start('center', '', '');

	include('./preferences.html');
    print_simple_box_end();

    print_footer();
?>
