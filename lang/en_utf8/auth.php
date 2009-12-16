<?php // $Id$
      // auth.php - created with Moodle 1.5 UNSTABLE DEVELOPMENT (2005010100)


$string['actauthhdr'] = 'Active authentication plugins';
$string['alternatelogin'] = 'If you enter a URL here, it will be used as the login page for this site. The page should contain a form which has the action property set to <strong>\'$a\'</strong> and return fields <strong>username</strong> and <strong>password</strong>.<br />Be careful not to enter an incorrect URL as you may lock yourself out of this site.<br />Leave this setting blank to use the default login page.';
$string['alternateloginurl'] = 'Alternate Login URL';
$string['forgottenpassword'] = 'If you enter a URL here, it will be used as the lost password recovery page for this site. This is intended for sites where passwords are handled entirely outside of Moodle. Leave this blank to use the default password recovery.';
$string['forgottenpasswordurl'] = 'Forgotten password URL';

$string['pluginnotenabled'] = 'Authentication plugin \'$a\' is not enabled.';
$string['pluginnotinstalled'] = 'Authentication plugin \'$a\' is not installed.';

// synchronization
$string['auth_sync_script'] ='Cron synchronization script';
$string['auth_dbinsertuser'] ='Inserted user $a[0] id $a[1]';
$string['auth_dbinsertusererror'] = 'Error inserting user $a';
$string['auth_dbdeleteuser'] ='Deleted user $a[0] id $a[1]';
$string['auth_dbdeleteusererror'] = 'Error deleting user $a';
$string['auth_dbreviveduser'] ='Revived user $a[0] id $a[1]';
$string['auth_dbrevivedusererror'] = 'Error reviving user $a';
$string['auth_dbsuspenduser'] ='Suspended user $a[0] id $a[1]';
$string['auth_dbsuspendusererror'] = 'Error suspending user $a';
$string['auth_dbupdatinguser'] ='Updating user $a[0] id $a[1]';
$string['auth_remove_user_key'] ='Removed ext user';
$string['auth_remove_user'] ='Specify what to do with internal user account during mass synchronization when user was removed from external source. Only suspended users are automatically revived if they reappear in ext source.';
$string['auth_remove_keep'] ='Keep internal';
$string['auth_remove_suspend'] ='Suspend internal';
$string['auth_remove_delete'] ='Full delete internal';

// nologin plugin
$string['auth_nologindescription'] = 'Auxiliary plugin that prevents user to login into system and also discards any mail sent to the user. Can be used to <em>suspend</em> user accounts.';
$string['auth_nologintitle'] = 'No login';

// CAS plugin
$string['auth_cas_proxycas_key'] = "Proxy mode";
$string['auth_cas_logoutcas_key'] = "Logout CAS";
$string['auth_cas_multiauth_key'] = "Multi-authentication";
$string['auth_cas_proxycas'] = "Turn this to 'yes'' if you use CASin proxy-mode";
$string['auth_cas_logoutcas'] = "Turn this to 'yes'' if tou want to logout from CAS when you deconnect from Moodle";
$string['auth_cas_multiauth'] = "Turn this to 'yes'' if you want to have multi-authentication (CAS + other authentication)";
$string['accesCAS'] = "CAS users";
$string['accesNOCAS'] = "other users";
$string['CASform'] = "Authentication choice";
$string['auth_cas_logincas'] = 'Secure connection access';
$string['auth_cas_invalidcaslogin'] = 'Sorry, your login has failed - you could not be authorised';
$string['auth_cas_server_settings'] = 'CAS server configuration';
$string['auth_castitle'] = 'CAS server (SSO)';
$string['auth_cas_hostname'] = 'Hostname of the CAS server <br />eg: host.domain.fr';
$string['auth_cas_baseuri'] = 'URI of the server (nothing if no baseUri)<br />For example, if the CAS server responds to host.domaine.fr/CAS/ then<br />cas_baseuri = CAS/';
$string['auth_cas_port'] = 'Port of the CAS server';
$string['auth_cas_version'] = 'Version of CAS';
$string['auth_cas_language'] = 'Selected language';
$string['auth_casdescription'] = 'This method uses a CAS server (Central Authentication Service) to authenticate users in a Single Sign On environment (SSO). You can also use a simple LDAP authentication. If the given username and password are valid according to CAS, Moodle creates a new user entry in its database, taking user attributes from LDAP if required. On following logins only the username and password are checked.';
$string['auth_cas_enabled'] = 'Turn this on if you want to use CAS authentication.';
$string['auth_cas_text'] = 'Secure connection';
$string['auth_cas_create_user'] = 'Turn this on if you want to insert CAS-authenticated users in Moodle database. If not then only users who already exist in the Moodle database can log in.';
$string['auth_casnotinstalled'] = 'Cannot use CAS authentication. The PHP LDAP module is not installed.';
$string['auth_cas_cantconnect'] ='LDAP part of CAS-module cannot connect to server: $a';
$string['auth_cas_use_cas'] ='Use CAS';
$string['auth_cas_broken_password'] ='You cannot proceed without changing your password, however there is no available page for changing it. Please contact your Moodle Administrator.';

