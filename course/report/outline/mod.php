<?php  // $Id$

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }

    if (has_capability('coursereport/outline:view', $context)) {
        echo '<p>';
        $activityreport = get_string( 'activityreport' );
        echo "<a href=\"{$CFG->wwwroot}/course/report/outline/index.php?id={$course->id}\">";
        echo "$activityreport</a>\n";
        echo '</p>';
    }
?>