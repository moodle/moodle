<?php

// Don't let lib/setup.php set any cookies
// as we will be executing under the OS security
// context of the user we are trying to login, rather than
// of the webserver.
define('NO_MOODLE_COOKIES', true);

require(__DIR__.'/../../config.php');

//HTTPS is required in this page when $CFG->loginhttps enabled
$PAGE->https_required();

$PAGE->set_context(context_system::instance());

$authsequence = get_enabled_auth_plugins(true); // auths, in sequence
if (!in_array('ldap', $authsequence, true)) {
    print_error('ldap_isdisabled', 'auth');
}

$authplugin = get_auth_plugin('ldap');
if (empty($authplugin->config->ntlmsso_enabled)) {
    print_error('ntlmsso_isdisabled', 'auth_ldap');
}

$sesskey = required_param('sesskey', PARAM_RAW);
$file = $CFG->dirroot.'/pix/spacer.gif';

if ($authplugin->ntlmsso_magic($sesskey) && file_exists($file)) {
    if (!empty($authplugin->config->ntlmsso_ie_fastpath)) {
        if (core_useragent::is_ie()) {
            // $PAGE->https_required() up above takes care of what $CFG->httpswwwroot should be.
            redirect($CFG->httpswwwroot.'/auth/ldap/ntlmsso_finish.php');
        }
    }

    // Serve GIF
    // Type
    header('Content-Type: image/gif');
    header('Content-Length: '.filesize($file));

    // Output file
    $handle = fopen($file, 'r');
    fpassthru($handle);
    fclose($handle);
    exit;
} else {
    print_error('ntlmsso_iwamagicnotenabled', 'auth_ldap');
}


