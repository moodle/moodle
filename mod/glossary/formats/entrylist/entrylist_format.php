<?php  // $Id$

function glossary_show_entry_entrylist($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL, $aliases=true) {
    global $THEME, $USER;

    $colour = "#FFFFFF";
    $return = false;

    echo "\n<table border=\"0\" cellspacing=\"0\" width=\"95%\" valign=\"top\" cellpadding=\"10\">";

    echo "\n<tr>";
    echo "<td width=\"100%\" bgcolor=\"$colour\">";
    if ($entry) {
        echo "<b><a href=\"showentry.php?courseid=$course->id\&amp;eid=$entry->id\&amp;displayformat=dictionary\" target=\"_blank\" onClick=\"return openpopup('/mod/glossary/showentry.php?courseid=$course->id\&amp;eid=$entry->id\&amp;displayformat=dictionary', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";

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

function glossary_print_entry_entrylist($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL) {

    //The print view for this format is different from the normal view, so we implement it here completely
    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    //Take out autolinking in definitions un print view
    $entry->definition = '<nolink>'.$entry->definition.'</nolink>';

    echo "\n<table border=0 width=95% cellspacing=0 valign=top cellpadding=3 class=forumpost align=center>\n";
    echo "<tr>\n";
    echo "<td width=\"100%\" valign=\"top\" bgcolor=\"#FFFFFF\">\n";
    echo "<b>";
    glossary_print_entry_concept($entry);
    echo ":</b> ";
    glossary_print_entry_definition($entry);
    $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    return $return;
}

?>
