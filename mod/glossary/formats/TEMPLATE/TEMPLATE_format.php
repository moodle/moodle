<?php  // $Id$

function glossary_show_entry_TEMPLATE($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {

    global $THEME, $CFG, $USER;

    $colour = $THEME->cellheading2;

    $user = get_record("user", "id", $entry->userid);
    $strby = get_string("writtenby", "glossary");

    echo "<table border=\"0\" width=\"95%\" cellspacing=\"0\" cellpadding=\"3\" class=\"forumpost\" align=\"center\"r>\n";
    echo "<tr>\n";
    echo "<td width=\"100%\" valign=\"top\" bgcolor=\"#FFFFFF\">\n";

    if ($entry) {
        //Use this function to show author's image
        //Comments: Configuration not supported
        print_user_picture($user->id, $course->id, $user->picture);

        //Line separator to show this template fine. :-)
        echo "<br />\n";

        //Use this code to show author's name
        //Comments: Configuration not supported
        echo "$strby " . fullname($user, isteacher($course->id)) . "<br />\n";

        //Use this code to show modification date
        //Comments: Configuration not supported
        echo get_string("lastedited").": ". userdate($entry->timemodified) . "<br />\n";

        //Use this function to show the approval button. It'll be showed if necessary       
        //Comments: You can configure this parameters:
        //----Define where to show the approval button
        $approvalalign = 'right'; //Values: left, center and right (default right)
        //----Define if the approval button must be showed into a 100% width table
        $approvalinsidetable = true; //Values: true, false (default true)
        //Call the function
        glossary_print_entry_approval($cm, $entry, $mode, $approvalalign, $approvalinsidetable);

        //Line separator to show this template fine. :-)
        echo "<br />\n";

        //Use this function to show the attachment. It'll be showed if necessary
        //Comments: You can configure this parameters:
        //----Define how to show the attachment 
        $attachmentformat = 'html'; //Values: html (link) and NULL (inline image if possible) (default NULL)
        //----Define where to show the attachment
        $attachmentalign = 'right'; //Values: left, center and right (default right)
        //----Define if the attachment must be showed into a 100% width table
        $attachmentinsidetable = true; //Values: true, false (default true)
        //Call the function
        glossary_print_entry_attachment($entry,$attachmentformat,$attachmentalign,$attachmentinsidetable);

        //Line separator to show this template fine. :-)
        echo "<br />\n";

        //Use this function to print the concept
        //Comments: Configuration not supported
        glossary_print_entry_concept($entry);

        //Line separator to show this template fine. :-)
        echo "<br />\n";

        //Use this function to show the definition 
        //Comments: Configuration not supported
        glossary_print_entry_definition($entry);

        //Line separator to show this template fine. :-)
        echo "<br />\n";

        //Use this function to show aliases, editing icons and ratings
        //Comments: You can configure this parameters:
        //----Define when to show the aliases popup
        $aliases = true; //Values: true, false (Default: true)
        //----Uncoment this line to avoid ratings being showed
        //    use it only if you are really sure! You can define this in the glossary conf. page.
        //$ratings = NULL;
        //----Uncoment this line to avoid editing icons being showed
        //    use it only if you are really sure!
        //$printicons = false;
        $return = glossary_print_entry_lower_section($course, $cm, $glossary, $entry,$mode,$hook,$printicons,$ratings,$aliases);
    } else {    
        echo "<center>";
        print_string("noentry", "glossary");
        echo "</center>";
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

    return $return;
}

?>
