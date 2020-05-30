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
 * @package auth_iomadoidc
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

$string['pluginname'] = 'IOMAD OpenID Connect';
$string['auth_iomadoidcdescription'] = 'The IOMAD OpenID Connect plugin provides single-sign-on functionality using configurable identity providers.';

$string['cfg_authendpoint_key'] = 'Authorization Endpoint';
$string['cfg_authendpoint_desc'] = 'The URI of the Authorization endpoint from your identity provider to use.';
$string['cfg_autoappend_key'] = 'Auto-Append';
$string['cfg_autoappend_desc'] = 'Automatically append this string when logging in users using the "Resource Owner Password Credentials" authentication method. This is useful when your identity provider requires a common domain, but don\'t want to require users to type it in when logging in. For example, if the full IOMAD OpenID Connect user is "james@example.com" and you enter "@example.com" here, the user will only have to enter "james" as their username. <br /><b>Note:</b> In the case where conflicting usernames exist - i.e. a Moodle user exists wth the same name, the priority of the authentication plugin is used to determine which user wins out.';
$string['cfg_clientid_key'] = 'Client ID';
$string['cfg_clientid_desc'] = 'Your registered Client ID on the identity provider';
$string['cfg_clientsecret_key'] = 'Client Secret';
$string['cfg_clientsecret_desc'] = 'Your registered Client Secret on the identity provider. On some providers, it is also referred to as a key.';
$string['cfg_domainhint_key'] = 'Domain Hint';
$string['cfg_domainhint_desc'] = 'When using the "Authorization Code" authentication method, pass this value as the "domain_hint" parameter. "domain_hint" is used by some IOMAD OpenID Connect providers to make the login process easier for users. Check with your provider to see whether they support this parameter.';
$string['cfg_err_invalidauthendpoint'] = 'Invalid Authorization Endpoint';
$string['cfg_err_invalidtokenendpoint'] = 'Invalid Token Endpoint';
$string['cfg_err_invalidclientid'] = 'Invalid client ID';
$string['cfg_err_invalidclientsecret'] = 'Invalid client secret';
$string['cfg_forceredirect_key'] = 'Force redirect';
$string['cfg_forceredirect_desc'] = 'If enabled, will skip the login index page and redirect to the IOMAD OpenID Connect page. Can be bypassed with ?noredirect=1 URL param';
$string['cfg_icon_key'] = 'Icon';
$string['cfg_icon_desc'] = 'An icon to display next to the provider name on the login page.';
$string['cfg_iconalt_o365'] = 'Office 365 icon';
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
$string['cfg_loginflow_key'] = 'Authentication Method';
$string['cfg_loginflow_authcode'] = 'Authorization Code Flow (recommended)';
$string['cfg_loginflow_authcode_desc'] = 'Using this flow, the user clicks the name of the identity provider (See "Provider Name" above) on the Moodle login page and is redirected to the provider to log in. Once successfully logged in, the user is redirected back to Moodle where the Moodle login takes place transparently. This is the most standardized, secure way for the user log in.';
$string['cfg_loginflow_rocreds'] = 'Resource Owner Password Credentials Grant';
$string['cfg_loginflow_rocreds_desc'] = 'Using this flow, the user enters their username and password into the Moodle login form like they would with a manual login. This will authorize the user with the identity provider, but will not create a session on the identity provider\'s site. For example, if using Office 365 with IOMAD OpenID Connect, the user will be logged in to Moodle but not the Office 365 web applications. Using the authorization request is recommended if you want users to be logged in to both Moodle and the identity provider.  Note that not all identity providers support this flow. This option should only be used when other authorization grant types are not available.';
$string['cfg_iomadoidcresource_key'] = 'Resource';
$string['cfg_iomadoidcresource_desc'] = 'The IOMAD OpenID Connect resource for which to send the request.';
$string['cfg_iomadoidcscope_key'] = 'Scope';
$string['cfg_iomadoidcscope_desc'] = 'The OIDC Scope to use.';
$string['cfg_opname_key'] = 'Provider Name';
$string['cfg_opname_desc'] = 'This is an end-user-facing label that identifies the type of credentials the user must use to login. This label is used throughout the user-facing portions of this plugin to identify your provider.';
$string['cfg_redirecturi_key'] = 'Redirect URI';
$string['cfg_redirecturi_desc'] = 'This is the URI to register as the "Redirect URI". Your IOMAD OpenID Connect identity provider should ask for this when registering Moodle as a client. <br /><b>NOTE:</b> You must enter this in your IOMAD OpenID Connect provider *exactly* as it appears here. Any difference will prevent logins using IOMAD OpenID Connect.';
$string['cfg_tokenendpoint_key'] = 'Token Endpoint';
$string['cfg_tokenendpoint_desc'] = 'The URI of the token endpoint from your identity provider to use.';
$string['cfg_userrestrictions_key'] = 'User Restrictions';
$string['cfg_userrestrictions_desc'] = 'Only allow users to log in that meet certain restrictions. <br /><b>How to use user restrictions: </b> <ul><li>Enter a <a href="https://en.wikipedia.org/wiki/Regular_expression">regular expression</a> pattern that matches the usernames of users you want to allow.</li><li>Enter one pattern per line</li><li>If you enter multiple patterns a user will be allowed if they match ANY of the patterns.</li><li>The character "/" should be escaped with "\".</li><li>If you don\'t enter any restrictions above, all users that can log in to the IOMAD OpenID Connect provider will be accepted by Moodle.</li><li>Any user that does not match any entered pattern(s) will be prevented from logging in using IOMAD OpenID Connect.</li></ul>';
$string['event_debug'] = 'Debug message';

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
$string['errorauthnoauthcode'] = 'No authorization code was received from the identity server. The error logs may have more information.';
$string['errorauthnocreds'] = 'Please configure IOMAD OpenID Connect client credentials.';
$string['errorauthnoendpoints'] = 'Please configure IOMAD OpenID Connect server endpoints.';
$string['errorauthnohttpclient'] = 'Please set an HTTP client.';
$string['errorauthnoidtoken'] = 'IOMAD OpenID Connect id_token not received.';
$string['errorauthunknownstate'] = 'Unknown state.';
$string['errorauthuseralreadyconnected'] = 'You\'re already connected to a different IOMAD OpenID Connect user.';
$string['errorauthuserconnectedtodifferent'] = 'The IOMAD OpenID Connect user that authenticated is already connected to a Moodle user.';
$string['errorbadloginflow'] = 'Invalid authentication type specified. Note: If you are receiving this after a recent installation or upgrade, please clear your Moodle cache.';
$string['errorjwtbadpayload'] = 'Could not read JWT payload.';
$string['errorjwtcouldnotreadheader'] = 'Could not read JWT header';
$string['errorjwtempty'] = 'Empty or non-string JWT received.';
$string['errorjwtinvalidheader'] = 'Invalid JWT header';
$string['errorjwtmalformed'] = 'Malformed JWT received.';
$string['errorjwtunsupportedalg'] = 'JWS Alg or JWE not supported';
$string['errorlogintoconnectedaccount'] = 'This Office 365 user is connected to a Moodle account, but IOMAD OpenID Connect login is not enabled for this Moodle account. Please log in to the Moodle account using the account\'s defined authentication method to use Office 365 features';
$string['erroriomadoidcnotenabled'] = 'The IOMAD OpenID Connect authentication plugin is not enabled.';
$string['errornodisconnectionauthmethod'] = 'Cannot disconnect because there is no enabled authentication plugin to fall back to. (either user\'s previous login method or the manual login method).';
$string['erroriomadoidcclientinvalidendpoint'] = 'Invalid Endpoint URI received.';
$string['erroriomadoidcclientnocreds'] = 'Please set client credentials with setcreds';
$string['erroriomadoidcclientnoauthendpoint'] = 'No authorization endpoint set. Please set with $this->setendpoints';
$string['erroriomadoidcclientnotokenendpoint'] = 'No token endpoint set. Please set with $this->setendpoints';
$string['erroriomadoidcclientinsecuretokenendpoint'] = 'The token endpoint must be using SSL/TLS for this.';
$string['errorrestricted'] = 'This site has restrictions in place on the users that can log in with IOMAD OpenID Connect. These restrictions currently prevent you from completing this login attempt.';
$string['errorucpinvalidaction'] = 'Invalid action received.';
$string['erroriomadoidccall'] = 'Error in IOMAD OpenID Connect. Please check logs for more information.';
$string['erroriomadoidccall_message'] = 'Error in IOMAD OpenID Connect: {$a}';
$string['errorinvalidredirect_message'] = 'The URL you are trying to redirect to does not exist.';

