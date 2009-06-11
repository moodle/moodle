<?php // $Id$
      // auth.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005010100)


$string['actauthhdr'] = 'Active authentication plugins';
$string['alternatelogin'] = 'If you enter a URL here, it will be used as the login page for this site. The page should contain a form which has the action property set to <strong>\'$a\'</strong> and return fields <strong>username</strong> and <strong>password</strong>.<br />Be careful not to enter an incorrect URL as you may lock yourself out of this site.<br />Leave this setting blank to use the default login page.';
$string['alternateloginurl'] = 'Alternate Login URL';
$string['forgottenpassword'] = 'If you enter a URL here, it will be used as the lost password recovery page for this site. This is intended for sites where passwords are handled entirely outside of Moodle. Leave this blank to use the default password recovery.';
$string['forgottenpasswordurl'] = 'Forgotten password URL';
$string['pluginnotenabled'] = 'Authentication plugin \'$a\' is not enabled.';
$string['pluginnotinstalled'] = 'Authentication plugin \'$a\' is not installed.';
$string['user_activatenotsupportusertype'] = 'auth: ldap user_activate() does not support selected usertype: $a';
$string['user_disablenotsupportusertype'] = 'auth: ldap user_disable() does not support selected usertype (..yet)';
// synchronization
$string['auth_sync_script'] ='Cron synchronization script';
$string['auth_remove_user_key'] ='Removed ext user';
$string['auth_remove_user'] ='Specify what to do with internal user account during mass synchronization when user was removed from external source. Only suspended users are automatically revived if they reappear in ext source.';
$string['auth_remove_keep'] ='Keep internal';
$string['auth_remove_suspend'] ='Suspend internal';
$string['auth_remove_delete'] ='Full delete internal';

$string['auth_changepasswordurl'] = 'Change password URL';
$string['auth_changepasswordurl_expl'] = 'Specify the url to send users who have lost their $a password. Set <strong>Use standard Change Password page</strong> to <strong>No</strong>.';
$string['auth_changepasswordhelp'] = 'Change password help';
$string['auth_changepasswordhelp_expl'] = 'Display lost password help to users who have lost their $a password. This will be displayed either as well as or instead of the <strong>Change Password URL</strong> or Internal Moodle password change.';
$string['auth_common_settings'] = 'Common settings';
$string['auth_data_mapping'] = 'Data mapping';

// Fieldlocks
$string['auth_fieldlock'] = 'Lock value';
$string['auth_fieldlock_expl'] = '<p><b>Lock value:</b> If enabled, will prevent Moodle users and admins from editing the field directly. Use this option if you are maintaining this data in the external auth system. </p>';
$string['auth_fieldlocks'] = 'Lock user fields';
$string['auth_fieldlocks_help'] = '<p>You can lock user data fields. This is useful for sites where the user data is maintained by the administrators manually by editing user records or uploading using the \'Upload users\' facility. If you are locking fields that are required by Moodle, make sure that you provide that data when creating user accounts or the accounts will be unusable.</p><p>Consider setting the lock mode to \'Unlocked if empty\' to avoid this problem.</p>';

