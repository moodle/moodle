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

                if (! $glossary = get_record("glossary", "id", $entry->glossaryid)) {
                    error("Glossary ID was incorrect or no longer exists");
                }
                if (! $course = get_record("course", "id", $glossary->course)) {
                    error("Glossary is misconfigured - don't know what course it's from");
                }
                if (!$cm = get_coursemodule_from_instance("glossary", $entry->glossaryid, $courseid) ) {
                    error("Glossary is misconfigured - don't know what course module it is ");
                }

                glossary_print_entry($course, $cm, $glossary, $entry);
            }
        }
        echo "</td>";
        echo "</TR></table></center>";
    }

?>
</body>
</html>