$string['auth_cas_hostname_key'] ='Hostname';
$string['auth_cas_changepasswordurl'] ='Password-change URL';
$string['auth_cas_create_user_key'] ='Create user';
$string['auth_cas_auth_user_create'] ='Create users externally';
$string['auth_cas_language_key'] ='Language';
$string['auth_cas_casversion'] ='Version';
$string['auth_cas_port_key'] ='Port';
$string['auth_cas_baseuri_key'] ='Base URI';

$string['auth_changepasswordurl'] = 'Change password URL';
$string['auth_changepasswordurl_expl'] = 'Specify the url to send users who have lost their $a password. Set <strong>Use standard Change Password page</strong> to <strong>No</strong>.';
$string['auth_changepasswordhelp'] = 'Change password help';
$string['auth_changepasswordhelp_expl'] = 'Display lost password help to users who have lost their $a password. This will be displayed either as well as or instead of the <strong>Change Password URL</strong> or Internal Moodle password change.';
$string['auth_common_settings'] = 'Common settings';
$string['auth_data_mapping'] = 'Data mapping';

// Database plugin
$string['auth_dbdescription'] = 'This method uses an external database table to check whether a given username and password is valid.  If the account is a new one, then information from other fields may also be copied across into Moodle.';
$string['auth_dbextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <b>external database fields</b> that you specify here. <p>If you leave these blank, then defaults will be used.</p><p>In either case, the user will be able to edit all of these fields after they log in.</p>';
$string['auth_dbfieldpass'] = 'Name of the field containing passwords';
$string['auth_dbfielduser'] = 'Name of the field containing usernames';
$string['auth_dbhost'] = 'The computer hosting the database server.';
$string['auth_dbname'] = 'Name of the database itself';
$string['auth_dbpass'] = 'Password matching the above username';
$string['auth_dbpasstype'] = '<p>Specify the format that the password field is using. MD5 hashing is useful for connecting to other common web applications like PostNuke.</p> <p>Use \'internal\' if you want to the external DB to manage usernames &amp; email addresses, but Moodle to manage passwords. If you use \'internal\', you <i>must</i> provide a populated email address field in the external DB, and you must execute both admin/cron.php and auth/db/auth_db_sync_users.php regularly. Moodle will send an email to new users with a temporary password.</p>';
$string['auth_dbtable'] = 'Name of the table in the database';
$string['auth_dbtitle'] = 'External database';
$string['auth_dbtype'] = 'The database type (See the <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb documentation</a> for details)';
$string['auth_dbuser'] = 'Username with read access to the database';
$string['auth_dbcantconnect'] ='Could not connect to the specified authentication database...';
$string['auth_dbuserstoadd'] = 'User entries to add: $a';
$string['auth_dbuserstoremove'] = 'User entries to remove: $a';
$string['auth_dbusernotexist'] = 'Cannot update non-existent user: $a';
$string['auth_dbhost_key'] = 'Host';
$string['auth_dbtype_key'] = 'Database';
$string['auth_dbsybasequoting'] = 'Use sybase quotes';
$string['auth_dbsybasequotinghelp'] = 'Sybase style single quote escaping - needed for Oracle, MS SQL and some other databases. Do not use for MySQL!';
$string['auth_dbname_key'] = 'DB Name';
$string['auth_dbuser_key'] = 'DB User';
$string['auth_dbpass_key'] = 'Password';
$string['auth_dbtable_key'] = 'Table';
$string['auth_dbfielduser_key'] = 'Username field';
$string['auth_dbfieldpass_key'] = 'Password field';
$string['auth_dbpasstype_key'] = 'Password format';
$string['auth_dbextencoding'] = 'External db encoding';
$string['auth_dbextencodinghelp'] = 'Encoding used in external database';
$string['auth_dbsetupsql'] = 'SQL setup command';
$string['auth_dbsetupsqlhelp'] = 'SQL command for special database setup, often used to setup communication encoding - example for MySQL and PostgreSQL: <em>SET NAMES \'utf8\'</em>';
$string['auth_dbdebugauthdb'] = 'Debug ADOdb';
$string['auth_dbdebugauthdbhelp'] = 'Debug ADOdb connection to external database - use when getting empty page during login. Not suitable for production sites.';
$string['auth_dbchangepasswordurl_key'] = 'Password-change URL';

// Email plugin
$string['auth_emailchangecancel'] = 'Cancel email change';
$string['auth_emailchangepending'] = 'Change pending. Open the link sent to you at $a->preference_newemail.';
$string['auth_emaildescription'] = 'Email confirmation is the default authentication method.  When the user signs up, choosing their own new username and password, a confirmation email is sent to the user\'s email address.  This email contains a secure link to a page where the user can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.';
$string['auth_emailnowexists'] = 'The email address you tried to assign to your profile has been assigned to someone else since your original request. Your request for change of email address is hereby cancelled, but you may try again with a different address.';
$string['auth_emailtitle'] = 'Email-based self-registration';
$string['auth_emailnoinsert'] = 'Could not add your record to the database!';
$string['auth_emailnoemail'] = 'Tried to send you an email but failed!';
$string['auth_emailrecaptcha'] = 'Adds a visual/audio confirmation form element to the signup page for email self-registering users. This protects your site against spammers and contributes to a worthwhile cause. See http://recaptcha.net/learnmore.html for more details. <br /><em>PHP cURL extension is required.</em>';
$string['auth_emailrecaptcha_key'] = 'Enable reCAPTCHA element';
$string['auth_emailsettings'] = 'Settings';
$string['auth_emailupdatemessage'] = 'Dear $a->fullname,

You have requested a change of your email address for your user account at $a->site. Please open the following URL in your browser in order to confirm this change.

$a->url';
$string['auth_emailupdatetitle'] = 'Confirmation of email update at $a->site';
$string['auth_invalidnewemailkey'] = 'Error: if you are trying to confirm a change of email address, you may have made a mistake in copying the URL we sent you by email. Please copy the address and try again.';
$string['auth_emailupdatesuccess'] = 'Email address of user <em>$a->fullname</em> was successfully updated to <em>$a->email</em>.';
$string['auth_outofnewemailupdateattempts'] = 'You have run out of allowed attempts to update your email address. Your update request has been cancelled.';
$string['auth_emailupdate'] = 'Email address update';
$string['auth_changingemailaddress'] = 'You have requested a change of email address, from $a->oldemail to $a->newemail. For security reasons, we are sending you an email message at the new address to confirm that it belongs to you. Your email address will be updated as soon as you open the URL sent to you in that message.';

// FirstClass plugin
$string['auth_fccreators'] = 'List of groups whose members are allowed to create new courses. Separate multiple groups with \';\'. Names must be spelled exactly as on FirstClass server. System is case-sensitive.';
$string['auth_fcdescription'] = 'This method uses a FirstClass server to check whether a given username and password is valid.';
$string['auth_fcfppport'] = 'Server port (3333 is the most common)';
$string['auth_fchost'] = 'The FirstClass server address. Use the IP number or DNS name.';
$string['auth_fcpasswd'] = 'Password for the account above.';
$string['auth_fctitle'] = 'FirstClass server';
$string['auth_fcuserid'] = 'Userid for FirstClass account with privilege \'Subadministrator\' set.';
$string['auth_fchost_key'] = 'Host';
$string['auth_fcfppport_key'] = 'Port';
$string['auth_fcuserid_key'] = 'User ID';
$string['auth_fcpasswd_key'] = 'Password';
$string['auth_fccreators_key'] = 'Creators';
$string['auth_fcchangepasswordurl'] = 'Password-change URL';
$string['auth_fcconnfail'] = 'Connection failed with Errno: $a[0] and Error String: $a[1]';

// Fieldlocks
$string['auth_fieldlock'] = 'Lock value';
$string['auth_fieldlock_expl'] = '<p><b>Lock value:</b> If enabled, will prevent Moodle users and admins from editing the field directly. Use this option if you are maintaining this data in the external auth system. </p>';
$string['auth_fieldlocks'] = 'Lock user fields';
$string['auth_fieldlocks_help'] = '<p>You can lock user data fields. This is useful for sites where the user data is maintained by the administrators manually by editing user records or uploading using the \'Upload users\' facility. If you are locking fields that are required by Moodle, make sure that you provide that data when creating user accounts or the accounts will be unusable.</p><p>Consider setting the lock mode to \'Unlocked if empty\' to avoid this problem.</p>';

// IMAP plugin
$string['auth_imapnotinstalled'] = 'Cannot use IMAP authentication. The PHP IMAP module is not installed.';
$string['auth_imapdescription'] = 'This method uses an IMAP server to check whether a given username and password is valid.';
$string['auth_imaphost'] = 'The IMAP server address. Use the IP number, not DNS name.';
$string['auth_imapport'] = 'IMAP server port number. Usually this is 143 or 993.';
$string['auth_imaptitle'] = 'IMAP server';
$string['auth_imaptype'] = 'The IMAP server type.  IMAP servers can have different types of authentication and negotiation.';
$string['auth_imaptype_key'] = 'Type';
$string['auth_imaphost_key'] = 'Host';
$string['auth_imapport_key'] = 'Port';
$string['auth_imapchangepasswordurl_key'] = 'Password-change URL';

// LDAP plugin
$string['auth_ldap_ad_create_req'] = 'Cannot create the new account in Active Directory. Make sure you meet all the requirements for this to work (LDAPS connection, bind user with adequate rights, etc.)';
$string['auth_ldap_attrcreators'] = 'List of groups or contexts whose members are allowed to create attributes. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_attrcreators_key'] = 'Attribute creators';
$string['auth_ldap_bind_dn'] = 'If you want to use bind-user to search users, specify it here. Something like \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Password for bind-user.';
$string['auth_ldap_bind_settings'] = 'Bind settings';
$string['auth_ldap_contexts'] = 'List of contexts where users are located. Separate different contexts with \';\'. For example: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'If you enable user creation with email confirmation, specify the context where users are created. This context should be different from other users to prevent security issues. You don\'t need to add this context to ldap_context-variable, Moodle will search for users from this context automatically.<br /><b>Note!</b> You have to modify the method user_create() in file auth/ldap/auth.php to make user creation work';
$string['auth_ldap_create_error'] = 'Error creating user in LDAP.';
$string['auth_ldap_creators'] = 'List of groups or contexts whose members are allowed to create new courses. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_expiration_desc'] = 'Select No to disable expired password checking or LDAP to read passwordexpiration time directly from LDAP';
$string['auth_ldap_expiration_warning_desc'] = 'Number of days before password expiration warning is issued.';
$string['auth_ldap_expireattr_desc'] = 'Optional: overrides ldap-attribute that stores password expiration time';
$string['auth_ldap_graceattr_desc'] = 'Optional: Overrides  gracelogin attribute';
$string['auth_ldap_gracelogins_desc'] = 'Enable LDAP gracelogin support. After password has expired user can login until gracelogin count is 0. Enabling this setting displays grace login message if password is expired.';
$string['auth_ldap_groupecreators'] = 'List of groups or contexts whose members are allowed to create groups. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_groupecreators_key'] = 'Group creators';
$string['auth_ldap_host_url'] = 'Specify LDAP host in URL-form like \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\' Separate multipleservers with \';\' to get failover support.';
$string['auth_ldap_ldap_encoding'] = 'Specify encoding used by LDAP server. Most probably utf-8, MS AD v2 uses default platform encoding such as cp1252, cp1250, etc.';
$string['auth_ldap_login_settings'] = 'Login settings';
$string['auth_ldap_memberattribute'] = 'Optional: Overrides user member attribute, when users belongs to a group. Usually \'member\'';
$string['auth_ldap_memberattribute_isdn'] = 'Optional: Overrides handling of member attribute values, either 0 or 1';
$string['auth_ldap_no_mbstring'] = 'You need the mbstring extension to create users in Active Directory.';
$string['auth_ldap_objectclass'] = 'Optional: Overrides objectClass used to name/search users on ldap_user_type. Usually you dont need to chage this.';
$string['auth_ldap_opt_deref'] = 'Determines how aliases are handled during search. Select one of the following values: \"No\" (LDAP_DEREF_NEVER) or \"Yes\" (LDAP_DEREF_ALWAYS)';
$string['auth_ldap_passtype'] = 'Specify the format of new or changed passwords in LDAP server.';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP password expiration settings.';
$string['auth_ldap_preventpassindb'] = 'Select yes to prevent passwords from being stored in Moodle\'s DB.';
$string['auth_ldap_search_sub'] = 'Search users from subcontexts.';
$string['auth_ldap_server_settings'] = 'LDAP server settings';
$string['auth_ldap_update_userinfo'] = 'Update user information (firstname, lastname, address..) from LDAP to Moodle.  Specify \"Data mapping\" settings as you need.';
$string['auth_ldap_user_exists'] = 'LDAP username already exists.';
$string['auth_ldap_user_attribute'] = 'Optional: Overrides the attribute used to name/search users. Usually \'cn\'.';
$string['auth_ldap_user_settings'] = 'User lookup settings';
$string['auth_ldap_user_type'] = 'Select how users are stored in LDAP. This setting also specifies how login expiration, grace logins and user creation will work.';
$string['auth_ldap_version'] = 'The version of the LDAP protocol your server is using.';
$string['auth_ldapdescription'] = 'This method provides authentication against an external LDAP server.

                                  If the given username and password are valid, Moodle creates a new user

                                  entry in its database. This module can read user attributes from LDAP and prefill

                                  wanted fields in Moodle.  For following logins only the username and

                                  password are checked.';
