<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry,$tab="",$cat="") {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<table border=1 cellspacing=0 width=95% valign=top cellpadding=10>";

    echo "\n<tr>";
    echo "<td width=100% bgcolor=\"$colour\">";
    glossary_print_entry_approval($cm, $entry, $tab);
    if ($entry) {
        glossary_print_entry_attachment($entry,"html","right");
        echo "<b>";
        glossary_print_entry_concept($entry);
        echo "</b><br />";
        echo "&nbsp;&nbsp;<font size=1>".get_string("lastedited").": ".userdate($entry->timemodified)."</font>";
        echo "</tr>";
        echo "\n<tr><td width=100% bgcolor=\"$THEME->cellcontent\">";
		
        glossary_print_entry_definition($entry);
        glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $tab, $cat);
    } else {
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }
    echo "</td></tr>";

    echo "</table>\n";

}

?>
