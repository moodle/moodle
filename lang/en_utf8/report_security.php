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
$string['timewarning'] = 'Data processing may take a long time, please be patient...';

$string['check_configrw_details'] = '<p>It is recommended that the file permissions of config.php are changed after installation so that the file cannot be modified by the web server.
Please note that this measure does not improve security of the server significantly, though it may slow down or limit general exploits.</p>';
$string['check_configrw_name'] = 'Writable config.php';
$string['check_configrw_ok'] = 'config.php can not be modified by PHP scripts.';
$string['check_configrw_warning'] = 'PHP scripts may modify config.php.';

$string['check_cookiesecure_details'] = '<p>If you enable https communication it is recommended that you also enable secure cookies. You should also add permanent redirection from http to https.</p>';
$string['check_cookiesecure_error'] = 'Please enable secure cookies';
$string['check_cookiesecure_name'] = 'Secure cookies';
$string['check_cookiesecure_ok'] = 'Secure cookies enabled.';

$string['check_courserole_anything'] = 'The \"doanything\" capability must not be allowed in this <a href=\"$a\">context</a>.';
$string['check_courserole_details'] = '<p>Each course has one default enrolment role specified. Please make sure no risky capabilities are allowed for this role.</p>
<p>The only supported legacy type for the default course role is <em>Student</em>.</p>';
$string['check_courserole_error'] = 'Incorrectly defined default course roles detected!';
$string['check_courserole_riskylegacy'] = 'Risky legacy type detected in <a href=\"$a->url\">$a->shortname</a>.';
$string['check_courserole_name'] = 'Default roles (courses)';
$string['check_courserole_notyet'] = 'Used only default course role.';
$string['check_courserole_ok'] = 'Default course role definitions is OK.';
$string['check_courserole_risky'] = 'Risky capabilities detected in <a href=\"$a\">context</a>.';

$string['check_defaultcourserole_anything'] = 'The \"doanything\" capability must not be allowed in this <a href=\"$a\">context</a>.';
$string['check_defaultcourserole_details'] = '<p>The default student role for course enrolment specifies the default role for courses. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for default role is <em>Student</em>.</p>';
$string['check_defaultcourserole_error'] = 'Incorrectly defined default course role \"$a\" detected!';
$string['check_defaultcourserole_legacy'] = 'Risky legacy type detected.';
$string['check_defaultcourserole_name'] = 'Default course role (global)';
$string['check_defaultcourserole_notset'] = 'Default role is not set.';
$string['check_defaultcourserole_ok'] = 'Site default role definition is OK.';
$string['check_defaultcourserole_risky'] = 'Risky capabilities detected in <a href=\"$a\">context</a>.';

$string['check_defaultuserrole_details'] = '<p>All logged in users are given capabilities of the default user role. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for the default user role is <em>Authenticated user</em>. The course view capability must not be enabled.</p>';
$string['check_defaultuserrole_error'] = 'The default user role \"$a\" is incorrectly defined!';
$string['check_defaultuserrole_name'] = 'Default role for all users';
$string['check_defaultuserrole_notset'] = 'Default role is not set.';
$string['check_defaultuserrole_ok'] = 'Default role for all users definition is OK.';

$string['check_displayerrors_details'] = '<p>Enabling the PHP setting <code>display_errors</code> is not recommended on production sites because error messages can reveal sensitive information about your server.</p>';
$string['check_displayerrors_error'] = 'The PHP setting to display errors is enabled. It is recommended that this is disabled.';
$string['check_displayerrors_name'] = 'Displaying of PHP errors';
$string['check_displayerrors_ok'] = 'Displaying of PHP errors disabled.';

$string['check_emailchangeconfirmation_details'] = '<p>It is recommended that an email confirmation step is required when users change their email address in their profile. If disabled, spammers may try to exploit the server to send spam.</p>
<p>Email field may be also locked from authentication plugins, this possibility is not considered here.</p>';
$string['check_emailchangeconfirmation_error'] = 'Users may enter any email address.';
$string['check_emailchangeconfirmation_info'] = 'Users may enter email addresses from allowed domains only.';
$string['check_emailchangeconfirmation_name'] = 'Email change confirmation';
$string['check_emailchangeconfirmation_ok'] = 'Confirmation of change of email address in user profile.';

