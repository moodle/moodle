<?php  // $Id$

function glossary_show_entry_faq($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL, $aliases=true) {
    global $THEME, $USER;
    $return = false;
    if ( $entry ) {
        $colour = $THEME->cellheading2;

        echo "\n<br /><table border=\"0\" width=\"95%\" cellspacing=\"0\" cellpadding=\"3\" class=\"forumpost\" align=\"center\">";

        echo '<tr valign=\"top\">';
        echo "<td valign=\"top\" width=\"100%\" bgcolor=\"$colour\">";
        $entry->course = $course->id;

        echo '<b>' . get_string("question","glossary") . ':</b> ';
        glossary_print_entry_concept($entry);
        echo '<br />';

        echo "<font size=\"1\">(".get_string("lastedited").": ".
             userdate($entry->timemodified).")</font>";
        echo "</td>";
        echo "\n<td bgcolor=\"$colour\" width=\"35\" valign=\"top\" class=\"forumpostheader\">";

        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");
        echo "</td>";

        echo "</tr>";

        echo "\n<tr>";
        echo "\n<td width=\"100%\" colspan=\"2\" bgcolor=\"$THEME->cellcontent\" class=\"forumpostmessage\">";
        echo '<b>' . get_string("answer","glossary") . ':</b> ';

        glossary_print_entry_definition($entry);


        $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry, $mode, $hook, $printicons, $ratings, $aliases);
        echo '</td></tr></table>';
        echo "</div>\n"; // end container div

    } else {
        echo "<div id=\"glossary-edit\" class=\"glossary\">\n"; // glossary-edit container div
        echo '<center>';
        print_string("noentry", "glossary");
        echo '</center>';
    }
    return $return;
}

function glossary_print_entry_faq($course, $cm, $glossary, $entry, $mode="", $hook="", $printicons=1, $ratings=NULL) {

    //The print view for this format is exactly the normal view, so we use it

    //Take out autolinking in definitions un print view
    $entry->definition = '<nolink>'.$entry->definition.'</nolink>';

    //Call to view function (without icons, ratings and aliases) and return its result
    return glossary_show_entry_faq($course, $cm, $glossary, $entry, $mode, $hook, false, false, false);

}

?>
