<?PHP  // $Id$

function glossary_print_entry_by_format($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {
    global $THEME, $USER;

    $colour = "#FFFFFF";
    $return = false;

    echo "\n<table class=\"generalbox\" border=0 cellspacing=0 width=95% valign=top cellpadding=10>";

    echo "\n<tr>";
    echo "<td width=100% bgcolor=\"$colour\">";
    if ($entry) {
        echo "<b><a href=\"view.php?id=$cm->id&mode=entry&hook=$entry->id\">";

        glossary_print_entry_concept($entry);
        echo '</a></b> ';
        if ( $return = glossary_print_entry_commentslink($course, $cm, $glossary, $entry,$mode,$hook, 'html') ) {
            echo "<font size=-1>($return)</font>";
        }

        echo '<br />';
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
