<?php

/**
 * Displays a glossary entry in full format but without the author information.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $glossary The glossary object.
 * @param stdClass $entry The glossary entry object.
 * @param string $mode The mode in which the entry is being displayed.
 * @param string $hook
 * @param int $printicons Whether to print editing icons.
 * @param bool $aliases Whether to show aliases popup.
 * @param int $conceptheadinglevel The heading level to use for rendering the concept within the heading element.
 * @return void
 * @package mod_glossary
 */
function glossary_show_entry_fullwithoutauthor(
    $course,
    $cm,
    $glossary,
    $entry,
    $mode = "",
    $hook = "",
    $printicons = 1,
    $aliases = true,
    $conceptheadinglevel = 3,
) {
    global $CFG, $USER, $OUTPUT;


    if ($entry) {
        echo '<table class="glossarypost fullwithoutauthor table-reboot" cellspacing="0" role="presentation">';
        echo '<tr valign="top">';

        echo '<th class="entryheader">';

        echo '<div class="concept">';
        glossary_print_entry_concept($entry, headinglevel: $conceptheadinglevel);
        echo '</div>';

        echo '<span class="time">('.get_string('lastedited').': '.
             userdate($entry->timemodified).')</span>';
        echo '</th>';
        echo '<td class="entryattachment">';

        glossary_print_entry_approval($cm, $entry, $mode);
        echo '</td>';

        echo '</tr>';

        echo '<tr valign="top">';
        echo '<td width="100%" colspan="2" class="entry">';

        glossary_print_entry_definition($entry, $glossary, $cm);
        glossary_print_entry_attachment($entry, $cm, 'html');

        if (core_tag_tag::is_enabled('mod_glossary', 'glossary_entries')) {
            echo $OUTPUT->tag_list(
                core_tag_tag::get_item_tags('mod_glossary', 'glossary_entries', $entry->id), null, 'glossary-tags');
        }

        echo '</td></tr>';
        echo '<tr valign="top"><td colspan="2" class="entrylowersection">';
        glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $aliases);

        echo ' ';
        echo '</td></tr>';
        echo "</table>\n";
    } else {
        echo html_writer::div(get_string('noentry', 'glossary'), 'text-center');
    }
}

/**
 * Display entries in the full, without author glossary format for printing.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $glossary The glossary object.
 * @param stdClass $entry The glossary entry object.
 * @param string $mode The mode in which the entry is being displayed.
 * @param string $hook
 * @param int $printicons Whether to print editing icons.
 * @param int $conceptheadinglevel The heading level to use for rendering the concept within the heading element.
 * @package mod_glossary
 */
function glossary_print_entry_fullwithoutauthor(
    $course,
    $cm,
    $glossary,
    $entry,
    $mode = "",
    $hook = "",
    $printicons = 1,
    $conceptheadinglevel = 3
) {
    // The print view for this format is exactly the normal view, so we use it.

    // Take out auto-linking in definitions in print view.
    $entry->definition = '<span class="nolink">' . $entry->definition . '</span>';

    // Call to view function (without icons, ratings and aliases) and return its result.
    glossary_show_entry_fullwithoutauthor($course, $cm, $glossary, $entry, $mode, $hook, false, false, $conceptheadinglevel);
}


