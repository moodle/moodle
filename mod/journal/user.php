<?PHP // $Id$

// Code fragment to list all the journals in a course by a particular user.
// Assumes $course, $user and $mod are all defined (see /course/user.php)

    include("$CFG->dirroot/mod/journal/lib.php");

    if (! $journals = get_all_instances_in_course("journal", $course->id)) {
        notify("There are no journals");
        die;
    }

    if (! $teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                       WHERE t.course = '$course->id' AND t.user = u.id
                                       ORDER BY u.lastaccess DESC")) {
        notify("No teachers");
        die;
    }

    $timenow = time();

    echo "<TABLE BORDER=1 CELLSPACING=0 valign=top align=center cellpadding=10>";
    echo "<TR><TH>Week<TH>Journal</TR>";
    foreach ($journals as $journal) {

        $journal->timestart  = $course->startdate + (($journal->week - 1) * 608400);
        if ($journal->daysopen) {
            $journal->timefinish = $journal->timestart + (3600 * 24 * $journal->daysopen);
        } else {
            $journal->timefinish = 9999999999;
        }

        $entry = get_record_sql("SELECT * FROM journal_entries 
                                 WHERE user='$user->id' AND journal='$journal->id'");

        if ($entry->text) {
            echo "<TR VALIGN=TOP>";
            $journalopen = ($journal->timestart < $timenow && $timenow < $journal->timefinish);
            if ($journalopen) {
                echo "<TD BGCOLOR=\"$THEME->cellheading2\">";
            } else {
                echo "<TD BGCOLOR=\"$THEME->cellheading\">";
            }
            echo "$journal->week</TD>";
            echo "<TD BGCOLOR=\"$THEME->cellcontent\">";
            echo "<P><A HREF=\"$CFG->wwwroot/mod/journal/view.php?id=$journal->coursemodule\">$journal->name</A></P>";
            if ($entry->modified) {
                echo "<P><FONT SIZE=1>Last edited: ".moodledate($entry->modified)."</FONT></P>";
            }
            echo text_to_html($entry->text);
            if ($entry->teacher) {
                echo "\n<BR CLEAR=ALL><TABLE><TR>";
                echo "<TD WIDTH=35 VALIGN=TOP>";
                print_user_picture($entry->teacher, $course->id, $teachers[$entry->teacher]->picture);
                echo "<TD BGCOLOR=\"$THEME->cellheading\">".$RATING[$entry->rating];
                if ($entry->timemarked) {
                    echo "&nbsp;&nbsp;<FONT SIZE=1>".moodledate($entry->timemarked)."</FONT>";
                }
                echo "<BR><FONT COLOR=#000055>";
                echo text_to_html($entry->comment);
                echo "</FONT><BR>";
                echo "</TD></TR></TABLE>";
            }
            echo "</TD></TR>";
        }
    }
    echo "</TABLE>";
 
?>

