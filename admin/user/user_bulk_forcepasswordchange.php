<?php
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
    $primaryadmin = get_admin();

    $parts = array_chunk($SESSION->bulk_users, 300);
    foreach ($parts as $users) {
        list($in, $params) = $DB->get_in_or_equal($users);
        if ($rs = $DB->get_recordset_select('user', "id $in", $params)) {
            foreach ($rs as $user) {
                set_user_preference('auth_forcepasswordchange', 1, $user->id);
            }
            $rs->close();
        }
    }
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
    echo $OUTPUT->continue_button($return);

} else {
    list($in, $params) = $DB->get_in_or_equal($SESSION->bulk_users);
    $userlist = $DB->get_records_select_menu('user', "id $in", $params, 'fullname', 'id,'.$DB->sql_fullname().' AS fullname', 0, MAX_BULK_USERS);
    $usernames = implode(', ', $userlist);
    if (count($SESSION->bulk_users) > MAX_BULK_USERS) {
        $usernames .= ', ...';
    }
    echo $OUTPUT->heading(get_string('confirmation', 'admin'));
    $formcontinue = html_form::make_button('user_bulk_forcepasswordchange.php', array('confirm' => 1), get_string('yes'));
    $formcancel = html_form::make_button('user_bulk.php', array(), get_string('no'), 'get');
    echo $OUTPUT->confirm(get_string('forcepasswordchangecheckfull', '', $usernames), $formcontinue, $formcancel);
}

echo $OUTPUT->footer();
