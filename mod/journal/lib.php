<?PHP // $Id$

$RATING = array ("3" => "Outstanding",
                 "2" => "Satisfactory",
                 "1" => "Not satisfactory");


function journal_user_summary($course, $user, $mod, $journal) {
    global $CFG;
}


function journal_user_outline($course, $user, $mod, $journal) {
    if ($entry = get_record_sql("SELECT * FROM journal_entries 
                                 WHERE user='$user->id' AND journal='$journal->id'")) {

        $numwords = count(preg_split("/\w\b/", $entry->text)) - 1;

        $result->info = "$numwords words";
        $result->time = $entry->modified;
        return $result;
    }
    return NULL;
}


function journal_user_complete($course, $user, $mod, $journal) {
    global $CFG, $THEME;

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
            echo "<P ALIGN=RIGHT><FONT SIZE=-1><I>";
            if ($RATING[$entry->rating]) {
                echo "Overall rating: ";
                echo $RATING[$entry->rating];
            } else {
                echo "No rating given";
            }
            echo "</I></FONT></P>";

            echo "<BR><FONT COLOR=#000055>";
            echo text_to_html($entry->comment);
            echo "</FONT><BR>";
            echo "</TD></TR></TABLE>";
        }
        print_simple_box_end();

    } else {
        echo "No entry";
    }
}

?>
