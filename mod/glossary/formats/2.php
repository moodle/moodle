<?PHP  // $Id$
require_once("lib.php");

function glossary_print_entry_by_format($course, $cm, $glossary, $entry) {
    global $THEME, $CFG, $USER;

//    if ($entry->timemarked < $entry->modified) {
        $colour = $THEME->cellheading2;
//    } else {
//        $colour = $THEME->cellheading;
//    }

    $user = get_record("user", "id", $entry->userid);
    $strby = get_string("writtenby","glossary");

    echo "<table width=95% border=0><tr><td>";

    echo "\n<TABLE BORDER=1 CELLSPACING=0 valign=top cellpadding=10>";

    echo "\n<TR>";
    echo "\n<TD ROWSPAN=2 BGCOLOR=\"$colour\" WIDTH=35 VALIGN=TOP>";
    if ($entry) {
          print_user_picture($user->id, $course->id, $user->picture);
    }
    echo "</TD>";
    echo "<TD NOWRAP WIDTH=100% BGCOLOR=\"$THEME->cellheading\">";
    if ($entry->attachment) {
          $entry->course = $glossary->course;
          echo "<table border=0 align=right><tr><td>";
          echo glossary_print_attachments($entry, "html");
          echo "</td></tr></table>";
    }
    if ($entry) {
    	     echo "<b>$entry->concept</b><br><FONT SIZE=2>$strby $user->firstname $user->lastname</font>";
          echo "&nbsp;&nbsp;<FONT SIZE=1>(".get_string("lastedited").": ".userdate($entry->timemodified).")</FONT></small>";
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
