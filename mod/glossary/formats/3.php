<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {
    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    $user = get_record("user", "id", $entry->userid);
    $strby = get_string("writtenby", "glossary");

    echo "\n<br /><table border=0 width=95% cellspacing=0 valign=top cellpadding=3 class=forumpost align=center>";

    echo "\n<tr>";
    echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostpicture\">";
    $return = false;
    if ($entry) {
        print_user_picture($user->id, $course->id, $user->picture);
        echo "</td>";
        echo "<td align=\"top\" width=100% bgcolor=\"$THEME->cellheading\" class=\"forumpostheader\">";
        glossary_print_entry_approval($cm, $entry, $mode);
        echo "<b>";
        glossary_print_entry_concept($entry);
		echo "</b><br />";
        echo "<font size=\"2\">$strby " . fullname($user, isteacher($course->id)) . "</font>";
        echo "&nbsp;&nbsp;<font size=1>(".get_string("lastedited").": ".
             userdate($entry->timemodified).")</font>";
        echo "</tr>";

        echo "\n<tr>";
        echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostside\">&nbsp;</td>";
        echo "\n<td width=100% align=\"top\" bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">";

        if ($entry->attachment) {
            $entry->course = $course->id;
            if (strlen($entry->definition)%2) {
                $align = "right";
            } else {
                $align = "left";
            }
            glossary_print_entry_attachment($entry,"",$align);
        }
        glossary_print_entry_definition($entry);

        glossary_print_entry_lower_section($course, $cm, $glossary, $entry,$mode,$hook,$printicons);
        echo ' ';
        $return = glossary_print_entry_ratings($course, $entry, $ratings);

    } else {
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }
    echo "</td></tr>";

    echo "</table>\n";
    
    return $return;
}

?>
