<?PHP  // $Id$
       // phpinfo.php - shows phpinfo for the current server

    require_once("../config.php");

    $topframe    = optional_param('topframe', false, PARAM_BOOL);
    $bottomframe = optional_param('bottomframe', false, PARAM_BOOL);

    require_login();

    if (!isadmin()) {
        error("Only the admin can use this page");
    }

    if (!$topframe && !$bottomframe) {
        ?>

        <head>
        <title>PHP info</title>
        </head>

        <frameset rows="80,*">
           <frame src="phpinfo.php?topframe=true&amp;sesskey=<?php echo $USER->sesskey ?>">
           <frame src="phpinfo.php?bottomframe=true&amp;sesskey=<?php echo $USER->sesskey ?>">
        </frameset>

        <?php
    } else if ($topframe && confirm_sesskey()) {
        $stradministration = get_string("administration");
        $site = get_site();

        print_header("$site->shortname: phpinfo", "$site->fullname",
                     "<a target=\"$CFG->framename\" href=\"index.php\">$stradministration</a> -> PHP info");
        exit;
    } else if ($bottomframe && confirm_sesskey()) {
        phpinfo();
        exit;
    }
?>
