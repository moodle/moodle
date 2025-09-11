<?php

function glossary_show_entry_entrylist($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1, $aliases=true) {
    global $USER, $OUTPUT;

    $return = false;

    echo '<table class="glossarypost entrylist table-reboot" cellspacing="0">';

    echo '<tr valign="top">';
    echo '<td class="entry">';
    if ($entry) {
        glossary_print_entry_approval($cm, $entry, $mode);

        $anchortagcontents = glossary_print_entry_concept($entry, true);

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
        echo '<div style="text-align:center">';
        print_string('noentry', 'glossary');
        echo '</div>';
    }
    echo '</td></tr>';

    echo "</table>";
    echo "<hr>\n";
    return $return;
}

function glossary_print_entry_entrylist($course, $cm, $glossary, $entry, $mode='', $hook='', $printicons=1) {
    //Take out autolinking in definitions un print view
    // TODO use <nolink> tags MDL-15555.
    $entry->definition = '<span class="nolink">'.$entry->definition.'</span>';

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