$string['check_embed_details'] = '<p>Unlimited object embedding is very dangerous - any registered user may launch an XSS attack against other server users. This setting should be disabled on production servers.</p>';
$string['check_embed_error'] = 'Unlimited object embedding enabled - this is very dangerous for the majority of servers.';
$string['check_embed_name'] = 'Allow EMBED and OBJECT';
$string['check_embed_ok'] = 'Unlimited object embedding is not allowed.';

$string['check_frontpagerole_details'] = '<p>The default frontpage role is given to all registered users for frontpage activities. Please make sure no risky capabilities are allowed for this role.</p>
<p>It is recommended that a special role is created for this purpose and a legacy type role is not used.</p>';
$string['check_frontpagerole_error'] = 'Incorrectly defined frontpage role \"$a\" detected!';
$string['check_frontpagerole_name'] = 'Frontpage role';
$string['check_frontpagerole_notset'] = 'Frontpage role is not set.';
$string['check_frontpagerole_ok'] = 'Frontpage role definition is OK.';

$string['check_globals_details'] = '<p>Register globals is considered to be a highly insecure PHP setting.</p>
<p><code>register_globals=off</code> must be set in PHP configuration. This setting is controlled by editing your <code>php.ini</code>, Apache/IIS configuration or <code>.htaccess</code> file.</p>';
$string['check_globals_error'] = 'Register globals MUST be disabled. Please fix the server PHP settings immediately!';
$string['check_globals_name'] = 'Register globals';
$string['check_globals_ok'] = 'Register globals are disabled.';

$string['check_google_details'] = '<p>The Open to Google setting enables search engines to enter courses with guest access. There is no point in enabling this setting if guest login is not allowed.</p>';
$string['check_google_error'] = 'Search engine access is allowed but guest access is disabled.';
$string['check_google_info'] = 'Search engines may enter as guests.';
$string['check_google_name'] = 'Open to Google';
$string['check_google_ok'] = 'Search engine access is not enabled.';

$string['check_guestrole_details'] = '<p>The guest role is used for guests, not logged in users and temporary guest course access. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for guest role is <em>Guest</em>.</p>';
$string['check_guestrole_error'] = 'The guest role \"$a\" is incorrectly defined!';
$string['check_guestrole_name'] = 'Guest role';
$string['check_guestrole_notset'] = 'Guest role is not set.';
$string['check_guestrole_ok'] = 'Guest role definition is OK.';

$string['check_mediafilterswf_details'] = '<p>Automatic swf embedding is very dangerous - any registered user may launch an XSS attack against other server users. Please disable it on production servers.</p>';
$string['check_mediafilterswf_error'] = 'Flash media filter is enabled - this is very dangerous for the majority of servers.';
$string['check_mediafilterswf_name'] = 'Enabled .swf media filter';
$string['check_mediafilterswf_ok'] = 'Flash media filter is not enabled.';

$string['check_noauth_details'] = '<p>The <em>No authentication</em> plugin is not intended for production sites. Please disable it unless this is a development test site.</p>';
$string['check_noauth_error'] = 'The No authentication plugin cannot be used on production sites.';
$string['check_noauth_name'] = 'No authentication';
$string['check_noauth_ok'] = 'No authentication plugin is disabled.';

$string['check_openprofiles_details'] = '<p>Open user profiles can be abused by spammers. It is recommended that either <code>Force users to login for profiles</code> or <code>Force users to login</code> are enabled.</p>';
$string['check_openprofiles_error'] = 'Anyone can may view user profiles without logging in.';
$string['check_openprofiles_name'] = 'Open user profiles';
$string['check_openprofiles_ok'] = 'Login is required before viewing user profiles.';

$string['check_passwordpolicy_details'] = '<p>It is recommended that a password policy is set, since password guessing is very often the easiest way to gain unauthorised access.
Do not make the requirements too strict though, as this can result in users not being able to remember their passwords and either forgetting them or writing them down.</p>';
$string['check_passwordpolicy_error'] = 'Password policy not set.';
$string['check_passwordpolicy_name'] = 'Password policy';
$string['check_passwordpolicy_ok'] = 'Password policy enabled.';

