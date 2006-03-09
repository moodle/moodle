<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    $activityreport = get_string( 'activityreport' );
    echo "<center><a href=\"{$CFG->wwwroot}/course/report/outline/index.php?id={$course->id}\">";
    echo "$activityreport</a></center>\n";

?>
