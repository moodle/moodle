<?php
/*********************************
 * Course outcomes editting page *
 *********************************/

include_once('../../../config.php');
$courseid = required_param('id', SITEID, PARAM_INT); // course id

    /// form processing
    print_header();

    // Add tabs
    $currenttab = 'outcomesettings';
    include('tabs.php');

    /// listing of all site outcomes + this course specific outcomes
    $outcomes = get_records_sql('SELECT * FROM '.$CFG->prefix.'grade_outcomes
                                 WHERE ISNULL(courseid)');
                get_records('grade_outcomes', 'courseid', $courseid);

    check_theme_arrows();
    include_once('course.html');

    print_footer();
?>