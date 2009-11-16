<?php //$Id$
/**
* script for bulk user force password change
*/

require_once('../../config.php');
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

//TODO: add support for large number of users

if ($confirm and confirm_sesskey()) {
    $primaryadmin = get_admin();

    $in = implode(',', $SESSION->bulk_users);
    if ($rs = get_recordset_select('user', "id IN ($in)")) {
        while ($user = rs_fetch_next_record($rs)) {
            if ($primaryadmin->id != $user->id and $USER->id != $user->id
                and set_user_preference('auth_forcepasswordchange', 1, $user->id)) {
                unset($SESSION->bulk_users[$user->id]);
            } else {
                notify(get_string('forcepasswordchangenot', '', fullname($user, true)));
            }
        }
        rs_close($rs);
    }
    redirect($return, get_string('changessaved'));

} else {
    $in = implode(',', $SESSION->bulk_users);
    $userlist = get_records_select_menu('user', "id IN ($in)", 'fullname', 'id,'.sql_fullname().' AS fullname');
    $usernames = implode(', ', $userlist);
    $optionsyes = array();
    $optionsyes['confirm'] = 1;
    $optionsyes['sesskey'] = sesskey();
    print_heading(get_string('confirmation', 'admin'));
    notice_yesno(get_string('forcepasswordchangecheckfull', '', $usernames), 'user_bulk_forcepasswordchange.php', 'user_bulk.php', $optionsyes, NULL, 'post', 'get');
}

admin_externalpage_print_footer();
?>
