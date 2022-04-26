<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Lang strings
 *
 * @package    report_security
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['configuration'] = 'Configuration';
$string['description'] = 'Description';
$string['details'] = 'Details';
$string['check_configrw_details'] = '<p>It is recommended that the file permissions of <code>config.php</code> are changed after installation so that the file cannot be modified by the web server.
Please note that this measure does not improve security of the server significantly, though it may slow down or limit general exploits.</p>';
$string['check_configrw_name'] = 'Writable config.php';
$string['check_configrw_ok'] = 'config.php can not be modified by PHP scripts.';
$string['check_configrw_warning'] = 'PHP scripts may modify config.php.';
$string['check_cookiesecure_details'] = '<p>If https communication is enabled, it is recommended to enable sending of secure cookies. You should have permanent redirection from http to https and ideally serve HSTS headers as well.</p>';
$string['check_cookiesecure_error'] = 'Please enable secure cookies';
$string['check_cookiesecure_http'] = 'You must turn on https in order to use secure cookies';
$string['check_cookiesecure_name'] = 'Secure cookies';
$string['check_cookiesecure_ok'] = 'Secure cookies enabled.';
$string['check_defaultuserrole_details'] = '<p>All logged in users are given capabilities of the default user role. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for the default user role is <em>Authenticated user</em>. The course view capability must not be enabled.</p>';
$string['check_defaultuserrole_error'] = 'The default user role "{$a}" is incorrectly defined!';
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
$string['check_frontpagerole_details'] = '<p>The default site home role is given to all authenticated users for site home activities. Please make sure no risky capabilities are allowed for this role.</p>
<p>It is recommended that a special role is created for this purpose and a legacy type role is not used.</p>';
$string['check_frontpagerole_error'] = 'Incorrectly defined site home role "{$a}" detected!';
$string['check_frontpagerole_name'] = 'Site home role';
$string['check_frontpagerole_notset'] = 'Site home role is not set.';
$string['check_frontpagerole_ok'] = 'Site home role definition is OK.';
$string['check_crawlers_details'] = '<p>The "Open to search engines" setting enables search engines to enter courses with guest access. There is no point in enabling this setting if guest login is not allowed.</p>';
$string['check_crawlers_error'] = 'Search engine access is allowed but guest access is disabled.';
$string['check_crawlers_info'] = 'Search engines may enter as guests.';
$string['check_crawlers_name'] = 'Open to search engines';
$string['check_crawlers_ok'] = 'Search engine access is not enabled.';

$string['check_antivirus_details'] = 'This status checks whether or not there has been a recent error detected based on the threshold set in the main antivirus settings.';
$string['check_antivirus_error'] = '{$a->errors} errors have been detected within the last {$a->lookback}';
$string['check_antivirus_info'] = 'No antivirus scanners are currently enabled';
$string['check_antivirus_name'] = 'Antivirus';
$string['check_antivirus_ok'] = '{$a->scanners} antivirus scanner(s) enabled, no issues have been detected in the last {$a->lookback}';
$string['check_antivirus_logstore_not_supported'] = 'Unable to verify state of antivirus scanners due to the type of log store chosen';