$string['eventuserauthed'] = 'User Authorized with IOMAD OpenID Connect';
$string['eventusercreated'] = 'User created with IOMAD OpenID Connect';
$string['eventuserconnected'] = 'User connected to IOMAD OpenID Connect';
$string['eventuserloggedin'] = 'User Logged In with IOMAD OpenID Connect';
$string['eventuserdisconnected'] = 'User disconnected from IOMAD OpenID Connect';
$string['exception_tokenemptyuserid'] = 'The existing token for this user does not contain a valid user ID. Please contact your administrator.';

$string['iomadoidc:manageconnection'] = 'Allow IOMAD OpenID Connection and Disconnection';
$string['iomadoidc:manageconnectionconnect'] = 'Allow IOMAD OpenID Connection';
$string['iomadoidc:manageconnectiondisconnect'] = 'Allow IOMAD OpenID Disconnection';

$string['privacy:metadata:auth_iomadoidc'] = 'IOMAD OpenID Connect Authentication';
$string['privacy:metadata:auth_iomadoidc_prevlogin'] = 'Previous login methods to undo Office 365 connections';
$string['privacy:metadata:auth_iomadoidc_prevlogin:userid'] = 'The ID of the Moodle user';
$string['privacy:metadata:auth_iomadoidc_prevlogin:method'] = 'The previous login method';
$string['privacy:metadata:auth_iomadoidc_prevlogin:password'] = 'The previous (encrypted) user password field.';
$string['privacy:metadata:auth_iomadoidc_token'] = 'IOMAD OpenID Connect tokens';
$string['privacy:metadata:auth_iomadoidc_token:iomadoidcuniqid'] = 'The OIDC unique user identifier.';
$string['privacy:metadata:auth_iomadoidc_token:username'] = 'The username of the Moodle user';
$string['privacy:metadata:auth_iomadoidc_token:userid'] = 'The user ID of the Moodle user';
$string['privacy:metadata:auth_iomadoidc_token:iomadoidcusername'] = 'The username of the OIDC user';
$string['privacy:metadata:auth_iomadoidc_token:scope'] = 'The scope of the token';
$string['privacy:metadata:auth_iomadoidc_token:resource'] = 'The resource of the token';
$string['privacy:metadata:auth_iomadoidc_token:authcode'] = 'The auth code for the token';
$string['privacy:metadata:auth_iomadoidc_token:token'] = 'The token';
$string['privacy:metadata:auth_iomadoidc_token:expiry'] = 'The token expiry';
$string['privacy:metadata:auth_iomadoidc_token:refreshtoken'] = 'The refresh token';
$string['privacy:metadata:auth_iomadoidc_token:idtoken'] = 'The ID token';

// In the following strings, $a refers to a customizable name for the identity manager. For example, this could be
// "Office 365", "IOMAD OpenID Connect", etc.
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
$string['ucp_o365accountconnected'] = 'This Office 365 account is already connected with another Moodle account.';
