<?PHP
// $Id: lib.php
// author: romualdLorthioir $
//CHANGELOG:
//05.02.2005 Added CAS module

/* README!
CAS Module
This Module can be turn ON/OFF on admin screen.
The /login module have to be changed to.
This module is using the LDAP Module so you need the /auth/ldap directory.



*/

define("AUTH_METHOD", 'cas');
require_once($CFG->dirroot.'/auth/cas/commonlib.php');


function auth_user_login ($username, $password) {
/// Returns true if the username and password work
/// and false if they don't

    global $CFG;
    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }
 
    if ($CFG->auth == "cas" && $CFG->cas_use_cas == "1" ){
       if ($CFG->cas_create_user=="0"){
          if (get_user_info_from_db("username", $username)){
             return true;
          }else{
             return false;
          }
       }else{
          return true;
       }
    }

    $ldap_connection = auth_ldap_connect();

    if ($ldap_connection) {
        $ldap_user_dn = auth_ldap_find_userdn($ldap_connection, $username);
      
        //if ldap_user_dn is empty, user does not exist
        if(!$ldap_user_dn){
            ldap_close($ldap_connection);
            return false;
        }

        // Try to bind with current username and password
        $ldap_login = ldap_bind($ldap_connection, $ldap_user_dn, $password);
        ldap_close($ldap_connection);
        if ($ldap_login) {
           if ($CFG->cas_create_user=="0"){
              if (get_user_info_from_db("username", $username)){
                return true;
              }else{
                return false;
              }
           }else{
              return true;
           }
        }
    } else {
        ldap_close($ldap_connection);
        error("LDAP part of CAS-module cannot connect to server: $CFG->ldap_host_url");
    }
    return false;
}

?>