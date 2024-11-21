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
 * Strings for component 'auth_casattras', language 'en'.
 *
 * @package   auth_casattras
 * @author Adam Franco
 * @copyright 2014 Middlebury College  {@link http://www.middlebury.edu}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_casattras_auth_logo_description'] = 'Provide a logo for the CAS authentication method that is familiar to your users.';
$string['auth_casattras_auth_logo'] = 'Authentication method logo';
$string['auth_casattras_auth_name_description'] = 'Provide a name for the CAS authentication method that is familiar to your users.';
$string['auth_casattras_auth_name'] = 'Authentication method name';
$string['auth_casattras_auth_service'] = 'CAS';
$string['auth_casattras_baseuri_key'] = 'Base URI';
$string['auth_casattras_baseuri'] = 'URI of the server (nothing if no baseUri)<br />For example, if the CAS server responds to login.example.edu/cas/ then<br />cas_baseuri = cas/';
$string['auth_casattras_casversion'] = 'CAS protocol version';
$string['auth_casattras_certificate_check_key'] = 'Server validation';
$string['auth_casattras_certificate_check'] = 'Select \'yes\' if you want to validate the server certificate';
$string['auth_casattras_certificate_path_empty'] = 'If you turn on Server validation, you need to specify a certificate path';
$string['auth_casattras_certificate_path_empty'] = 'If you turn on Server validation, you need to specify a certificate path';
$string['auth_casattras_certificate_path_key'] = 'Certificate path';
$string['auth_casattras_certificate_path'] = 'Path of the CA chain file (PEM Format) to validate the server certificate';
$string['auth_casattras_extrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the CAS authentication response if supported by your CAS server. <p>If you leave these fields blank, then nothing beyond the user id added to the user\'s account and Moodle defaults will be used instead.</p><p>In either case, the user will be able to edit all of these fields after they log in.</p>';
$string['auth_casattras_hostname_key'] = 'Hostname';
$string['auth_casattras_hostname'] = 'Hostname of the CAS server <br />eg: login.example.edu';
$string['auth_casattras_logout_return_url_key'] = 'Alternative logout return URL';
$string['auth_casattras_logout_return_url'] = 'Provide the URL that CAS users shall be redirected to after logging out.<br />If left empty, users will be redirected to the location that moodle will redirect users to';
$string['auth_casattras_logoutcas_key'] = 'CAS logout option';
$string['auth_casattras_logoutcas'] = 'Select \'yes\' if you want to logout from CAS when you disconnect from Moodle';
$string['auth_casattras_multiauth_key'] = 'Multi-authentication';
$string['auth_casattras_multiauth'] = 'Select \'yes\' if you want to have multi-authentication (CAS + other authentication)';
$string['auth_casattras_port_key'] = 'Port';
$string['auth_casattras_port'] = 'Port of the CAS server';
$string['auth_casattras_proxycas_key'] = 'Proxy mode';
$string['auth_casattras_proxycas'] = 'Select \'yes\' if you use CAS in proxy-mode';
$string['auth_casattras_server_settings'] = 'CAS server configuration';
$string['auth_casattras_version'] = 'CAS protocol version to use. Note that only SAML 1.1 supports attribute release by default. The CAS 2.0 response is often customized to return attributes.';
$string['auth_casattrasdescription'] = 'This method uses a CAS server (Central Authentication Service) to authenticate users in a Single Sign On environment (SSO). User attributes are returned in the CAS authentication response rather than from an LDAP server. This allows usage of CAS servers that are not backed by an LDAP server or are backed by multiple LDAP servers. If the given username and password are valid according to CAS, Moodle creates a new user entry in its database, taking user attributes from the CAS authentication response if configured.';
$string['cas_conflict_warning'] = 'Not compatible with "CAS server (SSO)", disable that plugin first.';
$string['casattras_disabled_by_cas'] = '"CAS server (SSO) with user-attribute release" was disabled because the "CAS server (SSO)" plugin is enable. These authentication plugins will conflict with each other and only one can be enabled at a time.';
$string['convert_authtype_cas_to_casattras'] = 'Convert {$a->cas} users from \'CAS server (SSO)\' to \'CAS server (SSO) with user-attribute release\'';
$string['convert_authtype_casattras_to_cas'] = 'Convert {$a->casattras} users from \'CAS server (SSO) with user-attribute release\' to \'CAS server (SSO)\'';
$string['convert_authtype_none'] = 'No conversion';
$string['convert_authtype'] = 'Convert:';
$string['convert_user_auth_types'] = 'Convert users\' authentication types (optional)';
$string['pluginname'] = 'CAS server (SSO) with user-attribute release';
$string['privacy:metadata'] = 'The CAS attributes authentication plugin does not store any personal data.';
