<?PHP
// $Id: lib.php
// author: romualdLorthioir $
//CHANGELOG:
//05.02.2005 Added CAS module

/* README!
CAS Module
This Module can be turn ON/OFF on admin screen.
The /login/index.php module is intercepted and replace with the login.php.
And use the /auth/cas/index_form.html and /auth/cas/caslogin.php.
This module is using the LDAP Module so you need the /auth/ldap directory.
*/

require_once($CFG->dirroot.'/config.php');
include_once($CFG->dirroot.'/lib/cas/CAS.php');
$cas_validate=false;
define("AUTH_METHOD", 'cas');
require_once($CFG->dirroot.'/auth/cas/commonlib.php');


function auth_user_login ($username, $password) {
/// Returns true if the username and password work
/// and false if they don't

    global $CFG;
    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }
 
    if ($CFG->auth == "cas" && !empty($CFG->cas_enabled)){
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

/**
 * authenticates user against CAS from screen login
 * the user doesn't have a CAS Ticket yet.
 *
 * Returns an object user if the username and password work
 * and nothing if they don't
 *
 * @param string  $username
 * @param string  $password
 *
*/
function cas_authenticate_user_login ($username, $password) {

   global $CFG;
   $cas_validate=true;

   phpCAS::client($CFG->cas_version,$CFG->cas_hostname,(Integer)$CFG->cas_port,$CFG->cas_baseuri);
   phpCAS::setLang($CFG->cas_language);
   if (!phpCAS::isAuthenticated()){
      phpCAS::authenticateIfNeeded();
   }
   if ($CFG->cas_create_user=="0"){
      if (get_user_info_from_db("username", phpCAS::getUser())){
         $user = authenticate_user_login(phpCAS::getUser(), 'cas');
      }else{
         //login as guest if CAS but not Moodle and not automatic creation
         if ($CFG->guestloginbutton){
             $user = authenticate_user_login('guest', 'guest');
         }else{
             $user = authenticate_user_login(phpCAS::getUser(), 'cas');
         }
      }
   }else{
      $user = authenticate_user_login(phpCAS::getUser(), 'cas');
   }
   return $user;
}

/**
 * authenticates user against CAS when first call of Moodle
 * if already in CAS (cookie with the CAS ticket), don't have to log again (SSO)
 *
 * Returns an object user if the username and password work
 * and nothing if they don't
 *
 * @param object $user
 *
*/
function cas_automatic_authenticate ($user="") {
   global $CFG;
   if (!$cas_validate){
        $cas_validate=true;
        phpCAS::client($CFG->cas_version,$CFG->cas_hostname,(Integer)$CFG->cas_port,$CFG->cas_baseuri);
        phpCAS::setLang($CFG->cas_language);
        if (!phpCAS::isAuthenticated() && !$CFG->guestloginbutton){
           phpCAS::authenticateIfNeeded();
        }
        if (phpCAS::isAuthenticated()){
           if ($CFG->cas_create_user=="0"){
              if (get_user_info_from_db("username", phpCAS::getUser())){
                 $user = authenticate_user_login(phpCAS::getUser(), 'cas');
              }else{
                 //login as guest if CAS but not Moodle and not automatic creation
                 if ($CFG->guestloginbutton){
                     $user = authenticate_user_login('guest', 'guest');
                 }else{
                     $user = authenticate_user_login(phpCAS::getUser(), 'cas');
                 }
              }
           }else{
              $user = authenticate_user_login(phpCAS::getUser(), 'cas');
           }
           return $user;
        }else{
           return;
        }

   }else{
      return $user;
   }
}

?>