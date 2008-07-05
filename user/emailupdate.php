<?php // $Id$
require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$key = required_param('key', PARAM_ALPHANUM);
$id  = required_param('id', PARAM_INT);

if (!$user = $DB->get_record('user', array('id' => $id))) {
    error("Unknown user ID");
}

$preferences = get_user_preferences(null, null, $id);
$a = new stdClass();
$a->fullname = fullname($user, true);
$stremailupdate = get_string('auth_emailupdate', 'auth', $a);
print_header(format_string($SITE->fullname) . ": $stremailupdate", format_string($SITE->fullname) . ": $stremailupdate");

$cancel_email_update = false;

if (empty($preferences['newemailattemptsleft'])) {
    redirect("$CFG->wwwroot/user/view.php?id=$user->id");

} elseif ($preferences['newemailattemptsleft'] < 1) {
    $cancel_email_update = true;
    $stroutofattempts = get_string('auth_outofnewemailupdateattempts', 'auth');
    print_box($stroutofattempts, 'center');

} elseif ($key == $preferences['newemailkey']) {
    $user->email = $preferences['newemail'];

    // Detect duplicate before saving
    if ($DB->get_record('user', array('email' => $user->email))) {
        $stremailnowexists = get_string('auth_emailnowexists', 'auth');
        print_box($stremailnowexists, 'center');
        $cancel_email_update = true;
        print_continue("$CFG->wwwroot/user/view.php?id=$user->id");
    } else {
        // update user email
        if (!$DB->set_field('user', 'email', $user->email, array('id' => $user->id))) {
            error('Error updating user record');

        } else {
            events_trigger('user_updated', $user);
            $stremailupdatesuccess = get_string('auth_emailupdatesuccess', 'auth', $user);
            print_box($stremailupdatesuccess, 'center');
            print_continue("$CFG->wwwroot/user/view.php?id=$user->id");

            $cancel_email_update = true;
        }
    }

} else {
    $preferences['newemailattemptsleft']--;
    set_user_preference('newemailattemptsleft', $preferences['newemailattemptsleft'], $user->id);
    $strinvalidkey = get_string('auth_invalidnewemailkey', 'auth');
    print_box($strinvalidkey, 'center');
}

if ($cancel_email_update) {
    require_once($CFG->dirroot . '/user/editlib.php');
    $user->preference_newemail = null;
    $user->preference_newemailkey = null;
    $user->preference_newemailattemptsleft = null;
    useredit_update_user_preference($user);
}