$string['auth_ldap_ldap_encoding_key'] = 'LDAP encoding';
$string['auth_ldap_host_url_key'] = 'Host URL';
$string['auth_ldap_version_key'] = 'Version';
$string['auth_ldap_preventpassindb_key'] = 'Hide passwords';
$string['auth_ldap_bind_dn_key'] = 'Distinguished Name';
$string['auth_ldap_bind_pw_key'] = 'Password';
$string['auth_ldap_user_type_key'] = 'User type';
$string['auth_ldap_contexts_key'] = 'Contexts';
$string['auth_ldap_search_sub_key'] = 'Search subcontexts';
$string['auth_ldap_opt_deref_key'] = 'Dereference aliases';
$string['auth_ldap_user_attribute_key'] = 'User attribute';
$string['auth_ldap_memberattribute_key'] = 'Member attribute';
$string['auth_ldap_memberattribute_isdn_key'] = 'Member attribute uses dn';
$string['auth_ldap_objectclass_key'] = 'Object class';
$string['auth_ldap_passtype_key'] = 'Password format';
$string['auth_ldap_changepasswordurl_key'] = 'Password-change URL';
$string['auth_ldap_expiration_key'] = 'Expiration';
$string['auth_ldap_expiration_warning_key'] = 'Expiration warning';
$string['auth_ldap_expireattr_key'] = 'Expiration attribute';
$string['auth_ldap_gracelogins_key'] = 'Grace logins';
$string['auth_ldap_gracelogin_key'] = 'Grace login attribute';
$string['auth_ldap_auth_user_create_key'] = 'Create users externally';
$string['auth_ldap_create_context_key'] = 'Context for new users';
$string['auth_ldap_creators_key'] = 'Creators';
$string['auth_ldap_noconnect'] = 'LDAP-module cannot connect to server: $a';
$string['auth_ldap_noconnect_all'] = 'LDAP-module cannot connect to any servers: $a';
$string['auth_ldap_unsupportedusertype'] = 'auth: ldap user_create() does not support selected usertype: $a (..yet)';
$string['auth_ldap_usertypeundefined'] = 'config.user_type not defined or function ldap_expirationtime2unix does not support selected type!';
$string['auth_ldap_usertypeundefined2'] = 'config.user_type not defined or function ldap_unixi2expirationtime does not support selected type!';
$string['auth_ldap_noextension'] = 'Warning: The PHP LDAP module does not seem to be present. Please ensure it is installed and enabled.';

