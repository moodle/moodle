<?PHP  // $Id$
       //
       // PAM (Pluggable Authentication Modules) for Moodle 
       // 
       // Description:
       // Authentication by using the PHP4 PAM module:
       // http://www.math.ohio-state.edu/~ccunning/pam_auth/
       // 
       // Version 0.2: 2004/09/01 by Martin Vögeli (stable version)
       // Version 0.1: 2004/08/30 by Martin Vögeli (first draft)
       // 
       // Contact: martinvoegeli@gmx.ch
       // Website 1: http://elearning.zhwin.ch/
       // Website 2: http://birdy1976.com/
       //
       // License:  GPL License v2
       // // // // // // // // // // // // // // // // // // //

function auth_user_login ($username, $password) {
    global $CFG;
    // returns true if the username and password work
    // and false if they are wrong or don't exist
    // variable to store possible errors during authentication
    $strErrorPAM = " ";
    // the maximal length of returned messages is 512
    // let's double the number to give it enough space ;)
    // (the errror variable is passed by reference)
    for ($i = 1; $i <= 1024; $i++) {
        $strErrorPAM += "{$strErrorPAM} ";
    }
    // just for testing and debugging
    // error_reporting(E_ALL);
    // finally the actual authentication part...
    if (pam_auth($username, $password, &$strErrorPAM)) {
        // authentication success
        return true;
    } else {
        // authentication failure
        return false;
    }
}
?>
