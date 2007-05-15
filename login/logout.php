<?php // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    // can be overriden by auth plugins
    $redirect = $CFG->wwwroot.'/';

    $sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW); // we want not null default to prevent required sesskey warning

    if (!isloggedin()) {
        // no confirmation, user has already logged out
        require_logout();
        redirect($redirect);

    } else if (!confirm_sesskey($sesskey)) {
        print_header($SITE->fullname, $SITE->fullname, 'home');
        notice_yesno(get_string('logoutconfirm'), 'logout.php', $CFG->wwwroot.'/', array('sesskey'=>sesskey()), null, 'post', 'get');
        print_footer();
        die;
    }

    $authsequence = get_enabled_auth_plugins(); // auths, in sequence
    foreach($authsequence as $authname) {
        $authplugin = get_auth_plugin($authname);
        $authplugin->logoutpage_hook();
    }

    require_logout();

    redirect($redirect);

?>
