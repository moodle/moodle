<?php // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/editlib.php');

$key = required_param('key', PARAM_ALPHANUM);
$id  = required_param('id', PARAM_INT);

if (!$user = get_record('user', 'id', $id)) {
    error("Unknown user ID");
}

$preferences = get_user_preferences(null, null, $user->id);
$a = new stdClass();
$a->fullname = fullname($user, true);
$stremailupdate = get_string('auth_emailupdate', 'auth', $a);
print_header(format_string($SITE->fullname) . ": $stremailupdate", format_string($SITE->fullname) . ": $stremailupdate");

if (empty($preferences['newemailattemptsleft'])) {
    redirect("$CFG->wwwroot/user/view.php?id=$user->id");

} elseif ($preferences['newemailattemptsleft'] < 1) {
    cancel_email_update($user->id);
    $stroutofattempts = get_string('auth_outofnewemailupdateattempts', 'auth');
    print_box($stroutofattempts, 'center');

} elseif ($key == $preferences['newemailkey']) {
    $olduser = clone($user);
    cancel_email_update($user->id);
    $user->email = $preferences['newemail'];

    // Detect duplicate before saving
    if (get_record('user', 'email', addslashes($user->email))) {
        $stremailnowexists = get_string('auth_emailnowexists', 'auth');
        print_box($stremailnowexists, 'center');
        print_continue("$CFG->wwwroot/user/view.php?id=$user->id");
    } else {
        // update user email
        if (!set_field('user', 'email', addslashes($user->email), 'id', $user->id)) {
            error('Error updating user record');

        } else {
            $authplugin = get_auth_plugin($user->auth);
            $authplugin->user_update($olduser, $user);
            events_trigger('user_updated', $user);
            $a->email = $user->email;
            $stremailupdatesuccess = get_string('auth_emailupdatesuccess', 'auth', $a);
            print_box($stremailupdatesuccess, 'center');
            print_continue("$CFG->wwwroot/user/view.php?id=$user->id");
        }
    }

} else {
    $preferences['newemailattemptsleft']--;
    set_user_preference('newemailattemptsleft', $preferences['newemailattemptsleft'], $user->id);
    $strinvalidkey = get_string('auth_invalidnewemailkey', 'auth');
    print_box($strinvalidkey, 'center');
}

print_footer('none');
