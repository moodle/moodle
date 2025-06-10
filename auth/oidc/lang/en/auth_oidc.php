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
 * English language strings.
 *
 * @package auth_oidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'OpenID Connect';
$string['auth_oidcdescription'] = 'The OpenID Connect authentication plugin provides single-sign-on functionality using configurable IdP.';

// Configuration pages.
$string['settings_page_other_settings'] = 'Other options';
$string['settings_page_application'] = 'IdP and authentication';
$string['settings_page_cleanup_oidc_tokens'] = 'Cleanup OpenID Connect tokens';
$string['settings_page_field_mapping'] = 'Field mappings';
$string['heading_basic'] = 'Basic settings';
$string['heading_basic_desc'] = '';
$string['heading_additional_options'] = 'Additional options';
$string['heading_additional_options_desc'] = '';
$string['heading_user_restrictions'] = 'User restrictions';
$string['heading_user_restrictions_desc'] = '';
$string['heading_sign_out'] = 'Sign out integration';
$string['heading_sign_out_desc'] = '';
$string['heading_display'] = 'Display';
$string['heading_display_desc'] = '';
$string['heading_debugging'] = 'Debugging';
$string['heading_debugging_desc'] = '';
$string['idptype'] = 'Identity Provider (IdP) Type';
$string['idptype_help'] = 'Three types of IdP are currently supported:
<ul>
<li><b>Azure AD (v1.0)</b>: Azure AD with oauth2 v1.0 endpoints, e.g. https://login.microsoftonline.com/common/oauth2/authorize.</li>
<li><b>Microsoft identity platform (v2.0)</b>: Azure AD with oath2 v2.0 endpoints, e.g. https://login.microsoftonline.com/common/oauth2/v2.0/authorize.</li>
<li><b>Other</b>: any non Azure AD IdP.</li>
</ul>
The differences between <b>Azure AD (v1.0)</b> and <b>Microsoft identity platform (v2.0)</b> options can be found at <a href="https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison">https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison</a>.<br/>
Notably, the configured application can use <b>certificate</b> besides <b>secret</b> for authentication when using <b>Microsoft identity platform (v2.0)</b> IdP.<br/>
Authorization and token endpoints need to be configured according to the configured IdP type.';
$string['idp_type_azuread'] = 'Azure AD (v1.0)';
$string['idp_type_microsoft'] = 'Microsoft identity platform (v2.0)';
$string['idp_type_other'] = 'Other';
$string['cfg_authenticationlink_desc'] = '<a href="{$a}" target="_blank">Link to IdP and authentication configuration</a>';
$string['authendpoint'] = 'Authorization Endpoint';
$string['authendpoint_help'] = 'The URI of the Authorization endpoint from your IdP to use.<br/>
Note if the site is to be configured to allow users from other tenants to access, tenant specific authorization endpoint cannot be used.';
$string['cfg_autoappend_key'] = 'Auto-Append';
$string['cfg_autoappend_desc'] = 'Automatically append this string when logging in users using the "Resource Owner Password Credentials" authentication method. This is useful when your IdP requires a common domain, but don\'t want to require users to type it in when logging in. For example, if the full OpenID Connect user is "james@example.com" and you enter "@example.com" here, the user will only have to enter "james" as their username. <br /><b>Note:</b> In the case where conflicting usernames exist - i.e. a Moodle user exists wth the same name, the priority of the authentication plugin is used to determine which user wins out.';
$string['clientid'] = 'Application ID';
$string['clientid_help'] = 'Your registered Application / Client ID on the IdP.';
$string['clientauthmethod'] = 'Client authentication method';
$string['clientauthmethod_help'] = '<ul>
<li>IdP in all types can use "<b>Secret</b>" authentication method.</li>
<li>IdP in <b>Microsoft identity platform (v2.0)</b> type can additionally use <b>Certificate</b> authentication method.</li>
</ul>
Note <b>Certificate</b> authentication method is not supported in <b>Resource Owner Password Credentials Grant</b> login flow.';
$string['auth_method_secret'] = 'Secret';
$string['auth_method_certificate'] = 'Certificate';
$string['clientsecret'] = 'Client Secret';
$string['clientsecret_help'] = 'When using <b>secret</b> authentication method, this is the client secret on the IdP. On some providers, it is also referred to as a key.';
$string['clientprivatekey'] = 'Client certificate private key';
$string['clientprivatekey_help'] = 'When using <b>certificate</b> authentication method, this is the private key of the certificate used to authenticate with IdP.';
$string['clientcert'] = 'Client certificate public key';
$string['clientcert_help'] = 'When using <b>certificate</b> authentication method, this is the public key, or certificate, used in to authenticate with IdP.';
$string['cfg_domainhint_key'] = 'Domain Hint';
$string['cfg_domainhint_desc'] = 'When using the <b>Authorization Code</b> login flow, pass this value as the "domain_hint" parameter. "domain_hint" is used by some OpenID Connect IdP to make the login process easier for users. Check with your provider to see whether they support this parameter.';
$string['cfg_err_invalidauthendpoint'] = 'Invalid Authorization Endpoint';
$string['cfg_err_invalidtokenendpoint'] = 'Invalid Token Endpoint';
$string['cfg_err_invalidclientid'] = 'Invalid client ID';
$string['cfg_err_invalidclientsecret'] = 'Invalid client secret';
$string['cfg_forceredirect_key'] = 'Force redirect';
$string['cfg_forceredirect_desc'] = 'If enabled, will skip the login index page and redirect to the OpenID Connect page. Can be bypassed with ?noredirect=1 URL param';
$string['cfg_icon_key'] = 'Icon';
$string['cfg_icon_desc'] = 'An icon to display next to the provider name on the login page.';
$string['cfg_iconalt_o365'] = 'Microsoft 365 icon';
$string['cfg_iconalt_locked'] = 'Locked icon';
$string['cfg_iconalt_lock'] = 'Lock icon';
$string['cfg_iconalt_go'] = 'Green circle';
$string['cfg_iconalt_stop'] = 'Red circle';
$string['cfg_iconalt_user'] = 'User icon';
$string['cfg_iconalt_user2'] = 'User icon alternate';
$string['cfg_iconalt_key'] = 'Key icon';
$string['cfg_iconalt_group'] = 'Group icon';
$string['cfg_iconalt_group2'] = 'Group icon alternate';
$string['cfg_iconalt_mnet'] = 'MNET icon';
$string['cfg_iconalt_userlock'] = 'User with lock icon';
$string['cfg_iconalt_plus'] = 'Plus icon';
$string['cfg_iconalt_check'] = 'Checkmark icon';
$string['cfg_iconalt_rightarrow'] = 'Right-facing arrow icon';
$string['cfg_customicon_key'] = 'Custom Icon';
$string['cfg_customicon_desc'] = 'If you\'d like to use your own icon, upload it here. This overrides any icon chosen above. <br /><br /><b>Notes on using custom icons:</b><ul><li>This image will <b>not</b> be resized on the login page, so we recommend uploading an image no bigger than 35x35 pixels.</li><li>If you have uploaded a custom icon and want to go back to one of the stock icons, click the custom icon in the box above, then click "Delete", then click "OK", then click "Save Changes" at the bottom of this form. The selected stock icon will now appear on the Moodle login page.</li></ul>';
$string['cfg_debugmode_key'] = 'Record debug messages';
$string['cfg_debugmode_desc'] = 'If enabled, information will be logged to the Moodle log that can help in identifying problems.';
$string['cfg_loginflow_key'] = 'Login Flow';
$string['cfg_loginflow_authcode'] = 'Authorization Code Flow <b>(recommended)</b>';
$string['cfg_loginflow_authcode_desc'] = 'Using this flow, the user clicks the name of the IdP (See "Provider Display Name" above) on the Moodle login page and is redirected to the provider to log in. Once successfully logged in, the user is redirected back to Moodle where the Moodle login takes place transparently. This is the most standardized, secure way for the user log in.';
$string['cfg_loginflow_rocreds'] = 'Resource Owner Password Credentials Grant <b>(deprecated)</b>';
$string['cfg_loginflow_rocreds_desc'] = '<b>This login flow is deprecated and will be removed from the plugin soon.</b><br/>Using this flow, the user enters their username and password into the Moodle login form like they would with a manual login. This will authorize the user with the IdP, but will not create a session on the IdP\'s site. For example, if using Microsoft 365 with OpenID Connect, the user will be logged in to Moodle but not the Microsoft 365 web applications. Using the authorization request is recommended if you want users to be logged in to both Moodle and the IdP. Note that not all IdP support this flow. This option should only be used when other authorization grant types are not available.';
$string['oidcresource'] = 'Resource';
$string['oidcresource_help'] = 'The OpenID Connect resource for which to send the request.<br/>
<b>Note</b> this is paramater is not supported in <b>Microsoft identity platform (v2.0)</b> IdP type.';
$string['oidcscope'] = 'Scope';
$string['oidcscope_help'] = 'The OIDC Scope to use.';
$string['cfg_opname_key'] = 'Provider Display Name';
$string['cfg_opname_desc'] = 'This is an end-user-facing label that identifies the type of credentials the user must use to login. This label is used throughout the user-facing portions of this plugin to identify your provider.';
$string['cfg_redirecturi_key'] = 'Redirect URI';
$string['cfg_redirecturi_desc'] = 'This is the URI to register as the "Redirect URI". Your OpenID Connect IdP should ask for this when registering Moodle as a client. <br /><b>NOTE:</b> You must enter this in your OpenID Connect IdP *exactly* as it appears here. Any difference will prevent logins using OpenID Connect.';
$string['tokenendpoint'] = 'Token Endpoint';
$string['tokenendpoint_help'] = 'The URI of the token endpoint from your IdP to use.<br/>
Note if the site is to be configured to allow users from other tenants to access, tenant specific token endpoint cannot be used.';
$string['cfg_userrestrictions_key'] = 'User Restrictions';
$string['cfg_userrestrictions_desc'] = 'Only allow users to log in that meet certain restrictions. <br /><b>How to use user restrictions: </b> <ul><li>Enter a <a href="https://en.wikipedia.org/wiki/Regular_expression">regular expression</a> pattern that matches the usernames of users you want to allow.</li><li>Enter one pattern per line</li><li>If you enter multiple patterns a user will be allowed if they match ANY of the patterns.</li><li>The character "/" should be escaped with "\".</li><li>If you don\'t enter any restrictions above, all users that can log in to the OpenID Connect IdP will be accepted by Moodle.</li><li>Any user that does not match any entered pattern(s) will be prevented from logging in using OpenID Connect.</li></ul>';
$string['cfg_userrestrictionscasesensitive_key'] = 'User Restrictions Case Sensitive';
$string['cfg_userrestrictionscasesensitive_desc'] = 'This controls if the "/i" option in regular expression is used in the user restriction match.<br/>If enabled, all user restriction checks will be performed as with case sensitive. Note if this is disabled, any patterns on letter cases will be ignored.';
$string['cfg_signoffintegration_key'] = 'Single Sign Out (from Moodle to IdP)';
$string['cfg_signoffintegration_desc'] = 'If the option is enabled, when a Moodle user connected to the configured IdP logs out of Moodle, the integration will trigger a request at the logout endpiont below, attempting to log the user off from IdP as well.<br/>
Note for integration with Microsoft Azure AD, the URL of Moodle site ({$a}) needs to be added as a redirect URI in the Azure app created for Moodle and Microsoft 365 integration.';
$string['cfg_logoutendpoint_key'] = 'IdP Logout Endpoint';
$string['cfg_logoutendpoint_desc'] = 'The URI of the logout endpoint from your IdP to use.';
$string['cfg_frontchannellogouturl_key'] = 'Front-channel Logout URL';
$string['cfg_frontchannellogouturl_desc'] = 'This is the URL that your IdP needs to trigger when it tries to log users out of Moodle.<br/>
For Microsoft Azure AD / Microsoft identity platform, the setting is called "Front-channel logout URL" and is configurable in the Azure app.';
$string['cfg_field_mapping_desc'] = 'User profile data can be mapped from Open ID Connect IdP to Moodle.<br/>
<ul>
<li>Basic profile data is available from ID tokens from all IdP.</li>
<li>If Azure AD is used as the IdP, additional profile data can be made available by installing and configuring the <a href="https://moodle.org/plugins/local_o365">Microsoft 365 integration plugin (local_o365)</a>.</li>
<li>If SDS profile sync feature is enabled in the local_o365 plugin, certain profile fields can be synchronised from SDS to Moodle. when running the "Sync with SDS" scheduled task, and will not happen when running the "Sync users with Azure AD" scheduled task, nor when user logs in.</li>
</ul>';
$string['cfg_cleanupoidctokens_key'] = 'Cleanup OpenID Connect Tokens';
$string['cfg_cleanupoidctokens_desc'] = 'If your users are experiencing problems logging in using their Microsoft 365 account, trying cleaning up OpenID Connect tokens. This removes stray and incomplete tokens that can cause errors. WARNING: This may interrupt logins in-process, so it\'s best to do this during downtime.';
$string['settings_section_basic'] = 'Basic settings';
$string['settings_section_authentication'] = 'Authentication';
$string['settings_section_endpoints'] = 'Endpoints';
$string['settings_section_other_params'] = 'Other parameters';
$string['authentication_and_endpoints_saved'] = 'Authentication and endpoint settings updated.';
$string['application_updated'] = 'OpenID Connect application setting was updated.';
$string['application_updated_azure'] = 'OpenID Connect application setting was updated.<br/>
<span class="warning" style="color: red;">Azure administrator will need to <b>Provide admin consent</b> and <b>Verify setup</b> again on the <a href="{$a}" target="_blank">Microsoft 365 integration configuration page</a> if "Identity Provider (IdP) Type" or "Client authentication method" settings are updated.</span>';

