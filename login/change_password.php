<?PHP // $Id$

    require_once('../config.php');
    require_once('change_password_form.php');

    $id = optional_param('id', SITEID, PARAM_INT); // current course

    $strparticipants = get_string('participants');

    //HTTPS is potentially required in this page
    httpsrequired();

    $systemcontext = get_context_instance(CONTEXT_SYSTEM);

    if (!$course = get_record('course', 'id', $id)) {
        error('No such course!');
    }

    // require proper login; guest user can not change password
    if (empty($USER->id) or isguestuser()) {
        if (empty($SESSION->wantsurl)) {
            $SESSION->wantsurl = $CFG->httpswwwroot.'/login/change_password.php';
        }
        redirect($CFG->httpswwwroot.'/login/index.php');
    }

    // do not require change own password cap if change forced
    if (!get_user_preferences('auth_forcepasswordchange', false)) {
        require_capability('moodle/user:changeownpassword', $systemcontext);
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
        print_error('nopasswordchange', 'auth');
    }

    if ($changeurl = $userauth->change_password_url()) {
        // this internal scrip not used
        redirect($changeurl);
    }

    $mform = new login_change_password_form();
    $mform->set_data(array('id'=>$course->id));

    $navlinks = array();
    $navlinks[] = array('name' => $strparticipants, 'link' => "$CFG->wwwroot/user/index.php?id=$course->id", 'type' => 'misc');

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id);
    } else if ($data = $mform->get_data()) {

        if (!$userauth->user_update_password(addslashes_recursive($USER), $data->newpassword1)) {
            print_error('errorpasswordupdate', 'auth');
        }

        // register success changing password
        unset_user_preference('auth_forcepasswordchange', $USER->id);

        $strpasswordchanged = get_string('passwordchanged');

        add_to_log($course->id, 'user', 'change password', "view.php?id=$USER->id&amp;course=$course->id", "$USER->id");

        $fullname = fullname($USER, true);

        $navlinks[] = array('name' => $fullname,
                            'link' => "$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id",
                            'type' => 'misc');
        $navlinks[] = array('name' => $strpasswordchanged, 'link' => null, 'type' => 'misc');
        $navigation = build_navigation($navlinks);

        print_header($strpasswordchanged, $strpasswordchanged, $navigation);

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

    $navlinks[] = array('name' => $fullname, 'link' => "$CFG->wwwroot/user/view.php?id=$USER->id&amp;course=$course->id", 'type' => 'misc');
    $navlinks[] = array('name' => $strchangepassword, 'link' => null, 'type' => 'misc');
    $navigation = build_navigation($navlinks);
    // Turn off pop-up messaging window for this page
    $CFG->messaging = 0;
    print_header($strchangepassword, $strchangepassword, $navigation);
    if (get_user_preferences('auth_forcepasswordchange')) {
        notify(get_string('forcepasswordchangenotice'));
    }
    $mform->display();
    print_footer();

?>
