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

    redirect("$CFG->wwwroot/");

?>
