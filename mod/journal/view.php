<?php  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    if (! $journal = get_record("journal", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    add_to_log($course->id, "journal", "view", "view.php?id=$cm->id", $journal->id, $cm->id);

    if (! $cw = get_record("course_sections", "id", $cm->section)) {
        error("Course module is incorrect");
    }

    $strjournal = get_string("modulename", "journal");
    $strjournals = get_string("modulenameplural", "journal");

    print_header_simple("$journal->name", "",
                 "<a href=\"index.php?id=$course->id\">$strjournals</a> -> $journal->name", "", "", true,
                  update_module_button($cm->id, $course->id, $strjournal), navmenu($course, $cm));

    if (isteacher($course->id)) {
        $currentgroup = get_current_group($course->id);
        if ($currentgroup and isteacheredit($course->id)) {
            $group = get_record("groups", "id", $currentgroup);
            $groupname = " ($group->name)";
        } else {
            $groupname = "";
        }
        $entrycount = journal_count_entries($journal, $currentgroup);

        echo "<p align=\"right\"><a href=\"report.php?id=$cm->id\">".
              get_string("viewallentries","journal", $entrycount)."</a>$groupname</p>";

    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    echo "<center>\n";
    
    $journal->intro = trim($journal->intro);

    if (!empty($journal->intro)) {
        print_simple_box( format_text($journal->intro,  $journal->introformat) , "center");
    }

    echo "<br />";

    $timenow = time();

    if ($course->format == "weeks" and $journal->days) {
        $timestart = $course->startdate + (($cw->section - 1) * 604800);
        if ($journal->days) {
            $timefinish = $timestart + (3600 * 24 * $journal->days);
        } else {
            $timefinish = $course->enddate;
        }
    } else {  // Have no time limits on the journals

        $timestart = $timenow - 1;
        $timefinish = $timenow + 1;
        $journal->days = 0;
    }

    if ($timenow > $timestart) {

        print_simple_box_start("center");

        if ($timenow < $timefinish) {
            $options = array ("id" => "$cm->id");
            echo "<center>";
            if (!isguest()) {
                print_single_button("edit.php", $options, get_string("startoredit","journal"));
            }
            echo "</center>";
        }


        if ($entry = get_record("journal_entries", "userid", $USER->id, "journal", $journal->id)) {

            if (empty($entry->text)) {
                echo "<p align=\"center\"><b>".get_string("blankentry","journal")."</b></p>";
            } else {
                echo format_text($entry->text, $entry->format);
            }
            
        } else {
            echo "<b><i>".get_string("notstarted","journal")."</i></b>";
        }

        print_simple_box_end();

        if ($timenow < $timefinish) {
            if ($entry->modified) {
                echo "<p><font size=\"-2\"><b>".get_string("lastedited").":</b> ";
                echo userdate($entry->modified);
                echo " (".get_string("numwords", "", count_words($entry->text)).")";
                echo "</font></p>";
            }
            if ($journal->days) {
                echo "<p><font size=\"-2\"><b>".get_string("editingends", "journal").":</b> ";
                echo userdate($timefinish)."</font></p>";
            }
        } else {
            echo "<p><font size=\"-2\"><b>".get_string("editingended", "journal").":</b> ";
            echo userdate($timefinish)."</p>";
        }

        if ($entry->comment or $entry->rating) {
            $grades = make_grades_menu($journal->assessed);
            print_heading(get_string("feedback"));
            journal_print_feedback($course, $entry, $grades);
        }


    } else {
        echo "<p><b>".get_string("notopenuntil", "journal").": ";
        echo userdate($timestart)."</b></p>";
    }

    print_footer($course);

?>
