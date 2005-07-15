<?php  // $Id$
       // Logs into Hive from HarvestRoad and stores session ID in Moodle session
       // Martin Dougiamas, Moodle
       //
       // Example CFG variables to make this work:

       // $CFG->sso         = 'hive';
       // $CFG->hiveprot    = 'http';
       // $CFG->hiveport    = '80';
       // $CFG->hivehost    = 'turkey.harvestroad.com.au';
       // $CFG->hivepath    = '/cgi-bin/hive/hivedb.cgi';
       // $CFG->hivecbid    = '28';

function sso_user_login($username, $password) {

    global $CFG, $SESSION;

    include($CFG->libdir.'/snoopy/Snoopy.class.inc');

    if (empty($CFG->hivehost)) {
        return false;   // Hive config variables not configured yet
    }

/// Set up Snoopy

    $snoopy = new Snoopy;

    $submit_url = $CFG->hiveprot .'://'. $CFG->hivehost .':'. $CFG->hiveport .''. $CFG->hivepath ;

    $submit_vars['HIVE_UNAME']  = $username;
    $submit_vars['HIVE_UPASS']  = $password;
    $submit_vars['HIVE_ENDUSER']= $username;
    $submit_vars['HIVE_REQ']    = '2112';
    $submit_vars['HIVE_REF']    = 'hin:hive@Moodle Login';
    $submit_vars['HIVE_RET']    = 'ORG';
    $submit_vars['HIVE_REM']    = '';
    $submit_vars['HIVE_PROD']   = '0';
    $submit_vars['HIVE_USERIP'] = getremoteaddr();


/// Ideally we would use POST for a bit more security, but there is a Hive bug preventing this
/// $snoopy->submit($submit_url,$submit_vars);

/// Using GET instead
    foreach ($submit_vars as $name => $value) {
        $params[] = "$name=".urlencode($value);
    }
    $submit_url .= '?'.implode('&', $params);
    $snoopy->fetch($submit_url);


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

    return false;  // No cookie found
}

?>
