<?php // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    require_logout();

    redirect("$CFG->wwwroot/");

?>
