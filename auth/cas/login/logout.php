<?PHP // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    if (ini_get_bool("register_globals") and check_php_version("4.3.0")) {
        // This method is just to try to avoid silly warnings from PHP 4.3.0
        session_unregister("USER");
        session_unregister("SESSION");
    }

    unset($_SESSION['USER']);
    unset($_SESSION['SESSION']);

    unset($SESSION);
    unset($USER);

if ($CFG->auth == "cas"){
 require_once ('../auth/cas/CAS/CAS.php');
    phpCAS::client($CFG->cas_version,$CFG->cas_hostname,(Integer)$CFG->cas_port,$CFG->cas_baseuri);
    $backurl = $CFG->wwwroot;
    phpCAS::logout($backurl);
}

redirect("$CFG->wwwroot/");

?>
