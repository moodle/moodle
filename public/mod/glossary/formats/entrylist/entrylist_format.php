<?php

/**
 * Displays a glossary entry as a list of entries.
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
 * @return bool
 * @package mod_glossary
 */
function glossary_show_entry_entrylist(
    $course,
    $cm,
    $glossary,
    $entry,
    $mode = '',
    $hook = '',
    $printicons = 1,
    $aliases = true,
    $conceptheadinglevel = 3,
) {
    $return = false;

    echo '<table class="glossarypost entrylist table-reboot" cellspacing="0" role="presentation">';

    echo '<tr valign="top">';
    echo '<td class="entry">';
    if ($entry) {
        glossary_print_entry_approval($cm, $entry, $mode);
        $anchortagcontents = glossary_print_entry_concept($entry, true, $conceptheadinglevel);

        $link = new moodle_url('/mod/glossary/showentry.php', array('courseid' => $course->id,
                'eid' => $entry->id, 'displayformat' => 'dictionary'));
        $anchor = html_writer::link($link, $anchortagcontents);

        echo "<div class=\"concept\">$anchor</div> ";
        echo '</td><td align="right" class="entrylowersection">';
        if ($printicons) {
            glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,'print');
        }
        if (!empty($entry->rating)) {
            echo '<br />';
            echo '<span class="ratings d-block pt-3">';
            $return = glossary_print_entry_ratings($course, $entry);
            echo '</span>';
        }
        echo '<br />';
    } else {
        echo html_writer::div(get_string('noentry', 'glossary'), 'text-center');
    }
    echo '</td></tr>';

    echo "</table>";
    echo "<hr>\n";
    return $return;
}

/**
 * Display entries in the entry list glossary format for printing.
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
function glossary_print_entry_entrylist(
    $course,
    $cm,
    $glossary,
    $entry,
    $mode = '',
    $hook = '',
    $printicons = 1,
    $conceptheadinglevel = 3
) {
    // Take out auto-linking in definitions in print view.
    $entry->definition = '<span class="nolink">' . $entry->definition . '</span>';

    echo html_writer::start_tag('table', array('class' => 'glossarypost entrylist mod-glossary-entrylist'));
    echo html_writer::start_tag('tr');
    echo html_writer::start_tag('td', array('class' => 'entry mod-glossary-entry'));
    echo html_writer::start_tag('div', array('class' => 'mod-glossary-concept'));
    glossary_print_entry_concept($entry);
    echo html_writer::end_tag('div');
    echo html_writer::start_tag('div', array('class' => 'mod-glossary-definition'));
    glossary_print_entry_definition($entry, $glossary, $cm);
    echo html_writer::end_tag('div');
    echo html_writer::start_tag('div', array('class' => 'mod-glossary-lower-section'));
    glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, false, false);
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('td');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('table');
}


