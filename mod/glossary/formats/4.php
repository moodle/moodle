<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {
    global $THEME, $USER;
    $return = false;
    if ( $entry ) {
        $colour = $THEME->cellheading2;

        echo '<br /><table border=0 cellspacing=0 width=95% valign=top cellpadding=10 class=forumpost>';

        echo '<tr>';
        echo "<td width=100% bgcolor=\"$colour\">";
        $entry->course = $course->id;
        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");

        echo '<b>' . get_string("question","glossary") . ':</b> ';
        glossary_print_entry_concept($entry) . '<br>';
        echo '&nbsp;&nbsp;<font size=1>' . get_string("lastedited").': '.userdate($entry->timemodified) . '</font></tr>';
        echo "<tr><td width=100% bgcolor=\"$THEME->cellcontent\">";		
        echo '<b>' . get_string("answer","glossary") . ':</b> ';
        glossary_print_entry_definition($entry);


        glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook,$printicons);
        echo ' ';
        $return = glossary_print_entry_ratings($course, $entry, $ratings);
        echo '</td></tr></table>';

    } else {
        echo '<center>';
        print_string("noentry", "glossary");
        echo '</center>';
    }
    return $return;
}

?>
