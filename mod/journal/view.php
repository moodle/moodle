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

    if (! $cw = get_record("course_weeks", "id", $cm->week)) {
        error("Course module is incorrect");
    }

    print_header("$course->shortname: $journal->name", "$course->fullname",
                 "<A HREF=../../course/view.php?id=$course->id>$course->shortname</A> -> 
                  <A HREF=index.php?id=$course->id>Journals</A> -> $journal->name", "");

    if ($USER->editing) {
        print_update_module_icon($cm->id);
    }

    if (isteacher($course->id)) {
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">View all responses</A></P>";
    }

    echo "<CENTER>\n";
    
    print_simple_box( text_to_html($journal->intro) , "center");

    echo "<BR>";

    $timenow = time();
    $timestart = $course->startdate + (($cw->week - 1) * 608400);
    if ($journal->days) {
        $timefinish = $timestart + (3600 * 24 * $journal->days);
    } else {
        $timefinish = $course->enddate;
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
                echo journaldate($entry->modified)."</FONT></P>";
            }
            if ($journal->days) {
                echo "<P><FONT SIZE=-2><B>Editing period ends:</B> ";
                echo journaldate($timefinish)."</FONT></P>";
            }
        } else {
            echo "<P><FONT SIZE=-2><B>Editing period has ended:</B> ";
            echo journaldate($timefinish)."</P>";
        }

        if ($entry->comment || $entry->rating) {
            print_heading("Feedback");
            print_feedback($course, $entry);
        }


    } else {
        echo "<P><B>This journal won't be open until: ";
        echo journaldate($timestart)."</B></P>";
    }

    print_footer($course);

// Functions

function print_feedback($course, $entry) {
    global $CFG, $THEME, $RATING;

    if (! $teacher = get_record("user", "id", $entry->teacher)) {
        error("Weird journal error");
    }

    echo "\n<TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=10>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=3 BGCOLOR=\"$THEME->body\" WIDTH=35 VALIGN=TOP>";
    print_user_picture($teacher->id, $course->id, $teacher->picture);
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">$teacher->firstname $teacher->lastname";
    echo "&nbsp;&nbsp;<FONT SIZE=2><I>".journaldate($entry->timemarked)."</I>";
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";
    echo "<P>Overall: ";
    if ($RATING[$entry->rating]) {
        echo $RATING[$entry->rating];
    } else {
        echo "Error";
    }
    echo "</P>";
    echo text_to_html($entry->comment);
    echo "</TD></TR></TABLE>";

}

?>
