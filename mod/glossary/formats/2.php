<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry, $tab="",$cat="") {
    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    $user = get_record("user", "id", $entry->userid);
    $strby = get_string("writtenby", "glossary");

    echo "\n<table border=0 width=95% cellspacing=0 valign=top cellpadding=3 class=forumpost align=center>";

    echo "\n<tr>";
    echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostpicture\">";
    if ($entry) {
        print_user_picture($user->id, $course->id, $user->picture);

        echo "</td>";
        echo "<td nowrap valign=\"top\" width=100% bgcolor=\"$THEME->cellheading\" class=\"forumpostheader\">";

        glossary_print_entry_approval($cm, $entry, $tab);
        glossary_print_entry_attachment($entry,"html","right");

        echo "<b>";
        glossary_print_entry_concept($entry);
		echo "</b><br />";

        echo "<font size=\"2\">$strby $user->firstname $user->lastname</font>";
        echo "&nbsp;&nbsp;<font size=1>(".get_string("lastedited").": ".
             userdate($entry->timemodified).")</font>";
        echo "</tr>";

        echo "\n<tr>";
        echo "\n<td bgcolor=\"$colour\" width=35 valign=top class=\"forumpostside\">&nbsp;</td>";
        echo "\n<td width=100% bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">";

        glossary_print_entry_definition($entry);
        glossary_print_entry_icons($course, $cm, $glossary, $entry,$tab,$cat);

    } else {
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }
    echo "</td></tr>";

    echo "</table>\n";
}

?>