$string['event_debug'] = 'Debug message';

$string['task_cleanup_oidc_state_and_token'] = 'Clean up OIDC state and invalid token';

$string['errorauthdisconnectemptypassword'] = 'Password cannot be empty';
$string['errorauthdisconnectemptyusername'] = 'Username cannot be empty';
$string['errorauthdisconnectusernameexists'] = 'That username is already taken. Please choose a different one.';
$string['errorauthdisconnectnewmethod'] = 'Use Login Method';
$string['errorauthdisconnectinvalidmethod'] = 'Invalid login method received.';
$string['errorauthdisconnectifmanual'] = 'If using the manual login method, enter credentials below.';
$string['errorauthdisconnectinvalidmethod'] = 'Invalid login method received.';
$string['errorauthgeneral'] = 'There was a problem logging you in. Please contact your administrator for assistance.';
$string['errorauthinvalididtoken'] = 'Invalid id_token received.';
$string['errorauthloginfailednouser'] = 'Invalid login: User not found in Moodle. If this site has the "authpreventaccountcreation" setting enabled, this may mean you need an administrator to create an account for you first.';
$string['errorauthloginfaileddupemail'] = 'Invalid login: An existing account on this Moodle has the same email address as the account you try to create, and "Allow accounts with same email" (allowaccountssameemail) setting is disabled.';
$string['errorauthnoauthcode'] = 'No authorization code was received from the identity server. The error logs may have more information.';
$string['errorauthnocredsandendpoints'] = 'Please configure OpenID Connect client credentials and endpoints.';
$string['errorauthnohttpclient'] = 'Please set an HTTP client.';
$string['errorauthnoidtoken'] = 'OpenID Connect id_token not received.';
$string['errorauthnoaccesstoken'] = 'Access token not received.';
$string['errorauthunknownstate'] = 'Unknown state.';
$string['errorauthuseralreadyconnected'] = 'You\'re already connected to a different OpenID Connect user.';
$string['errorauthuserconnectedtodifferent'] = 'The OpenID Connect user that authenticated is already connected to a Moodle user.';
$string['errorbadloginflow'] = 'Invalid authentication type specified. Note: If you are receiving this after a recent installation or upgrade, please clear your Moodle cache.';
$string['errorjwtbadpayload'] = 'Could not read JWT payload.';
$string['errorjwtcouldnotreadheader'] = 'Could not read JWT header';
$string['errorjwtempty'] = 'Empty or non-string JWT received.';
$string['errorjwtinvalidheader'] = 'Invalid JWT header';
$string['errorjwtmalformed'] = 'Malformed JWT received.';
$string['errorjwtunsupportedalg'] = 'JWS Alg or JWE not supported';
$string['errorlogintoconnectedaccount'] = 'This Microsoft 365 user is connected to a Moodle account, but OpenID Connect login is not enabled for this Moodle account. Please log in to the Moodle account using the account\'s defined authentication method to use Microsoft 365 features';
$string['erroroidcnotenabled'] = 'The OpenID Connect authentication plugin is not enabled.';
$string['errornodisconnectionauthmethod'] = 'Cannot disconnect because there is no enabled authentication plugin to fall back to. (either user\'s previous login method or the manual login method).';
$string['erroroidcclientinvalidendpoint'] = 'Invalid Endpoint URI received.';
$string['erroroidcclientnocreds'] = 'Please set client credentials with setcreds';
$string['erroroidcclientnoauthendpoint'] = 'No authorization endpoint set. Please set with $this->setendpoints';
$string['erroroidcclientnotokenendpoint'] = 'No token endpoint set. Please set with $this->setendpoints';
$string['erroroidcclientinsecuretokenendpoint'] = 'The token endpoint must be using SSL/TLS for this.';
$string['errorrestricted'] = 'This site has restrictions in place on the users that can log in with OpenID Connect. These restrictions currently prevent you from completing this login attempt.';
$string['errorucpinvalidaction'] = 'Invalid action received.';
$string['erroroidccall'] = 'Error in OpenID Connect. Please check logs for more information.';
$string['erroroidccall_message'] = 'Error in OpenID Connect: {$a}';
$string['errorinvalidredirect_message'] = 'The URL you are trying to redirect to does not exist.';
$string['error_empty_tenantnameorguid'] = 'Tenant name or GUID cannot be empty when using Azure AD (v1.0) or Microsoft identity platform (v2.0) IdPs.';
$string['error_invalid_client_authentication_method'] = "Invalid client authentication method";
$string['error_empty_client_secret'] = 'Client secret cannot be empty when using "secret" authentication method';
$string['error_empty_client_private_key'] = 'Client certificate private key cannot be empty when using "certificate" authentication method';
$string['error_empty_client_cert'] = 'Client certificate public key cannot be empty when using "certificate" authentication method';
$string['error_empty_tenantname_or_guid'] = 'Tenant name or GUID cannot be empty when using "certificate" authentication method';
$string['error_endpoint_mismatch_auth_endpoint'] = 'The configured authorization endpoint does not match configured IdP type.<br/>
<ul>
<li>When using "Azure AD (v1.0)" IdP type, use v1.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/authorize</li>
<li>When using "Microsoft identity platform (v2.0)" IdP type, use v2.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_endpoint_mismatch_token_endpoint'] = 'The configured token endpoint does not match configured IdP type.<br/>
<ul>
<li>When using "Azure AD (v1.0)" IdP type, use v1.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/token</li>
<li>When using "Microsoft identity platform (v2.0)" IdP type, use v2.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_empty_oidcresource'] = 'Resource cannot be empty when using Azure AD (v1.0) or other types of IdP.';
$string['erroruserwithusernamealreadyexists'] = 'Error occurred when trying to rename your Moodle account. A Moodle user with the new username already exists. Ask your site administrator to resolve this first.';

