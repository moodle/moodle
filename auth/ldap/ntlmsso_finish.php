<?php

require(__DIR__.'/../../config.php');

$PAGE->set_url('/auth/ldap/ntlmsso_finish.php');
$PAGE->set_context(context_system::instance());

// Define variables used in page
$site = get_site();

$authsequence = get_enabled_auth_plugins(); // Auths, in sequence.
if (!in_array('ldap', $authsequence, true)) {
    throw new \moodle_exception('ldap_isdisabled', 'auth');
}

$authplugin = get_auth_plugin('ldap');
if (empty($authplugin->config->ntlmsso_enabled)) {
    throw new \moodle_exception('ntlmsso_isdisabled', 'auth_ldap');
}

// If ntlmsso_finish() succeeds, then the code never returns,
// so we only worry about failure.
if (!$authplugin->ntlmsso_finish()) {
    // Redirect to login, saying "don't try again!"
    // Display the page header. This makes redirect respect the timeout we specify
    // here (and not add 3 more secs).
    $loginsite = get_string("loginsite");
    $PAGE->navbar->add($loginsite);
    $PAGE->set_title("$site->fullname: $loginsite");
    $PAGE->set_heading($site->fullname);
    echo $OUTPUT->header();
    redirect($CFG->wwwroot . '/login/index.php?authldap_skipntlmsso=1',
             get_string('ntlmsso_failed','auth_ldap'), 3);
}
