<?PHP  // $Id$

function glossary_show_entry_continuous($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {

    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<table border=0 width=95% cellspacing=0 valign=top cellpadding=0 align=center>\n";
    echo "<tr>\n";
    echo "<td width=\"100%\" valign=\"top\"\n";
    glossary_print_entry_approval($cm, $entry, $mode);
    glossary_print_entry_attachment($entry,"html","right");
    echo "<b>";
    glossary_print_entry_concept($entry);
    echo ":</b> ";
    glossary_print_entry_definition($entry);
    $entry->alias = "";
    $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings,false);
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    return $return;
}

?>
