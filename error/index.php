<?php

    require('../config.php');
    require_once($CFG->libdir.'/eventslib.php');

    if ($form = data_submitted()) { // form submitted, do not check referer (original page unknown)!

    /// Only deal with real users
        if (!isloggedin()) {
            redirect($CFG->wwwroot);
        }

    /// Work out who to send the message to
        if (!$admin = get_admin() ) {
            print_error('cannotfindadmin', 'debug');
        }

        $supportuser = new stdClass();
        $supportuser->email = $CFG->supportemail ? $CFG->supportemail : $admin->email;
        $supportuser->firstname = $CFG->supportname ? $CFG->supportname : $admin->firstname;
        $supportuser->lastname = $CFG->supportname ? '' : $admin->lastname;
        // emailstop could be hard coded "false" to ensure error reports are sent
        // but then admin's would have to alter their messaging preferences to temporarily stop them
        $supportuser->emailstop = $admin->emailstop;
        $supportuser->maildisplay = true;

    /// Send the message and redirect
        $eventdata = new stdClass();
        $eventdata->modulename        = 'moodle';
        $eventdata->userfrom          = $USER;
        $eventdata->userto            = $supportuser;
        $eventdata->subject           = 'Error: '. $form->referer .' -> '. $form->requested;
        $eventdata->fullmessage       = $form->text;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);

        redirect($CFG->wwwroot .'/course/', 'Message sent, thanks', 3);
        exit;
    }

    $site = get_site();
    $redirecturl = empty($_SERVER['REDIRECT_URL']) ? '' : $_SERVER['REDIRECT_URL'];
    $httpreferer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
    $requesturi  = empty($_SERVER['REQUEST_URI'])  ? '' : $_SERVER['REQUEST_URI'];

    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");

    $PAGE->set_url('/error/');
    $PAGE->set_title($site->fullname .':Error');
    $PAGE->set_heading($site->fullname .': Error 404');
    $PAGE->set_context(get_system_context());
    $PAGE->navbar->add('Error 404 - File not Found');
    echo $OUTPUT->header();
    echo $OUTPUT->box(get_string('pagenotexist', 'error'). '<br />'.s($requesturi), 'generalbox boxaligncenter');

    if (isloggedin()) {
?>
        <p><?php echo get_string('pleasereport', 'error'); ?>
        <p><form action="<?php echo $CFG->wwwroot ?>/error/index.php" method="post">
           <textarea rows="3" cols="50" name="text" id="text"></textarea><br />
           <input type="hidden" name="referer" value="<?php p($httpreferer) ?>">
           <input type="hidden" name="requested" value="<?php p($requesturi) ?>">
           <input type="submit" value="<?php echo get_string('sendmessage', 'error'); ?>">
           </form>
<?php
    } else {
        echo $OUTPUT->continue_button($CFG->wwwroot);
    }
    echo $OUTPUT->footer();
?>
