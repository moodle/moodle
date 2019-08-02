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
 * Strings for component 'auth_shibboleth', language 'en'.
 *
 * @package   auth_shibboleth
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_shib_auth_method'] = 'Authentication method name';
$string['auth_shib_auth_method_description'] = 'Provide a name for the Shibboleth authentication method that is familiar to your users. This could be the name of your Shibboleth federation, e.g. <tt>SWITCHaai Login</tt> or <tt>InCommon Login</tt> or similar.';
$string['auth_shib_auth_logo'] = 'Authentication method logo';
$string['auth_shib_auth_logo_description'] = 'Provide a logo for the Shibboleth authentication method that is familiar to your users. This could be the logo of your Shibboleth federation, e.g. <tt>SWITCHaai Login</tt> or <tt>InCommon Login</tt> or similar.';
$string['auth_shib_contact_administrator'] = 'In case you are not associated with the given organizations and you need access to a course on this server, please contact the <a href="mailto:{$a}">Moodle Administrator</a>.';
$string['auth_shibbolethdescription'] = 'Using this method users are created and authenticated using Shibboleth. For set-up details, see the <a href="../auth/shibboleth/README.txt">Shibboleth README</a>.';
$string['auth_shibboleth_errormsg'] = 'Please select the organization you are member of!';
$string['auth_shibboleth_login'] = 'Shibboleth login';
$string['auth_shibboleth_login_long'] = 'Login to Moodle via Shibboleth';
$string['auth_shibboleth_manual_login'] = 'Manual login';
$string['auth_shibboleth_select_member'] = 'I\'m a member of ...';
$string['auth_shibboleth_select_organization'] = 'For authentication via Shibboleth, please select your organisation from the drop-down menu:';
$string['auth_shib_convert_data'] = 'Data modification API';
$string['auth_shib_convert_data_description'] = 'You can use this API to further modify the data provided by Shibboleth. Read the <a href="../auth/shibboleth/README.txt">README</a> for further instructions.';
$string['auth_shib_convert_data_warning'] = 'The file does not exist or is not readable by the webserver process!';
$string['auth_shib_changepasswordurl'] = 'Password-change URL';
$string['auth_shib_idp_list'] = 'Identity providers';
$string['auth_shib_idp_list_description'] = 'Provide a list of Identity Provider entityIDs to let the user choose from on the login page.<br />On each line there must be a comma-separated tuple for entityID of the IdP (see the Shibboleth metadata file) and Name of IdP as it shall be displayed in the drop-down list.<br />As an optional third parameter you can add the location of a Shibboleth session initiator that shall be used in case your Moodle installation is part of a multi federation setup.';
$string['auth_shib_instructions'] = 'Use the <a href="{$a}">Shibboleth login</a> to get access via Shibboleth, if your institution supports it. Otherwise, use the normal login form shown here.';
$string['auth_shib_instructions_help'] = 'Here you should provide custom instructions for your users to explain Shibboleth.  It will be shown on the login page in the instructions section. The instructions must include a link to "<b>{$a}</b>" that users click when they want to log in.';
$string['auth_shib_instructions_key'] = 'Login instructions';
$string['auth_shib_integrated_wayf'] = 'Moodle WAYF service';
$string['auth_shib_integrated_wayf_description'] = 'If you enable this, Moodle will use its own WAYF service instead of the one configured for Shibboleth. Moodle will display a drop-down list on this alternative login page where the user has to select his Identity Provider.';
$string['auth_shib_logout_return_url'] = 'Alternative logout return URL';
$string['auth_shib_logout_return_url_description'] = 'Provide the URL that Shibboleth users shall be redirected to after logging out.<br />If left empty, users will be redirected to the location that moodle will redirect users to';
$string['auth_shib_logout_url'] = 'Shibboleth Service Provider logout handler URL';
$string['auth_shib_logout_url_description'] = 'Provide the URL to the Shibboleth Service Provider logout handler. This typically is <tt>/Shibboleth.sso/Logout</tt>';
$string['auth_shib_no_organizations_warning'] = 'If you want to use the integrated WAYF service, you must provide a coma-separated list of Identity Provider entityIDs, their names and optionally a session initiator.';
$string['auth_shib_only'] = 'Shibboleth only';
$string['auth_shib_only_description'] = 'Check this option if a Shibboleth authentication shall be enforced';
$string['auth_shib_username_description'] = 'Name of the webserver Shibboleth environment variable that shall be used as Moodle username';
$string['shib_invalid_account_error'] = 'You seem to be Shibboleth authenticated but Moodle has no valid account for your username. Your account may not exist or it may have been suspended.';
$string['shib_no_attributes_error'] = 'You seem to be Shibboleth authenticated but Moodle didn\'t receive any user attributes. Please check that your Identity Provider releases the necessary attributes ({$a}) to the Service Provider Moodle is running on or inform the webmaster of this server.';
$string['shib_not_all_attributes_error'] = 'Moodle needs certain Shibboleth attributes which are not present in your case. The attributes are: {$a}<br />Please contact the webmaster of this server or your Identity Provider.';
$string['shib_not_set_up_error'] = 'Shibboleth authentication doesn\'t seem to be set up correctly because no Shibboleth environment variables are present for this page. Please consult the <a href="README.txt">README</a> for further instructions on how to set up Shibboleth authentication or contact the webmaster of this Moodle installation.';
$string['pluginname'] = 'Shibboleth';
$string['privacy:metadata'] = 'The Shibboleth authentication plugin does not store any personal data.';
