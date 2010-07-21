<?php

function glossary_show_entry_entrylist($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1, $aliases=true) {
    global $USER, $OUTPUT;

    $return = false;

    echo '<table class="glossarypost entrylist" cellspacing="0">';

    echo '<tr valign="top">';
    echo '<td class="entry">';
    if ($entry) {
        glossary_print_entry_approval($cm, $entry, $mode);

        $anchortagcontents = glossary_print_entry_concept($entry, true);

        $link = "/mod/glossary/showentry.php?courseid={$course->id}&eid={$entry->id}&displayformat=dictionary";
        $action = new popup_action('click', $link.'&popup=1', 'entry',array('title'=>'entry','width'=>600,'height'=>450));

        $anchor = $OUTPUT->action_link($link, $anchortagcontents, $action);

        echo "<div class=\"concept\">$anchor</div> ";
        echo '</td><td align="right" class="entrylowersection">';
        if ($printicons) {
            glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,'print');
        }
        if (!empty($entry->rating)) {
            echo '<br />';
            echo '<span class="ratings">';
            $return = glossary_print_entry_ratings($course, $entry);
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

function glossary_print_entry_entrylist($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1) {

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
    glossary_print_entry_definition($entry, $glossary, $cm);
    glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, false, false);
    echo '</td>';
    echo '</tr>';
    echo "</table>\n";
}


