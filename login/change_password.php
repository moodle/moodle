<?PHP // $Id$

    require_once('../config.php');
    require_once('change_password_form.php');

    $id = optional_param('id', SITEID, PARAM_INT);

    //HTTPS is potentially required in this page
    httpsrequired();

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

    if (!$course = get_record('course', 'id', $id)) {
        error('No such course!');
    }

    if (is_mnet_remote_user($USER)) {
        $message = get_string('usercannotchangepassword', 'mnet');
        if ($idprovider = get_record('mnet_host', 'id', $USER->mnethostid)) {
            $message .= get_string('userchangepasswordlink', 'mnet', $idprovider);
        }
        error($message);
    }

    // require proper login; guest can not change passwords anymore!
    // TODO: add change password capability so that we can prevent participants to change password
    if (empty($USER->id) or $USER->username=='guest' or has_capability('moodle/legacy:guest', $sitecontext, $USER->id, false)) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->httpswwwroot.'/login/change_password.php';
        }
        redirect($CFG->httpswwwroot.'/login/index.php');
    }

    // do not allow "Logged in as" users to change any passwords
    if (!empty($USER->realuser)) {
        error('Can not use this script when "Logged in as"!');
    }

    $mform = new login_change_password_form();
    $mform->set_data(array('id'=>$course->id, 'username'=>$USER->username));

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id);
    } else if ($data = $mform->get_data()) {

        if (!has_capability('moodle/user:update', $sitecontext)) {
            //ignore submitted username - the same is done in form validation
            $data->username = $USER->username;
        }

        if ($data->username == $USER->username) {
            $user =& $USER;
        } else {
            $user = get_complete_user_data('username', $data->username);
        }

        // load the appropriate auth plugin
        $userauth = get_auth_plugin($user->auth);
        if ($userauth->can_change_password()){
            if ($userauth->user_update_password($user, $data->newpassword1)) {
            } else {
                error('Could not set the new password');
            }
        } else { // external users
            $message = 'You cannot change your password this way.';
            if (method_exists($userauth, 'change_password_url') and $userauth->change_password_url()) {
                $message .= '<br /><br />' . get_string('passwordextlink')
                    .  '<br /><br />' . '<a href="' . $userauth->change_password_url() . '">'
                    .  $userauth->change_password_url() . '</a>';            error('You cannot change your password this way.');
            }
            error($message);
        }

        // register success changing password
        unset_user_preference('auth_forcepasswordchange', $user->id);

        $strpasswordchanged = get_string('passwordchanged');

        add_to_log($course->id, 'user', 'change password', "view.php?id=$user->id&amp;course=$course->id", "$user->id");

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
    if (!empty($USER->preference['auth_forcepasswordchange'])) {
        notify(get_string('forcepasswordchangenotice'));
    }
    $mform->display();
    print_footer();

?>
