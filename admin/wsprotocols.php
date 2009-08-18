<?php
// $Id$
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$hide    = optional_param('hide', '', PARAM_ALPHANUM);
$username    = optional_param('username', '', PARAM_ALPHANUM);
$settings    = optional_param('settings', '', PARAM_ALPHANUM);

$pagename = 'managews';

admin_externalpage_setup($pagename);
require_login(SITEID, false);
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$baseurl    = "$CFG->wwwroot/$CFG->admin/settings.php?section=webservices";

if (!empty($hide)) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', '', $baseurl);
    }
    set_config("enable", !get_config($hide, "enable"), $hide);   
    $return = true;
} else if (!empty($username)) {
    admin_externalpage_print_header();
    $mform = new wsuser_form('', array('username' => $username));
    if ($mform->is_cancelled()){
        redirect($baseurl);
        exit;
    }
    $fromform = $mform->get_data();



    if (!empty($fromform)) {
        $wsuser = $DB->get_record("user", array("username" => $fromform->username));
        set_user_preference("ipwhitelist", $fromform->ipwhitelist, $wsuser->id);
        redirect($baseurl,get_string("changessaved"));
    }

    echo $OUTPUT->box_start();
    $mform->display();
    echo $OUTPUT->box_end();

} else if (!empty($settings)) {
/// Server settings page
    admin_externalpage_print_header();

    $mform = new wssettings_form('', array('settings' => $settings)); // load the server settings form
    
    if ($mform->is_cancelled()){
    /// user pressed cancel button and return to the security web service page
        redirect($baseurl);
        exit;
    }

    $fromform = $mform->get_data();

    if (!empty($fromform)) {
    /// save the new setting 
        require_once($CFG->dirroot . '/webservice/'. $settings . '/lib.php');
        $settingnames = call_user_func(array($settings.'_server', 'get_setting_names'));
        foreach($settingnames as $settingname) {
            if (empty($fromform->$settingname)) {
                set_config($settingname, null, $settings);
            } else {
                set_config($settingname, $fromform->$settingname, $settings);
            }
        }

        redirect($baseurl,get_string("changessaved")); // return to the security web service page
    }
/// display the server settings form
    echo $OUTPUT->box_start();
    $mform->display();
    echo $OUTPUT->box_end();
} else {
    $return = true;
}

if (!empty($return)) {
    redirect($baseurl);
}

echo $OUTPUT->footer();
