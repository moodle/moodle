<?PHP
    require_once("../../config.php");
    require_once("lib.php");

    require_variable($courseid);
    require_variable($concept);  // entry id

    $entries = get_records("glossary_entries","ucase(concept)",strtoupper(trim($concept)));

    print_header();
    
    glossary_show_entry($courseid, $entries);
    
    close_window_button();

    function glossary_show_entry($courseid, $entries) {
        global $THEME, $USER;

        $colour = $THEME->cellheading2;

        echo "\n<center><table width=95% border=0><TR>";
        echo "<TD WIDTH=100% BGCOLOR=\"#FFFFFF\">";
        if ( $entries ) {
            foreach ( $entries as $entry ) {
                if( $ConceptIsPrinted ) {
                    echo "<hr>";
                }
                if ( !$ConceptIsPrinted ) {
                    echo "<b>" . $entry->concept . "</b>:<br>";
                    $ConceptIsPrinted = 1;
                }

                if ($entry->attachment) {
                    $entry->course = $courseid;
                    echo "<table border=0 align=right><tr><td>";
                    echo glossary_print_attachments($entry,"html");
                    echo "</td></tr></table>";
                }
                echo format_text($entry->definition, $entry->format);
            }
        }
        echo "</td>";
        echo "</TR></table></center>";
    }

?>
</body>
</html>

