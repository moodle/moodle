<?PHP  // $Id$


//NOTE TO TRANSLATORS: please do not translate yet, we are going to finalise this file sometime in January and backport to 1.9.x ;-)

$string['configuration'] = 'Configuration';
$string['details'] = 'Details';
$string['description'] = 'Description';
$string['issue'] = 'Issue';
$string['reportsecurity'] = 'Security overview';
$string['security:view'] = 'View security report';
$string['status'] = 'Status';
$string['statuscritical'] = 'Critical';
$string['statusinfo'] = 'Information';
$string['statusok'] = 'OK';
$string['statusserious'] = 'Serious';
$string['statuswarning'] = 'Warning';

$string['check_configrw_details'] = '<p>It is recommended to change file permissions of config.php script after installation so that the file can not be modified by web server.
Please note that this measure does not improve security of the server significantly, but on the other hand it might slow down or limit general exploits.</p>';
$string['check_configrw_name'] = 'Writable config.php';
$string['check_configrw_ok'] = 'config.php can not be modified by PHP scripts.';
$string['check_configrw_warning'] = 'PHP scripts may modify config.php.';

$string['check_cookiesecure_details'] = '<p>If you enable https communication it is recommended to enable secure cookies. You should also add permanent redirection from http to https.</p>';
$string['check_cookiesecure_error'] = 'Please enable secure cookies';
$string['check_cookiesecure_name'] = 'Secure cookies';
$string['check_cookiesecure_ok'] = 'Secure cookies enabled.';

$string['check_courserole_anything'] = 'Do anything capability must not be allowed in this <a href=\"$a\">context</a>.';
$string['check_courserole_details'] = '<p>Each course has one default enrolment role specified. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for course default role is <em>Student</em>.</p>';
$string['check_courserole_error'] = 'Incorrectly defined course default roles detected!';
$string['check_courserole_legacy'] = 'Unsupported legacy type detected in <a href=\"$a\">role</a>.';
$string['check_courserole_name'] = 'Course default roles';
$string['check_courserole_notyet'] = 'Used only default course role.';
$string['check_courserole_ok'] = 'Course default role definitions ok.';
$string['check_courserole_risky'] = 'Risky capabilities detected in <a href=\"$a\">context</a>.';

$string['check_defaultcourserole_anything'] = 'Do anything capability must not be allowed in this <a href=\"$a\">context</a>.';
$string['check_defaultcourserole_details'] = '<p>Default student role for course enrolment specifies the default role for courses. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for default role is <em>Student</em>.</p>';
$string['check_defaultcourserole_error'] = 'Incorrectly defined default course role \"$a\" detected!';
$string['check_defaultcourserole_legacy'] = 'Unsupported legacy type detected.';
$string['check_defaultcourserole_name'] = 'Site default course role';
$string['check_defaultcourserole_notset'] = 'Default role is not set.';
$string['check_defaultcourserole_ok'] = 'Site default role definition ok.';
$string['check_defaultcourserole_risky'] = 'Risky capabilities detected in <a href=\"$a\">context</a>.';

$string['check_defaultuserrole_details'] = '<p>All logged in users are given capabilities of the default user role. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for default user role is <em>Authenticated user</em>. Course view capability must not be enabled.</p>';
$string['check_defaultuserrole_error'] = 'Incorrectly defined default user role \"$a\" detected!';
$string['check_defaultuserrole_name'] = 'Registered user role';
$string['check_defaultuserrole_notset'] = 'Default role is not set.';
$string['check_defaultuserrole_ok'] = 'Registered user role definition ok.';

$string['check_displayerrors_details'] = '<p>Enabling the PHP setting <code>display_errors</code> is not recommended on production sites because some error messages may reveal sensitive information about your server.</p>';
$string['check_displayerrors_error'] = 'PHP errors displaying is enabled. It is recommended to disable displaying of errors in PHP configuration.';
$string['check_displayerrors_name'] = 'Displaying of PHP errors';
$string['check_displayerrors_ok'] = 'Displaying of PHP errors disabled.';

$string['check_emailchangeconfirmation_details'] = '<p>It is recommended to require email confirmation step when user enters a new email address in user profile. If disabled spammers might try to exploit server for resending of spam.</p>';
$string['check_emailchangeconfirmation_error'] = 'Users may enter any email address.';
$string['check_emailchangeconfirmation_name'] = 'Email change confirmation';
$string['check_emailchangeconfirmation_ok'] = 'Changing of email must be confirmed.';

$string['check_embed_details'] = '<p>Unlimited object embedding is very dangerous - any registered user may launch XSS attack against other server users. Please disable it on production servers.</p>';
$string['check_embed_error'] = 'Unlimited object embedding enabled - this is very dangerous for majority of servers.';
$string['check_embed_name'] = 'Allow EMBED and OBJECT';
$string['check_embed_ok'] = 'Unlimited object embedding not allowed.';

$string['check_frontpagerole_details'] = '<p>Frontpage role is give to all registered users on frontpage. Please make sure no risky capabilities are allowed in this role.</p>
<p>It is recommended to create a special role only for this purpose and not set any legacy type.</p>';
$string['check_frontpagerole_error'] = 'Incorrectly defined frontpage role \"$a\" detected!';
$string['check_frontpagerole_name'] = 'Frontpage role';
$string['check_frontpagerole_notset'] = 'Frontpage role is not set.';
$string['check_frontpagerole_ok'] = 'Frontpage role definition ok.';

