<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

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

    add_to_log($course->id, "journal", "view", "view.php?id=$cm->id", "$journal->id");

    if (! $cw = get_record("course_sections", "id", $cm->section)) {
        error("Course module is incorrect");
    }

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }
    print_header("$course->shortname: $journal->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>Journals</A> -> $journal->name", "", "", true,
                  update_module_icon($cm->id, $course->id));

    if (isteacher($course->id)) {
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">View all responses</A></P>";
    }

    echo "<CENTER>\n";
    
    print_simple_box( text_to_html($journal->intro) , "center");

    echo "<BR>";

    $timenow = time();

    if ($course->format == "weeks" and $journal->days) {
        $timestart = $course->startdate + (($cw->section - 1) * 608400);
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
            print_single_button("edit.php", $options, "Start or edit my journal entry");
            echo "</CENTER>";
        }

        if ($entry = get_record_sql("SELECT * FROM journal_entries 
                                     WHERE user='$USER->id' AND journal='$journal->id'")) {

            if (empty($entry->text)) {
                echo "<P ALIGN=center><B>Blank entry</B></P>";
            } else {
                echo text_to_html($entry->text);
            }
            
        } else {
            echo "<B><I>You have not started this journal yet.</I></B>";
        }

        print_simple_box_end();

        if ($timenow < $timefinish) {
            if ($entry->modified) {
                echo "<P><FONT SIZE=-2><B>Last edited:</B> ";
                echo userdate($entry->modified);
                echo " (".count_words($entry->text)." words)";
                echo "</FONT></P>";
            }
            if ($journal->days) {
                echo "<P><FONT SIZE=-2><B>Editing period ends:</B> ";
                echo userdate($timefinish)."</FONT></P>";
            }
        } else {
            echo "<P><FONT SIZE=-2><B>Editing period has ended:</B> ";
            echo userdate($timefinish)."</P>";
        }

        if ($entry->comment || $entry->rating) {
            print_heading("Feedback");
            journal_print_feedback($course, $entry);
        }


    } else {
        echo "<P><B>This journal won't be open until: ";
        echo userdate($timestart)."</B></P>";
    }

    print_footer($course);

?>
