<?php // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");


    $sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW); // we want not null default to prevent required sesskey warning

    if (!confirm_sesskey($sesskey)) {
        print_header($SITE->fullname, $SITE->fullname, 'home');
        notice_yesno(get_string('logoutconfirm'), 'logout.php?sesskey='.sesskey(), $CFG->wwwroot.'/');
        print_footer();
        die;
    }

    require_logout();

    redirect("$CFG->wwwroot/");

?>
