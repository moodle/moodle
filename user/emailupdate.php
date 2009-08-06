<?php // $Id$

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/editlib.php');

$key = required_param('key', PARAM_ALPHANUM);
$id  = required_param('id', PARAM_INT);

if (!$user = $DB->get_record('user', array('id' => $id))) {
    print_error('invaliduserid');
}

$preferences = get_user_preferences(null, null, $user->id);
$a = new stdClass();
$a->fullname = fullname($user, true);
$stremailupdate = get_string('auth_emailupdate', 'auth_email', $a);
print_header(format_string($SITE->fullname) . ": $stremailupdate", format_string($SITE->fullname) . ": $stremailupdate");

if (empty($preferences['newemailattemptsleft'])) {
    redirect("$CFG->wwwroot/user/view.php?id=$user->id");

} elseif ($preferences['newemailattemptsleft'] < 1) {
    cancel_email_update($user->id);
    $stroutofattempts = get_string('auth_outofnewemailupdateattempts', 'auth_email');
    print_box($stroutofattempts, 'center');

} elseif ($key == $preferences['newemailkey']) {
    cancel_email_update($user->id);
    $user->email = $preferences['newemail'];

    // Detect duplicate before saving
    if ($DB->get_record('user', array('email' => $user->email))) {
        $stremailnowexists = get_string('auth_emailnowexists', 'auth_email');
        print_box($stremailnowexists, 'center');
        print_continue("$CFG->wwwroot/user/view.php?id=$user->id");
    } else {
        // update user email
        $DB->set_field('user', 'email', $user->email, array('id' => $user->id));
        events_trigger('user_updated', $user);
        $a->email = $user->email;
        $stremailupdatesuccess = get_string('auth_emailupdatesuccess', 'auth_email', $a);
        print_box($stremailupdatesuccess, 'center');
        print_continue("$CFG->wwwroot/user/view.php?id=$user->id");
    }

} else {
    $preferences['newemailattemptsleft']--;
    set_user_preference('newemailattemptsleft', $preferences['newemailattemptsleft'], $user->id);
    $strinvalidkey = get_string('auth_invalidnewemailkey', 'auth_email');
    print_box($strinvalidkey, 'center');
}

echo $OUTPUT->footer();
