<?php

function glossary_show_entry_dictionary($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1, $aliases=true) {

    global $CFG, $USER, $OUTPUT;

    echo '<table class="glossarypost dictionary" cellspacing="0">';
    echo '<tr valign="top">';
    echo '<td class="entry">';
    glossary_print_entry_approval($cm, $entry, $mode);
    echo '<div class="concept">';
    glossary_print_entry_concept($entry);
    echo '</div> ';
    glossary_print_entry_definition($entry, $glossary, $cm);
    glossary_print_entry_attachment($entry, $cm, 'html');
    if (core_tag_tag::is_enabled('mod_glossary', 'glossary_entries')) {
        echo $OUTPUT->tag_list(core_tag_tag::get_item_tags('mod_glossary', 'glossary_entries', $entry->id), null, 'glossary-tags');
    }
    echo '</td></tr>';
    echo '<tr valign="top"><td class="entrylowersection">';
    glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $aliases);
    echo '</td>';
    echo '</tr>';
    echo "</table>\n";
}

function glossary_print_entry_dictionary($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1) {

    //The print view for this format is exactly the normal view, so we use it

    //Take out autolinking in definitions in print view
    $entry->definition = '<span class="nolink">'.$entry->definition.'</span>';

    //Call to view function (without icons, ratings and aliases) and return its result
    return glossary_show_entry_dictionary($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);
}


