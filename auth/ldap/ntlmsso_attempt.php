<?php

require_once(dirname(dirname(dirname(__FILE__)))."/config.php");

//HTTPS is potentially required in this page
httpsrequired();

/// Define variables used in page
if (!$site = get_site()) {
    error("No site found!");
}

$authsequence = get_enabled_auth_plugins(true); // auths, in sequence
if (!in_array('ldap',$authsequence,true)) {
    print_error('ldap_isdisabled','auth');
}

$authplugin = get_auth_plugin('ldap');
if (empty($authplugin->config->ntlmsso_enabled)) {
    print_error('ntlmsso_isdisabled','auth');
}

$sesskey = sesskey();

// Display the page header. This makes redirect respect the timeout we specify
// here (and not add 3 more secs) which in turn prevents a bug in both IE 6.x
// and FF 3.x (Windows version at least) where javascript timers fire up even
// when we've already left the page that set the timer.
$loginsite = get_string("loginsite");
$navlinks = array(array('name' => $loginsite, 'link' => null, 'type' => 'misc'));
$navigation = build_navigation($navlinks);
print_header("$site->fullname: $loginsite", $site->fullname, $navigation, '', '', true);

$msg = '<p>'.get_string('ntlmsso_attempting','auth').'</p>'
    . '<img width="1", height="1" '
    . ' src="' . $CFG->wwwroot . '/auth/ldap/ntlmsso_magic.php?sesskey='
    . $sesskey . '" />';
redirect($CFG->wwwroot . '/auth/ldap/ntlmsso_finish.php', $msg, 3);



?>
