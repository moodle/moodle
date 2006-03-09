<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    $strlogs = get_string('logs');
    echo "<center><a href=\"{$CFG->wwwroot}/course/report/logs/index.php?id={$course->id}\">";
    echo "$strlogs</a></center>\n";

?>