$string['eventuserauthed'] = 'User Authorized with OpenID Connect';
$string['eventusercreated'] = 'User created with OpenID Connect';
$string['eventuserconnected'] = 'User connected to OpenID Connect';
$string['eventuserloggedin'] = 'User Logged In with OpenID Connect';
$string['eventuserdisconnected'] = 'User disconnected from OpenID Connect';
$string['eventuserrenameattempt'] = 'The auth_oidc plugin attempted to rename a user';

$string['oidc:manageconnection'] = 'Allow OpenID Connection and Disconnection';
$string['oidc:manageconnectionconnect'] = 'Allow OpenID Connection';
$string['oidc:manageconnectiondisconnect'] = 'Allow OpenID Disconnection';

$string['privacy:metadata:auth_oidc'] = 'OpenID Connect Authentication';
$string['privacy:metadata:auth_oidc_prevlogin'] = 'Previous login methods to undo Microsoft 365 connections';
$string['privacy:metadata:auth_oidc_prevlogin:userid'] = 'The ID of the Moodle user';
$string['privacy:metadata:auth_oidc_prevlogin:method'] = 'The previous login method';
$string['privacy:metadata:auth_oidc_prevlogin:password'] = 'The previous (encrypted) user password field.';
$string['privacy:metadata:auth_oidc_token'] = 'OpenID Connect tokens';
$string['privacy:metadata:auth_oidc_token:oidcuniqid'] = 'The OIDC unique user identifier.';
$string['privacy:metadata:auth_oidc_token:username'] = 'The username of the Moodle user';
$string['privacy:metadata:auth_oidc_token:userid'] = 'The user ID of the Moodle user';
$string['privacy:metadata:auth_oidc_token:oidcusername'] = 'The username of the OIDC user';
$string['privacy:metadata:auth_oidc_token:scope'] = 'The scope of the token';
$string['privacy:metadata:auth_oidc_token:tokenresource'] = 'The resource of the token';
$string['privacy:metadata:auth_oidc_token:authcode'] = 'The auth code for the token';
$string['privacy:metadata:auth_oidc_token:token'] = 'The token';
$string['privacy:metadata:auth_oidc_token:expiry'] = 'The token expiry';
$string['privacy:metadata:auth_oidc_token:refreshtoken'] = 'The refresh token';
$string['privacy:metadata:auth_oidc_token:idtoken'] = 'The ID token';