$string['auth_multiplehosts'] = 'Multiple hosts OR addresses can be specified (eg host1.com;host2.com;host3.com) or (eg xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_updatelocal'] = 'Update local';
$string['auth_updatelocal_expl'] = '<p><b>Update local:</b> If enabled, the field will be updated (from external auth) every time the user logs in or there is a user synchronization. Fields set to update locally should be locked.</p>';
$string['auth_updateremote'] = 'Update external';
$string['auth_updateremote_expl'] = '<p><b>Update external:</b> If enabled, the external auth will be updated when the user record is updated. Fields should be unlocked to allow edits.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Note:</b> Updating external LDAP data requires that you set binddn and bindpw to a bind-user with editing privileges to all the user records. It currently does not preserve multi-valued attributes, and will remove extra values on update. </p>';
$string['auth_user_create'] = 'Enable user creation';
$string['auth_user_creation'] = 'New (anonymous) users can create user accounts on the external authentication source and confirmed via email. If you enable this , remember to also configure module-specific options for user creation.';
$string['auth_usernameexists'] = 'Selected username already exists. Please choose a new one.';
$string['authenticationoptions'] = 'Authentication options';
$string['authinstructions'] = 'Here you can provide instructions for your users, so they know which username and password they should be using.  The text you enter here will appear on the login page.  If you leave this blank then no instructions will be printed.';
$string['changepassword'] = 'Change password URL';
$string['changepasswordhelp'] = 'Here you can specify a location at which your users can recover or change their username/password if they\'ve forgotten it. This will be provided to users as a button on the login page and their user page. If you leave this blank the button will not be printed.';
$string['chooseauthmethod'] = 'Choose an authentication method';
$string['createpasswordifneeded'] = 'Create password if needed';
$string['errorpasswordupdate'] = 'Error updating password, password not changed';
$string['errormaxconsecutiveidentchars'] = 'Passwords must have at most $a consecutive identical characters.';
$string['errorminpasswordlength'] = 'Passwords must be at least $a characters long.';
$string['errorminpassworddigits'] = 'Passwords must have at least $a digit(s).';
$string['errorminpasswordlower'] = 'Passwords must have at least $a lower case letter(s).';
$string['errorminpasswordnonalphanum'] = 'Passwords must have at least $a non-alphanumeric character(s).';
$string['errorminpasswordupper'] = 'Passwords must have at least $a upper case letter(s).';
$string['infilefield'] = 'Field required in file';
$string['forcechangepassword'] = 'Force change password';
$string['forcechangepassword_help'] = 'Force users to change password on their next login to Moodle.';
$string['forcechangepasswordfirst_help'] = 'Force users to change password on their first login to Moodle.';
$string['guestloginbutton'] = 'Guest login button';
$string['instructions'] = 'Instructions';
$string['internal'] = 'Internal';
$string['md5'] = 'MD5 hash';
$string['nopasswordchange'] = 'Password can not be changed';
$string['nopasswordchangeforced'] ='You cannot proceed without changing your password, however there is no available page for changing it. Please contact your Moodle Administrator.';
$string['passwordhandling'] = 'Password field handling';
$string['plaintext'] = 'Plain text';
$string['selfregistration'] = 'Self registration';
$string['selfregistration_help'] = 'If an authentication plugin, such as email-based self-registration, is selected, then it enables potential users to register themselves and create accounts. This results in the possibility of spammers creating accounts in order to use forum posts, blog entries etc. for spam. To avoid this risk, self-registration should be disabled or limited by <em>Allowed email domains</em> setting.';
$string['sha1'] = 'SHA-1 hash';
$string['showguestlogin'] = 'You can hide or show the guest login button on the login page.';
$string['stdchangepassword'] = 'Use standard Change Password Page';
$string['stdchangepassword_expl'] = 'If the external authentication system allows password changes through Moodle, switch this to Yes. This setting overrides \'Change Password URL\'.';
$string['stdchangepassword_explldap'] = 'NOTE: It is recommended that you use LDAP over an SSL encrypted tunnel (ldaps://) if the LDAP server is remote.';
$string['update_oncreate'] = 'On creation';
$string['update_onlogin']  = 'On every login';
$string['update_onupdate']  = 'On update';
$string['update_never']    = 'Never';
$string['unlocked'] = 'Unlocked';
$string['unlockedifempty'] = 'Unlocked if empty';
$string['locked'] = 'Locked';
$string['incorrectpleasetryagain'] = 'Incorrect. Please try again.';
$string['enterthewordsabove'] = 'Enter the words above';
$string['enterthenumbersyouhear'] = 'Enter the numbers you hear';
$string['getanothercaptcha'] = 'Get another CAPTCHA';
$string['getanaudiocaptcha'] = 'Get an audio CAPTCHA';
$string['getanimagecaptcha'] = 'Get an image CAPTCHA';
$string['recaptcha'] = 'reCAPTCHA';
?>
