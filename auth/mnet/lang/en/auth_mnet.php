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
$string['rpc_negotiation_timeout'] = 'RPC negotiation timeout';
$string['sso_idp_description'] = 'Publish this service to allow your users to roam to the {$a} site without having to re-login there. <ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Service Provider) service on {$a}.</li></ul><br />Subscribe to this service to allow authenticated users from {$a} to access your site without having to re-login. <ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Service Provider) service to {$a}.</li></ul><br />';
$string['sso_idp_name'] = 'SSO  (Identity Provider)';
$string['sso_mnet_login_refused'] = 'Username {$a->user} is not permitted to login from {$a->host}.';
$string['sso_sp_description'] = 'Publish  this service to allow authenticated users from {$a} to access your site without having to re-login. <ul><li><em>Dependency</em>: You must also <strong>subscribe</strong> to the SSO (Identity Provider) service on {$a}.</li></ul><br />Subscribe to this service to allow your users to roam to the {$a} site without having to re-login there. <ul><li><em>Dependency</em>: You must also <strong>publish</strong> the SSO (Identity Provider) service to {$a}.</li></ul><br />';
$string['sso_sp_name'] = 'SSO (Service Provider)';
$string['pluginname'] = 'MNet authentication';
$string['privacy:metadata'] = 'The MNet authentication plugin does not store any personal data.';
