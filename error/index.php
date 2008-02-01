<?PHP // $Id$

    require('../config.php');

    if ($form = data_submitted('nomatch')) { // form submitted, do not check referer (original page unknown)!

    /// Only deal with real users
        if (!isloggedin()) { 
            redirect($CFG->wwwroot);
        }

    /// Work out who to send the message to
        if (!$admin = get_admin() ) {
            error('Could not find an admin user!');
        }

        $supportuser = new object;
        $supportuser->email = $CFG->supportemail ? $CFG->supportemail : $admin->email;
        $supportuser->firstname = $CFG->supportname ? $CFG->supportname : $admin->firstname;
        $supportuser->lastname = $CFG->supportname ? '' : $admin->lastname;
        $supportuser->maildisplay = true;

    /// Send the email and redirect
        email_to_user($supportuser, $USER, 'Error: '. $form->referer .' -> '. $form->requested, $form->text);

        redirect($CFG->wwwroot .'/course/', 'Message sent, thanks', 3);
        exit;
    }

    $site = get_site();
    $redirecturl = empty($_SERVER['REDIRECT_URL']) ? '' : $_SERVER['REDIRECT_URL'];
    $httpreferer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
    $requesturi  = empty($_SERVER['REQUEST_URI'])  ? '' : $_SERVER['REQUEST_URI'];
    
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");

    print_header($site->fullname .':Error', $site->fullname .': Error 404', 'Error 404 - File not Found', '');

    print_simple_box('<p align="center">'. get_string('pagenotexist', 'error'). '<br />'.s($requesturi).'</p>', 'center');

    if (isloggedin()) {
?>
        <center>
        <p><?php echo get_string('pleasereport', 'error'); ?>
        <p><form action="<?php echo $CFG->wwwroot ?>/error/index.php" method="post">
           <textarea rows="3" cols="50" name="text" id="text"></textarea><br />
           <input type="hidden" name="referer" value="<?php p($httpreferer) ?>">
           <input type="hidden" name="requested" value="<?php p($requesturi) ?>">
           <input type="submit" value="<?php echo get_string('sendmessage', 'error'); ?>">
           </form>
<?php
    } else {
        print_continue($CFG->wwwroot);
    }
    print_footer();
?>
