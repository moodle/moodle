<?php 

    require_once("../../config.php");
    require_once("lib.php");
    global $CFG;
    
    require_variable($id);                         // Course Module ID
    require_variable($mode,"letter"); // format to show the entries
    optional_variable($sortkey,"UPDATE");          // Sorting key if TAB = GLOSSARY_DATE_VIEW
    optional_variable($sortorder,"asc");           // Sorting order if TAB = GLOSSARY_DATE_VIEW
    optional_variable($hook,"ALL");          
    optional_variable($eid);          
    optional_variable($search);

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 
    
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 
    
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
    if ($course->category) {
        require_login($course->id);    
        if (isguest()) {
            error("You must be logged to use this page.");
        } 
    }

    if ( $eid ) {
        $mode = 'entry';
    }
/// Generating the SQL based on the format to show
    switch ($mode) {
    case "cat":
        $where = '';
        if ($hook) {
            if ( $hook != GLOSSARY_SHOW_ALL_CATEGORIES and $hook != GLOSSARY_SHOW_NOT_CATEGORISED ) {
                    $where = 'and c.id = ' . $hook;
            }
        }
        $entries = get_records_sql("SELECT ec.id, c.name pivot, e.*
                                    FROM {$CFG->prefix}glossary_entries e,
                                         {$CFG->prefix}glossary_entries_categories ec,
                                         {$CFG->prefix}glossary_categories as c
                                    WHERE e.id = ec.entryid AND ec.categoryid = c.id AND
                                          (e.glossaryid = $glossary->id or e.sourceglossaryid = $glossary->id)
                                          AND e.approved != 0 $where
                                    ORDER BY c.name, e.concept");

    break;

    case "date":
    //// Valid sorting values
        switch ($sortkey) {
        case 'CREATION':
            $sortkey = 'timecreated';
        break;

        case 'UPDATE':
        default:
            $sortkey = 'timemodified';
        break;
        }
        if ($sortorder != 'asc' and $sortorder != 'desc') {
            $sortorder = 'asc';
        }

        $entries = get_records_sql("SELECT e.timemodified pivot, e.*
                                    FROM {$CFG->prefix}glossary_entries e
                                    WHERE (e.glossaryid = $glossary->id or e.sourceglossaryid = $glossary->id)
                                          AND e.approved != 0
                                    ORDER BY e.$sortkey $sortorder");

    break;
    case "letter":
    default:
        switch ($CFG->dbtype) {
        case "postgres7":
            $pivot = "substring(e.concept, 1,1)";
        break;

        case "mysql":
            $pivot = "left(e.concept,1)";
        break;
        default:
            $pivot = "e.concept";
        break;
        }

        if ( $hook ) {
            if ($hook != 'ALL' and $hook != 'SPECIAL') {
                switch ($CFG->dbtype) {
                case 'postgres7':
                    $where = 'and substr(ucase(concept),1,' .  strlen($hook) . ') = \'' . strtoupper($hook) . '\'';
                break;
                case 'mysql':
                    $where = 'and left(ucase(concept),' .  strlen($hook) . ") = '" . strtoupper($hook) . "'";
                break;
                default:
                    $where = '';
                }
            }
        } elseif ($eid) {
            $where = " and e.id = $eid";
        }

        $entries = get_records_sql("SELECT e.id, $pivot pivot, e.*
                                    FROM {$CFG->prefix}glossary_entries e
                                    WHERE (e.glossaryid = $glossary->id or e.sourceglossaryid = $glossary->id)
                                          AND e.approved != 0 $where
                                    ORDER BY e.concept $sortorder");
    break;
    } 

    echo '<p><STRONG>' . get_string("course") . ': <i>' . $course->fullname . '</i><br />';
    echo get_string("modulename","glossary") . ': <i>' . $glossary->name . '</i></STRONG></p>';

    if ( !$entries ) {
        echo get_string("noentries","glossary");
        exit;
    }
                                    
    $groupheader = '';
    $tableisopen = 0;
    foreach ($entries as $entry) {
        $pivot = $entry->pivot;
        if ( $CFG->dbtype != "postgres7" and $CFG->dbtype != "mysql" and $mode != "cat") {
            $pivot = $pivot[0];
        }
        
        if ($mode != "date") {
            if (strtoupper($groupheader) != strtoupper($pivot)) {
            /// Printing th eheader of the group

                if ($tableisopen) {
                    echo '</table>';
                    echo '</center>';
                    $tableisopen = 0;
                }
                $groupheader = $pivot;
                echo '<p align="center"><STRONG><font size="4" color="#0000FF">' . $groupheader . '</font></STRONG></p>';
            }
        }
        if ( !$tableisopen ) {
            echo '<center>';
            echo '<table border="1" cellpadding="5" cellspacing="0" width="95%">';
            $tableisopen = 1;
        }

        echo '<tr>';
        echo '<td width="25%" align="right" valign="top"><b>'. $entry->concept . ': </b></td>';
        echo '<td width="75%">';

        if ( $entry->attachment) {
            glossary_print_entry_attachment($entry);
        }

        echo format_text("<nolink>$entry->definition</nolink>",$entry->format);

        echo '</tr>';
    }
    if ($tableisopen) {
        echo '</table>';
        echo '</center>';
    }
?>
