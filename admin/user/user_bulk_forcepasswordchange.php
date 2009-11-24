<?php //$Id$
/**
* script for bulk user force password change
*/

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');

$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();
admin_externalpage_setup('userbulk');
require_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM));

$return = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

if (empty($SESSION->bulk_users)) {
    redirect($return);
}

admin_externalpage_print_header();

if ($confirm and confirm_sesskey()) {
    // only force password change if user may actually change the password
    $authsavailable = get_list_of_plugins('auth');
    $changeable = array();
    foreach($authsavailable as $authname) {
        if (!$auth = get_auth_plugin($authname)) {
            continue;
        }
        if (@$auth->is_internal() and @$auth->can_change_password()) { // plugins may not be configured yet, not nice :-(
            $changeable[$authname] = true;
        }
    }

    $parts = array_chunk($SESSION->bulk_users, 300);
    foreach ($parts as $users) {
        $in = implode(',', $users);
        if ($rs = get_recordset_select('user', "id IN ($in)")) {
            while ($user = rs_fetch_next_record($rs)) {
                if (!empty($changeable[$user->auth])) {
                    set_user_preference('auth_forcepasswordchange', 1, $user->id);
                    unset($SESSION->bulk_users[$user->id]);
                } else {
                    notify(get_string('forcepasswordchangenot', '', fullname($user, true)));
                }
            }
            rs_close($rs);
        }
    }
    notify(get_string('changessaved'), 'notifysuccess');
    print_continue($return);

} else {
    $in = implode(',', $SESSION->bulk_users);
    $userlist = get_records_select_menu('user', "id IN ($in)", 'fullname', 'id,'.sql_fullname().' AS fullname', 0, MAX_BULK_USERS);
    $usernames = implode(', ', $userlist);
    if (count($SESSION->bulk_users) > MAX_BULK_USERS) {
        $usernames .= ', ...';
    }
    $optionsyes = array();
    $optionsyes['confirm'] = 1;
    $optionsyes['sesskey'] = sesskey();
    print_heading(get_string('confirmation', 'admin'));
    notice_yesno(get_string('forcepasswordchangecheckfull', '', $usernames), 'user_bulk_forcepasswordchange.php', 'user_bulk.php', $optionsyes, NULL, 'post', 'get');
}

admin_externalpage_print_footer();
?>
