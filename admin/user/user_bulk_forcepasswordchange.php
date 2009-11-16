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
    if ($rs = $DB->get_recordset_select('user', "id IN ($in)", null)) {
        foreach ($rs as $user) {
            if ($primaryadmin->id != $user->id and $USER->id != $user->id
                and set_user_preference('auth_forcepasswordchange', 1, $user->id)) {
                unset($SESSION->bulk_users[$user->id]);
            } else {
                echo $OUTPUT->notification(get_string('forcepasswordchangenot', '', fullname($user, true)));
            }
        }
        $rs->close;
    }
    redirect($return, get_string('changessaved'));

} else {
    $in = implode(',', $SESSION->bulk_users);
    $userlist = $DB->get_records_select_menu('user', "id IN ($in)", null, 'fullname', 'id,'.$DB->sql_fullname().' AS fullname');
    $usernames = implode(', ', $userlist);
    echo $OUTPUT->heading(get_string('confirmation', 'admin'));
    $formcontinue = html_form::make_button('user_bulk_forcepasswordchange.php', array('confirm' => 1), get_string('yes'));
    $formcancel = html_form::make_button('user_bulk.php', $optionsno, get_string('no'), 'get');
    echo $OUTPUT->confirm(get_string('forcepasswordchangecheckfull', '', $usernames), $formcontinue, $formcancel);
}

echo $OUTPUT->footer();
?>
