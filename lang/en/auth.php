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
 * Strings for component 'auth', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   core_auth
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actauthhdr'] = 'Available authentication plugins';
$string['alternatelogin'] = 'If you enter a URL here, it will be used as the login page for this site. The page should contain a form which has the action property set to <strong>\'{$a}\'</strong> and return fields <strong>username</strong> and <strong>password</strong>.<br />Be careful not to enter an incorrect URL as you may lock yourself out of this site.<br />Leave this setting blank to use the default login page.';
$string['alternateloginurl'] = 'Alternate login URL';
$string['auth_common_settings'] = 'Common settings';
$string['auth_data_mapping'] = 'Data mapping';
$string['authenticationoptions'] = 'Authentication options';
$string['auth_fieldlock'] = 'Lock value';
$string['auth_fieldlockfield'] = 'Lock value ({$a})';
$string['auth_fieldlock_expl'] = '<p><b>Lock value:</b> If enabled, will prevent Moodle users and admins from editing the field directly. Use this option if you are maintaining this data in the external auth system. </p>';
$string['auth_fieldlocks'] = 'Lock user fields';
$string['auth_fieldlocks_help'] = '<p>You can lock user data fields. This is useful for sites where the user data is maintained by the administrators manually by editing user records or uploading using the \'Upload users\' facility. If you are locking fields that are required by Moodle, make sure that you provide that data when creating user accounts or the accounts will be unusable.</p><p>Consider setting the lock mode to \'Unlocked if empty\' to avoid this problem.</p>';
$string['auth_fieldmapping'] = 'Data mapping ({$a})';
$string['auth_changepasswordhelp'] = 'Change password help';
$string['auth_changepasswordhelp_expl'] = 'Display lost password help to users who have lost their {$a} password. This will be displayed either as well as or instead of the <strong>Change Password URL</strong> or Internal Moodle password change.';
$string['auth_changepasswordurl'] = 'Change password URL';
$string['auth_changepasswordurl_expl'] = 'Specify the url to send users who have lost their {$a} password. Set <strong>Use standard Change Password page</strong> to <strong>No</strong>.';
$string['auth_changingemailaddress'] = 'You have requested a change of email address, from {$a->oldemail} to {$a->newemail}. For security reasons, we are sending you an email message at the new address to confirm that it belongs to you. Your email address will be updated as soon as you open the URL sent to you in that message.';
$string['authinstructions'] = 'Leave this blank for the default login instructions to be displayed on the login page. If you want to provide custom login instructions, enter them here.';
$string['auth_invalidnewemailkey'] = 'Error: if you are trying to confirm a change of email address, you may have made a mistake in copying the URL we sent you by email. Please copy the address and try again.';
$string['auth_multiplehosts'] = 'Multiple hosts OR addresses can be specified (eg host1.com;host2.com;host3.com) or (eg xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_notconfigured'] = 'The authentication method {$a} is not configured.';
$string['auth_outofnewemailupdateattempts'] = 'You have run out of allowed attempts to update your email address. Your update request has been cancelled.';
$string['auth_passwordisexpired'] = 'Your password has expired. Please change it now.';
$string['auth_passwordwillexpire'] = 'Your password will expire in {$a} days. Do you want to change your password now?';
$string['auth_remove_delete'] = 'Full delete internal';
$string['auth_remove_keep'] = 'Keep internal';
$string['auth_remove_suspend'] = 'Suspend internal';
$string['auth_remove_user'] = 'Specify what to do with internal user account during mass synchronisation when user was removed from external source. Only suspended users are automatically restored if they reappear in the external source.';
$string['auth_remove_user_key'] = 'Removed ext user';
$string['auth_sync_suspended']  = 'When enabled, the suspended attribute will be used to update the local user account\'s suspension status.';
$string['auth_sync_suspended_key'] = 'Synchronise local user suspension status';
$string['auth_sync_script'] = 'User account synchronisation';
$string['auth_updatelocal'] = 'Update local';
$string['auth_updatelocalfield'] = 'Update local ({$a})';
$string['auth_updatelocal_expl'] = '<p><b>Update local:</b> If enabled, the field will be updated (from external auth) every time the user logs in or there is a user synchronisation. Fields set to update locally should be locked.</p>';
$string['auth_updateremote'] = 'Update external';
$string['auth_updateremotefield'] = 'Update external ({$a})';
$string['auth_updateremote_expl'] = '<p><b>Update external:</b> If enabled, the external auth will be updated when the user record is updated. Fields should be unlocked to allow edits.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Note:</b> Updating external LDAP data requires that you set binddn and bindpw to a bind-user with editing privileges to all the user records. It currently does not preserve multi-valued attributes, and will remove extra values on update. </p>';
$string['auth_user_create'] = 'Enable user creation';
$string['auth_user_creation'] = 'New (anonymous) users can create user accounts on the external authentication source and confirmed via email. If you enable this , remember to also configure module-specific options for user creation.';
$string['auth_usernameexists'] = 'Selected username already exists. Please choose a new one.';
$string['auto_add_remote_users'] = 'Auto add remote users';
$string['cannotmapfield'] = 'The field "{$a->fieldname}" can\'t be mapped because its short name "{$a->shortname}" is too long. To allow it to be mapped, you need to reduce the short name to {$a->charlimit} characters. <a href="{$a->link}">Edit user profile fields</a>';
$string['createpassword'] = 'Generate password and notify user';
$string['createpasswordifneeded'] = 'Create password if needed and send via email';
$string['emailchangecancel'] = 'Cancel email change';
$string['emailchangepending'] = 'Change pending. Open the link sent to you at {$a->preference_newemail}.';
$string['emailnowexists'] = 'The email address you tried to assign to your profile has been assigned to someone else since your original request. Your request for change of email address is hereby cancelled, but you may try again with a different address.';
$string['emailupdate'] = 'Email address update';
$string['emailupdatemessage'] = 'Dear {$a->fullname},

