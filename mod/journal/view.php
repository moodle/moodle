<?PHP  // $Id$

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

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strjournal = get_string("modulename", "journal");
    $strjournals = get_string("modulenameplural", "journal");

    print_header("$course->shortname: $journal->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strjournals</A> -> $journal->name", "", "", true,
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

        echo "<p align=right><a href=\"report.php?id=$cm->id\">".
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
            echo "<CENTER>";
            if (!isguest()) {
                print_single_button("edit.php", $options, get_string("startoredit","journal"));
            }
            echo "</CENTER>";
        }


        if ($entry = get_record("journal_entries", "userid", $USER->id, "journal", $journal->id)) {

            if (empty($entry->text)) {
                echo "<P ALIGN=center><B>".get_string("blankentry","journal")."</B></P>";
            } else {
                echo format_text($entry->text, $entry->format);
            }
            
        } else {
            echo "<B><I>".get_string("notstarted","journal")."</I></B>";
        }

        print_simple_box_end();

        if ($timenow < $timefinish) {
            if ($entry->modified) {
                echo "<P><FONT SIZE=-2><B>".get_string("lastedited").":</B> ";
                echo userdate($entry->modified);
                echo " (".get_string("numwords", "", count_words($entry->text)).")";
                echo "</FONT></P>";
            }
            if ($journal->days) {
                echo "<P><FONT SIZE=-2><B>".get_string("editingends", "journal").":</B> ";
                echo userdate($timefinish)."</FONT></P>";
            }
        } else {
            echo "<P><FONT SIZE=-2><B>".get_string("editingended", "journal").":</B> ";
            echo userdate($timefinish)."</P>";
        }

        if ($entry->comment or $entry->rating) {
            $grades = make_grades_menu($journal->assessed);
            print_heading(get_string("feedback"));
            journal_print_feedback($course, $entry, $grades);
        }


    } else {
        echo "<P><B>".get_string("notopenuntil", "journal").": ";
        echo userdate($timestart)."</B></P>";
    }

    print_footer($course);

?>
