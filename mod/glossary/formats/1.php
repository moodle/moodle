<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry,$currentview="",$cat="") {
    global $THEME, $USER;

    $colour = $THEME->cellheading2;

    echo "\n<table border=1 cellspacing=0 width=95% valign=top cellpadding=10>";

    echo "\n<tr>";
    echo "<td width=100% bgcolor=\"$colour\">";
    if ($entry->attachment) {
        $entry->course = $course->id;
        echo "<table border=0 align=right><tr><td>";
        echo glossary_print_attachments($entry, "html");
        echo "</td></tr></table>";
    }
    echo "<b>$entry->concept</b><br>";
    if ($entry) {
        echo "&nbsp;&nbsp;<font size=1>".get_string("lastedited").": ".userdate($entry->timemodified)."</font>";
    }
    echo "</tr>";

    echo "\n<tr><td width=100% bgcolor=\"$THEME->cellcontent\">";
    if ($entry) {
        glossary_print_entry_definition($entry);
        glossary_print_entry_icons($course, $cm, $glossary, $entry, $currentview, $cat);

    } else {
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }
    echo "</td></tr>";

    echo "</table>\n";

}

?>
