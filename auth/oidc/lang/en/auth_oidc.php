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
$string['settings_page_binding_username_claim'] = 'Binding Username Claim';
$string['settings_page_change_binding_username_claim_tool'] = 'Change binding username claim tool';
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
<li><b>Microsoft Entra ID (v1.0)</b>: Microsoft Entra ID with oauth2 v1.0 endpoints, e.g. https://login.microsoftonline.com/common/oauth2/authorize.</li>
<li><b>Microsoft identity platform (v2.0)</b>: Microsoft Entra ID with oath2 v2.0 endpoints, e.g. https://login.microsoftonline.com/common/oauth2/v2.0/authorize.</li>
<li><b>Other</b>: any non Microsoft IdP.</li>
</ul>
The differences between <b>Microsoft Entra ID (v1.0)</b> and <b>Microsoft identity platform (v2.0)</b> options can be found at <a href="https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison">https://docs.microsoft.com/en-us/azure/active-directory/azuread-dev/azure-ad-endpoint-comparison</a>.<br/>
Notably, the configured application can use <b>certificate</b> besides <b>secret</b> for authentication when using <b>Microsoft identity platform (v2.0)</b> IdP.<br/>
Authorization and token endpoints need to be configured according to the configured IdP type.';
$string['idp_type_microsoft_entra_id'] = 'Microsoft Entra ID (v1.0)';
$string['idp_type_microsoft_identity_platform'] = 'Microsoft identity platform (v2.0)';
$string['idp_type_other'] = 'Other';
$string['cfg_authenticationlink_desc'] = '<a href="{$a}" target="_blank">Link to IdP and authentication configuration</a>';
$string['authendpoint'] = 'Authorization Endpoint';
$string['authendpoint_help'] = 'The URI of the Authorization endpoint from your IdP to use.<br/>
Note if the site is to be configured to allow users from other tenants to access, tenant specific authorization endpoint cannot be used.';
$string['cfg_autoappend_key'] = 'Auto-Append';
$string['cfg_autoappend_desc'] = 'Automatically append this string when logging in users using the "Resource Owner Password Credentials" authentication method. This is useful when your IdP requires a common domain, but don\'t want to require users to type it in when logging in. For example, if the full OpenID Connect user is "james@example.com" and you enter "@example.com" here, the user will only have to enter "james" as their username. <br /><b>Note:</b> In the case where conflicting usernames exist - i.e. a Moodle user exists wth the same name, the priority of the authentication plugin is used to determine which user wins out.';
$string['clientid'] = 'Application ID';
$string['clientid_help'] = 'The registered Application / Client ID on the IdP.';
$string['clientauthmethod'] = 'Client authentication method';
$string['clientauthmethod_help'] = '<ul>
<li>IdP in all types can use "<b>Secret</b>" authentication method.</li>
<li>IdP in <b>Microsoft identity platform (v2.0)</b> type can additionally use <b>Certificate</b> authentication method.</li>
</ul>';
$string['auth_method_secret'] = 'Secret';
$string['auth_method_certificate'] = 'Certificate';
$string['clientsecret'] = 'Client Secret';
$string['clientsecret_help'] = 'When using <b>secret</b> authentication method, this is the client secret on the IdP. On some providers, it is also referred to as a key.';
$string['clientprivatekey'] = 'Client certificate private key';
$string['clientprivatekey_help'] = 'When using <b>certificate</b> authentication method and <b>Plain text</b> certificate source, this is the private key of the certificate used to authenticate with IdP.';
$string['clientcert'] = 'Client certificate public key';
$string['clientcert_help'] = 'When using <b>certificate</b> authentication method and <b>Plain text</b> certificate source, this is the public key, or certificate, used in to authenticate with IdP.';
$string['clientcertsource'] = 'Certificate source';
$string['clientcertsource_help'] = 'When using <b>certificate</b> authentication method, this is used to define where to retrieve the certificate from.
<ul>
<li><b>Plain text</b> source requires the certificate/private key file contents to be configured in the subsequent text area settings.</li>
<li><b>File name</b> source requires the certificate/private key files exist in a folder <b>microsoft_certs</b> in the Moodle data folder.</li>
</ul>';
$string['cert_source_text'] = 'Plain text';
$string['cert_source_path'] = 'File name';
$string['clientprivatekeyfile'] = 'File name of client certificate private key';
$string['clientprivatekeyfile_help'] = 'When using <b>certificate</b> authentication method and <b>File name</b> certificate source, this is the file name of private key used to authenticate with IdP. The file needs to present in a folder <b>microsoft_certs</b> in the Moodle data folder.';
$string['clientcertfile'] = 'File name of client certificate public key';
$string['clientcertfile_help'] = 'When using <b>certificate</b> authentication method and <b>File name</b> certificate source, this is the file name of public key, or certificate, used to authenticate with IdP. The file needs to present in a folder <b>microsoft_certs</b> in the Moodle data folder.';
$string['clientcertpassphrase'] = 'Client certificate passphrase';
$string['clientcertpassphrase_help'] = 'If the client certificate private key is encrypted, this is the passphrase to decrypt it.';
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
$string['cfg_silentloginmode_key'] = 'Silent Login Mode';
$string['cfg_silentloginmode_desc'] = 'If enabled, Moodle will try to use the active session of a user authenticated to the configured authorization endpoint to log the user in.<br/>
To use this feature, the following configurations are required:
<ul>
<li><b>Force users to log in</b> (forcelogin) in the <a href="{$a}" target="_blank">Site policies section</a> is enabled.</li>
<li><b>Force redirect</b> (auth_oidc/forceredirect) setting above is enabled.</li>
</ul>
In order to avoid Moodle trying to use personal accounts or accounts from other tenants to login, it is also recommended to use tenant specific endpoints, rather than generic ones using "common" or "organization" etc. paths.<br/>
<br/>
For Microsoft IdPs, the user experience is as follows:
<ul>
<li>If no active user session is found, Moodle login page will show.</li>
<li>If only one active user session is found, and the user has access to the Entra ID app (i.e. user is from the same tenant, or is a guest user of the tenant), the user will be logged in to Moodle automatically using SSO.</li>
<li>If only one active user session is found, but the user doesn\'t have access to the Entra ID app (e.g. the user is from a different tenant, or the app requires user assignment and the user isn\'t assigned), the Moodle login page will show.</li>
<li>If there are multiple active user sessions who have access to the Entra ID app, a page will show to allow the user to select the account to log in with.</li>
</ul>';
$string['oidcresource'] = 'Resource';
$string['oidcresource_help'] = 'The OpenID Connect resource for which to send the request.<br/>
<b>Note</b> this is paramater is not supported in <b>Microsoft identity platform (v2.0)</b> IdP type.';
$string['oidcscope'] = 'Scope';
$string['oidcscope_help'] = 'The OIDC Scope to use.';
$string['secretexpiryrecipients'] = 'Secret Expiry Notification Recipients';
$string['secretexpiryrecipients_help'] = 'A comma-separated list of email addresses to send secret expiry notifications to.<br/>
If no email address is entered, the main site administrator will be notified.';
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
Note for integration with Microsoft Entra ID, the URL of Moodle site ({$a}) needs to be added as a redirect URI in the Azure app created for Moodle and Microsoft 365 integration.';
$string['cfg_logoutendpoint_key'] = 'IdP Logout Endpoint';
$string['cfg_logoutendpoint_desc'] = 'The URI of the logout endpoint from your IdP to use.';
$string['cfg_frontchannellogouturl_key'] = 'Front-channel Logout URL';
$string['cfg_frontchannellogouturl_desc'] = 'This is the URL that your IdP needs to trigger when it tries to log users out of Moodle.<br/>
For Microsoft Entra ID / Microsoft identity platform, the setting is called "Front-channel logout URL" and is configurable in the Azure app.';
$string['cfg_field_mapping_desc'] = 'User profile data can be mapped from Open ID Connect IdP to Moodle. The remote fields available to map heavily depends on the IdP type.<br/>
<ul>
<li>Some basic profile fields are available from access token and ID token claims from all IdP types.</li>
<li>If Microsoft IdP type is configured (either v1.0 or v2.0), additional profile data can be made available via Graph API calls by installing and configuring the <a href="https://moodle.org/plugins/local_o365">Microsoft 365 integration plugin (local_o365)</a>.</li>
<li>If SDS profile sync feature is enabled in the local_o365 plugin, certain profile fields can be synchronised from SDS to Moodle. when running the "Sync with SDS" scheduled task, and will not happen when running the "Sync users with Microsoft Entra ID" scheduled task, nor when user logs in.</li>
</ul>