$string['check_dotfiles_info'] = 'All dotfiles except /.well-known/* should not be public';
$string['check_dirindex_info'] = 'Directory index should not be enabled';
$string['check_guestrole_details'] = '<p>The guest role is used for guests, not logged in users and temporary guest course access. Please make sure no risky capabilities are allowed in this role.</p>
<p>The only supported legacy type for guest role is <em>Guest</em>.</p>';
$string['check_guestrole_error'] = 'The guest role "{$a}" is incorrectly defined!';
$string['check_guestrole_name'] = 'Guest role';
$string['check_guestrole_notset'] = 'Guest role is not set.';
$string['check_guestrole_ok'] = 'Guest role definition is OK.';
$string['check_nodemodules_details'] = '<p>The directory <code>{$a->path}</code> contains Node.js modules and their dependencies, typically installed by the NPM utility. These modules may be needed for local Moodle development, such as for using the grunt framework. They are not needed to run a Moodle site in production and they can contain potentially dangerous code exposing your site to remote attacks.</p><p>It is strongly recommended to remove the directory if the site is available via a public URL, or at least prohibit web access to it in your webserver configuration.</p>';
$string['check_nodemodules_info'] = 'The node_modules directory should not be present on public sites.';
$string['check_nodemodules_name'] = 'Node.js modules directory';
$string['check_openprofiles_details'] = 'Open user profiles can be abused by spammers. It is recommended that either <code>Force users to log in for profiles</code> or <code>Force users to log in</code> are enabled.';
$string['check_openprofiles_error'] = 'Anyone can may view user profiles without logging in.';
$string['check_openprofiles_name'] = 'Open user profiles';
$string['check_openprofiles_ok'] = 'Login is required before viewing user profiles.';
$string['check_passwordpolicy_details'] = '<p>It is recommended that a password policy is set, since password guessing is very often the easiest way to gain unauthorised access.
Do not make the requirements too strict though, as this can result in users not being able to remember their passwords and either forgetting them or writing them down.</p>';
$string['check_passwordpolicy_error'] = 'Password policy not set.';
$string['check_passwordpolicy_name'] = 'Password policy';
$string['check_passwordpolicy_ok'] = 'Password policy enabled.';
$string['check_preventexecpath_name'] = 'Executable paths';
$string['check_preventexecpath_ok'] = 'Executable paths only settable in config.php.';
$string['check_preventexecpath_warning'] = 'Executable paths can be set in the Admin GUI.';
$string['check_preventexecpath_details'] = '<p>Allowing executable paths to be set via the Admin GUI is a vector for privilege escalation. This must be forced in config.php:</p><p><code>$CFG->preventexecpath = true;</code></p>';
$string['check_publicpaths_name'] = 'Check all public / private paths';
$string['check_publicpaths_ok'] = 'All internal paths are not publicly accessible';
$string['check_publicpaths_warning'] = 'Some internal paths are publicly accessible';
$string['check_publicpaths_generic'] = '{$a} files should not be public';
$string['check_publicpaths_403'] = ' (Returned a 403, ideally should be 404)';
$string['check_riskadmin_detailsok'] = '<p>Please verify the following list of system administrators:</p>{$a}';
$string['check_riskadmin_detailswarning'] = '<p>Please verify the following list of system administrators:</p>{$a->admins}
<p>It is recommended to assign administrator role in the system context only. The following users have (unsupported) admin role assignments in other contexts:</p>{$a->unsupported}';
$string['check_riskadmin_name'] = 'Administrators';
$string['check_riskadmin_ok'] = 'Found {$a} server administrator(s).';
$string['check_riskadmin_unassign'] = '<a href="{$a->url}">{$a->fullname} ({$a->email}) review role assignment</a>';
$string['check_riskadmin_warning'] = 'Found {$a->admincount} server administrators and {$a->unsupcount} unsupported admin role assignments.';
$string['check_riskbackup_detailsok'] = 'No roles explicitly allow backup of user data.  However, note that admins with the "doanything" capability are still likely to be able to do this.';
$string['check_riskbackup_details_overriddenroles'] = '<p>These active overrides give users the ability to include user data in backups. Please make sure this permission is necessary.</p> {$a}';
$string['check_riskbackup_details_systemroles'] = '<p>The following system roles currently allow users to include user data in backups.  Please make sure this permission is necessary.</p> {$a}';
$string['check_riskbackup_details_users'] = '<p>Because of the above roles or local overrides, the following user accounts currently have permission to make backups containing private data from any users enrolled in their course.  Make sure they are (a) trusted and (b) protected by strong passwords:</p> {$a}';
$string['check_riskbackup_editoverride'] = '<a href="{$a->url}">{$a->name} in {$a->contextname}</a>';
$string['check_riskbackup_editrole'] = '<a href="{$a->url}">{$a->name}</a>';
$string['check_riskbackup_name'] = 'Backup of user data';
$string['check_riskbackup_ok'] = 'No roles explicitly allow backup of user data';
$string['check_riskbackup_unassign'] = '<a href="{$a->url}">{$a->fullname} ({$a->email}) in {$a->contextname}</a>';
$string['check_riskbackup_warning'] = 'Found {$a->rolecount} roles, {$a->overridecount} overrides and {$a->usercount} users with the ability to backup user data.';
$string['check_riskxss_details'] = '<p>RISK_XSS denotes all dangerous capabilities that only trusted users may use.</p>
<p>Please verify the following list of users and make sure that you trust them completely on this server:</p><p>{$a}</p>';
$string['check_riskxss_name'] = 'XSS trusted users';
$string['check_riskxss_warning'] = 'RISK_XSS - found {$a} users that have to be trusted.';
$string['check_unsecuredataroot_details'] = '<p>The dataroot directory must not be accessible via web. The best way to make sure the directory is not accessible is to use a directory outside the public web directory.</p>
<p>If you move the directory, you need to update the <code>$CFG->dataroot</code> setting in <code>config.php</code> accordingly.</p>';
$string['check_unsecuredataroot_error'] = 'Your dataroot directory <code>{$a}</code> is in the wrong location and is exposed to the web!';
$string['check_unsecuredataroot_name'] = 'Insecure dataroot';
$string['check_unsecuredataroot_ok'] = 'Dataroot directory must not be accessible via the web.';
$string['check_unsecuredataroot_warning'] = 'Your dataroot directory <code>{$a}</code> is in the wrong location and might be exposed to the web.';
$string['check_vendordir_details'] = '<p>The directory <code>{$a->path}</code> contains various third-party libraries and their dependencies, typically installed by the PHP Composer. These libraries may be needed for local Moodle development, such as for installing the PHPUnit framework. They are not needed to run a Moodle site in production and they can contain potentially dangerous code exposing your site to remote attacks.</p><p>It is strongly recommended to remove the directory if the site is available via a public URL, or at least prohibit web access to it in your webserver configuration.</p>';
$string['check_vendordir_info'] = 'The vendor directory should not be present on public sites.';
$string['check_vendordir_name'] = 'Vendor directory';
$string['check_webcron_details'] = '<p>Running the cron from a web browser can expose privileged information to anonymous users. It is recommended to only run the cron from the command line or set a cron password for remote access.</p>';
$string['check_webcron_warning'] = 'Anonymous users can access cron.';
$string['check_webcron_name'] = 'Web cron';
$string['check_webcron_ok'] = 'Anonymous users can not access cron.';
$string['eventreportviewed'] = 'Viewed security check report';
$string['issue'] = 'Issue';
$string['pluginname'] = 'Security checks';
$string['security:view'] = 'View security report';
$string['timewarning'] = 'Data processing may take a long time, please be patient...';
$string['privacy:metadata'] = 'The Security overview plugin does not store any personal data.';

// Deprecated since Moodle 4.0.
$string['check_mediafilterswf_details'] = '<p>Automatic swf embedding is very dangerous - any registered user may launch an XSS attack against other server users. Please disable it on production servers.</p>';
$string['check_mediafilterswf_error'] = 'Flash media filter is enabled - this is very dangerous for the majority of servers.';
$string['check_mediafilterswf_name'] = 'Enabled .swf media filter';
$string['check_mediafilterswf_ok'] = 'Flash media filter is not enabled.';
