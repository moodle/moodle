<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;
    $return = false;

    echo "\n<br /><table border=0 width=95% cellspacing=0 valign=top cellpadding=3 class=forumpost align=center>";

    echo "\n<tr>";
    echo "<td width=100% bgcolor=\"$THEME->cellheading\">";
    glossary_print_entry_approval($cm, $entry, $mode);
    if ($entry) {
        glossary_print_entry_attachment($entry,"html","right");
        echo "<b>";
        glossary_print_entry_concept($entry);
        echo "</b><br />";
        echo "<font size=1>".get_string("lastedited").": ".userdate($entry->timemodified)."</font>";
        echo "</tr>";
        echo "\n<tr><td width=100% bgcolor=\"$THEME->cellcontent\">";
		
        glossary_print_entry_definition($entry);
        glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook,$printicons);
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
