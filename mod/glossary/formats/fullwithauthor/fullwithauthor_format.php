<?php  // $Id$

function glossary_show_entry_fullwithauthor($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL, $aliases=true) {
    global $CFG, $USER;


    $user = get_record('user', 'id', $entry->userid);
    $strby = get_string('writtenby', 'glossary');

    echo '<table class="glossarypost fullwithauthor">';

    echo '<tr valign="top">';
    $return = false;
    if ($entry) {
        echo '<td class="picture">';
        print_user_picture($user->id, $course->id, $user->picture);
        echo '</td>';
        
        echo '<td class="entryheader">';

        echo '<strong>';
        glossary_print_entry_concept($entry);
		echo '</strong><br />';

        echo '<span class="author">'.$strby.' '.fullname($user, isteacher($course->id));
        echo '&nbsp;&nbsp;('.get_string('lastedited').': '.
             userdate($entry->timemodified).')</span>';
        echo '</td>';
        echo '<td class="entryattachment">';

        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,'html','right');
        echo '</td>';

        echo '</tr>';

        echo '<tr valign="top">';
        echo '<td class="left">&nbsp;</td>';
        echo '<td colspan="2" class="entry">';

        glossary_print_entry_definition($entry);
        $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $ratings, $aliases);
        echo ' ';
    } else {
        echo '<td align="center">';
        print_string('noentry', 'glossary');
    }
    echo '</td></tr>';

    echo "</table>\n";
    return $return;
}

function glossary_print_entry_fullwithauthor($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL) {

    //The print view for this format is exactly the normal view, so we use it

    //Take out autolinking in definitions un print view
    $entry->definition = '<nolink>'.$entry->definition.'</nolink>';

    //Call to view function (without icons, ratings and aliases) and return its result
    return glossary_show_entry_fullwithauthor($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);

}

?>
