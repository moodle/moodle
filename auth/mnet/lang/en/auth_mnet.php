<?php // $Id$ 
$string['sso_idp_name']                = 'SSO  (Identity Provider)';
$string['sso_idp_description']         = 'Publish this service to allow your users to roam to the $a Moodle site without having to re-login there. '.
                                         '<ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on $a.</li></ul><br />'.
                                         'Subscribe to this service to allow authenticated users from $a to access your site without having to re-login. '.
                                         '<ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Service Provider) service to $a.</li></ul><br />';

$string['sso_sp_name']                 = 'SSO (Service Provider)';
$string['sso_sp_description']          = 'Publish  this service to allow authenticated users from $a to access your site without having to re-login. '.
                                         '<ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Identity Provider) service on $a.</li></ul><br />'.
                                         'Subscribe to this service to allow your users to roam to the $a Moodle site without having to re-login there. '.
                                         '<ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identity Provider) service to $a.</li></ul><br />';
$string['sso_mnet_login_refused']      = 'Username $a[0] is not permitted to login from $a[1].';
// MNET plugin
$string['auth_mnettitle'] = 'Moodle Network authentication';
$string['auth_mnetdescription'] = 'Users are authenticated according to the web of trust defined in your Moodle Network settings.';
$string['auth_mnet_rpc_negotiation_timeout'] = 'The timeout in seconds for authentication over the XMLRPC transport.';
$string['auth_mnet_roamout'] = 'Your users can roam out to these hosts';
$string['auth_mnet_roamin'] = 'These host\'s users can roam in to your site';
$string['auth_mnet_auto_add_remote_users'] = 'When set to Yes, a local user record is auto-created when a remote user logs in for the first time.';
$string['auto_add_remote_users'] = 'Auto add remote users';
$string['rpc_negotiation_timeout'] = 'RPC negotiation timeout';
?>