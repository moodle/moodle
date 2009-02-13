<?php
// $Id$
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->libdir . '/adminlib.php');

$CFG->pagepath = 'admin/managewsprotocols';

$hide    = optional_param('hide', '', PARAM_ALPHANUM);
$username    = optional_param('username', '', PARAM_ALPHANUM);

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

    print_simple_box_start();
    $mform->display();
    print_simple_box_end();

}

if (!empty($return)) {
    redirect($baseurl);
}

admin_externalpage_print_footer();
