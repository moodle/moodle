<?php // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    if (!empty($USER->mnethostid) and $USER->mnethostid != $CFG->mnet_localhost_id) {
        $host = get_record('mnet_host', 'id', $USER->mnethostid);
        $wwwroot = $host->wwwroot;
    } else {
        $wwwroot = $CFG->wwwroot;
    }

    $sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW); // we want not null default to prevent required sesskey warning

    if (!confirm_sesskey($sesskey)) {
        print_header($SITE->fullname, $SITE->fullname, 'home');
        notice_yesno(get_string('logoutconfirm'), 'logout.php', $CFG->wwwroot.'/', array('sesskey'=>sesskey()), null, 'post', 'get');
        print_footer();
        die;
    }

    require_logout();

    redirect("$wwwroot/");

?>