$string['check_passwordsaltmain_name'] = 'Password salt';
$string['check_passwordsaltmain_warning'] = 'No password salt has been set';
$string['check_passwordsaltmain_ok'] = 'Password salt is OK';
$string['check_passwordsaltmain_weak'] = 'Password salt is weak';
$string['check_passwordsaltmain_details'] = '<p>Setting a password salt greatly reduces the risk of password theft.</p>
<p>To set a password salt, add the following line to your config.php file:</p>
<code>\$CFG->passwordsaltmain = \'some long random string here with lots of characters\';</code>
<p>The random string of characters should be a mix of letters, numbers and other characters. A string length of at least 40 characters is recommended.</p>
<p>Please refer to the <a href=\"$a\" target=\"_blank\">password salting documentation</a> if you wish to change the password salt. Once set, do NOT delete your password salt otherwise you will no longer be able to login to your site!</p>';
$string['check_riskadmin_detailsok'] = '<p>Please verify the following list of system administrators:</p>$a';
$string['check_riskadmin_detailswarning'] = '<p>Please verify the following list of system administrators:</p>$a->admins
<p>It is recommended to assign administrator role in the system context only. The following users have (unsupported) admin role assignments in other contexts:</p>$a->unsupported';
$string['check_riskadmin_name'] = 'Administrators';
$string['check_riskadmin_ok'] = 'Found $a server administrator(s).';
$string['check_riskadmin_unassign'] = '<a href=\"$a->url\">$a->fullname ($a->email) review role assignment</a>';
$string['check_riskadmin_warning'] = 'Found $a->admincount server administrators and $a->unsupcount unsupported admin role assignments.';
$string['check_riskadmin_name'] = 'Administrators';

$string['check_riskbackup_name'] = 'Backup of user data';
$string['check_riskbackup_warning'] = 'Found $a->rolecount roles, $a->overridecount overrides and $a->usercount users with the ability to backup user data.';
$string['check_riskbackup_details_systemroles'] = '<p>The following system roles currently allow users to include user data in backups.  Please make sure this permission is necessary.</p> $a';
$string['check_riskbackup_details_overriddenroles'] = '<p>These active overrides give users the ability to include user data in backups. Please make sure this permission is necessary.</p> $a';
$string['check_riskbackup_details_users'] = '<p>Because of the above roles or local overrides, the following user accounts currently have permission to make backups containing private data from any users enrolled in their course.  Make sure they are (a) trusted and (b) protected by strong passwords:</p> $a';
$string['check_riskbackup_editrole'] = '<a href=\"$a->url\">$a->name</a>';
$string['check_riskbackup_editoverride'] = '<a href=\"$a->url\">$a->name in $a->contextname</a>';
$string['check_riskbackup_unassign'] = '<a href=\"$a->url\">$a->fullname ($a->email) in $a->contextname</a>';
$string['check_riskbackup_ok'] = 'No roles explicitly allow backup of user data';
$string['check_riskbackup_detailsok'] = 'No roles explicitly allow backup of user data.  However, note that admins with the \"doanything\" capability are still likely to be able to do this.';

$string['check_riskxss_details'] = '<p>RISK_XSS denotes all dangerous capabilities that only trusted users may use.</p>
<p>Please verify the following list of users and make sure that you trust them completely on this server:</p><p>$a</p>';
$string['check_riskxss_name'] = 'XSS trusted users';
$string['check_riskxss_warning'] = 'RISK_XSS - found $a users that have to be trusted.';

$string['check_unsecuredataroot_details'] = '<p>The dataroot directory must not be accessible via web. The best way to make sure the directory is not accessible is to use a directory outside the public web directory.</p>
<p>If you move the directory, you need to update the <code>\$CFG->dataroot</code> setting in <code>config.php</code> accordingly.</p>';
$string['check_unsecuredataroot_error'] = 'Your dataroot directory <code>$a</code> is in the wrong location and is exposed to the web!';
$string['check_unsecuredataroot_name'] = 'Insecure dataroot';
$string['check_unsecuredataroot_ok'] = 'Dataroot directory must not be accessible via the web.';
$string['check_unsecuredataroot_warning'] = 'Your dataroot directory <code>$a</code> is in the wrong location and might be exposed to the web.';
?>