$string['check_globals_details'] = '<p>Register globals is considered to be a highly insecure PHP setting, there is no reason why it should be enabled. Moodle is not compatible with register globals.</p>
<p><code>register_globals=off</code> must be set in PHP configuration. This setting is controlled by editing your <code>php.ini</code>, Apache/IIS configuration or <code>.htaccess</code> file.</p>';
$string['check_globals_error'] = 'Register globals MUST be disabled. Please fix server PHP settings immediately!';
$string['check_globals_name'] = 'Register globals';
$string['check_globals_ok'] = 'Register globals are disabled.';

$string['check_google_details'] = '<p>Open to Google settings helps search engines enter courses with guest access. Please note this settings is not expected to be enabled if guest login not allowed.</p>';
$string['check_google_error'] = 'Search engines guest access allowed and guest access disabled.';
$string['check_google_info'] = 'Search engines may enter as guests.';
$string['check_google_name'] = 'Open to Google';
$string['check_google_ok'] = 'Search engines guest access not enabled.';

$string['check_guestrole_details'] = '<p>Guest role is used for guests, not logged in users and temporary guest course access. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for guest role is <em>Guest</em>.</p>';
$string['check_guestrole_error'] = 'Incorrectly defined guest role \"$a\" detected!';
$string['check_guestrole_name'] = 'Guest role';
$string['check_guestrole_notset'] = 'Guest role is not set.';
$string['check_guestrole_ok'] = 'Guest role definition ok.';

$string['check_mediafilterswf_details'] = '<p>Automatic swf embedding is very dangerous - any registered user may launch XSS attack against other server users. Please disable it on production servers.</p>';
$string['check_mediafilterswf_error'] = 'Flash media filter is enabled - this is very dangerous for majority of servers.';
$string['check_mediafilterswf_name'] = 'Enabled .swf media filter';
$string['check_mediafilterswf_ok'] = 'Flash media filter is not enabled.';

$string['check_noauth_details'] = '<p><em>No authentication</em> plugin is not intended for any production sites. Please disable it unless this is a development test site.</p>';
$string['check_noauth_error'] = 'No authentication pluing can not be used on production sites.';
$string['check_noauth_name'] = 'No authentication';
$string['check_noauth_ok'] = 'No authentication plugin is disabled.';

$string['check_openprofiles_details'] = '<p>Open user profiles are often abused by spammers, it is usually recommended to enable <code>Force users to login for profiles</code> or <code>Force users to login</code> if you require login before any access.</p>';
$string['check_openprofiles_error'] = 'Anybody may view user profiles without logging in.';
$string['check_openprofiles_name'] = 'Open user profiles';
$string['check_openprofiles_ok'] = 'Login is required before viewing user profile.';

$string['check_passwordpolicy_details'] = '<p>It is recommended to enforce user password policy because password guessing is very often the easiest way to gain unauthorised access.
Do not make the requirements too strict, because users would not be able to remember their passwords and would keep forgetting them or write them down.</p>';
$string['check_passwordpolicy_error'] = 'Password policy not set.';
$string['check_passwordpolicy_name'] = 'Password policy';
$string['check_passwordpolicy_ok'] = 'Password policy enabled.';

$string['check_riskadmin_detailsok'] = '<p>Please verify following list of administrators.<br />$a</p>';
$string['check_riskadmin_detailswarning'] = '<p>Please verify following list of administrators:<br />$a->admins</p>
<p>It is recommended to assign administrator role in system context only. Following users have unsuported admin role assignments:<br />$a->unsupported</p>';
$string['check_riskadmin_name'] = 'Administrators';
$string['check_riskadmin_ok'] = 'Found $a server administrators.';
$string['check_riskadmin_warning'] = 'Found $a->admincount server administrators and $a->unsupcount unsuported admin role assignments.';

$string['check_riskxss_details'] = '<p>RISK_XSS marks all dangerous capabilities that only trusted users may use.</p>
<p>Please verify following list of users and make sure that you trust them completely on this server:<br />$a</p>';
$string['check_riskxss_name'] = 'XSS trusted users';
$string['check_riskxss_warning'] = 'RISK_XSS - found $a users that have to be trusted.';

$string['check_unsecuredataroot_details'] = '<p>Dataroot directory must not be accessible via web. The best way to make sure the directory is not accessible is to use directory outside of public web directory.</p>
<p>If you move the directory you need to update <code>\$CFG->dataroot</code> setting in <code>config.php</code> accordingly.</p>';
$string['check_unsecuredataroot_error'] = 'Your dataroot directory <code>$a</code> is in the wrong location and is exposed to the web!';
$string['check_unsecuredataroot_name'] = 'Unsecure dataroot';
$string['check_unsecuredataroot_ok'] = 'Dataroot directory must not be accessible via web.';
$string['check_unsecuredataroot_warning'] = 'Your dataroot directory <code>$a</code> is in the wrong location and might be exposed to the web.';

?>
