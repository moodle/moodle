<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry,$tab="",$cat="") {
    global $THEME, $USER;

    if ( $entry ) {
        $colour = $THEME->cellheading2;

        echo "\n<table border=1 cellspacing=0 width=95% valign=top cellpadding=10>";

        echo "\n<tr>";
        echo "<td width=100% bgcolor=\"$colour\">";
        $entry->course = $course->id;
        if ( $tab == GLOSSARY_APPROVAL_VIEW ) {
            echo "<a title=\"" . get_string("approve","glossary"). "\" href=\"approve.php?id=$cm->id&eid=$entry->id&tab=$tab\"><IMG align=\"right\" src=\"check.gif\" border=0 width=\"34\" height=\"34\"></a>";
        }
        if ($entry->attachment) {
            echo "<table border=0 align=right><tr><td>";
            echo glossary_print_attachments($entry, "html");
            echo "</td></tr></table>";
        }
        echo "<b>" . get_string("question","glossary") . ":</b> $entry->concept<br>";
        echo "&nbsp;&nbsp;<font size=1>".get_string("lastedited").": ".userdate($entry->timemodified)."</font></tr>";
        echo "\n<tr><td width=100% bgcolor=\"$THEME->cellcontent\">";		
        echo "<b>" . get_string("answer","glossary") . ":</b> " . format_text($entry->definition, $entry->format);

        glossary_print_entry_icons($course, $cm, $glossary, $entry, $tab, $cat);
        echo "</td></tr></table>\n";

    } else {
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }

}

?>
