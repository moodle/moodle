<?php  // $Id$
       // Logs into Hive from HarvestRoad and stores session ID in Moodle session
       // Martin Dougiamas, Moodle
       //
       // Example CFG variables to make this work:

       // $CFG->sso          = 'hive';
       // $CFG->hiveprotocol = 'http';
       // $CFG->hiveport     = '80';
       // $CFG->hivehost     = 'turkey.harvestroad.com.au';
       // $CFG->hivepath     = '/cgi-bin/hive/hive.cgi';
       // $CFG->hivecbid     = '28';

function sso_user_login($username, $password) {

    global $CFG, $SESSION;

    include($CFG->libdir.'/snoopy/Snoopy.class.inc');

    if (empty($CFG->hivehost)) {
        return false;   // Hive config variables not configured yet
    }

/// Set up Snoopy

    $snoopy = new Snoopy;

    $submit_url = $CFG->hiveprotocol .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath ;

    $submit_vars['HIVE_UNAME']  = $username;
    $submit_vars['HIVE_UPASS']  = $password;
    $submit_vars['HIVE_ENDUSER']= $username;
    $submit_vars['HIVE_REQ']    = '2112';
    $submit_vars['HIVE_REF']    = 'hin:hive@API Login 3';
    $submit_vars['HIVE_RET']    = 'ORG';
    $submit_vars['HIVE_REM']    = '';
    $submit_vars['HIVE_PROD']   = '0';
    $submit_vars['HIVE_USERIP'] = getremoteaddr();


/// We use POST to call Hive with a bit more security
    $snoopy->submit($submit_url,$submit_vars);

/// Extract HIVE_SESSION from headers

    foreach ($snoopy->headers as $header) {
        if (strpos($header, 'HIVE_SESSION=') !== false) {
            $header = explode('HIVE_SESSION=', $header);
            if (count($header) > 1) {
                $cookie = explode(';', $header[1]);
                $cookie = $cookie[0];
                $SESSION->HIVE_SESSION = $cookie;
                return true;
            }
        }
    }

/// Try again with the guest username and password

    $submit_vars['HIVE_UNAME']  = $CFG->hiveusername;
    $submit_vars['HIVE_UPASS']  = $CFG->hivepassword;
    $submit_vars['HIVE_ENDUSER']= $CFG->hiveusername;
    $snoopy->submit($submit_url,$submit_vars);
    foreach ($snoopy->headers as $header) {
        if (strpos($header, 'HIVE_SESSION=') !== false) {
            $header = explode('HIVE_SESSION=', $header);
            if (count($header) > 1) {
                $cookie = explode(';', $header[1]);
                $cookie = $cookie[0];
                $SESSION->HIVE_SESSION = $cookie;
                return true;
            }
        }
    }

    return false;  // No cookie found
}

?>