You have requested a change of your email address for your user account at {$a->site}. Please open the following URL in your browser in order to confirm this change.

If you have any questions please contact support on: {$a->supportemail}

{$a->url}';
$string['emailupdatesuccess'] = 'Email address of user <em>{$a->fullname}</em> was successfully updated to <em>{$a->email}</em>.';
$string['emailupdatetitle'] = 'Confirmation of email update at {$a->site}';
$string['enterthenumbersyouhear'] = 'Enter the numbers you hear';
$string['enterthewordsabove'] = 'Enter the words above';
$string['errormaxconsecutiveidentchars'] = 'Passwords must have at most {$a} consecutive identical characters.';
$string['errorminpassworddigits'] = 'Passwords must have at least {$a} digit(s).';
$string['errorminpasswordlength'] = 'Passwords must be at least {$a} characters long.';
$string['errorminpasswordlower'] = 'Passwords must have at least {$a} lower case letter(s).';
$string['errorminpasswordnonalphanum'] = 'Passwords must have at least {$a} non-alphanumeric character(s) such as as *, -, or #.';
$string['errorpasswordreused'] = 'This password has been used before, and is not permitted to be reused';
$string['errorminpasswordupper'] = 'Passwords must have at least {$a} upper case letter(s).';
$string['errorpasswordupdate'] = 'Error updating password, password not changed';
$string['eventuserloggedin'] = 'User has logged in';
$string['eventuserloggedinas'] = 'User logged in as another user';
$string['eventuserloginfailed'] = 'User login failed';
$string['forcechangepassword'] = 'Force change password';
$string['forcechangepasswordfirst_help'] = 'Force users to change password on their first login to Moodle.';
$string['forcechangepassword_help'] = 'Force users to change password on their next login to Moodle.';
$string['forgottenpassword'] = 'If you enter a URL here, it will be used as the lost password recovery page for this site. This is intended for sites where passwords are handled entirely outside of Moodle. Leave this blank to use the default password recovery.';
$string['forgottenpasswordurl'] = 'Forgotten password URL';
$string['getanaudiocaptcha'] = 'Get an audio CAPTCHA';
$string['getanimagecaptcha'] = 'Get an image CAPTCHA';
$string['getanothercaptcha'] = 'Get another CAPTCHA';
$string['getrecaptchaapi'] = 'To use reCAPTCHA you must get an API key from <a href=\'https://www.google.com/recaptcha/admin\'>https://www.google.com/recaptcha/admin</a>';
$string['guestloginbutton'] = 'Guest login button';
$string['changepassword'] = 'Change password URL';
$string['changepasswordhelp'] = 'URL of lost password recovery page, which will be sent to users in an email. Note that this setting will have no effect if a forgotten password URL is set in the authentication common settings.';
$string['chooseauthmethod'] = 'Choose an authentication method';
$string['chooseauthmethod_help'] = 'This setting determines the authentication method used when the user logs in. Only enabled authentication plugins should be chosen, otherwise the user will no longer be able to log in. To block the user from logging in, select "No login".';
$string['incorrectpleasetryagain'] = 'Incorrect. Please try again.';
$string['infilefield'] = 'Field required in file';
$string['informminpassworddigits'] = 'at least {$a} digit(s)';
$string['informminpasswordlength'] = 'at least {$a} characters';
$string['informminpasswordlower'] = 'at least {$a} lower case letter(s)';
$string['informminpasswordnonalphanum'] = 'at least {$a} non-alphanumeric character(s) such as as *, -, or #';
$string['informminpasswordreuselimit'] = 'Passwords can be reused after {$a} changes';
$string['informminpasswordupper'] = 'at least {$a} upper case letter(s)';
$string['informpasswordpolicy'] = 'The password must have {$a}';
$string['instructions'] = 'Instructions';
$string['internal'] = 'Internal';
$string['limitconcurrentlogins'] = 'Limit concurrent logins';
$string['limitconcurrentlogins_desc'] = 'If enabled the number of concurrent browser logins for each user is restricted. The oldest session is terminated after reaching the limit, please note that users may lose all unsaved work. This setting is not compatible with single sign-on (SSO) authentication plugins.';
$string['locked'] = 'Locked';
$string['authloginviaemail'] = 'Allow log in via email';
$string['authloginviaemail_desc'] = 'Allow users to use both username and email address (if unique) for site login.';
$string['allowaccountssameemail'] = 'Allow accounts with same email';
$string['allowaccountssameemail_desc'] = 'If enabled, more than one user account can share the same email address. This may result in security or privacy issues, for example with the password change confirmation email.';
$string['md5'] = 'MD5 hash';
$string['nopasswordchange'] = 'Password can not be changed';
$string['nopasswordchangeforced'] = 'You cannot proceed without changing your password, however there is no available page for changing it. Please contact your Moodle Administrator.';
$string['noprofileedit'] = 'Profile can not be edited';
$string['ntlmsso_attempting'] = 'Attempting Single Sign On via NTLM...';
$string['ntlmsso_failed'] = 'Auto-login failed, try the normal login page...';
$string['ntlmsso_isdisabled'] = 'NTLM SSO is disabled.';
$string['passwordhandling'] = 'Password field handling';
$string['plaintext'] = 'Plain text';
$string['pluginnotenabled'] = 'Authentication plugin \'{$a}\' is not enabled.';
$string['pluginnotinstalled'] = 'Authentication plugin \'{$a}\' is not installed.';
$string['privacy:metadata:userpref:createpassword'] = 'Indicates that a password should be generated for the user';
$string['privacy:metadata:userpref:forcepasswordchange'] = 'Indicates whether the user should change their password upon logging in';
$string['privacy:metadata:userpref:loginfailedcount'] = 'The number of times the user failed to log in';
$string['privacy:metadata:userpref:loginfailedcountsincesuccess'] = 'The number of times the user failed to login since their last successful login';
$string['privacy:metadata:userpref:loginfailedlast'] = 'The date at which the last failed login attempt was recorded';
$string['privacy:metadata:userpref:loginlockout'] = 'Indicates whether the user\'s account is locked due to failed login attempts, and the date at which the account entered the lockout state';
$string['privacy:metadata:userpref:loginlockoutignored'] = 'Indicates that a user\'s account should never be subject to lockouts';
$string['privacy:metadata:userpref:loginlockoutsecret'] = 'When locked, the secret the user must use for unlocking their account';
$string['potentialidps'] = 'Log in using your account on:';
$string['recaptcha'] = 'reCAPTCHA';
$string['recaptcha_help'] = 'The CAPTCHA is for preventing abuse from automated programs. Follow the instructions to verify you are a person. This could be a box to check, characters presented in an image you must enter or a set of images to select from.

