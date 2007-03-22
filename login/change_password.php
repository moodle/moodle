<?PHP // $Id$

    require_once('../config.php');
    require_once('change_password_form.php');

    $id = optional_param('id', SITEID, PARAM_INT); // current course

    //HTTPS is potentially required in this page
    httpsrequired();

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    if (!$course = get_record('course', 'id', $id)) {
        error('No such course!');
    }

    // require proper login; guest can not change password
    // TODO: add change password capability so that we can prevent participants from changing password
    if (empty($USER->id) or isguestuser() or has_capability('moodle/legacy:guest', $systemcontext, $USER->id, false)) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->httpswwwroot.'/login/change_password.php';
        }
        redirect($CFG->httpswwwroot.'/login/index.php');
    }

    // do not allow "Logged in as" users to change any passwords
    if (!empty($USER->realuser)) {
        error('Can not use this script when "Logged in as"!');
    }

    if (is_mnet_remote_user($USER)) {
        $message = get_string('usercannotchangepassword', 'mnet');
        if ($idprovider = get_record('mnet_host', 'id', $USER->mnethostid)) {
            $message .= get_string('userchangepasswordlink', 'mnet', $idprovider);
        }
        error($message);
    }

    // load the appropriate auth plugin
    $userauth = get_auth_plugin($USER->auth);

    if (!$userauth->can_change_password()) {
        error(get_string('nopasswordchange', 'auth'));
    }

    if ($userauth->change_password_url()) {
        // this internal scrip not used
        redirect($userauth->change_password_url());
    }

    $mform = new login_change_password_form();
    $mform->set_data(array('id'=>$course->id));

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id);
    } else if ($data = $mform->get_data()) {

        if (!$userauth->user_update_password(addslashes_recursive($USER), $data->newpassword1)) {
            error(get_string('errorpasswordupdate', 'auth'));
        }

        // register success changing password
        unset_user_preference('auth_forcepasswordchange', $USER->id);

        $strpasswordchanged = get_string('passwordchanged');

        add_to_log($course->id, 'user', 'change password', "view.php?id=$USER->id&amp;course=$course->id", "$USER->id");

        $fullname = fullname($USER, true);

        if ($course->id != SITEID) {
            $navstr = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ";
        } else {
            $navstr = '';
        }
        $navstr .= "<a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">".get_string("participants")."</a> -> <a href=\"$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id\">$fullname</a> -> $strpasswordchanged";

        print_header($strpasswordchanged, $strpasswordchanged, $navstr);

        if (empty($SESSION->wantsurl) or $SESSION->wantsurl == $CFG->httpswwwroot.'/login/change_password.php') {
            $returnto = "$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$id";
        } else {
            $returnto = $SESSION->wantsurl;
        }

        notice($strpasswordchanged, $returnto);

        print_footer();
        exit;
    }


    $strchangepassword = get_string('changepassword');

    $fullname = fullname($USER, true);

    if ($course->id != SITEID) {
        $navstr = "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</a> -> ";
    } else {
        $navstr = '';
    }
    $navstr .= "<a href=\"$CFG->wwwroot/user/index.php?id=$course->id\">".get_string('participants')."</a> -> <a href=\"$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id\">$fullname</a> -> $strchangepassword";


    print_header($strchangepassword, $strchangepassword, $navstr);
    if (get_user_preferences('auth_forcepasswordchange')) {
        notify(get_string('forcepasswordchangenotice'));
    }
    $mform->display();
    print_footer();

?>
