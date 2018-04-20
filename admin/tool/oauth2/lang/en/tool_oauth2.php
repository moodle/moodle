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

$string['authconfirm'] = 'This action will grant permanent API access to Moodle for the authenticated account. This is intended to be used as a system account for managing files owned by Moodle.';
$string['authconnected'] = 'The system account is now connected for offline access';
$string['authnotconnected'] = 'The system account was not connected for offline access';
$string['configured'] = 'Configured';
$string['configuredstatus'] = 'Configured';
$string['connectsystemaccount'] = 'Connect to a system account';
$string['createfromtemplate'] = 'Create an OAuth 2 service from a template';
$string['createfromtemplatedesc'] = 'Choose one of the OAuth 2 service templates below to create an OAuth service with a valid configuration for one of the known service types. This will create the OAuth 2 service, with all the correct end points and parameters required for authentication, though you will still need to enter the client ID and secret for the new service before it can be used.';
$string['createnewendpoint'] = 'Create new endpoint for issuer "{$a}"';
$string['createnewfacebookissuer'] = 'Create new Facebook service';
$string['createnewgoogleissuer'] = 'Create new Google service';
$string['createnewissuer'] = 'Create new custom service';
$string['createnewmicrosoftissuer'] = 'Create new Microsoft service';
$string['createnewuserfieldmapping'] = 'Create new user field mapping for issuer "{$a}"';
$string['deleteconfirm'] = 'Are you sure you want to delete the identity issuer "{$a}"? Any plugins relying on this issuer will stop working.';
$string['deleteendpointconfirm'] = 'Are you sure you want to delete the endpoint "{$a->endpoint}" for issuer "{$a->issuer}"? Any plugins relying on this endpoint will stop working.';
$string['deleteuserfieldmappingconfirm'] = 'Are you sure you want to delete the user field mapping for issuer "{$a}"?';
$string['discovered_help'] = 'Discovery means that the OAuth 2 endpoints could be automatically determined from the base URL for the OAuth service. Not all services are required to be "discovered", but if they are not, then the endpoints and user mapping information will need to be entered manually.';
$string['discovered'] = 'Service discovery successful';
$string['discoverystatus'] = 'Discovery';
$string['editendpoint'] = 'Edit endpoint: {$a->endpoint} for issuer {$a->issuer}';
$string['editendpoints'] = 'Configure endpoints';
$string['editissuer'] = 'Edit identity issuer: {$a}';
$string['edituserfieldmapping'] = 'Edit user field mapping for issuer {$a}';
$string['edituserfieldmappings'] = 'Configure user field mappings';
$string['endpointdeleted'] = 'Endpoint deleted';
$string['endpointname_help'] = 'Key used to search for this endpoint. Must end with "_endpoint".';
$string['endpointname'] = 'Name';
$string['endpointsforissuer'] = 'Endpoints for issuer: {$a}';
$string['endpointurl_help'] = 'URL for this endpoint. Must use https:// protocol.';
$string['endpointurl'] = 'URL';
$string['issuersetup'] = 'Detailed instructions on configuring the common OAuth 2 services';
$string['issuersetuptype'] = 'Detailed instructions on setting up the {$a} OAuth 2 provider';
$string['issueralloweddomains_help'] = 'If set, this setting is a comma separated list of domains that logins will be restricted to when using this provider.';
$string['issueralloweddomains_link'] = 'OAuth_2_login_domains';
$string['issueralloweddomains'] = 'Login domains';
$string['issuerbaseurl_help'] = 'Base URL used to access the service.';
$string['issuerbaseurl'] = 'Service base URL';
$string['issuerclientid'] = 'Client ID';
$string['issuerclientid_help'] = 'The OAuth client ID for this issuer.';
$string['issuerclientsecret'] = 'Client secret';
$string['issuerclientsecret_help'] = 'The OAuth client secret for this issuer.';
$string['issuerdeleted'] = 'Identity issuer deleted';
$string['issuerdisabled'] = 'Identity issuer disabled';
$string['issuerenabled'] = 'Identity issuer enabled';
$string['issuerimage_help'] = 'An image URL used to show a logo for this issuer. May be displayed on login page.';
$string['issuerimage'] = 'Logo URL';
$string['issuerloginparams'] = 'Additional parameters included in a login request.';
$string['issuerloginparams_help'] = 'Some systems require additional parameters for a login request in order to read the user\'s basic profile.';
$string['issuerloginparamsoffline'] = 'Additional parameters included in a login request for offline access.';
$string['issuerloginparamsoffline_help'] = 'Each OAuth system defines a different way to request offline access. E.g. Google requires the additional params: "access_type=offline&prompt=consent". These parameters should be in URL query parameter format.';
$string['issuerloginscopes_help'] = 'Some systems require additional scopes for a login request in order to read the user\'s basic profile. The standard scopes for an OpenID Connect compliant system are "openid profile email".';
$string['issuerloginscopesoffline_help'] = 'Each OAuth system defines a different way to request offline access. E.g. Microsoft requires an additional scope "offline_access".';
$string['issuerloginscopesoffline'] = 'Scopes included in a login request for offline access.';
$string['issuerloginscopes'] = 'Scopes included in a login request.';
$string['issuername_help'] = 'Name of the identity issuer. May be displayed on login page.';
$string['issuername'] = 'Name';
$string['issuershowonloginpage_help'] = 'If the OAuth 2 authentication plugin is enabled, this login issuer will be listed on the login page to allow users to log in with accounts from this issuer.';
$string['issuershowonloginpage'] = 'Show on login page';
$string['issuerrequireconfirmation_help'] = 'Require that all users verify their email address before they can log in with OAuth. This applies to newly created accounts as part of the login process, or when an existing Moodle account is connected to an OAuth login via matching email addresses.';
$string['issuerrequireconfirmation'] = 'Require email verification';
$string['issuers'] = 'Issuers';
$string['loginissuer'] = 'Allow login';
$string['notconfigured'] = 'Not configured';
$string['notdiscovered'] = 'Service discovery not successful';
$string['notloginissuer'] = 'Do not allow login';
$string['pluginname'] = 'OAuth 2 services';
$string['savechanges'] = 'Save changes';
$string['serviceshelp'] = 'Service provider setup instructions.';
$string['systemaccountconnected_help'] = 'System accounts are used to provide advanced functionality for plugins. They are not required for login functionality only, but other plugins using the OAuth service may offer a reduced set of features if the system account has not been connected. For example repositories cannot support "controlled links" without a system account to perform file operations.';
$string['systemaccountconnected'] = 'System account connected';
$string['systemaccountnotconnected'] = 'System account not connected';
$string['systemauthstatus'] = 'System account connected';
$string['usebasicauth'] = 'Authenticate token requests via HTTP headers';
$string['usebasicauth_help'] = 'Utilise the HTTP Basic authentication scheme when sending client ID and password with a refresh token request. Recommended by the OAuth 2 standard, but may not be available with some issuers.';
$string['userfieldexternalfield'] = 'External field name';
$string['userfieldexternalfield_error'] = 'This field cannot contain HTML.';
$string['userfieldexternalfield_help'] = 'Name of the field provided by the external OAuth system.';
$string['userfieldinternalfield_help'] = 'Name of the Moodle user field that should be mapped from the external field.';
$string['userfieldinternalfield'] = 'Internal field name';
$string['userfieldmappingdeleted'] = 'User field mapping deleted';
$string['userfieldmappingsforissuer'] = 'User field mappings for issuer: {$a}';
$string['privacy:metadata'] = 'The OAuth 2 services plugin does not store any personal data.';
