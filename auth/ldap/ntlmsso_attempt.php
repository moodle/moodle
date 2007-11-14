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

//print_header("$site->fullname: $loginsite", $site->fullname, $loginsite, $focus, '', true);
$msg = '<p>'.get_string('ntlmsso_attempting','auth').'</p>'
    . '<img width="1", height="1" '
    . ' src="' . $CFG->wwwroot . '/auth/ldap/ntlmsso_magic.php?sesskey='
    . $sesskey . '" />';
redirect($CFG->wwwroot . '/auth/ldap/ntlmsso_finish.php', $msg, 3);



?>