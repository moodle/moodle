<?php

// Disable session handling here?
require_once("../../config.php");
session_write_close();

//HTTPS is potentially required in this page
httpsrequired();

$authsequence = get_enabled_auth_plugins(true); // auths, in sequence
if (!in_array('ldap',$authsequence,true)) {
    print_error('ldap_isdisabled','auth');
}

$authplugin = get_auth_plugin('ldap');
if (empty($authplugin->config->ntlmsso_enabled)) {
    print_error('ntlmsso_isdisabled','auth');
}

$sesskey = required_param('sesskey', PARAM_RAW);
if ($authplugin->ntlmsso_magic($sesskey)) {
    // Serve GIF
    $file = $CFG->dirroot . '/pix/spacer.gif';
    
    // Type
    header('Content-Type: image/gif');
    header('Content-Length: '.filesize($file));

    // Output file
    $handle=fopen($file,'r');
    fpassthru($handle);
    fclose($handle);
    exit;
} else {
    print_error('ntlmsso_iwamagicnotenabled','auth');
}

?>