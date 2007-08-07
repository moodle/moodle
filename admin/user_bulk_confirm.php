<?php //$Id$
/**
* script for bulk user delete operations
*/

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$confirm     = optional_param('confirm', 0, PARAM_BOOL);
$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
require_capability('moodle/user:update', $sitecontext);

// clean-up users list
$primaryadmin = get_admin();
$userlist = array();
foreach ($SESSION->bulk_susers as $k => $v) {
    $user = get_record('user', 'id', $v, null, null, null, null, 'id,firstname,lastname,username,secret,confirmed,mnethostid,auth');
    if (!empty($user) && $user->id != $primaryadmin->id && !$user->confirmed && !is_mnet_remote_user($user)) {
        $userlist[$k] = $user;
    }
}

if (empty($userlist)) {
    redirect($CFG->wwwroot . '/admin/user_bulk.php');
}

admin_externalpage_setup('editusers');
admin_externalpage_print_header();
if (empty($confirm)) {
    $usernames = array();
    foreach ($userlist as $user) {
        $usernames[] =& fullname($user, true);
    }
    $usernames = implode(', ', $usernames);
    $optionsyes['confirm'] = 1;
    $optionsyes['sesskey'] = sesskey();
    print_heading(get_string('confirmation', 'admin'));
    notice_yesno(get_string('confirmcheckfull', '', $usernames), 'user_bulk_confirm.php', 'user_bulk.php', $optionsyes, NULL, 'post', 'get');
} else {
    foreach ($userlist as $k => $user) {
        $auth = get_auth_plugin($user->auth);
        $result = $auth->user_confirm(addslashes($user->username), addslashes($user->secret));
        if ($result != AUTH_CONFIRM_OK && $result != AUTH_CONFIRM_ALREADY) {
            notify(get_string('usernotconfirmed', '', fullname($user, true)));
        }
    }
    redirect($CFG->wwwroot . '/admin/user_bulk.php', get_string('changessaved'));
}
admin_externalpage_print_footer();
