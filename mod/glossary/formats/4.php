<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry,$mode="",$hook="",$printicons=1) {
    global $THEME, $USER;

    if ( $entry ) {
        $colour = $THEME->cellheading2;

        echo '<table border=1 cellspacing=0 width=95% valign=top cellpadding=10>';

        echo '<tr>';
        echo "<td width=100% bgcolor=\"$colour\">";
        $entry->course = $course->id;
        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");

        echo '<b>' . get_string("question","glossary") . ':</b> ';
        echo  glossary_print_entry_concept($entry) . '<br>';
        echo '&nbsp;&nbsp;<font size=1>' . get_string("lastedited").': '.userdate($entry->timemodified) . '</font></tr>';
        echo "<tr><td width=100% bgcolor=\"$THEME->cellcontent\">";		
        echo '<b>' . get_string("answer","glossary") . ':</b> ';
        echo glossary_print_entry_definition($entry);


        glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook,$printicons);
        echo '</td></tr></table>';

    } else {
        echo '<center>';
        print_string("noentry", "glossary");
        echo '</center>';
    }

}

?>
