<?php // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    // can be overriden by auth plugins
    $redirect = $CFG->wwwroot.'/';

    $authsequence = explode(',', $CFG->auth); // auths, in sequence
    foreach($authsequence as $authname) {
        $authplugin = get_auth_plugin($authname);
        $authplugin->prelogin_hook();
    }

    $sesskey = optional_param('sesskey', '__notpresent__', PARAM_RAW); // we want not null default to prevent required sesskey warning

    if (!confirm_sesskey($sesskey)) {
        print_header($SITE->fullname, $SITE->fullname, 'home');
        notice_yesno(get_string('logoutconfirm'), 'logout.php', $CFG->wwwroot.'/', array('sesskey'=>sesskey()), null, 'post', 'get');
        print_footer();
        die;
    }

    require_logout();

    redirect($redirect);

?>
