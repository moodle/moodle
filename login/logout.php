<?PHP // $Id$
// Logs the user out and sends them back where they came from

    require("../config.php");

    add_to_log("Logged out");
    $USER = NULL;
    redirect($HTTP_REFERER);
    exit;

?>
