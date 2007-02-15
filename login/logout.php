<?php // $Id$
// Logs the user out and sends them to the home page

    require_once("../config.php");

    if (!empty($USER->mnethostid) and $USER->mnethostid != $CFG->mnet_localhost_id) {
        $host = get_record('mnet_host', 'id', $USER->mnethostid);
        $wwwroot = $host->wwwroot;
    } else {
        $wwwroot = $CFG->wwwroot;
    }

    require_logout();

    redirect("$wwwroot/");

?>
