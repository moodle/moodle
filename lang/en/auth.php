<?PHP // $Id$ 
      // auth.php - created with Moodle 1.0.6.4 beta (2002112001)


$string['auth_dbdescription'] = "This method uses an external database table to check whether a given username and password is valid.  If the account is a new one, then information from other fields may also be copied across into Moodle.";
$string['auth_dbextrafields'] = "These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <B>external database fields</B> that you specify here. <P>If you leave these blank, then defaults will be used.<P>In either case, the user will be able to edit all of these fields after they log in.";
$string['auth_dbfieldpass'] = "Name of the field containing passwords";
$string['auth_dbfielduser'] = "Name of the field containing usernames";
$string['auth_dbhost'] = "The computer hosting the database server.";
$string['auth_dbname'] = "Name of the database itself";
$string['auth_dbpass'] = "Password matching the above username";
$string['auth_dbpasstype'] = "Specify the format that the password field is using.  MD5 encryption is useful for connecting to other common web applications like PostNuke";
$string['auth_dbtable'] = "Name of the table in the database";
$string['auth_dbtitle'] = "Use an external database";
$string['auth_dbtype'] = "The database type (See the <A HREF=../lib/adodb/readme.htm#drivers>ADOdb documentation</A> for details)";
$string['auth_dbuser'] = "Username with read access to the database";
$string['auth_emaildescription'] = "Email confirmation is the default authentication method.  When the user signs up, choosing their own new username and password, a confirmation email is sent to the user's email address.  This email contains a secure link to a page where the user can confirm their account. Future logins just check the username and password against the stored values in the Moodle database.";
$string['auth_emailtitle'] = "Email-based authentication";
$string['auth_imapdescription'] = "This method uses an IMAP server to check whether a given username and password is valid.";
$string['auth_imaphost'] = "The IMAP server address. Use the IP number, not DNS name.";
$string['auth_imapport'] = "IMAP server port number. Usually this is 143 or 993.";
$string['auth_imaptitle'] = "Use an IMAP server";
$string['auth_imaptype'] = "The IMAP server type.  IMAP servers can have different types of authentication and negotiation.";
$string['instructions'] = "Instructions";
$string['auth_ldap_bind_dn'] = "If you want to use bind-user to search users, specify it here. Someting like 'cn=ldapuser,ou=public,o=org'";
$string['auth_ldap_bind_pw'] = "Password for bind-user.";
$string['auth_ldap_contexts'] = "List of contexts where users are located. Separate different contexts with ';'. For example: 'ou=users,o=org; ou=others,o=org'";
$string['auth_ldap_host_url'] = "Specify LDAP host in URL-form like 'ldap://ldap.myorg.com/' or 'ldaps://ldap.myorg.com/' ";
$string['auth_ldap_search_sub'] = "Put value &lt;&gt; 0 if  you like to search users from subcontexts.";
$string['auth_ldap_update_userinfo'] = "Update user information (firstname, lastname, address..) from LDAP to Moodle. Look at /auth/ldap/attr_mappings.php for mapping information";
$string['auth_ldap_user_attribute'] = "The attribute used to name/search users. Usually 'cn'.";
$string['auth_ldapdescription'] = "This method provides authentication against an external LDAP server.
                                  If the given username and password are valid, Moodle creates a new user 
                                  entry in its database. This module can read user attributes from LDAP and prefill 
                                  wanted fields in Moodle.  For following logins only the username and 
                                  password are checked.";
$string['auth_ldapextrafields'] = "These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <B>LDAP fields</B> that you specify here. <P>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.<P>In either case, the user will be able to edit all of these fields after they log in.";
$string['auth_ldaptitle'] = "Use an LDAP server";
$string['auth_nntpdescription'] = "This method uses an NNTP server to check whether a given username and password is valid.";
$string['auth_nntphost'] = "The NNTP server address. Use the IP number, not DNS name.";
$string['auth_nntpport'] = "Server port (119 is the most common)";
$string['auth_nntptitle'] = "Use an NNTP server";
$string['auth_nonedescription'] = "Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.";
$string['auth_nonetitle'] = "No authentication";
$string['auth_pop3description'] = "This method uses a POP3 server to check whether a given username and password is valid.";
$string['auth_pop3host'] = "The POP3 server address. Use the IP number, not DNS name.";
$string['auth_pop3port'] = "Server port (110 is the most common)";
$string['auth_pop3title'] = "Use a POP3 server";
$string['auth_pop3type'] = "Server type. If your server uses certificate security, choose pop3cert.";
$string['authenticationoptions'] = "Authentication options";
$string['authinstructions'] = "Here you can provide instructions for your users, so they know which username and password they should be using.  The text you enter here will appear on the login page.  If you leave this blank then no instructions will be printed.";
$string['changepassword'] = "Change password URL";
$string['changepasswordhelp'] = "Here you can specify a location at which your users can recover or change their username/password if they've forgotten it.  This will be provided to users as a button on the login page and their user page.  if you leave this blank the button will not be printed.";
$string['chooseauthmethod'] = "Choose an authentication method: ";
$string['guestloginbutton'] = "Guest login button";
$string['md5'] = "MD5 encryption";
$string['plaintext'] = "Plain text";
$string['showguestlogin'] = "You can hide or show the guest login button on the login page.";

?>
