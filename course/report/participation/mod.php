<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (has_capability('coursereport/participation:view', $context)) {
        echo '<p>';
        $participationreport = get_string('participationreport');
        echo "<a href=\"{$CFG->wwwroot}/course/report/participation/index.php?id={$course->id}\">";
        echo "$participationreport</a>\n";
        echo '</p>';
    }

