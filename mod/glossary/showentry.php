<?PHP
    require_once("../../config.php");
    require_once("lib.php");

    require_variable($courseid);
    require_variable($concept);

    print_header();
    $entries = get_records_sql("select e.* from {$CFG->prefix}glossary_entries e, {$CFG->prefix}glossary g".
                                  " where e.glossaryid = g.id and".
                                      " (e.casesensitive != 0 and ucase(concept) = '" . strtoupper(trim($concept)). "' or".
                                      " e.casesensitive = 0 and concept = '$concept') and".
                                      " (g.course = $courseid or g.globalglossary) and".
                                      " e.usedynalink != 0 and g.usedynalink != 0");
    
    glossary_print_dynaentry($courseid, $entries);
    
    close_window_button();
?>
</body>
</html>

