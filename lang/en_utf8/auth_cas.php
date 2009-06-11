<?php 
// CAS plugin
$string['auth_cas_proxycas_key'] = "Proxy mode";
$string['auth_cas_logoutcas_key'] = "Logout CAS";
$string['auth_cas_multiauth_key'] = "Multi-authentication";
$string['auth_cas_proxycas'] = 'Turn this to \'yes\' if you use CASin proxy-mode';
$string['auth_cas_logoutcas'] = 'Turn this to \'yes\' if tou want to logout from CAS when you deconnect from Moodle';
$string['auth_cas_multiauth'] = 'Turn this to \'yes\' if you want to have multi-authentication (CAS + other authentication)';
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
?>