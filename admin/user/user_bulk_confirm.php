<?php //$Id$
/**
* script for bulk user delete operations
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
    $in = implode(',', $SESSION->bulk_users);
    if ($rs = $DB->get_recordset_select('user', "id IN ($in)", null, '', 'id, username, secret, confirmed, auth, firstname, lastname')) {
        foreach ($rs as $user) {
            if ($user->confirmed) {
                continue;
            }
            $auth = get_auth_plugin($user->auth);
            $result = $auth->user_confirm($user->username, $user->secret);
            if ($result != AUTH_CONFIRM_OK && $result != AUTH_CONFIRM_ALREADY) {
                notify(get_string('usernotconfirmed', '', fullname($user, true)));
            }
        }
        $rs->close();
    }
    redirect($return, get_string('changessaved'));

} else {
    $in = implode(',', $SESSION->bulk_users);
    $userlist = $DB->get_records_select_menu('user', "id IN ($in)", null, 'fullname', 'id,'.$DB->sql_fullname().' AS fullname');
    $usernames = implode(', ', $userlist);
    $optionsyes = array();
    $optionsyes['confirm'] = 1;
    $optionsyes['sesskey'] = sesskey();
    print_heading(get_string('confirmation', 'admin'));
    notice_yesno(get_string('confirmcheckfull', '', $usernames), 'user_bulk_confirm.php', 'user_bulk.php', $optionsyes, NULL, 'post', 'get');
}

admin_externalpage_print_footer();
?>