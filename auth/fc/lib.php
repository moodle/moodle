<?php  // $Id$
       
// FirstClass authentication using FirstClass Flexible Provisining Protocol

/* Author: Torsten Anderson, torsten.anderson@skeria.skelleftea.se

CHANGELOG

README
  Module will authenticate user against FirstClass server and check if user belongs to any of
  the defined creator groups.
  User authenticates using their existing FirstClass username and password.
  Where possible userdata is copied from the FirstClass directory to Moodle. You may
  want to modify this.
  Module requires the fcFPP class to do it's jobb.
 */
       

require('fcFPP.php');
   

function auth_user_login ($username, $password) {
/// Returns true if the username and password work
/// and false if they don't

    global $CFG;

    $hostname = $CFG->auth_fchost;
    $port     = $CFG->auth_fcfppport;

    $retval = FALSE;

    if (!$username or !$password) {    // Don't allow blank usernames or passwords
        return $retval;
    }


    $fpp = new fcFPP($hostname,$port);
    if ($fpp->open()) {
       if ($fpp->login($username,$password)){
          $retval = TRUE;
       }
    }
    $fpp->close();

    return $retval;
 

}

function auth_get_userinfo($username){
// Get user information from FirstCLass server and return it in an array.
// Localize this routine to fit your needs. 

/*
Moodle                FirstCLass fieldID in UserInfo form
------                -----------------------------------
firstname             1202
lastname              1204
email                 1252
icq                   -
phone1                1206
phone2                1207 (Fax)
institution           -  
department            - 
address               1205
city                  - 
country               -
lang                  -
timezone              8030 (Not used yet. Need to figure out how FC codes timezones)

description           Get data from users resume. Pictures will be removed.

*/

    global $CFG;
    
    $hostname = $CFG->auth_fchost;
    $port     = $CFG->auth_fcfppport;
    $userid   = $CFG->auth_fcuserid;
    $passwd   = $CFG->auth_fcpasswd; 

    $userinfo = array();

    $fpp = new fcFPP($hostname,$port);
    if ($fpp->open()) {
       if ($fpp->login($userid,$passwd)){

          $userinfo['firstname']   = $fpp->getUserInfo($username,"1202");
          $userinfo['lastname']    = $fpp->getUserInfo($username,"1204");
          $userinfo['email']       = strtok($fpp->getUserInfo($username,"1252"),',');
          $userinfo['phone1']      = $fpp->getUserInfo($username,"1206");
          $userinfo['phone2']      = $fpp->getUserInfo($username,"1207");
	  $userinfo['description'] = $fpp->getResume($username);

       }
    }

    $fpp->close();

    foreach($userinfo as $key => $value) {
       if (!$value) {
          unset($userinfo[$key]);
       }
    }
    
    return $userinfo;

}


function auth_iscreator($username=0) {
//Get users group membership from the FirstClass server user and check if
// user is member of one of the groups of creators.

    global $CFG, $USER;

    if (! $CFG->auth_fccreators) {
       return false;
    }

    if (! $username) {
       $username=$USER->username;
    }

    $fcgroups = array();

    $hostname = $CFG->auth_fchost;
    $port     = $CFG->auth_fcfppport;
    $userid   = $CFG->auth_fcuserid;
    $passwd   = $CFG->auth_fcpasswd; 

    $fpp = new fcFPP($hostname,$port);
    if ($fpp->open()) {
       if ($fpp->login($userid,$passwd)){
          $fcgroups = $fpp->getGroups($username);
       }
    }
    $fpp->close();

   
    if ((! $fcgroups)) {
      return false;
    }

    $creators = explode(";",$CFG->auth_fccreators);
   
    foreach($creators as $creator) {
        If (in_array($creator, $fcgroups)) return true;
    }
    
    return false;
}
  