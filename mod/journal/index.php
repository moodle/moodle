<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);
    add_to_log($course->id, "journal", "view all", "index.php?id=$course->id", "");

    $strjournal = get_string("modulename", "journal");
    $strjournals = get_string("modulenameplural", "journal");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    
    $crumbs[] = array('name' => $strjournals, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($crumbs, $course);

    print_header_simple("$strjournals", "", $navigation, 
                 "", "", true, "", navmenu($course));


    if (! $journals = get_all_instances_in_course("journal", $course)) {
        notice("There are no journals", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $strsection = $strweek;
    } else if ($course->format == "topics") {
        $strsection = $strtopic;
    } else {
        $strsection = "";
    }

    foreach ($journals as $journal) {

        $journal->timestart  = $course->startdate + (($journal->section - 1) * 608400);
        if (!empty($journal->daysopen)) {
            $journal->timefinish = $journal->timestart + (3600 * 24 * $journal->daysopen);
        } else {
            $journal->timefinish = 9999999999;
        }

        $journalopen = ($journal->timestart < $timenow && $timenow < $journal->timefinish);

        journal_user_complete_index($course, $USER, $journal, $journalopen, "$strsection $journal->section");

    }

    echo "<br />";

    print_footer($course);
 
?>

