<?PHP // $Id$

    require_once("../config.php");
    $cas_validate=false; //Modif SG - RL pour CAS
    optional_variable($loginguest, false); // determines whether visitors are logged in as guest automatically

    // Check if the guest user exists.  If not, create one.
    if (! record_exists("user", "username", "guest")) {
        $guest->auth        = "manual";
        $guest->username    = "guest";
        $guest->password    = md5("guest");
        $guest->firstname   = addslashes(get_string("guestuser"));
        $guest->lastname    = " ";
        $guest->email       = "root@localhost";
        $guest->description = addslashes(get_string("guestuserinfo"));
        $guest->confirmed   = 1;
        $guest->lang        = $CFG->lang;
        $guest->timemodified= time();

        if (! $guest->id = insert_record("user", $guest)) {
            notify("Could not create guest user record !!!");
        }
    }

    $frm = false;
    if ((!empty($SESSION->wantsurl) and strstr($SESSION->wantsurl,"username=guest")) or $loginguest) {
        /// Log in as guest automatically (idea from Zbigniew Fiedorowicz)
        $frm->username = "guest";
        $frm->password = "guest";
    } else {
        $frm = data_submitted();
    }

    if ($frm) {
        $frm->username = trim(moodle_strtolower($frm->username));

        if (($frm->username == 'guest') and empty($CFG->guestloginbutton)) {
            $user = false;    /// Can't log in as guest if guest button is disabled
            $frm = false;
        } else {


            //Modif SG - RL pour CAS
            if ($CFG->auth == "cas" && $CFG->cas_use_cas == "1" && $frm->username != 'guest'){
               $cas_validate=true;

               include_once('../auth/cas/CAS/CAS.php');

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
               if ($user){
                  $USER = $user;
                  $USER->loggedin = true;
                  $USER->site = $CFG->wwwroot;   // for added security
              
                  //$USER->username = phpCAS::getUser();
                  set_moodle_cookie($USER->username);
                          $wantsurl = $SESSION->wantsurl;
                          unset($SESSION->wantsurl);
                          unset($SESSION->lang);
                          $SESSION->justloggedin = true;
                
                  if (user_not_fully_set_up($USER)) {
                      $site = get_site();
                      redirect("$CFG->wwwroot/user/edit.php?id=$USER->id&course=$site->id");
                  } else if (strpos($wantsurl, $CFG->wwwroot) === 0) {   /// Matches site address
                      redirect($wantsurl);
                  } else {
                      redirect("$CFG->wwwroot/");      /// Go to the standard home page
                  }
                
                  reset_login_count();
                  die;
               }

            }else{
               $user = authenticate_user_login($frm->username, $frm->password);
            }
            //Fin Modif SG - RL pour CAS

        }
        update_login_count();

        if ($user) {
            if (! $user->confirmed ) {       // they never confirmed via email 
                print_header(get_string("mustconfirm"), get_string("mustconfirm") ); 
                print_heading(get_string("mustconfirm"));
                print_simple_box(get_string("emailconfirmsent", "", $user->email), "center");
                print_footer();
                die;
            }

            $USER = $user;
            if (!empty($USER->description)) {
                $USER->description = true;       // No need to cart all of it around
            }
            $USER->loggedin = true;
            $USER->site     = $CFG->wwwroot;     // for added security, store the site in the session
            $USER->sesskey  = random_string(10); // for added security, used to check script parameters
        
            if ($USER->username == "guest") {
                $USER->lang       = $CFG->lang;               // Guest language always same as site
                $USER->firstname  = get_string("guestuser");  // Name always in current language
                $USER->lastname   = " ";
            }


            if (!update_user_login_times()) {
                error("Wierd error: could not update login records");
            }

            set_moodle_cookie($USER->username);

            $wantsurl = $SESSION->wantsurl;

            unset($SESSION->wantsurl);
            unset($SESSION->lang);
            $SESSION->justloggedin = true;

            add_to_log(SITEID, "user", "login", "view.php?id=$user->id&course=".SITEID, $user->id, 0, $user->id);

            reset_login_count();

            if (user_not_fully_set_up($USER)) {
                $site = get_site();
                redirect("$CFG->wwwroot/user/edit.php?id=$USER->id&course=$site->id");

            } else if (strpos($wantsurl, $CFG->wwwroot) === 0) {   /// Matches site address
                redirect($wantsurl);

            } else {
                redirect("$CFG->wwwroot/");      /// Go to the standard home page
            }

            die;

        } else {
          if ($CFG->auth == "cas" && $CFG->cas_use_cas == "1"){
            //Fin Modif SG - RL pour CAS Logout
            $errormsg = get_string("invalidcaslogin");
            phpCAS::logout("$CFG->wwwroot/auth/cas/forbidden.php");
          }else{
            $errormsg = get_string("invalidlogin");
          }
        }
    }

    //Modif SG - RL pour CAS
    if ($CFG->auth == "cas" && $CFG->cas_use_cas == "1" && ! $cas_validate){

        include_once('../auth/cas/CAS/CAS.php');
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

           if ($user){
              $USER = $user;
              $USER->loggedin = true;
              $USER->site = $CFG->wwwroot;   // for added security
          
              //$USER->username = phpCAS::getUser();
              set_moodle_cookie($USER->username);
                      $wantsurl = $SESSION->wantsurl;
                      unset($SESSION->wantsurl);
                      unset($SESSION->lang);
                      $SESSION->justloggedin = true;
            
              if (user_not_fully_set_up($USER)) {
                  $site = get_site();
                  redirect("$CFG->wwwroot/user/edit.php?id=$USER->id&course=$site->id");
              } else if (strpos($wantsurl, $CFG->wwwroot) === 0) {   /// Matches site address
                  redirect($wantsurl);
              } else {
                  redirect("$CFG->wwwroot/");      /// Go to the standard home page
              }
            
              reset_login_count();
              die;
           } else {
              //Fin Modif SG - RL pour CAS Logout
              $errormsg = get_string("invalidcaslogin");
              phpCAS::logout("$CFG->wwwroot/auth/cas/forbidden.php");
           }
        }
    }
    //Fin Modif SG - RL pour CAS

    if (empty($errormsg)) {
        $errormsg = "";
    }

    if (empty($SESSION->wantsurl)) {
        $SESSION->wantsurl = array_key_exists('HTTP_REFERER',$_SERVER) ? $_SERVER["HTTP_REFERER"] : $CFG->wwwroot; 
    }
    
    if (empty($frm->username)) {
        $frm->username = get_moodle_cookie();
        $frm->password = "";
    }

    if (!empty($frm->username)) {
        $focus = "login.password";
    } else {
        $focus = "login.username";
    }

    if ($CFG->auth == "email" or $CFG->auth == "none" or chop($CFG->auth_instructions) <> "" ) {
        $show_instructions = true;
    } else {
        $show_instructions = false;
    }

    if (!$site = get_site()) {
        error("No site found!");
    }

    if (empty($CFG->langmenu)) {
        $langmenu = "";
    } else {
        $currlang = current_language();
        $langs    = get_list_of_languages();
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace('http','https',$CFG->wwwroot);
        }
        $langmenu = popup_form ("$wwwroot/login/index.php?lang=", $langs, "chooselang", $currlang, "", "", "", true);
    }

    $loginsite = get_string("loginsite");

    print_header("$site->fullname: $loginsite", "$site->fullname", $loginsite, $focus, "", true, "<div align=right>$langmenu</div>"); 

    include("index_form.html");

    print_footer();

    exit;

    // No footer on this page


?>