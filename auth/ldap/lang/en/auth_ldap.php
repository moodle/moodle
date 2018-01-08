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
 * Strings for component 'auth_ldap', language 'en'.
 *
 * @package   auth_ldap
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_ldap_ad_create_req'] = 'Cannot create the new account in Active Directory. Make sure you meet all the requirements for this to work (LDAPS connection, bind user with adequate rights, etc.)';
$string['auth_ldap_attrcreators'] = 'List of groups or contexts whose members are allowed to create attributes. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_attrcreators_key'] = 'Attribute creators';
$string['auth_ldap_auth_user_create_key'] = 'Create users externally';
$string['auth_ldap_bind_dn'] = 'If you want to use bind-user to search users, specify it here. Something like \'cn=ldapuser,ou=public,o=org\'';
$string['auth_ldap_bind_dn_key'] = 'Distinguished name';
$string['auth_ldap_bind_pw'] = 'Password for bind-user.';
$string['auth_ldap_bind_pw_key'] = 'Password';
$string['auth_ldap_bind_settings'] = 'Bind settings';
$string['auth_ldap_contexts'] = 'List of contexts where users are located. Separate different contexts with \';\'. For example: \'ou=users,o=org; ou=others,o=org\'';
$string['auth_ldap_contexts_key'] = 'Contexts';
$string['auth_ldap_create_context'] = 'If you enable user creation with email confirmation, specify the context where users are created. This context should be different from other users to prevent security issues. You don\'t need to add this context to ldap_context-variable, Moodle will search for users from this context automatically.<br /><b>Note!</b> You have to modify the method user_create() in file auth/ldap/auth.php to make user creation work';
$string['auth_ldap_create_context_key'] = 'Context for new users';
$string['auth_ldap_create_error'] = 'Error creating user in LDAP.';
$string['auth_ldapdescription'] = 'This method provides authentication against an external LDAP server.
                                  If the given username and password are valid, Moodle creates a new user
                                  entry in its database. This module can read user attributes from LDAP and prefill
                                  wanted fields in Moodle.  For following logins only the username and
                                  password are checked.';
