<?PHP
// $Id$
// author: romuald Lorthioir
//CHANGELOG:
//16/03/2005 Use of LDAP Module
//05.02.2005 Added CAS module

/* README!
CAS Module
This Module can be turn ON/OFF on admin screen.
The /login/index.php module is intercepted and replace with the login.php.
This Module is using the /auth/cas/index_form.html.
This module is using the LDAP Module so you need the /auth/ldap directory.
You can see /auth/ldap/lib.php for the other functions.
*/
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

define('AUTH_LDAP_NAME', 'cas'); // for ldap module
require_once($CFG->dirroot.'/config.php');
require_once($CFG->dirroot.'/auth/ldap/lib.php');
require_once($CFG->dirroot.'/lib/cas/CAS.php');
$cas_validate=false;

/**
 * replace the ldap auth_user_login function
 * authenticates user againt CAS with ldap
 * Returns true if the username and password work
 * and false if they don't
 *
 * @param string  $username
 * @param string  $password
 *
*/
function cas_ldap_auth_user_login ($username, $password) {
/// Returns true if the username and password work
/// and false if they don't

    global $CFG;
    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return false;
    }
 
    if ($CFG->auth == "cas" && !empty($CFG->cas_enabled)){ //cas specific
       if ($CFG->cas_create_user=="0"){
          if (record_exists('user', 'username', $username)){
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
           if ($CFG->cas_create_user=="0"){  //cas specific
              if (record_exists('user', 'username', $username)){
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
   phpCAS::forceAuthentication();
   if ($CFG->cas_create_user=="0"){
      if (record_exists('user', 'username', phpCAS::getUser())) {
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
        $cas_user_exist=phpCAS::checkAuthentication();
        if (!$cas_user_exist && !$CFG->guestloginbutton){
           $cas_user_exist=phpCAS::forceAuthentication();
        }
        if ($cas_user_exist){
           if ($CFG->cas_create_user=="0"){
              if (record_exists('user', 'username', phpCAS::getUser())) {
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