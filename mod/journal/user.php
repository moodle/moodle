<?PHP // $Id$

// Code fragment to list entries in a journal by a particular user.
// Assumes $mode is defined as a selector for the type of report.
// $course, $user, $mod and $instance are all objects (see /course/user.php)

require_once("$CFG->dirroot/mod/journal/lib.php");

switch ($mode) {
    case "summary":
        return user_journal_summary($course, $user, $mod, $instance);
        break;
    case "outline":
        return user_journal_outline($course, $user, $mod, $instance);
        break;
    case "complete":
        return user_journal_complete($course, $user, $mod, $instance);
        break;
}

function user_journal_summary($course, $user, $mod, $journal) {
    global $CFG;
}


function user_journal_outline($course, $user, $mod, $journal) {
    if ($entry = get_record_sql("SELECT * FROM journal_entries 
                                 WHERE user='$user->id' AND journal='$journal->id'")) {

        $numwords = count(preg_split("/\w\b/", $entry->text)) - 1;

        return "$numwords words, last edited ".userdate($entry->modified);

    } else {
        return "No entry";
    }
}


function user_journal_complete($course, $user, $mod, $journal) {
    global $CFG, $THEME;

    echo "<H3>Journal: <A HREF=\"$CFG->wwwroot/mod/journal/view.php?id=$mod->id\">$journal->name</A></H3>";
    echo "<UL>";

    if ($entry = get_record_sql("SELECT * FROM journal_entries 
                             WHERE user='$user->id' AND journal='$journal->id'")) {

        print_simple_box_start();
        if ($entry->modified) {
            echo "<P><FONT SIZE=1>Last edited: ".userdate($entry->modified)."</FONT></P>";
        }
        if ($entry->text) {
            echo text_to_html($entry->text);
        }
        if ($entry->teacher) {
            $teacher = get_record("user", "id", $entry->teacher);
    
            echo "\n<BR CLEAR=ALL>";
            echo "<TABLE><TR>";
            echo "<TD WIDTH=35 VALIGN=TOP>";
            print_user_picture($entry->teacher, $course->id, $teacher->picture);
            echo "<TD BGCOLOR=\"$THEME->cellheading\">".$RATING[$entry->rating];
            if ($entry->timemarked) {
                echo "&nbsp;&nbsp;<FONT SIZE=1>".userdate($entry->timemarked)."</FONT>";
            }
            echo "<BR><FONT COLOR=#000055>";
            echo text_to_html($entry->comment);
            echo "</FONT><BR>";
            echo "</TD></TR></TABLE>";
        }
        print_simple_box_end();

    } else {
        echo "No entry";
    }
    echo "</UL>";
}

?>