$string['auth_ldap_expiration_desc'] = 'Select \'{$a->no}\' to disable expired password checking or \'{$a->ldapserver}\' to read the password expiry time directly from the LDAP server.';
$string['auth_ldap_expiration_key'] = 'Expiry';
$string['auth_ldap_expiration_warning_desc'] = 'Number of days before password expiry warning is issued.';
$string['auth_ldap_expiration_warning_key'] = 'Expiry warning';
$string['auth_ldap_expireattr_desc'] = 'Optional: Overrides the LDAP attribute that stores password expiry time.';
$string['auth_ldap_expireattr_key'] = 'Expiry attribute';
$string['auth_ldapextrafields'] = 'These fields are optional.  You can choose to pre-fill some Moodle user fields with information from the <b>LDAP fields</b> that you specify here. <p>If you leave these fields blank, then nothing will be transferred from LDAP and Moodle defaults will be used instead.</p><p>In either case, the user will be able to edit all of these fields after they log in.</p>';
$string['auth_ldap_graceattr_desc'] = 'Optional: Overrides  gracelogin attribute';
$string['auth_ldap_gracelogin_key'] = 'Grace login attribute';
$string['auth_ldap_gracelogins_desc'] = 'Enable LDAP gracelogin support. After password has expired user can login until gracelogin count is 0. Enabling this setting displays grace login message if password is expired.';
$string['auth_ldap_gracelogins_key'] = 'Grace logins';
$string['auth_ldap_groupecreators'] = 'List of groups or contexts whose members are allowed to create groups. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_groupecreators_key'] = 'Group creators';
$string['auth_ldap_host_url'] = 'Specify LDAP host in URL-form like \'ldap://ldap.myorg.com/\' or \'ldaps://ldap.myorg.com/\'. Separate multiple servers with \';\' to get failover support.';
$string['auth_ldap_host_url_key'] = 'Host URL';
$string['auth_ldap_changepasswordurl_key'] = 'Password-change URL';
$string['auth_ldap_ldap_encoding'] = 'Specify encoding used by LDAP server. Most probably utf-8, MS AD v2 uses default platform encoding such as cp1252, cp1250, etc.';
$string['auth_ldap_ldap_encoding_key'] = 'LDAP encoding';
$string['auth_ldap_login_settings'] = 'Login settings';
$string['auth_ldap_memberattribute'] = 'Optional: Overrides user member attribute, when users belongs to a group. Usually \'member\'';
$string['auth_ldap_memberattribute_isdn'] = 'Optional: Overrides handling of member attribute values, either 0 or 1';
$string['auth_ldap_memberattribute_isdn_key'] = 'Member attribute uses dn';
$string['auth_ldap_memberattribute_key'] = 'Member attribute';
$string['auth_ldap_noconnect'] = 'LDAP-module cannot connect to server: {$a}';
$string['auth_ldap_noconnect_all'] = 'LDAP-module cannot connect to any servers: {$a}';
$string['auth_ldap_noextension'] = '<em>The PHP LDAP module does not seem to be present. Please ensure it is installed and enabled if you want to use this authentication plugin.</em>';
$string['auth_ldap_no_mbstring'] = 'You need the mbstring extension to create users in Active Directory.';
$string['auth_ldapnotinstalled'] = 'Cannot use LDAP authentication. The PHP LDAP module is not installed.';
$string['auth_ldap_objectclass'] = 'Optional: Overrides objectClass used to name/search users on ldap_user_type. Usually you don\'t need to change this.';
$string['auth_ldap_objectclass_key'] = 'Object class';
$string['auth_ldap_opt_deref'] = 'Determines how aliases are handled during search. Select one of the following values: "No" (LDAP_DEREF_NEVER) or "Yes" (LDAP_DEREF_ALWAYS)';
$string['auth_ldap_opt_deref_key'] = 'Dereference aliases';
$string['auth_ldap_passtype'] = 'Specify the format of new or changed passwords in LDAP server.';
$string['auth_ldap_passtype_key'] = 'Password format';
$string['auth_ldap_passwdexpire_settings'] = 'LDAP password expiry settings';
$string['auth_ldap_preventpassindb'] = 'Select yes to prevent passwords from being stored in Moodle\'s DB.';
$string['auth_ldap_preventpassindb_key'] = 'Prevent password caching';
$string['auth_ldap_rolecontext'] = '{$a->localname} context';
$string['auth_ldap_rolecontext_help'] = 'LDAP context used to select for <i>{$a->localname}</i> mapping. Separate multiple groups with \';\'. Usually something like "cn={$a->shortname},ou=staff,o=myorg".';
$string['auth_ldap_search_sub'] = 'Search users from subcontexts.';
$string['auth_ldap_search_sub_key'] = 'Search subcontexts';
$string['auth_ldap_server_settings'] = 'LDAP server settings';
$string['auth_ldap_unsupportedusertype'] = 'auth: ldap user_create() does not support selected usertype: {$a}';
$string['auth_ldap_update_userinfo'] = 'Update user information (firstname, lastname, address..) from LDAP to Moodle.  Specify "Data mapping" settings as you need.';
$string['auth_ldap_user_attribute'] = 'Optional: Overrides the attribute used to name/search users. Usually \'cn\'.';
$string['auth_ldap_user_attribute_key'] = 'User attribute';
$string['auth_ldap_suspended_attribute'] = 'Optional: When provided this attribute will be used to enable/suspend the locally created user account.';
$string['auth_ldap_suspended_attribute_key'] = 'Suspended attribute';
$string['auth_ldap_user_exists'] = 'LDAP username already exists.';
$string['auth_ldap_user_settings'] = 'User lookup settings';
$string['auth_ldap_user_type'] = 'Select how users are stored in LDAP. This setting also specifies how login expiry, grace logins and user creation will work.';
$string['auth_ldap_user_type_key'] = 'User type';
$string['auth_ldap_usertypeundefined'] = 'config.user_type not defined or function ldap_expirationtime2unix does not support selected type!';
$string['auth_ldap_usertypeundefined2'] = 'config.user_type not defined or function ldap_unixi2expirationtime does not support selected type!';
$string['auth_ldap_version'] = 'The version of the LDAP protocol your server is using.';
$string['auth_ldap_version_key'] = 'Version';
$string['auth_ntlmsso'] = 'NTLM SSO';
$string['auth_ntlmsso_enabled'] = 'Set to yes to attempt Single Sign On with the NTLM domain. <strong>Note:</strong> this requires additional setup on the webserver to work, see <a href="http://docs.moodle.org/en/NTLM_authentication">http://docs.moodle.org/en/NTLM_authentication</a>';
$string['auth_ntlmsso_enabled_key'] = 'Enable';
$string['auth_ntlmsso_ie_fastpath'] = 'Set to enable the NTLM SSO fast path (bypasses certain steps if the client\'s browser is MS Internet Explorer).';
$string['auth_ntlmsso_ie_fastpath_key'] = 'MS IE fast path?';
$string['auth_ntlmsso_ie_fastpath_yesform'] = 'Yes, all other browsers use standard login form';
$string['auth_ntlmsso_ie_fastpath_yesattempt'] = 'Yes, attempt NTLM other browsers';
$string['auth_ntlmsso_ie_fastpath_attempt'] = 'Attempt NTLM with all browsers';
$string['auth_ntlmsso_maybeinvalidformat'] = 'Unable to extract the username from the REMOTE_USER header. Is the configured format right?';
$string['auth_ntlmsso_missing_username'] = 'You need to specify at least %username% in the remote username format';
$string['auth_ntlmsso_remoteuserformat_key'] = 'Remote username format';
$string['auth_ntlmsso_remoteuserformat'] = 'If you have chosen \'NTLM\' in \'Authentication type\', you can specify the remote username format here. If you leave this empty, the default DOMAIN\\username format will be used. You can use the optional <b>%domain%</b> placeholder to specify where the domain name appears, and the mandatory <b>%username%</b> placeholder to specify where the username appears. <br /><br />Some widely used formats are <tt>%domain%\\%username%</tt> (MS Windows default), <tt>%domain%/%username%</tt>, <tt>%domain%+%username%</tt> and just <tt>%username%</tt> (if there is no domain part).';
$string['auth_ntlmsso_subnet'] = 'If set, it will only attempt SSO with clients in this subnet. Format: xxx.xxx.xxx.xxx/bitmask. Separate multiple subnets with \',\' (comma).';
$string['auth_ntlmsso_subnet_key'] = 'Subnet';
$string['auth_ntlmsso_type_key'] = 'Authentication type';
$string['auth_ntlmsso_type'] = 'The authentication method configured in the web server to authenticate the users (if in doubt, choose NTLM)';
$string['connectingldap'] = "Connecting to LDAP server...\n";
$string['connectingldapsuccess'] = "Connecting to your LDAP server was successful";
$string['creatingtemptable'] = "Creating temporary table {\$a}\n";
$string['didntfindexpiretime'] = 'password_expire() didn\'t find expiration time.';
$string['didntgetusersfromldap'] = "Did not get any users from LDAP -- error? -- exiting\n";
$string['gotcountrecordsfromldap'] = "Got {\$a} records from LDAP\n";
$string['ldapnotconfigured'] = 'The LDAP host url is currently not configured';
$string['morethanoneuser'] = 'Strange! More than one user record found in ldap. Only using the first one.';
$string['needbcmath'] = 'You need the BCMath extension to use expired password checking with Active Directory.';
$string['needmbstring'] = 'You need the mbstring extension to change passwords in Active Directory';
$string['nodnforusername'] = 'Error in user_update_password(). No DN for: {$a->username}';
$string['noemail'] = 'Tried to send you an email but failed!';
$string['notcalledfromserver'] = 'Should not be called from the web server!';
$string['noupdatestobedone'] = "No updates to be done\n";
$string['nouserentriestoremove'] = "No user entries to be removed\n";
$string['nouserentriestorevive'] = "No user entries to be revived\n";
$string['nouserstobeadded'] = 'No user entries to be added';
$string['ntlmsso_attempting'] = 'Attempting Single Sign On via NTLM...';
$string['ntlmsso_failed'] = 'Auto-login failed, try the normal login page...';
$string['ntlmsso_isdisabled'] = 'NTLM SSO is disabled.';
$string['ntlmsso_unknowntype'] = 'Unknown ntlmsso type!';
$string['pagedresultsnotsupp'] = 'LDAP paged results not supported (either your PHP version lacks support, you have configured Moodle to use LDAP protocol version 2 or Moodle cannot contact your LDAP server to see if paged support is available.)';
$string['pagesize'] = 'Make sure this value is smaller than your LDAP server result set size limit (the maximum number of entries that can be returned in a single query)';
$string['pagesize_key'] = 'Page size';
$string['pluginname'] = 'LDAP server';
$string['pluginnotenabled'] = 'Plugin not enabled!';
$string['renamingnotallowed'] = 'User renaming not allowed in LDAP';
$string['rootdseerror'] = 'Error querying rootDSE for Active Directory';
$string['syncroles'] = 'Synchronise system roles from LDAP';
$string['synctask'] = 'LDAP users sync job';
$string['systemrolemapping'] = 'System role mapping';
$string['start_tls'] = 'Use regular LDAP service (port 389) with TLS encryption';
$string['start_tls_key'] = 'Use TLS';
$string['updateremfail'] = 'Error updating LDAP record. Error code: {$a->errno}; Error string: {$a->errstring}<br/>Key ({$a->key}) - old moodle value: \'{$a->ouvalue}\' new value: \'{$a->nuvalue}\'';
$string['updateremfailamb'] = 'Failed to update LDAP with ambiguous field {$a->key}; old moodle value: \'{$a->ouvalue}\', new value: \'{$a->nuvalue}\'';
$string['updatepasserror'] = 'Error in user_update_password(). Error code: {$a->errno}; Error string: {$a->errstring}';
$string['updatepasserrorexpire'] = 'Error in user_update_password() when reading password expiry time. Error code: {$a->errno}; Error string: {$a->errstring}';
$string['updatepasserrorexpiregrace'] = 'Error in user_update_password() when modifying expirationtime and/or gracelogins. Error code: {$a->errno}; Error string: {$a->errstring}';
$string['updateusernotfound'] = 'Could not find user while updating externally. Details follow: search base: \'{$a->userdn}\'; search filter: \'(objectClass=*)\'; search attributes: {$a->attribs}';
$string['user_activatenotsupportusertype'] = 'auth: ldap user_activate() does not support selected usertype: {$a}';
$string['user_disablenotsupportusertype'] = 'auth: ldap user_disable() does not support selected usertype: {$a}';
$string['userentriestoadd'] = "User entries to be added: {\$a}\n";
$string['userentriestoremove'] = "User entries to be removed: {\$a}\n";
$string['userentriestorevive'] = "User entries to be revived: {\$a}\n";
$string['userentriestoupdate'] = "User entries to be updated: {\$a}\n";
$string['usernotfound'] = 'User not found in LDAP';
$string['useracctctrlerror'] = 'Error getting userAccountControl for {$a}';

// Deprecated since Moodle 3.4.
$string['auth_ldap_creators'] = 'List of groups or contexts whose members are allowed to create new courses. Separate multiple groups with \';\'. Usually something like \'cn=teachers,ou=staff,o=myorg\'';
$string['auth_ldap_creators_key'] = 'Creators';
