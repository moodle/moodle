<?php //$Id$
/**
* script for bulk user delete operations
*/

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$confirm     = optional_param('confirm', 0, PARAM_BOOL);
$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

require_capability('moodle/user:delete', $sitecontext);

// clean-up users list
$primaryadmin = get_admin();
$userlist = array();
foreach ($SESSION->bulk_susers as $k => $v) {
    $user = get_record('user', 'id', $v, null, null, null, null, 'id,firstname,lastname,email');
    if (!empty($user) && $user->id != $primaryadmin->id) {
        $userlist[$k] = $user;
    }
}

if (empty($userlist)) {
    redirect($CFG->wwwroot . '/admin/user_bulk.php');
}

admin_externalpage_setup('userbulk');
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
    notice_yesno(get_string('deletecheckfull', '', $usernames), 'user_bulk_delete.php', 'user_bulk.php', $optionsyes, NULL, 'post', 'get');
} else {
    foreach ($userlist as $k => $user) {
        $user->username     = addslashes($user->email . time());  // Remember it just in case
        $user->deleted      = 1;
        $user->email        = '';               // Clear this field to free it up
        $user->timemodified = time();
        $user->idnumber     = '';               // Clear this field to free it up
        if (update_record('user', $user)) {
            // not sure if this is needed. unenrol_student($user->id);  // From all courses
            delete_records('role_assignments', 'userid', $user->id); // unassign all roles
            // remove all context assigned on this user?
            // notify(get_string('deletedactivity', '', fullname($user, true)) );
            unset($SESSION->bulk_susers[$k]);
        } else {
            notify(get_string('deletednot', '', fullname($user, true)));
        }
    }
    redirect($CFG->wwwroot . '/admin/user_bulk.php', get_string('changessaved'));
}
admin_externalpage_print_footer();