The claims available from the ID and access tokens vary depending on IdP type, but most IdP allows some level of customisation of the claims. Documentation on Microsoft IdPs are linked below:
<ul>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/access-token-claims-reference">Access token claims</a></li>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/id-token-claims-reference">ID token claims</a></li>
<li><a target="_blank" href="https://learn.microsoft.com/en-us/entra/identity-platform/optional-claims-reference">Optional claim configuration</a>: Note "Email" is an optional claim in Microsoft Entra ID (v1.0) IdP type.</li>
</ul>';

$string['cfg_cleanupoidctokens_key'] = 'Cleanup OpenID Connect Tokens';
$string['cfg_cleanupoidctokens_desc'] = 'If your users are experiencing problems logging in using their Microsoft 365 account, trying cleaning up OpenID Connect tokens. This removes stray and incomplete tokens that can cause errors. WARNING: This may interrupt logins in-process, so it\'s best to do this during downtime.';
$string['settings_section_basic'] = 'Basic settings';
$string['settings_section_authentication'] = 'Authentication';
$string['settings_section_endpoints'] = 'Endpoints';
$string['settings_section_binding_username_claim'] = 'Binding Username Claim';
$string['settings_section_other_params'] = 'Other parameters';
$string['settings_section_secret_expiry_notification'] = 'Secret expiry notification';
$string['authentication_and_endpoints_saved'] = 'Authentication and endpoint settings updated.';
$string['application_updated'] = 'OpenID Connect application setting have been updated.';
$string['application_updated_microsoft'] = 'OpenID Connect application setting was updated.<br/>
<span class="warning" style="color: red;">Azure administrator will need to <b>Provide admin consent</b> and <b>Verify setup</b> again on the Microsoft 365 integration configuration page if "Identity Provider (IdP) Type" or "Client authentication method" settings are updated.</span>';
$string['application_not_changed'] = 'OpenID Connect application setting was not changed.';

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
$string['errorinvalidcertificatesource'] = 'Invalid certificate source';
$string['error_empty_tenantnameorguid'] = 'Tenant name or GUID cannot be empty when using Microsoft Entra ID (v1.0) or Microsoft identity platform (v2.0) IdPs.';
$string['error_invalid_client_authentication_method'] = "Invalid client authentication method";
$string['error_empty_client_secret'] = 'Client secret cannot be empty when using "secret" authentication method';
$string['error_empty_client_private_key'] = 'Client certificate private key cannot be empty when using "certificate" authentication method';
$string['error_empty_client_cert'] = 'Client certificate public key cannot be empty when using "certificate" authentication method';
$string['error_empty_client_private_key_file'] = 'Client certificate private key file cannot be empty when using "certificate" authentication method';
$string['error_empty_client_cert_file'] = 'Client certificate public key file cannot be empty when using "certificate" authentication method';
$string['error_empty_tenantname_or_guid'] = 'Tenant name or GUID cannot be empty when using "certificate" authentication method';
$string['error_endpoint_mismatch_auth_endpoint'] = 'The configured authorization endpoint does not match configured IdP type.<br/>
<ul>
<li>When using "Microsoft Entra ID (v1.0)" IdP type, use v1.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/authorize</li>
<li>When using "Microsoft identity platform (v2.0)" IdP type, use v2.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_endpoint_mismatch_token_endpoint'] = 'The configured token endpoint does not match configured IdP type.<br/>
<ul>
<li>When using "Microsoft Entra ID (v1.0)" IdP type, use v1.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/token</li>
<li>When using "Microsoft identity platform (v2.0)" IdP type, use v2.0 endpoint, e.g. https://login.microsoftonline.com/common/oauth2/v2.0/authorize</li>
</ul>';
$string['error_tenant_specific_endpoint_required'] = 'When using "Microsoft identity platform (v2.0)" IdP type and "Certificate" authentication method, tenant specific endpoint (i.e. not common/organizations/consumers) is required.';
$string['error_empty_oidcresource'] = 'Resource cannot be empty when using Microsoft Entra ID (v1.0) or other types of IdP.';
$string['erroruserwithusernamealreadyexists'] = 'Error occurred when trying to rename your Moodle account. A Moodle user with the new username already exists. Ask your site administrator to resolve this first.';
$string['error_no_response_available'] = 'No responses available.';

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
$string['privacy:metadata:auth_oidc_token:useridentifier'] = 'The user identifier of the OIDC user';
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
$string['table_oidc_unique_identifier'] = 'OIDC unique identifier';
$string['table_token_unique_id'] = 'OIDC unique ID';
$string['table_matching_status'] = 'Matching status';
$string['table_matching_details'] = 'Details';
$string['table_action'] = 'Action';
$string['token_deleted'] = 'Token was deleted successfully';
$string['no_token_to_cleanup'] = 'There are no OIDC token to cleanup.';

