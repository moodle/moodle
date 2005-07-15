<?php  // $Id$
       // expired.php - called by hive when the session has expired.

    require('../../config.php');

    require('lib.php');

    require_login();

    if (empty($SESSION->HIVE_PASSWORD)) {  // We don't have old password
        error('Sorry, but Hive has timed out, you need to log in again', 
               $CFG->wwwroot.'/login/logout.php');
    }

/// Try and log back in silently

    if (sso_user_login($USER->username, $SESSION->HIVE_PASSWORD)) {  // Log back into Hive

        /// Need something in here to redirect back to Hive

    } else {
        error('Sorry, could not restore Hive connection, please try logging in again', 
               $CFG->wwwroot.'/login/logout.php');
    }

   
?>
