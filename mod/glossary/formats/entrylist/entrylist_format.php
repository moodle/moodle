<?php  // $Id$

function glossary_show_entry_entrylist($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1, $ratings=NULL, $aliases=true) {
    global $USER;

    $return = false;

    echo '<table class="glossarypost entrylist" cellspacing="0">';

    echo '<tr valign="top">';
    echo '<td class="entry">';
    if ($entry) {
        glossary_print_entry_approval($cm, $entry, $mode);
        echo "<div class=\"concept\"><a href=\"showentry.php?courseid=$course->id&amp;eid=$entry->id&amp;displayformat=dictionary\" target=\"_blank\" onclick=\"return openpopup('/mod/glossary/showentry.php?courseid=$course->id&amp;eid=$entry->id&amp;displayformat=dictionary', 'entry', 'menubar=0,location=0,scrollbars,resizable,width=600,height=450', 0);\">";
        glossary_print_entry_concept($entry);
        echo '</a></div> ';
        echo '</td><td align="right" class="entrylowersection">';
        if ($printicons) {
            glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,'print');
        }
        if ($ratings) {
            echo '<br />';
            echo '<span class="ratings">';
            $return = glossary_print_entry_ratings($course, $entry, $ratings);
            echo '</span>';
        }
        echo '<br />';
    } else {
        echo '<div style="text-align:center">';
        print_string('noentry', 'glossary');
        echo '</div>';
    }
    echo '</td></tr>';

    echo "</table>\n";
    return $return;
}

function glossary_print_entry_entrylist($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1, $ratings=NULL) {

    //The print view for this format is different from the normal view, so we implement it here completely
    global $CFG, $USER;


    //Take out autolinking in definitions un print view
    $entry->definition = '<span class="nolink">'.$entry->definition.'</span>';

    echo '<table class="glossarypost entrylist">';
    echo '<tr valign="top">';
    echo '<td class="entry">';
    echo '<b>';
    glossary_print_entry_concept($entry);
    echo ':</b> ';
    glossary_print_entry_definition($entry);
    $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);
    echo '</td>';
    echo '</tr>';
    echo "</table>\n";

    return $return;
}

?>
