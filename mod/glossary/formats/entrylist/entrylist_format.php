<?php  // $Id$

function glossary_show_entry_entrylist($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {
    global $THEME, $USER;

    $colour = "#FFFFFF";
    $return = false;

    echo "\n<table border=\"0\" cellspacing=\"0\" width=\"95%\" valign=\"top\" cellpadding=\"10\">";

    echo "\n<tr>";
    echo "<td width=\"100%\" bgcolor=\"$colour\">";
    if ($entry) {
        echo "<b><a href=\"showentry.php?courseid=$course->id\&eid=$entry->id\&displayformat=dictionary\" target=\"_blank\" onClick=\"return openpopup('/mod/glossary/showentry.php?courseid=$course->id\&eid=$entry->id\&displayformat=dictionary', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";

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