// In the following strings, $a refers to a customizable name for the identity manager. For example, this could be
// "Microsoft 365", "OpenID Connect", etc.
$string['ucp_general_intro'] = 'Here you can manage your connection to {$a}. If enabled, you will be able to use your {$a} account to log in to Moodle instead of a separate username and password. Once connected, you\'ll no longer have to remember a username and password for Moodle, all log-ins will be handled by {$a}.';
$string['ucp_login_start'] = 'Start using {$a} to log in to Moodle';
$string['ucp_login_start_desc'] = 'This will switch your account to use {$a} to log in to Moodle. Once enabled, you will log in using your {$a} credentials - your current Moodle username and password will not work. You can disconnect your account at any time and return to logging in normally.';
$string['ucp_login_stop'] = 'Stop using {$a} to log in to Moodle';
$string['ucp_login_stop_desc'] = 'You are currently using {$a} to log in to Moodle. Clicking "Stop using {$a} login" will disconnect your Moodle account from {$a}. You will no longer be able to log in to Moodle with your {$a} account. You\'ll be asked to create a username and password, and from then on you will then be able to log in to Moodle directly.';
$string['ucp_login_status'] = '{$a} login is:';
$string['ucp_status_enabled'] = 'Enabled';
$string['ucp_status_disabled'] = 'Disabled';
$string['ucp_disconnect_title'] = '{$a} Disconnection';
$string['ucp_disconnect_details'] = 'This will disconnect your Moodle account from {$a}. You\'ll need to create a username and password to log in to Moodle.';
$string['ucp_title'] = '{$a} Management';
$string['ucp_o365accountconnected'] = 'This Microsoft 365 account is already connected with another Moodle account.';

