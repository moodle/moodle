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
 * Anobody can login using iomadsaml2
 *
 * @package   auth_iomadsaml2
 * @copyright Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['alterlogout'] = 'Alternative Logout URL';
$string['alterlogout_help'] = 'The URL to redirect a user after all internal logout mechanisms are run';
$string['anyauth'] = 'Allowed any auth type';
$string['anyauth_help'] = 'Yes: Allow SAML login for all users? No: Only users who have iomadsaml2 as their type.';
$string['anyauthotherdisabled'] = 'You have logged in successfully as \'{$a->username}\' but your auth type of \'{$a->auth}\' is disabled.';
$string['attemptsignout'] = 'Attempt IdP Signout';
$string['attemptsignout_help'] = 'This will attempt to communicate with the IdP to send a sign out request';
$string['auth_iomadsaml2description'] = 'Authenticate with a IOMAD SAML2 Identity Provider (IdP)';
$string['auth_iomadsaml2blockredirectdescription'] = 'Redirect or display message to IOMAD SAML2 logins based on configured group restrictions';
$string['autocreate'] = 'Auto create users';
$string['autocreate_help'] = 'Allow creation of Moodle users on demand';
$string['autologin'] = 'Auto-login';
$string['autologin_help'] = 'On pages that allow guest access without login, automatically log users into Moodle with a real user account if they are logged in to the IdP (using passive authentication).';
$string['autologinbysession'] = 'Check once per session';
$string['autologinbycookie'] = 'Check when the specified cookie exists or changes';
$string['autologincookie'] = 'Auto-login cookie';
$string['autologincookie_help'] = 'Name of cookie used to decide when to attempt auto-login (only relevant if the cookie option is selected above).';
$string['availableidps'] = 'Select available IdPs';
$string['availableidps_help'] = 'If an IdP metadata xml contains multiple IdP entities, you will need to select which entities are availiable
for users to login with.';
$string['blockredirectheading'] = 'Account blocking actions';
$string['attrsimple'] = 'Simplify attributes';
$string['attrsimple_help'] = 'Various IdP\'s such as ADFS use long attribute keys such as urns or namespaced xml schema names. If set to Yes this will simplify these, eg map http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname to such \'givenname\'.';
$string['certificatedetails'] = 'Certificate details';
$string['certificatedetailshelp'] = '<h1>SAML2 auto generated public certificate contents</h1><p>The path for the cert is here:</p>';
$string['checkcertificateexpiry'] = 'SAML certificate expiry';
$string['checkcertificateexpired'] = 'SAML certificate has expired {$a} ago';
$string['checkcertificatewarn'] = 'SAML certificate will expire in {$a}';
$string['checkcertificateok'] = 'SAML certificate will expire in {$a}';
$string['certificate_help'] = 'Regenerate the Private Key and Certificate used by this SP. | <a href=\'{$a}\'>View SP certificate</a>';
$string['certificatelock_help'] = 'Locking the certificates will prevent them from being overwritten once generated.';
$string['certificatelock'] = 'Lock certificate';
$string['certificatelock_locked'] = 'The certificate is locked';
$string['certificatelock_lockedmessage'] = 'The certificates are currently locked.';
$string['certificatelock_unlock'] = 'Unlock certificates';
$string['certificatelock_regenerate'] = 'Not regenerating certificates because they have been locked!';
$string['certificatelock_warning'] = 'Warning. You are about to lock the certificates, are you sure you want to do this? <br> The certificates are not currently locked';
$string['certificate'] = 'Regenerate certificate';
$string['commonname'] = 'Common Name';
$string['countryname'] = 'Country';
$string['debug'] = 'Debugging';
$string['debug_help'] = '<p>This adds extra debugging to the normal moodle log | <a href=\'{$a}\'>View SSP config</a></p>';
$string['duallogin'] = 'Dual login';
$string['duallogin_help'] = '
<p>If on, then users will see both manual and a SAML login button. If off they will always be taken directly to the IdP login page.</p>
<p>If passive, then the users that are already authenticated into the IDP will be automatically logged in, otherwise they will be sent to Moodle login page.</p>
<p>If off, then admins can still see the manual login page via /login/index.php?saml=off</p>
<p>If on, then external pages can deep link into moodle using saml eg /course/view.php?id=45&saml=on</p>
<p>If set to test IdP connection, the network will be checked for connectivity, and if functional, SAML login will be initiated.<p/>';
$string['emailtaken'] = 'Can\'t create a new account, because {$a} email address is already registered';
$string['emailtakenupdate'] = 'Your email wasn\'t updated, because email address {$a} is already registered';
$string['errorinvalidautologin'] = 'Invalid autologin request';
$string['errorparsingxml'] = 'Error parsing XML: {$a}';
$string['exception'] = 'SAML2 exception: {$a}';
$string['expirydays'] = 'Expiry in Days';
$string['error'] = 'Login error';
$string['fielddelimiter'] = 'Field delimiter';
$string['fielddelimiter_help'] = 'The delimiter to use when a field receives an array of values from the IdP.';
$string['flaggedresponsetypemessage'] = 'Display custom message';
$string['flaggedresponsetyperedirect'] = 'Redirect to external URL';
$string['flagredirecturl'] = 'Redirect URL';
$string['flagredirecturl_help'] = '
<p>The URL to redirect a user is not allowed to access Moodle based on configured group restrictions.</p>
<p>(Only utilised when \'Response type\' is \'Redirect to external URL\'.)</p>';
$string['flagmessage'] = 'Response message';
$string['flagmessage_help'] = '
<p>The message to display when a user is not allowed to access Moodle based on configured group restrictions.</p>
<p>(Only displayed when \'Response response type\' is \'Display custom message\'.)</p>';
$string['flagmessage_default'] = 'You are logged in to your identity provider however, this account has limited access to Moodle, please contact your administrator for more details.';
$string['flagresponsetype'] = 'Account blocking response type';
$string['flagresponsetype_help'] = 'If access is blocked based on configured group restrictions, how should Moodle respond?';
$string['idpattr_help'] = 'Which IdP attribute should be matched against a Moodle user field?';
$string['idpattr'] = 'Mapping IdP';
$string['idpmetadata_badurl'] = 'Invalid metadata at {$a}';
$string['idpmetadata_help'] = 'To use multiple IdPs enter each public metadata url on a new line.<br/>To override a name, place text before the http. eg. "Forced IdP Name http://ssp.local/simplesaml/iomadsaml2/idp/metadata.php"';
$string['idpmetadata'] = 'IdP metadata xml OR public xml URL';
$string['idpmetadata_invalid'] = 'The IdP XML isn\'t valid';
$string['idpmetadata_noentityid'] = 'The IdP XML has no entityID';
$string['idpmetadatarefresh_help'] = 'Run a scheduled task to update IdP metadata from IdP metadata URL';
$string['idpmetadatarefresh'] = 'IdP metadata refresh';
$string['idpnamedefault'] = 'Login via IOMAD SAML2';
$string['idpnamedefault_varaible'] = 'Login via IOMAD SAML2 ({$a})';
$string['idpname_help'] = 'eg myUNI - this is detected from the metadata and will show on the dual login page (if enabled)';
$string['idpname'] = 'IdP label override';
$string['localityname'] = 'Locality';
$string['logdirdefault'] = '/tmp/';
$string['logdir_help'] = 'The log directory SSPHP will write to, the file will be named simplesamlphp.log';
$string['logdir'] = 'Log Directory';
$string['logtofile'] = 'Enable logging to file';
$string['logtofile_help'] = 'Turning this on will redirect SSPHP log output to a file in the logdir';
$string['manageidpsheading'] = 'Manage available Identity Providers (IdPs)';
$string['mdlattr_help'] = 'Which Moodle user field should the IdP attribute be matched to?';
$string['mdlattr'] = 'Mapping Moodle';
$string['wantassertionssigned'] = 'Want assertions signed';
$string['wantassertionssigned_help'] = 'Whether assertions received by this SP must be signed';
$string['assertionsconsumerservices'] = 'Assertions consumer services';
$string['assertionsconsumerservices_help'] = 'List of bindings the SP should support';
$string['spentityid'] = 'Entity ID';
$string['spentityid_help'] = 'Override the Entity Id of the Service Provider. In most cases leave blank and a good default will be used instead.';
$string['allowcreate'] = 'Allow create';
$string['allowcreate_help'] = 'Allow creation of IdP users on demand';
$string['authncontext'] = 'AuthnContext';
$string['authncontext_help'] = 'Allows augmentation of assertions. Leave blank unless required';
$string['metadatafetchfailed'] = 'Metadata fetch failed: {$a}';
$string['metadatafetchfailedstatus'] = 'Metadata fetch failed: Status code {$a}';
$string['metadatafetchfailedunknown'] = 'Metadata fetch failed: Unknown cURL error';
$string['multiidp:label:displayname'] = 'Display name';
$string['multiidp:label:alias'] = 'Alias';
$string['multiidp:label:active'] = 'Active';
$string['multiidp:label:defaultidp'] = 'Default IdP';
$string['multiidp:label:admin'] = 'For admin users only';
$string['multiidp:label:admin_help'] = 'Any users that log in using this IdP will automatically be made an site administrator';
$string['multiidp:label:whitelist'] = 'Redirected IP addresses';
$string['multiidp:label:whitelist_help'] = 'If set, it will force clients to this IdP. Format: xxx.xxx.xxx.xxx/bitmask. Separate multiple subnets on a new line.';
$string['multiidpinfo'] = '
<ul>
<li>An IdP can only be used if it is set as Active</li>
<li>When duallogin has been turned on all active IdPs will be displayed on the login page</li>
<li>When an IdP has been set as Default and duallogin is not turned on, this IdP will automatically be used unless ?multiidp=on or saml=off is passed on /login/index.php</li>
<li>An IdP can be given an Alias, when going to /login/index.php?idpalias={alias} the alias can be passed to directly use that IdP</li>
</ul>';
$string['multiidpbuttons'] = 'Buttons with icons';
$string['multiidpdisplay'] = 'Multiple IdP display type';
$string['multiidpdisplay_help'] = 'If an IdP metadata xml contains multiple IdP entities, how will each available IdP be displayed?';
$string['multiidpdropdown'] = 'Drop-down list';
$string['nameidasattrib'] = 'Expose NameID as attribute';
$string['nameidasattrib_help'] = 'The NameID claim will be exposed to SSPHP as an attribute named nameid';
$string['noattribute'] = 'You have logged in successfully but we could not find your \'{$a}\' attribute to associate you to an account in Moodle.';
$string['noidpfound'] = 'The IdP \'{$a}\' was not found as a configured IdP.';
$string['nouser'] = 'You have logged in successfully as \'{$a}\' but do not have an account in Moodle.';
$string['nullprivatecert'] = 'Creation of Private Certificate failed.';
$string['nullpubliccert'] = 'Creation of Public Certificate failed.';
$string['organizationalunitname'] = 'Organisational Unit';
$string['organizationname'] = 'Organisation';
$string['passivemode'] = 'Passive mode';
$string['plugindisabled'] = 'IOMAD SAML2 authentication plugin is disabled';
$string['pluginname'] = 'IOMAD SAML2';
$string['privatekeypass'] = 'Private certificate key password';
$string['privatekeypass_help'] = 'This is used for signing the local Moodle certificate, changing this will invalidate the current certificate.';
$string['regenerateheading'] = 'Regenerate Private Key and Certificate';
$string['regenerate_submit'] = 'Regenerate';
$string['requestedattributes'] = 'Requested attributes';
$string['requestedattributes_help'] = 'Some IdP\'s need the SP to declare which attributes will be requested or are required. Add each attribute on a new line and these will be present in the SP metadata under the <code>AttributeConsumingService</code> tag. If you want a field to be required put a space and then * after that line. {$a->example}';
$string['rememberidp'] = 'Remember login service';
$string['required'] = 'This field is required';
$string['requireint'] = 'This field is required and needs to be a positive integer';
$string['showidplink'] = 'Display IdP link';
$string['showidplink_help'] = 'This will display the IdP link when the site is configured.';
$string['source'] = 'Source: {$a}';
$string['spmetadata_help'] = '<a href=\'{$a}\'>View Service Provider Metadata</a> | <a href=\'{$a}?download=1\'>Download SP Metadata</a>
<p>You may need to give this to the IdP admin to whitelist you.</p>';
$string['spmetadatasign_help'] = 'Sign the SP Metadata.';
$string['spmetadatasign'] = 'SP Metadata signature';
$string['spmetadata'] = 'SP Metadata';
$string['sspversion'] = 'SimpleSAMLphp version';
$string['stateorprovincename'] = 'State or Province';
$string['status'] = 'Status';
$string['suspendeduser'] = 'You have logged in successfully as \'{$a}\' but your account has been suspended in Moodle.';
$string['taskmetadatarefresh'] = 'Metadata refresh task';
$string['test_auth_button_login'] = 'IdP Login';
$string['test_auth_button_logout'] = 'IdP Logout';
$string['test_auth_str'] = 'Test isAuthenticated and login';
$string['test_endpoint'] = 'Connection test URL';
$string['test_endpoint_desc'] = 'Enter a URL to test connection against for IdP redirection from the client browser. Some users or networks may not have connectivity to the IdP based on account or network permissions.';
$string['test_idp_conn'] = 'Test IdP connection';
$string['test_noticetestrequirements'] = 'In order to use this test, plugin needs to be configured, enabled and debugging mode should be enabled in plugin settings.';
$string['test_passive_str'] = 'Test using isPassive';
$string['testdebuggingdisabled'] = 'To use this testing page SAML debugging must be on';
$string['tolower'] = 'Case matching';
$string['tolower:exact'] = 'Exact';
$string['tolower:lowercase'] = 'Lower case';
$string['tolower:caseandaccentinsensitive'] = 'Case and accent insensitive';
$string['tolower:caseinsensitive'] = 'Case insensitive';
$string['tolower_help'] = '
<p>Exact: match is case sensitive (default).</p>
<p>Lower case: applies lower case to the IdP attribute before matching.</p>
<p>Case insensitive: ignore case when matching.</p>';
$string['wrongauth'] = 'You have logged in successfully as \'{$a}\' but are not authorized to access Moodle.';
$string['auth_data_mapping'] = 'Data mapping';
$string['auth_fieldlockfield'] = 'Lock value ({$a})';
$string['auth_fieldmapping'] = 'Data mapping ({$a})';
$string['auth_fieldlock_expl'] = '<p><b>Lock value:</b> If enabled, will prevent Moodle users and admins from editing the field directly. Use this option if you are maintaining this data in the external auth system. </p>';
$string['auth_fieldlocks'] = 'Lock user fields';
$string['auth_updatelocalfield'] = 'Update local ({$a})';
$string['auth_updateremotefield'] = 'Update external ({$a})';
$string['cannotmapfield'] = 'Mapping collision detected - two fields maps to the same grade item {$a}';
$string['locked'] = 'Locked';
$string['unlocked'] = 'Unlocked';
$string['unlockedifempty'] = 'Unlocked if empty';
$string['update_never'] = 'Never';
$string['update_oncreate'] = 'On creation';
$string['update_onlogin'] = 'On every login';
$string['update_onupdate'] = 'On update';
$string['phone1'] = 'Phone';
$string['phone2'] = 'Mobile phone';
$string['nameidpolicy'] = 'NameID Policy';
$string['nameidpolicy_help'] = '';
$string['grouprules'] = 'Group rules';
$string['grouprules_help'] = '<p>A list of rules to be able to control access based on the group attribute value.</p>
<p>Each line should have one rule in format: {allow or deny} {groups attribute}={value}.</p>
<p>Higher in the list rule will be applied first.</p>
Example: <br/>
allow admins=yes<br>
deny admins=no<br>
allow examrole=proctor<br>
deny library=overdue<br>';
/*
 * Privacy provider (GDPR)
 */
$string["privacy:no_data_reason"] = "The IOMAD Saml2 authentication plugin does not store any personal data.";

/*
 * Signing Algorithm
 */
$string['sha1'] = 'Legacy SHA1 (Dangerous)';
$string['sha256'] = 'SHA256';
$string['sha384'] = 'SHA384';
$string['sha512'] = 'SHA512';
$string['signaturealgorithm'] = 'Signing Algorithm';
$string['signaturealgorithm_help'] = 'This is the algorithm that will be used to sign SAML requests. Warning: The SHA1 Algorithm is only provided for backwards compatibility, unless you absolutely must use it it is recommended to avoid it and use at least SHA256 instead.';
$string['selectloginservice'] = 'Select a login service';
$string['regenerateheader'] = 'Regenerate Private Key and Certificate';
$string['regeneratewarning'] = 'Warning! Generating a new certificate will overwrite the current one and you may need to update your IDP';
$string['regeneratepath'] = 'Certificate path path: {$a}';
$string['regenerateheader'] = 'Regenerate Private Key and Certificate';
$string['regeneratesuccess'] = 'Private Key and Certificate successfully regenerated';
