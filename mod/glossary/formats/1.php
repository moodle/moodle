<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry) {
    global $THEME, $USER;

//    if ($entry->timemarked < $entry->modified) {
        $colour = $THEME->cellheading2;
//    } else {
//        $colour = $THEME->cellheading;
//    }
    echo "<table width=95% border=0><tr><td>";

    echo "\n<TABLE BORDER=1 CELLSPACING=0 width=100% valign=top cellpadding=10>";

    echo "\n<TR>";
    echo "<TD WIDTH=100% BGCOLOR=\"$colour\">";
    if ($entry->attachment) {
          $entry->course = $course->id;
          echo "<table border=0 align=right><tr><td>";
          echo glossary_print_attachments($entry, "html");
          echo "</td></tr></table>";
    }
    echo "<b>$entry->concept</b><br>";
    if ($entry) {
        echo "&nbsp;&nbsp;<FONT SIZE=1>".get_string("lastedited").": ".userdate($entry->timemodified)."</FONT>";
    }
    echo "</TR>";

    echo "\n<TR><TD WIDTH=100% BGCOLOR=\"$THEME->cellcontent\">";
    if ($entry) {
	  echo format_text($entry->definition, $entry->format);

	  glossary_print_entry_icons($course, $cm, $glossary, $entry);

    } else {
	  echo "<center>";
        print_string("noentry", "glossary");
	  echo "</center>";
    }
    echo "</TD></TR>";

    echo "</TABLE>\n";
    
    echo "</td></tr></table>";

}

?>