// Clean up OIDC tokens.
$string['cleanup_oidc_tokens'] = 'Cleanup OpenID Connect tokens';
$string['unmatched'] = 'Unmatched';
$string['delete_token'] = 'Delete token';
$string['mismatched'] = 'Mismatched';
$string['na'] = 'n/a';
$string['mismatched_details'] = 'Token record contains username "{$a->tokenusername}"; matched Moodle user has username "{$a->moodleusername}".';
$string['delete_token_and_reference'] = 'Delete token and reference';
$string['table_token_id'] = 'Token record ID';
$string['table_oidc_username'] = 'OIDC username';
$string['table_token_unique_id'] = 'OIDC unique ID';
$string['table_matching_status'] = 'Matching status';
$string['table_matching_details'] = 'Details';
$string['table_action'] = 'Action';
$string['token_deleted'] = 'Token was deleted successfully';
$string['no_token_to_cleanup'] = 'There are no OIDC token to cleanup.';

$string['errorusermatched'] = 'The Microsoft 365 account "{$a->aadupn}" is already matched with Moodle user "{$a->username}". To complete the connection, please log in as that Moodle user first and follow the instructions in the Microsoft block.';

// User mapping options.
$string['update_oncreate_and_onlogin'] = 'On creation and every login';
$string['update_oncreate_and_onlogin_and_usersync'] = 'On creation, every login, and every user sync task run';
$string['update_onlogin_and_usersync'] = 'On every login and every user sync task run';

