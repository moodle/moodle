<?php // $Id$ 
      // auth.php - created with Moodle 1.2 development (2003111400)


$string['auth_dbdescription'] = 'This method uses an external database table to check whether a given username and password is valid.  If the account is a new one, then information from other fields may also be copied across into Moodle.';
$string['auth_dbextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <b>external database fields</b> that you specify here. <p>If you leave these blank, then defaults will be used.<p>In either case, the user will be able to edit all of these fields after they log in.';
$string['auth_dbfieldpass'] = 'Name of the field containing passwords';
$string['auth_dbfielduser'] = 'Name of the field containing usernames';
$string['auth_dbhost'] = 'The computer hosting the database server.';
$string['auth_dbname'] = 'Name of the database itself';
$string['auth_dbpass'] = 'Password matching the above username';
$string['auth_dbpasstype'] = 'Specify the format that the password field is using.  MD5 encryption is useful for connecting to other common web applications like PostNuke';
$string['auth_dbtable'] = 'Name of the table in the database';
$string['auth_dbtitle'] = 'Use an external database';
$string['auth_dbtype'] = 'The database type (See the <a href=\"../lib/adodb/readme.htm#drivers\">ADOdb documentation</a> for details)';
$string['auth_dbuser'] = 'Username with read access to the database';
$string['auth_editlock'] = 'Lock value';
$string['auth_editlock_expl'] = '<p><b>Lock value:</b> If enabled, will prevent Moodle users and admins from editing the field directly. Use this option if you are maintaining this data in the external auth system. </p>';
$string['auth_emaildescription'] = 'Email confirmation is the default authentication method.  When the user signs up, choosing their own new username and password, a confirmation email is sent to the user\'s email address.  This email contains a secure link to a page where the user can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.';
$string['auth_emailtitle'] = 'Email-based authentication';
$string['auth_fctitle'] = 'Use a FirstClass server';
$string['auth_fcdescription'] = 'This method uses a FisrtClass server to check whether a given username and password is valid.';
$string['auth_fchost'] = 'The FirstClass server address. Use the IP number or DNS name.';
$string['auth_fcfppport'] = 'Server port (3333 is the most common)';
$string['auth_fcuserid'] = "Userid for FirstClass account with privilege 'Subadministrator' set.";
$string['auth_fcpasswd'] = 'Password for the account above.';
$string['auth_fccreators'] = "List of groups whose members are allowed to create new courses. Separate multiple groups with ';'. Names must be spelled exactly as on FirstClass server. System is case-sensitive.";
$string['auth_imapdescription'] = 'This method uses an IMAP server to check whether a given username and password is valid.';
$string['auth_imaphost'] = 'The IMAP server address. Use the IP number, not DNS name.';
$string['auth_imapport'] = 'IMAP server port number. Usually this is 143 or 993.';
$string['auth_imaptitle'] = 'Use an IMAP server';
$string['auth_imaptype'] = 'The IMAP server type.  IMAP servers can have different types of authentication and negotiation.';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP password expiration settings.';
$string['auth_ldap_expiration_desc'] = 'Select No to disable expired password checking or LDAP to read passwordexpiration time directly from LDAP.Remeber to fill attribute name ldap_expireattr';
$string['auth_ldap_expireattr_desc'] = 'Ldap-attribute what stores password expiration time asswordAxpirationTime';
$string['auth_ldap_gracelogins_desc'] = 'Enable LDAP gracelogin support. After password has expired user can login until gracelogin count is 0. Enabling this setting displays grace login message if password is exprired.';
$string['auth_ldap_graceattr_desc'] = 'LDAP gracelogin attribute';
$string['auth_ldap_server_settings'] = 'LDAP server settings';
$string['auth_ldap_bind_settings'] = 'Bind settings';
$string['auth_ldap_user_settings'] = 'User lookup settings';
$string['auth_ldap_login_settings'] = 'Login settings';
$string['auth_common_settings'] = 'Common settings';
$string['auth_data_mapping'] = 'Data mapping';
$string['auth_ldap_bind_dn'] = 'If you want to use bind-user to search users, specify it here. Someting like \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_pw'] = 'Password for bind-user.';
$string['auth_ldap_contexts'] = 'List of contexts where users are located. Separate different contexts with \';\'. For example: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_create_context'] = 'If you enable user creation with email confirmation, specify the context where users are created. This context should be different from other users to prevent security issues. You don\'t need to add this context to ldap_context-variable, Moodle will search for users from this context automatically.<br/><b>Note!</b> You have to modify function auth_user_create() in file auth/ldap/lib.phpi to make usercreation work';
$string['auth_ldap_creators'] = 'List of groups whose members are allowed to create new courses. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_host_url'] = 'Specify LDAP host in URL-form like \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\' Separate multipleservers with \';\' to get failover support. ';
$string['auth_ldap_memberattribute'] = 'Specify user member attribute, when users belongs to a group. Usually \'member\'';
$string['auth_ldap_search_sub'] = 'Put value &lt;&gt; 0 if  you like to search users from subcontexts.';
$string['auth_ldap_update_userinfo'] = 'Update user information (firstname, lastname, address..) from LDAP to Moodle. Look at /auth/ldap/attr_mappings.php for mapping information';
$string['auth_ldap_user_attribute'] = 'The attribute used to name/search users. Usually \'cn\'.';
$string['auth_ldap_objectclass'] = 'The filter used to name/search users. Usually you will set it to something like objectClass=posixAccount . Defaults to objectClass=* what will return all objects from LDAP.';
$string['auth_ldap_version'] = 'The version of the LDAP protocol your server is using.';
$string['auth_ldapdescription'] = 'This method provides authentication against an external LDAP server.

                                  If the given username and password are valid, Moodle creates a new user 

                                  entry in its database. This module can read user attributes from LDAP and prefill 

                                  wanted fields in Moodle.  For following logins only the username and 

                                  password are checked.';
