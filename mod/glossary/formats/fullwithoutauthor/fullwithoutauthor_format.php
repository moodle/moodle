<?php

function glossary_show_entry_fullwithoutauthor($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $aliases=true) {
    global $CFG, $USER;


    if ($entry) {
        echo '<table class="glossarypost fullwithoutauthor" cellspacing="0">';
        echo '<tr valign="top">';

        echo '<th class="entryheader">';

        echo '<div class="concept">';
        glossary_print_entry_concept($entry);
        echo '</div>';

        echo '<span class="time">('.get_string('lastedited').': '.
             userdate($entry->timemodified).')</span>';
        echo '</th>';
        echo '<td class="entryattachment">';

        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry, $cm, 'html', 'right');
        echo '</td>';

        echo '</tr>';

        echo '<tr valign="top">';
        echo '<td width="100%" colspan="2" class="entry">';

        glossary_print_entry_definition($entry, $glossary, $cm);

        echo '</td></tr>';
        echo '<tr valign="top"><td colspan="2" class="entrylowersection">';
        glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $aliases);

        echo ' ';
        echo '</td></tr>';
        echo "</table>\n";
    } else {
        echo '<center>';
        print_string('noentry', 'glossary');
        echo '</center>';
    }
}

function glossary_print_entry_fullwithoutauthor($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1) {

    //The print view for this format is exactly the normal view, so we use it

    //Take out autolinking in definitions un print view
    $entry->definition = '<span class="nolink">'.$entry->definition.'</span>';

    //Call to view function (without icons, ratings and aliases) and return its result
    return glossary_show_entry_fullwithoutauthor($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);

}