// Remote fields.
$string['settings_fieldmap_feild_not_mapped'] = '(not mapped)';
$string['settings_fieldmap_field_city'] = 'City';
$string['settings_fieldmap_field_companyName'] = 'Company Name';
$string['settings_fieldmap_field_objectId'] = 'Object ID';
$string['settings_fieldmap_field_country'] = 'Country';
$string['settings_fieldmap_field_department'] = 'Department';
$string['settings_fieldmap_field_displayName'] = 'Display Name';
$string['settings_fieldmap_field_surname'] = 'Surname';
$string['settings_fieldmap_field_faxNumber'] = 'Fax Number';
$string['settings_fieldmap_field_telephoneNumber'] = 'Telephone Number';
$string['settings_fieldmap_field_givenName'] = 'Given Name';
$string['settings_fieldmap_field_jobTitle'] = 'Job Title';
$string['settings_fieldmap_field_mail'] = 'Email';
$string['settings_fieldmap_field_mobile'] = 'Mobile';
$string['settings_fieldmap_field_postalCode'] = 'Postal Code';
$string['settings_fieldmap_field_preferredLanguage'] = 'Language';
$string['settings_fieldmap_field_state'] = 'State';
$string['settings_fieldmap_field_streetAddress'] = 'Street Address';
$string['settings_fieldmap_field_userPrincipalName'] = 'Username (UPN)';
$string['settings_fieldmap_field_employeeId'] = 'Employee ID';
$string['settings_fieldmap_field_businessPhones'] = 'Office phone';
$string['settings_fieldmap_field_mobilePhone'] = 'Mobile phone';
$string['settings_fieldmap_field_officeLocation'] = 'Office';
$string['settings_fieldmap_field_preferredName'] = 'Preferred Name';
$string['settings_fieldmap_field_manager'] = 'Manager name';
$string['settings_fieldmap_field_manager_email'] = 'Manager email';
$string['settings_fieldmap_field_teams'] = 'Teams';
$string['settings_fieldmap_field_groups'] = 'Groups';
$string['settings_fieldmap_field_roles'] = 'Roles';
$string['settings_fieldmap_field_onPremisesSamAccountName'] = 'On-premises SAM account name';
$string['settings_fieldmap_field_extensionattribute'] = 'Extension attribute {$a}';
$string['settings_fieldmap_field_sds_school_id'] = 'SDS school ID ({$a})';
$string['settings_fieldmap_field_sds_school_name'] = 'SDS school name ({$a})';
$string['settings_fieldmap_field_sds_school_role'] = 'SDS school role ("Student" or "Teacher")';
$string['settings_fieldmap_field_sds_student_externalId'] = 'SDS student external ID';
$string['settings_fieldmap_field_sds_student_birthDate'] = 'SDS student birth date';
$string['settings_fieldmap_field_sds_student_grade'] = 'SDS student grade';
$string['settings_fieldmap_field_sds_student_graduationYear'] = 'SDS student graduation year';
$string['settings_fieldmap_field_sds_student_studentNumber'] = 'SDS student number';
$string['settings_fieldmap_field_sds_teacher_externalId'] = 'SDS teacher external ID';
$string['settings_fieldmap_field_sds_teacher_teacherNumber'] = 'SDS teacher number';