$string['auth_ldapextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <b>LDAP fields</b> that you specify here. <p>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.<p>In either case, the user will be able to edit all of these fields after they log in.';
$string['auth_ldaptitle'] = 'Use an LDAP server';
$string['auth_manualdescription'] = 'This method removes any way for users to create their own accounts.  All accounts must be manually created by the admin user.';
$string['auth_manualtitle'] = 'Manual accounts only';
$string['auth_multiplehosts'] = 'Multiple hosts OR addresses can be specified (eg host1.com;host2.com;host3.com) or (eg xxx.xxx.xxx.xxx;xxx.xxx.xxx.xxx)';
$string['auth_nntpdescription'] = 'This method uses an NNTP server to check whether a given username and password is valid.';
$string['auth_nntphost'] = 'The NNTP server address. Use the IP number, not DNS name.';
$string['auth_nntpport'] = 'Server port (119 is the most common)';
$string['auth_nntptitle'] = 'Use an NNTP server';
$string['auth_nonedescription'] = 'Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.';
$string['auth_nonetitle'] = 'No authentication';
$string['auth_pamtitle'] = 'PAM (Pluggable Authentication Modules)';
$string['auth_pamdescription'] = 'This method uses PAM to access the native usernames on this server. You have to install <a href=\"http://www.math.ohio-state.edu/~ccunning/pam_auth/\" target=\"_blank\">PHP4 PAM Authentication</a> in order to use this module.';
$string['auth_pop3description'] = 'This method uses a POP3 server to check whether a given username and password is valid.';
$string['auth_pop3host'] = 'The POP3 server address. Use the IP number, not DNS name.';
$string['auth_pop3mailbox'] = 'Name of the mailbox to attempt a connection with.  (usually INBOX)';
$string['auth_pop3port'] = 'Server port (110 is the most common, 995 is common for SSL)';
$string['auth_pop3title'] = 'Use a POP3 server';
$string['auth_pop3type'] = 'Server type. If your server uses certificate security, choose pop3cert.';
$string['auth_user_create'] = 'Enable user creation';
$string['auth_user_creation'] = 'New (anonymous) users can create user accounts on the external authentication source and confirmed via email. If you enable this , remember to also configure module-specific options for user creation.';
$string['auth_usernameexists'] = 'Selected username already exists. Please choose a new one.';
$string['auth_updatelocal'] = 'Update local data';
$string['auth_updatelocal_expl'] = '<p><b>Update local data:</b> If enabled, the field will be updated (from external auth) every time the user logs in or there is a user synchronization. Fields set to update locally should be locked.</p>';
$string['auth_updateremote'] = 'Update external data';
$string['auth_updateremote_expl'] = '<p><b>Update external data:</b> If enabled, the external auth will be updated when the user record is updated. Fields should be unlocked to allow edits.</p>';
$string['auth_updateremote_ldap'] = '<p><b>Note:</b> Updating external LDAP data requires that you set binddn and bindpw to a bind-user with editing privileges to all the user records. It currently does not preserve multi-valued attributes, and will remove extra values on update. </p>';
$string['authenticationoptions'] = 'Authentication options';
$string['authinstructions'] = 'Here you can provide instructions for your users, so they know which username and password they should be using.  The text you enter here will appear on the login page.  If you leave this blank then no instructions will be printed.';
$string['changepassword'] = 'Change password URL';
$string['changepasswordhelp'] = 'Here you can specify a location at which your users can recover or change their username/password if they\'ve forgotten it.  This will be provided to users as a button on the login page and their user page.  if you leave this blank the button will not be printed.';
$string['chooseauthmethod'] = 'Choose an authentication method: ';
$string['instructions'] = 'Instructions';
$string['md5'] = 'MD5 encryption';
$string['plaintext'] = 'Plain text';
$string['showguestlogin'] = 'You can hide or show the guest login button on the login page.';
$string['stdchangepassword'] = 'Use standard Change Password Page';
$string['stdchangepassword_expl'] = 'If the external authentication system allows password changes through Moodle, switch this to Yes. This setting overrides \'Change Password URL\'.';
$string['stdchangepassword_explldap'] = 'NOTE: It is recommended that you use LDAP over an SSL encrypted tunnel (ldaps://) if the LDAP server is remote.';
$string['forcechangepassword'] = 'Force change password';
$string['forcechangepassword_help'] = 'Force users to change password on their next login to Moodle.';$string['guestloginbutton'] = 'Guest login button';
$string['forcechangepasswordfirst_help'] = 'Force users to change password on their first login to Moodle.';$string['guestloginbutton'] = 'Guest login button';

?>
