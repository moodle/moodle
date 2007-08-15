<?php //$Id$
require_once('../../config.php');
require_once($CFG->dirroot.'/message/lib.php');
require_once($CFG->libdir.'/adminlib.php');

$users       = $SESSION->bulk_susers;
$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
require_login();
require_capability('moodle/site:readallmessages', $sitecontext);

// fix for MDL-10112
if (empty($CFG->messaging)) {
    error("Messaging is disabled on this site");  
}

require_once('user_message_form.php');
$extradata['userlist'] =& $users;
$noteform =& new user_message_form('user_bulk_message.php', $extradata);
// if no users or action canceled, return to users page
if (empty($users) || $noteform->is_cancelled()) {
    redirect($CFG->wwwroot . '/admin/user/user_bulk.php');
}

$formdata =& $noteform->get_data();
// if we have the message and the command, then send it
if ($noteform->is_submitted() && !empty($formdata->send)) {
    if(empty($formdata->messagebody)) {
        notify(get_string('allfieldsrequired'));
    } else {
        foreach ($users as $u) {
            if ($user = get_record('user', 'id', $u)) {
                message_post_message($USER, $user, addslashes($formdata->messagebody), $formdata->format, 'direct');
            }
        }
        redirect($CFG->wwwroot . '/admin/user/user_bulk.php');
    }
}

admin_externalpage_setup('userbulk');
admin_externalpage_print_header();
if ($noteform->is_submitted() && !empty($formdata->preview)) {
    echo '<h3>'.get_string('previewhtml').'</h3>';
    echo '<div class="messagepreview">'. format_text(stripslashes($formdata->messagebody),$formdata->format). '</div>';
}

$noteform->display();
admin_externalpage_print_footer();
