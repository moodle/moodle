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

$string['pluginname'] = 'OAuth 2 Services';
$string['editissuer'] = 'Edit identity issuer: {$a}';
$string['editendpoint'] = 'Edit endpoint: {$a->endpoint} for issuer {$a->issuer}';
$string['endpointsforissuer'] = 'Endpoints for issuer: {$a}';
$string['edituserfieldmapping'] = 'Edit user field mapping for issuer {$a}';
$string['userfieldmappingsforissuer'] = 'User field mappings for issuer: {$a}';
$string['issuers'] = 'Issuers';
$string['endpointname'] = 'Name';
$string['endpointname_help'] = 'Key used to search for this endpoint. Must end with "_endpoint".';
$string['endpointurl'] = 'Url';
$string['endpointurl_help'] = 'URL for this endpoint. Must use https:// protocol.';
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
$string['issuerloginscopes'] = 'Scopes included in a login request.';
$string['issuerloginscopes_help'] = 'Some systems require additional scopes for a login request in order to read the users basic profile. The standard scopes for an OpenID Connect compliant system are "openid profile email".';
$string['issuerloginscopesoffline'] = 'Scopes included in a login request for offline access.';
$string['issuerloginscopesoffline_help'] = 'Each OAuth system defines a different way to request offline access. E.g. Microsoft requires an additional scope "offline_access"';
$string['issuerloginparams'] = 'Additional parameters included in a login request.';
$string['issuerloginparams_help'] = 'Some systems require additional parameters for a login request in order to read the users basic profile.';
$string['issuerloginparamsoffline'] = 'Additional parameters included in a login request for offline access.';
$string['issuerloginparamsoffline_help'] = 'Each OAuth system defines a different way to request offline access. E.g. Google requires the additional params: "access_type=offline&prompt=consent" these parameters should be in url query parameter format.';
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
$string['editendpoints'] = 'Configure endpoints';
$string['edituserfieldmappings'] = 'Configure user field mappings';
$string['notconfigured'] = 'Not configured';
$string['discovered'] = 'Service discovery successful';
$string['notdiscovered'] = 'Service discovery not successful';
$string['loginissuer'] = 'Allow login';
$string['notloginissuer'] = 'Do not allow login';
$string['systemaccountconnected'] = 'System account connected';
$string['systemaccountnotconnected'] = 'System account not connected';
$string['createnewissuer'] = 'Create new custom service';
$string['createnewgoogleissuer'] = 'Create new Google service';
$string['createnewmicrosoftissuer'] = 'Create new Microsoft service';
$string['createnewfacebookissuer'] = 'Create new Facebook service';
$string['createnewstandardissuer'] = 'Create service from a template';
$string['createnewendpoint'] = 'Create new endpoint for issuer "{$a}"';
$string['createnewuserfieldmapping'] = 'Create new user field mapping for issuer "{$a}"';
$string['deleteconfirm'] = 'Are you sure you want to delete the identity issuer "{$a}"? Any plugins relying on this issuer will stop working.';
$string['deleteendpointconfirm'] = 'Are you sure you want to delete the endpoint "{$a->endpoint}" for issuer "{$a->issuer}"? Any plugins relying on this endpoint will stop working.';
$string['deleteuserfieldmappingconfirm'] = 'Are you sure you want to delete the user field mapping for issuer "{$a}"?';
$string['issuerdeleted'] = 'Identity issuer deleted';
$string['endpointdeleted'] = 'Endpoint deleted';
$string['userfieldmappingdeleted'] = 'User field mapping deleted';
$string['connectsystemaccount'] = 'Connect to a system account';
$string['authconfirm'] = 'This action will grant permanent API access to Moodle for the authenticated account. This is intended to be used as a system account for managing files owned by Moodle.';
$string['authconnected'] = 'The system account is now connected for offline access';
$string['authnotconnected'] = 'The system account was not connected for offline access';
$string['userfieldexternalfield'] = 'External field name';
$string['userfieldexternalfield_help'] = 'Name of the field provided by the external OAuth system.';
$string['userfieldinternalfield'] = 'Internal field name';
$string['userfieldinternalfield_help'] = 'Name of the Moodle user field that should be mapped from the external field.';
$string['createfromtemplate'] = 'Create an OAuth 2 service from a template';
$string['createfromtemplatedesc'] = 'Choose one of the OAuth 2 service template below to create an OAuth service with a valid configuration for one of the known service types. This will create the OAuth 2 service, with all the correct end points and parameters required for authentication, but you will still need to enter the client ID and secret for the new service before it can be used.';
$string['serviceshelp'] = 'Service provider setup instructions: (Google, Facebook, Microsoft).';
