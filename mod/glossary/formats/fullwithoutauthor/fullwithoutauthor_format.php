<?php  // $Id$

function glossary_show_entry_fullwithoutauthor($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {
    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<br /><table border=\"0\" width=\"95%\" cellspacing=\"0\" valign=\"top\" cellpadding=\"3\" class=\"forumpost\" align=\"center\">";

    echo "\n<tr>";
    $return = false;
    if ($entry) {

        echo "<td valign=\"top\" width=\"100%\" bgcolor=\"$THEME->cellheading\" class=\"forumpostheader\">";

        echo "<b>";
        glossary_print_entry_concept($entry);
		echo "</b><br />";

        echo "<font size=\"1\">(".get_string("lastedited").": ".
             userdate($entry->timemodified).")</font>";
        echo "</td>";
        echo "\n<td bgcolor=\"$THEME->cellheading\" width=\"35\" valign=\"top\" class=\"forumpostheader\">";

        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");
        echo "</td>";

        echo "</tr>";

        echo "\n<tr>";
        echo "\n<td width=\"100%\" colspan=\"2\" bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">";

        glossary_print_entry_definition($entry);
        $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings);
        echo ' ';
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