$string['auth_ldapextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <b>LDAP fields</b> that you specify here. <p>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.</p><p>In either case, the user will be able to edit all of these fields after they log in.</p>';
$string['auth_ldaptitle'] = 'LDAP server';
$string['auth_ldapnotinstalled'] = 'Cannot use LDAP authentication. The PHP LDAP module is not installed.';
$string['auth_ntlmsso'] = 'NTLM SSO';
$string['auth_ntlmsso_enabled_key'] = 'Enable';
$string['auth_ntlmsso_enabled'] = 'Set to yes to attempt Single Sign On with the NTLM domain. <strong>Note:</strong> this requires additional setup on the webserver to work, see <a href=\"http://docs.moodle.org/en/NTLM_authentication\">http://docs.moodle.org/en/NTLM_authentication</a>';
$string['auth_ntlmsso_ie_fastpath'] = 'Set to yes to enable the NTLM SSO fast path (bypasses certain steps and only works if the client\'s browser is MS Internet Explorer).';
$string['auth_ntlmsso_ie_fastpath_key'] = 'MS IE fast path?';
$string['auth_ntlmsso_subnet_key'] = 'Subnet';
$string['auth_ntlmsso_subnet'] = 'If set, it will only attempt SSO with clients in this subnet. Format: xxx.xxx.xxx.xxx/bitmask';
$string['ntlmsso_attempting'] = 'Attempting Single Sign On via NTLM...';
$string['ntlmsso_failed'] = 'Auto-login failed, try the normal login page...';
$string['ntlmsso_isdisabled'] = 'NTLM SSO is disabled.';

// Manual plugin
$string['auth_manualdescription'] = 'This method removes any way for users to create their own accounts.  All accounts must be manually created by the admin user.';
$string['auth_manualtitle'] = 'Manual accounts';

// MNET plugin
$string['auth_mnettitle'] = 'Moodle Network authentication';
$string['auth_mnetdescription'] = 'Users are authenticated according to the web of trust defined in your Moodle Network settings.';
$string['auth_mnet_rpc_negotiation_timeout'] = 'The timeout in seconds for authentication over the XMLRPC transport.';
$string['auth_mnet_roamout'] = 'Your users can roam out to these hosts';
$string['auth_mnet_roamin'] = 'These host\'s users can roam in to your site';
$string['auth_mnet_auto_add_remote_users'] = 'When set to Yes, a local user record is auto-created when a remote user logs in for the first time.';
$string['auto_add_remote_users'] = 'Auto add remote users';
$string['rpc_negotiation_timeout'] = 'RPC negotiation timeout';

$string['auth_multiplehosts'] = 'Multiple hosts OR addresses can be specified (eg host1.com;host2.com;host3.com) or (eg xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';

// NNTP plugin
$string['auth_nntpdescription'] = 'This method uses an NNTP server to check whether a given username and password is valid.';
$string['auth_nntphost'] = 'The NNTP server address. Use the IP number, not DNS name.';
$string['auth_nntpport'] = 'Server port (119 is the most common)';
$string['auth_nntptitle'] = 'NNTP server';
$string['auth_nntpnotinstalled'] = 'Cannot use NNTP authentication. The PHP IMAP module is not installed.';
$string['auth_nntpchangepasswordurl_key'] = 'Password-change URL';
$string['auth_nntpport_key'] = 'Port';
$string['auth_nntphost_key'] = 'Host';

// None plugin
$string['auth_nonedescription'] = 'Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.';
$string['auth_nonetitle'] = 'No authentication';

// PAM plugin
$string['auth_pamdescription'] = 'This method uses PAM to access the native usernames on this server. You have to install <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\">PHP4 PAM Authentication</a> in order to use this module.';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';

$string['auth_passwordisexpired'] = 'Your password is expired. Do you want change your password now?';
$string['auth_passwordwillexpire'] = 'Your password will expire in $a days. Do you want change your password now?';

// POP3 plugin
$string['auth_pop3description'] = 'This method uses a POP3 server to check whether a given username and password is valid.';
$string['auth_pop3host'] = 'The POP3 server address. Use the IP number, not DNS name.';
$string['auth_pop3mailbox'] = 'Name of the mailbox to attempt a connection with.  (usually INBOX)';
$string['auth_pop3port'] = 'Server port (110 is the most common, 995 is common for SSL)';
$string['auth_pop3title'] = 'POP3 server';
$string['auth_pop3type'] = 'Server type. If your server uses certificate security, choose pop3cert.';
$string['auth_pop3notinstalled'] = 'Cannot use POP3 authentication. The PHP IMAP module is not installed.';
$string['auth_pop3changepasswordurl_key'] = 'Password-change URL';
$string['auth_pop3mailbox_key'] = 'Mailbox';
$string['auth_pop3port_key'] = 'Port';
$string['auth_pop3type_key'] = 'Type';
$string['auth_pop3host_key'] = 'Host';

// RADIUS plugin
$string['auth_radiustitle'] = 'RADIUS server';
$string['auth_radiusdescription'] = 'This method uses a <a href=\"http://en.wikipedia.org/wiki/RADIUS\">RADIUS</a> server to check whether a given username and password is valid.';
$string['auth_radiushost'] = 'Address of the RADIUS server';
$string['auth_radiusnasport'] = 'Port to use to connect';
$string['auth_radiussecret'] = 'Shared secret';
$string['auth_radiustype'] = 'Choose an authentication scheme to use with the RADIUS server.';
$string['auth_radiustypepap'] = 'PAP';
$string['auth_radiustypechapmd5'] = 'CHAP MD5';
$string['auth_radiustypemschapv1'] = 'Microsoft CHAP version 1';
$string['auth_radiustypemschapv2'] = 'Microsoft CHAP version 2';
$string['auth_radiuschangepasswordurl_key'] = 'Password-change URL';
$string['auth_radiusnasport_key'] = 'Port';
$string['auth_radiushost_key'] = 'Host';
$string['auth_radiussecret_key'] = 'Secret';
$string['auth_radiustype_key'] = 'Authentication';

// Shibboleth plugin
$string['auth_shibbolethdescription'] = 'Using this method users are created and authenticated using <a href=\"http://shibboleth.internet2.edu/\">Shibboleth</a>.<br />Be sure to read the <a href=\"../auth/shibboleth/README.txt\">README</a> for Shibboleth on how to set up your Moodle with Shibboleth';
$string['auth_shibbolethtitle'] = 'Shibboleth';
$string['auth_shibboleth_login'] = 'Shibboleth Login';
$string['auth_shibboleth_manual_login'] = 'Manual Login';
$string['auth_shib_only'] = 'Shibboleth only';
$string['auth_shib_only_description'] = 'Check this option if a Shibboleth authentication shall be enforced';
$string['auth_shib_username_description'] = 'Name of the webserver Shibboleth environment variable that shall be used as Moodle username';
$string['auth_shib_instructions'] = 'Use the <a href=\"$a\">Shibboleth login</a> to get access via Shibboleth, if your institution supports it.<br />Otherwise, use the normal login form shown here.';
$string['auth_shib_convert_data'] = 'Data modification API';
$string['auth_shib_convert_data_description'] = 'You can use this API to further modify the data provided by Shibboleth. Read the <a href=\"../auth/shibboleth/README.txt\">README</a> for further instructions.';
$string['auth_shib_instructions_help'] = 'Here you should provide custom instructions for your users to explain Shibboleth.  It will be shown on the login page in the instructions section. The instructions must include a link to \"<b>$a</b>\" that users click when they want to log in.';
$string['auth_shib_convert_data_warning'] = 'The file does not exist or is not readable by the webserver process!';
$string['auth_shib_changepasswordurl'] = 'Password-change URL';
$string['auth_shibboleth_login_long'] = 'Login to Moodle via Shibboleth';
$string['auth_shibboleth_select_organization'] = 'For authentication via Shibboleth, please select your organization from the drop down list:';
$string['auth_shibboleth_contact_administrator'] = 'In case you are not associated with the given organizations and you need access to a course on this server, please contact the';
$string['auth_shibboleth_select_member'] = 'I\'m a member of ...';
$string['auth_shibboleth_errormsg'] ='Please select the organization you are member of!';
$string['auth_shib_no_organizations_warning'] ='If you want to use the integrated WAYF service, you must provide a coma-separated list of Identity Provider entityIDs, their names and optionally a session initiator.';
$string['shib_not_set_up_error'] = 'Shibboleth authentication doesn\'t seem to be set up correctly because no Shibboleth environment variables are present for this page. Please consult the <a href=\"README.txt\">README</a> for further instructions on how to set up Shibboleth authentication or contact the webmaster of this Moodle installation.';
$string['shib_no_attributes_error'] = 'You seem to be Shibboleth authenticated but Moodle didn\'t receive any user attributes. Please check that your Identity Provider releases the necessary attributes ($a) to the Service Provider Moodle is running on or inform the webmaster of this server.';
$string['shib_not_all_attributes_error'] = 'Moodle needs certain Shibboleth attributes which are not present in your case. The attributes are: $a<br />Please contact the webmaster of this server or your Identity Provider.';
$string['auth_shib_integrated_wayf'] = 'Moodle WAYF Service';
$string['auth_shib_integrated_wayf_description'] = 'If you check this, Moodle will use its own WAYF service instead of the one configured for Shibboleth. Moodle will display a drop-down list on this alternative login page where the user has to select his Identity Provider.';
$string['auth_shib_idp_list'] = 'Identity Providers';
$string['auth_shib_idp_list_description'] = 'Provide a list of Identity Provider entityIDs to let the user choose from on the login page.<br />On each line there must be a comma-separated tuple for entityID of the IdP (see the Shibboleth metadata file) and Name of IdP as it shall be displayed in the drop-down list.<br />As an optional third parameter you can add the location of a Shibboleth session initiator that shall be used in case your Moodle installation is part of a multi federation setup.';
$string['auth_shib_logout_url'] = 'Shibboleth Service Provider logout handler URL';
$string['auth_shib_logout_url_description'] = 'Provide the URL to the Shibboleth Service Provider logout handler. This typically is <tt>/Shibboleth.sso/Logout</tt>';
$string['auth_shib_auth_method'] = 'Authentication Method Name';
$string['auth_shib_auth_method_description'] = 'Provide a name for the Shibboleth authentication method that is familiar to your users. This could be the name of your Shibboleth federation, e.g. <tt>SWITCHaai Login</tt> or <tt>InCommon Login</tt> or similar.';
$string['auth_shib_logout_return_url'] = 'Alternative logout return URL';
$string['auth_shib_logout_return_url_description'] = 'Provide the URL that Shibboleth users shall be redirected to after logging out.<br />If left empty, users will be redirected to the location that moodle will redirect users to';



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
$string['informminpasswordlength'] = 'at least $a characters';
$string['informminpassworddigits'] = 'at least $a digit(s)';
$string['informminpasswordlower'] = 'at least $a lower case letter(s)';
$string['informminpasswordnonalphanum'] = 'at least $a non-alphanumeric character(s)';
$string['informminpasswordupper'] = 'at least $a upper case letter(s)';
$string['informpasswordpolicy'] = 'Your password must have $a';
?>
