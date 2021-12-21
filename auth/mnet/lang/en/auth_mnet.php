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
 * Strings for component 'auth_mnet', language 'en'.
 *
 * @package   auth_mnet
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_mnet_auto_add_remote_users'] = 'When set to Yes, a local user record is auto-created when a remote user logs in for the first time.';
$string['auth_mnetdescription'] = 'Users are authenticated according to the web of trust defined in your Moodle Network settings.';
$string['auth_mnet_roamin'] = 'These host\'s users can roam in to your site';
$string['auth_mnet_roamout'] = 'Your users can roam out to these hosts';
$string['auth_mnet_rpc_negotiation_timeout'] = 'The timeout in seconds for authentication over the XMLRPC transport.';
$string['auto_add_remote_users'] = 'Auto add remote users';
$string['crontask'] = 'Background processing for MNET authentication';
$string['rpc_negotiation_timeout'] = 'RPC negotiation timeout';
$string['sso_idp_description'] = 'Publish this service to allow your users to roam to the {$a} site without having to re-login there. <ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on {$a}.</li></ul><br />Subscribe to this service to allow authenticated users from {$a} to access your site without having to re-login. <ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Service Provider) service to {$a}.</li></ul><br />';
$string['sso_idp_name'] = 'SSO  (Identity Provider)';
$string['sso_mnet_login_refused'] = 'Username {$a->user} is not permitted to login from {$a->host}.';
$string['sso_sp_description'] = 'Publish  this service to allow authenticated users from {$a} to access your site without having to re-login. <ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Identity Provider) service on {$a}.</li></ul><br />Subscribe to this service to allow your users to roam to the {$a} site without having to re-login there. <ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identity Provider) service to {$a}.</li></ul><br />';
$string['sso_sp_name'] = 'SSO (Service Provider)';
$string['pluginname'] = 'MNet authentication';
$string['privacy:metadata:external:mahara'] = 'This plugin can send data externally to a linked Mahara application.';
$string['privacy:metadata:external:moodle'] = 'This plugin can send data externally to a linked Moodle application.';
$string['privacy:metadata:mnet_external:address'] = 'The address of the user.';
$string['privacy:metadata:mnet_external:alternatename'] = 'An alternative name for the user.';
$string['privacy:metadata:mnet_external:autosubscribe'] = 'A preference as to if the user should be auto-subscribed to forums the user posts in.';
$string['privacy:metadata:mnet_external:calendartype'] = 'A user preference for the type of calendar to use.';
$string['privacy:metadata:mnet_external:city'] = 'The city of the user.';
$string['privacy:metadata:mnet_external:country'] = 'The country that the user is in.';
$string['privacy:metadata:mnet_external:currentlogin'] = 'The current login for this user.';
$string['privacy:metadata:mnet_external:department'] = 'The department that this user can be found in.';
$string['privacy:metadata:mnet_external:description'] = 'General details about this user.';
$string['privacy:metadata:mnet_external:email'] = 'An email address for contact.';
$string['privacy:metadata:mnet_external:emailstop'] = 'A preference to stop email being sent to the user.';
$string['privacy:metadata:mnet_external:firstaccess'] = 'The time that this user first accessed the site.';
$string['privacy:metadata:mnet_external:firstname'] = 'The first name of the user.';
$string['privacy:metadata:mnet_external:firstnamephonetic'] = 'The phonetic details about the user\'s first name.';
$string['privacy:metadata:mnet_external:id'] = 'The user ID';
$string['privacy:metadata:mnet_external:idnumber'] = 'An identification number given by the institution';
$string['privacy:metadata:mnet_external:imagealt'] = 'Alternative text for the user\'s image.';
$string['privacy:metadata:mnet_external:institution'] = 'The institution that this user is a member of.';
$string['privacy:metadata:mnet_external:lang'] = 'A user preference for the language shown.';
$string['privacy:metadata:mnet_external:lastaccess'] = 'The time that the user last accessed the site.';
$string['privacy:metadata:mnet_external:lastlogin'] = 'The last login of this user.';
$string['privacy:metadata:mnet_external:lastname'] = 'The surname of the user.';
$string['privacy:metadata:mnet_external:lastnamephonetic'] = 'The phonetic details about the user\'s surname.';
$string['privacy:metadata:mnet_external:maildigest'] = 'A setting for the mail digest for this user.';
$string['privacy:metadata:mnet_external:maildisplay'] = 'A preference for the user about displaying their email address to other users.';
$string['privacy:metadata:mnet_external:middlename'] = 'The middle name of the user';
$string['privacy:metadata:mnet_external:phone1'] = 'A phone number for the user.';
$string['privacy:metadata:mnet_external:phone2'] = 'An additional phone number for the user.';
$string['privacy:metadata:mnet_external:picture'] = 'The picture details associated with this user.';
$string['privacy:metadata:mnet_external:policyagreed'] = 'A flag to determine if the user has agreed to the site policy.';
$string['privacy:metadata:mnet_external:suspended'] = 'A flag to show if the user has been suspended on this system.';
$string['privacy:metadata:mnet_external:timezone'] = 'The timezone of the user';
$string['privacy:metadata:mnet_external:trackforums'] = 'A preference for forums and tracking them.';
$string['privacy:metadata:mnet_external:trustbitmask'] = 'The trust bit mask';
$string['privacy:metadata:mnet_external:username'] = 'The username for this user.';
$string['privacy:metadata:mnet_log'] = 'Details of remote actions carried out by a local user logged in a remote system.';
$string['privacy:metadata:mnet_log:action'] = 'Action carried out by the user.';
$string['privacy:metadata:mnet_log:cmid'] = 'ID of the course module.';
$string['privacy:metadata:mnet_log:course'] = 'Remote system course ID where the action occurred.';
$string['privacy:metadata:mnet_log:coursename'] = 'Remote system course full name where the action occurred.';
$string['privacy:metadata:mnet_log:hostid'] = 'Remote system MNet ID.';
$string['privacy:metadata:mnet_log:info'] = 'Additional information about the action.';
$string['privacy:metadata:mnet_log:ip'] = 'The IP address used at the time of the action occurred.';
$string['privacy:metadata:mnet_log:module'] = 'Remote system module where the action occurred.';
$string['privacy:metadata:mnet_log:remoteid'] = 'Remote ID of the user who carried out the action in the remote system.';
$string['privacy:metadata:mnet_log:time'] = 'Time when the action occurred.';
$string['privacy:metadata:mnet_log:url'] = 'Remote system URL where the action occurred.';
$string['privacy:metadata:mnet_log:userid'] = 'Local ID of the user who carried out the action in the remote system.';
$string['privacy:metadata:mnet_session'] = 'The details of each MNet user session in a remote system. The data is stored temporarily.';
$string['privacy:metadata:mnet_session:expires'] = 'Time when the session expires.';
$string['privacy:metadata:mnet_session:mnethostid'] = 'Remote system MNet ID.';
$string['privacy:metadata:mnet_session:token'] = 'Unique session identifier';
$string['privacy:metadata:mnet_session:useragent'] = 'User agent used to access the remote system';
$string['privacy:metadata:mnet_session:userid'] = 'ID of the user jumping to remote system.';
$string['privacy:metadata:mnet_session:username'] = 'Username of the user jumping to remote system.';
$string['unknownhost'] = 'Unknown host';

// Deprecated since Moodle 4.0.
$string['privacy:metadata:mnet_external:aim'] = 'The AIM identifier of the user';
$string['privacy:metadata:mnet_external:icq'] = 'The ICQ number of the user.';
$string['privacy:metadata:mnet_external:msn'] = 'The MSN identifier of the user';
$string['privacy:metadata:mnet_external:skype'] = 'The Skype identifier of the user';
$string['privacy:metadata:mnet_external:url'] = 'A URL related to this user.';
$string['privacy:metadata:mnet_external:yahoo'] = 'The Yahoo identifier of the user';