$string['errorusermatched'] = 'The Microsoft 365 account "{$a->entraidupn}" is already matched with Moodle user "{$a->username}". To complete the connection, please log in as that Moodle user first and follow the instructions in the Microsoft block.';

// User mapping options.
$string['update_oncreate_and_onlogin'] = 'On creation and every login';
$string['update_oncreate_and_onlogin_and_usersync'] = 'On creation, every login, and every user sync task run';
$string['update_onlogin_and_usersync'] = 'On every login and every user sync task run';

// Remote fields.
$string['settings_fieldmap_feild_not_mapped'] = '(not mapped)';
$string['settings_fieldmap_field_bindingusernameclaim'] = 'Binding Username Claim (can only be mapped during login)';
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
$string['settings_fieldmap_field_userPrincipalName'] = 'User Principal Name';
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

// Binding username claim options.
$string['binding_username_claim_heading'] = 'Binding Username Claim';
$string['binding_username_claim_description'] = '<p class="warning_header">This is an advanced feature!</p>
<p>This page allows site administrators to select the token claim to use for binding with Moodle username.</p>
<p class="warning">Be very cautious when changing this setting. Follow the steps below to change this setting on Moodle sites with existing users using OpenID Connect authentication method. Failure to do so may result in users being logged out and/or duplicate accounts being created.</p>
<ol>
<li>Make sure you have a manual site administrator account, i.e. not using OpenID Connect authentication method.</li>
<li>Schedule enough downtime and put the Moodle site into maintenance mode.</li>
<li>Backup Moodle database, in particular <span class="code">user</span> and <span class="code">auth_oidc_tokens</span> tables. If local_o365 plugin is installed, backup <span class="code">local_o365_objects</span> table too.</li>
<li>Use the <a href="{$a}" target="_blank">update binding username tool</a> to update Moodle username, auth_oidc token, and other connection records of the existing user to match the value of the claim to be changed to.</li>
<li>Update the binding username token setting on this page.</li>
<li>Purge caches.</li>
<li>Move the Moodle site out of maintenance mode.</li>
</ol>
<p>In most cases this setting should be set to the default option "Choose automatically", meaning the plugin will try to determine the token to use depending on IdP type. Misconfiguration or unexpected change of this setting will result in SSO failure.</p>';
$string['binding_username_claim_description_existing_claims'] = 'The following claims are present in existing user ID tokens. Choose claims not on the list may results in SSO failure.<br/>
<div class="existing_claims">{$a}</div>';
$string['binding_username_auto'] = 'Choose automatically';
$string['binding_username_custom'] = 'Custom';
$string['binding_username_claim'] = 'Binding username claim';
$string['customclaimname'] = 'Custom claim name';
$string['customclaimname_description'] = 'This field is used only when the <b>Binding Username Claim</b> setting is set to <b>Custom</b>.';
$string['binding_username_claim_help_ms_no_user_sync'] = 'The options for non Microsoft IdPs include:
<ul>
<li><b>Choose automatically</b>: Uses current logic, determining the token by IdP type and falling back to <b>sub</b> if no claim is found.</li>
<li><b>preferred_username</b>: Default for Microsoft identity platform (v2.0) IdP type. <span class="not_support_user_sync">Does not support user sync.</span></li>
<li><b>email</b>: Fallback for Microsoft identity platform (v2.0).</li>
<li><b>upn</b>: Default for Microsoft Entra ID (v1.0) and other IdP types.</li>
<li><b>unique_name</b>: Fallback for Microsoft Entra ID (v1.0) and other IdP types. <span class="not_support_user_sync">Does not support user sync.</span></li>
<li><b>oid</b>: Fallback if no other claims are present. Only present in Microsoft IdP.</li>
<li><b>sub</b>: Fallback if no other claims are present. <span class="not_support_user_sync">Does not support user sync.</span></li>
<li><b>samaccountname</b>: Custom claim.</li>
<li><b>Custom</b>: Allows the site admin to enter a custom value. <span class="not_support_user_sync">Does not support user sync.</span></li>
</ul>
Note some options do not support user sync.';
$string['binding_username_claim_help_ms_with_user_sync'] = 'The options for Microsoft IdP with user sync feature enabled include:
<ul>
<li><b>Choose automatically</b>: Uses current logic, determining the token by IdP type and falling back to <b>sub</b> if no claim is found.</li>
<li><b>email</b>: Fallback for Microsoft identity platform (v2.0).</li>
<li><b>upn</b>: Default for Microsoft Entra ID (v1.0) and other IdP types.</li>
<li><b>oid</b>: Fallback if no other claims are present. Only present in Microsoft IdP.</li>
<li><b>samaccountname</b>: Custom claim.</li>
</ul>';
$string['binding_username_claim_help_non_ms'] = 'The options for Microsoft IdP without user sync feature enabled include:
<ul>
<li><b>Choose automatically</b>: Uses current logic, determining the token by IdP type and falling back to <b>sub</b> if no claim is found.</li>
<li><b>preferred_username</b></li>
<li><b>email</b></li>
<li><b>unique_name</b></li>
<li><b>sub</b></li>
<li><b>samaccountname</b></li>
<li><b>custom</b>: Custom claim.</li>
</ul>';
$string['binding_username_claim_updated'] = 'Binding Username Claim was updated successfully.';
$string['examplecsv'] = 'Example upload file';
$string['usernamefile'] = 'File';
$string['csvdelimiter'] = 'CSV separator';
$string['encoding'] = 'Encoding';
$string['rowpreviewnum'] = 'Preview rows';
$string['upload_usernames'] = 'Update binding usernames';
$string['update_stats_users_updated'] = '{$a} users were updated';
$string['update_stats_users_errors'] = '{$a} users had errors';
$string['update_error_incomplete_line'] = 'The line does not contain required fields.';
$string['update_error_user_not_found'] = 'No user found matching the username. Will try update manually matched user.';
$string['update_error_user_not_oidc'] = 'The user is not using OpenID Connect authentication method. Will try update manually matched user.';
$string['update_error_invalid_new_username'] = 'New username is invalid.';
$string['update_error_user_update_failed'] = 'Failed to update user.';
$string['update_warning_email_match'] = 'Email matches existing user.';
$string['update_success_username'] = 'Username updated successfully.';
$string['update_success_token'] = 'Token updated successfully.';
$string['update_success_o365'] = 'Microsoft 365 connection record updated successfully.';
$string['update_error_nothing_updated'] = 'Nothing was updated.';
$string['error_invalid_upload_file'] = 'Invalid upload file.';
$string['csvline'] = 'CSV line';
$string['change_binding_username_claim_tool'] = 'Change binding username claim tool';
$string['change_binding_username_claim_tool_description'] = '<p class="warning_header">This is an advanced feature!</p>
<p>This tool allows site administrators to bulk update the following records:</p>
<ul>
<li>Moodle account usernames,</li>
<li>Binding usernames in stored OpenID Connect ID tokens,</li>
<li>Moodle and Microsoft account connection records.</li>
</ul>
<p>This should only be used when changing the <b>Binding username claim</b> settings.</p>
<p class="warning">Be very cautious when using this feature, and follow the steps on the <a href="{$a}" target="_blank">Binding username claim configuration page</a>. Misuse of this tool will result in Moodle user records being damaged and/or SSO failure.</p>
<p>The tool accepts a simple CSV file with two columns:</p>
<ul>
<li><b><span class="code">username</span></b>: The current username of the Moodle account to be updated, or if the current user is manually matched, this needs to be the current binding claim value.</li>
<li><b><span class="code">new_username</span></b>: The case-sensitive value of the new token claim to be used as the binding username claim. If the user is automatically matched and uses the OpenID Connect authentication type, the lowercase of this value will be used as Moodle username.</li>
</ul>
<p>When the file is uploaded, the tool will perform the following actions:</p>
<ol>
<li>Find an existing Moodle user with the given <span class="code">username</span> as either username or email address, and using the OpenID Connect authentication method, and if one is found, update the username of the user to be the lowercase of <span class="code">new_username</span>.</li>
<li>Update OpenID Connect token record.
<ul>
<li>If a user is found in the step 1 above, then find the token record in the <span class="code">auth_oidc_token</span> table for the user, and update <span class="code">username</span> column to be the lowercase of <span class="code">new_username</span>, and <span class="code">oidcusername</span> column to be the same as <span class="code">new_username</span>.</li>
<li>If no record is found above, it will try to find record in the <span class="code">auth_oidc_token</span> with <span class="code">oidcusername</span> column matching the old <span class="code">username</span>, and update it to be <span class="code">newusername</span>.</li>
</ul>
<li>Providing the <span class="code">local_365</span> plugin is installed, update user connection record.
<ul>
<li>If a user is found in stpe 1 above, then find the connection record of the user in the <span class="code">local_o365_objects</span> table, and update the <span class="code">o365name</span> column to be the same as <span class="code">new_username</span>.</li>
<li>If no user is found in step 1, then it will try to find a record for a user in <span class="code">local_o365_objects</span> table with <span class="code">o365name</span> matching the <span class="code">username</span> value, and update it to be <span class="code">newusername</span> value.</li>
</ul>
</ol>
<p>The example file below would change the binding username claim from <span class="code">upn</span> or <span class="code">email</span> to <span class="code">oid</span>.</p>';
$string['change_binding_username_claim_tool_result'] = 'Update results';
$string['update_username_results'] = 'Update username results';
$string['new_username'] = 'New username';
$string['missing_idp_type'] = 'This configuration is only available if an IdP type is configured.';
