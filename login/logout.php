<?PHP // $Id$
// Logs the user out and sends them to the home page

    require("../config.php");

    $USER = NULL;
    $SESSION = NULL;
    save_session("USER");
    save_session("SESSION");
    redirect($CFG->wwwroot);

?>
