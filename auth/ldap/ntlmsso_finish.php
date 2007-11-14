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

// If ntlmsso_finish() succeeds, then the code never returns,
// so we only worry about failure.
if (!$authplugin->ntlmsso_finish()) {
    // Redirect to login, saying "don't try again!"
    redirect($CFG->wwwroot . '/login/index.php?authldap_skipntlmsso=1', 
             get_string('ntlmsso_failed','auth'), 3);
}
?>