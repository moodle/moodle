<?PHP
    require_once("../../config.php");
    require_once("lib.php");

    require_variable($courseid);
    require_variable($eid);  // entry id

    $entry = get_record("glossary_entries","id",$eid);

    print_header();
    
    glossary_show_entry($courseid, $entry);
    
    close_window_button();

    function glossary_show_entry($courseid, $entry) {
        global $THEME, $USER;

        $colour = $THEME->cellheading2;

        echo "\n<center><table width=95% border=0><TR>";
        echo "<TD WIDTH=100% BGCOLOR=\"#FFFFFF\">";
        if ($entry->attachment) {
            $entry->course = $courseid;
            echo "<table border=0 align=right><tr><td>";
            echo glossary_print_attachments($entry,"html");
            echo "</td></tr></table>";
        }
        echo "<b>$entry->concept</b>: ";
        echo format_text($entry->definition, $entry->format);
        echo "</td>";
        echo "</TR></table></center>";
    }

?>
</body>
</html>

