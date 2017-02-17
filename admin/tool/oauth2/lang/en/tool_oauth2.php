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
 * Strings for component 'tool_oauth2', language 'en'
 *
 * @package    tool_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Open ID Connect configuration';
$string['editissuer'] = 'Edit identity issuer: {$a}';
$string['issuername'] = 'Name';
$string['issuername_help'] = 'Name of the identity issuer. May be displayed on login page.';
$string['issuerimage'] = 'Logo URL';
$string['issuerimage_help'] = 'An image url used to show a logo for this issuer. May be displayed on login page.';
$string['issuerclientid'] = 'Client Id';
$string['issuerclientid_help'] = 'The OAuth client ID for this issuer.';
$string['issuerclientsecret'] = 'Client Secret';
$string['issuerclientsecret_help'] = 'The OAuth client secret for this issuer.';
$string['issuerbaseurl'] = 'Service base url';
$string['issuerbaseurl_help'] = 'Base url used to access the service.';
$string['issuershowonloginpage'] = 'Show on login page.';
$string['issuershowonloginpage_help'] = 'If the OpenID Connect Authentication plugin is enabled, this login issuer will be listed on the login page to allow users to login with accounts from this issuer.';
$string['issuerbehaviour'] = 'Behaviour';
$string['issuerbehaviour_help'] = 'Choose from one of the supported behaviours.

* OAuth 2.0 - OAuth 2.0 API with no authentication
* OpenID Connect - Standards based OAuth 2.0 API with Authentication
* Microsoft OAuth 2.0 - Non-standard OAuth 2.0 combined with Microsoft Graph API';
$string['savechanges'] = 'Save changes';
$string['configuredstatus'] = 'Configured';
$string['discoverystatus'] = 'Discovery';
$string['systemauthstatus'] = 'System account connected';
$string['configured'] = 'Configured';
$string['notconfigured'] = 'Not configured';
$string['discovered'] = 'Service discovery successful';
$string['notdiscovered'] = 'Service discovery not successful';
$string['loginissuer'] = 'Allow login';
$string['notloginissuer'] = 'Do not allow login';
$string['systemaccountconnected'] = 'System account connected';
$string['systemaccountnotconnected'] = 'System account not connected';
$string['createnewissuer'] = 'Create new identity issuer';
$string['deleteconfirm'] = 'Are you sure you want to delete the identity issuer "{$a}"? Any plugins relying on this issuer will stop working.';
$string['issuerdeleted'] = 'Identity issuer deleted';
$string['connectsystemaccount'] = 'Connect to a system account';
$string['authconfirm'] = 'This action will grant permanent API access to Moodle for the authenticated account. This is intended to be used as a system account for managing files owned by Moodle.';
$string['authconnected'] = 'The system account is now connected for offline access';
$string['authnotconnected'] = 'The system account was not connected for offline access';
