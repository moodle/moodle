<?PHP  // $Id$

function glossary_show_entry_continuous($course, $cm, $glossary, $entry, $mode="",$hook="",$printicons=1,$ratings=NULL) {

    global $THEME, $USER;

    $return = false;
    if ($entry) {
        glossary_print_entry_approval($cm, $entry, $mode);
        glossary_print_entry_attachment($entry,"html","right");
        glossary_print_entry_concept($entry);
        echo ": ";

        glossary_print_entry_definition($entry);

        $icons = '';
        if ( $printicons ) {
            $icons = glossary_print_entry_icons($course, $cm, $glossary, $entry, $mode, $hook,"html");
        }

        echo '(';
        if ( $icons ) {
            echo $icons;
        }
        $return = glossary_print_entry_ratings($course, $entry, $ratings);

        echo ')<br>';

    }
    return $return;

}

?>