If you are not sure what the images are, you can try getting another CAPTCHA or an audio CAPTCHA.';
$string['recaptcha_link'] = 'auth/email';
$string['security_question'] = 'Security question';
$string['selfregistration'] = 'Self registration';
$string['selfregistration_help'] = 'If an authentication plugin, such as email-based self-registration, is selected, then it enables potential users to register themselves and create accounts. This results in the possibility of spammers creating accounts in order to use forum posts, blog entries etc. for spam. To avoid this risk, self-registration should be disabled or limited by <em>Allowed email domains</em> setting.';
$string['settingmigrationmismatch'] = 'Values mismatch detected while correcting the plugin setting names! The authentication plugin \'{$a->plugin}\' had the setting \'{$a->setting}\' configured to \'{$a->legacy}\' under the legacy name and to \'{$a->current}\' under the current name. The latter value has been set as the valid one but you should check and confirm that it is expected.';
$string['sha1'] = 'SHA-1 hash';
$string['showguestlogin'] = 'You can hide or show the guest login button on the login page.';
$string['stdchangepassword'] = 'Use standard page for changing password';
$string['stdchangepassword_expl'] = 'If the external authentication system allows password changes through Moodle, switch this to Yes. This setting overrides \'Change Password URL\'.';
$string['stdchangepassword_explldap'] = 'NOTE: It is recommended that you use LDAP over an SSL encrypted tunnel (ldaps://) if the LDAP server is remote.';
$string['suspended'] = 'Suspended account';
$string['suspended_help'] = 'Suspended user accounts cannot log in or use web services, and any outgoing messages are discarded.';
$string['testsettings'] = 'Test settings';
$string['testsettingsheading'] = 'Test authentication settings - {$a}';
$string['unlocked'] = 'Unlocked';
$string['unlockedifempty'] = 'Unlocked if empty';
$string['update_never'] = 'Never';
$string['update_oncreate'] = 'On creation';
$string['update_onlogin'] = 'On every login';
$string['update_onupdate'] = 'On update';
$string['user_activatenotsupportusertype'] = 'auth: ldap user_activate() does not support selected usertype: {$a}';
$string['user_disablenotsupportusertype'] = 'auth: ldap user_disable() does not support selected usertype (..yet)';
$string['username'] = 'Username';
$string['username_help'] = 'Please be aware that some authentication plugins will not allow you to change the username.';
