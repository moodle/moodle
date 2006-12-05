<?PHP // $Id$

    require_once('../config.php');
    require_once('change_password_form.php');

    $id = optional_param('id', SITEID, PARAM_INT);

    $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

    //HTTPS is potentially required in this page
    httpsrequired();

    if (!$course = get_record('course', 'id', $id)) {
        error('No such course!');
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

    $mform = new change_password_form('change_password.php');
    $mform->set_defaults(array('id'=>$course->id, 'username'=>$USER->username));

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id);
    } else if ($data = $mform->data_submitted()) {

        if (!has_capability('moodle/user:update', $sitecontext)) {
            //ignore submitted username - the same is done in form validation
            $data->username = $USER->username;
        }

        if ($data->username == $USER->username) {
            $user =& $USER;
        } else {
            $user = get_complete_user_data('username', $data->username);
        }

        if (is_internal_auth($user->auth)){
            if (!update_internal_user_password($user, $data->newpassword1)) {
                error('Could not set the new password');
            }
        } else { // external users
            // the relevant auth libs should be loaded already
            // as part of form validation in function authenticate_user_login()
            // check that we allow changes through moodle
            if (!empty($CFG->{'auth_'. $user->auth.'_stdchangepassword'})) {
                if (function_exists('auth_user_update_password')){
                    // note that we pass cleartext password
                    if (auth_user_update_password($user->username, $data->newpassword1)){
                        update_internal_user_password($user, $data->newpassword1, false);
                    } else {
                        error('Could not set the new password');
                    }
                } else {
                    error('The authentication module is misconfigured (missing auth_user_update_password)');
                }
            } else {
                error('You cannot change your password this way.');
            }
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
