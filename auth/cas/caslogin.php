<?PHP
/**
 * @author Romuald Lorthioir
 * CAS for login module
 * 10.03.2004 Creation
 */
    require_once($CFG->dirroot.'/config.php');
    include_once($CFG->dirroot.'/lib/cas/CAS.php');
    $cas_validate=false;

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