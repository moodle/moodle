<?PHP // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    if (ini_get_bool("register_globals")) {
        // This method is to try to avoid silly warnings from PHP 4.3.0
        session_unregister("SESSION");
        session_unregister("USER");
    } else {
        unset($_SESSION['USER']);
        unset($_SESSION['SESSION']);
    }
    redirect($CFG->wwwroot);

?>
