<?php // $Id$

/// This page prints a particular instance of glossary
    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);           // Course Module ID
    optional_variable($l,"");        // letter to look for
    optional_variable($offset,0);    // entries to bypass (paging purpouses)
    optional_variable($eid);         // Entry ID
    optional_variable($search, "");  // search string
    optional_variable($includedefinition); // include definition in search function?
    
    optional_variable($tab); // browsing entries by categories?
    optional_variable($cat);         // categoryID

    optional_variable($sortkey,"");  // Sorted view: CREATION or UPDATE
    optional_variable($sortorder,"");  // it define the order of the sorting (ASC or DESC)

    global $CFG;
    $entriesbypage = $CFG->glossary_entbypage;

    if (!$entriesbypage) {
        $entriesbypage = 10;
    }
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 
    
    if ($tab == GLOSSARY_ADDENTRY_VIEW and !$eid) {
        redirect("edit.php?id=$cm->id&tab=$tab");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 
    
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
    require_login($course->id);
    if (!$cm->visible and !isteacher($course->id)) {
        notice(get_string("activityiscurrentlyhidden"));
    } 
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id&tab=$tab", "$glossary->id");
    
/// stablishing default tab
    $framebydefault = GLOSSARY_STANDARD_VIEW;
    if ($glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS) {
        $framebydefault = GLOSSARY_DATE_VIEW;
    }

/// checking for valid values for sortorder and sortkey
    if ( $sortorder = strtolower($sortorder) ) {
        if ($sortorder != 'asc' and $sortorder != 'desc') {
            $sortorder = '';
        } else {
            $l = ''; /// if we are sorting by date, reset the searching by terms or letters
            $search = '';
        }
    }
    if ( $sortkey = strtoupper($sortkey) ) {
        if ($sortkey != 'CREATION' and $sortkey != 'UPDATE') {
            $sortkey = '';
        }
    }
/// in this point:
///       $sortkey   = CREATION | UPDATE | ''
///       $sortorder = asc | desc | ''

    if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
        $tab = $framebydefault;

        if ( !$sortkey ) {
            $sortkey = 'CREATION';
        } 
        if ( !$sortorder ) {
            $sortorder = 'asc';
        }
    } else {
        if ( !$sortkey ) {
            $sortkey = 'concept';
        } 
        if ( !$sortorder ) {
            $sortorder = 'asc';
        }
    }

// creating matrix of words to search if apply
    $search = trim(strip_tags($search));
    if ($search and !$eid) {   /// searching terms
        $l = '';
        $searchterms = explode(' ', $search); // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (strlen($searchterm) < 2) {
                unset($searchterms[$key]);
            } 
        } 
        $search = trim(implode(' ', $searchterms));
        $tab = $framebydefault;
    } elseif ($eid) {   /// searching a specify entry
        $tab = GLOSSARY_STANDARD_VIEW;
        $search = '';
    } 

    $alphabet = explode(',', get_string("alphabet"));
    if ($l == '' and $search == '' and !$eid) {
        // if the user is just entering the glossary...
        if ($tab != GLOSSARY_APPROVAL_VIEW) {
            $l = $alphabet[0];
        } else {
            $l = 'ALL';  /// show ALL by default in the waiting approval frame
        }
    } elseif ($eid) {
        $l = '';
    } 
    
    $category = '';
    if ($tab == GLOSSARY_CATEGORY_VIEW) {
        $l = '';
        if ($cat > 0) {
            $category = get_record("glossary_categories", "id", $cat);
            if (!$category) {
                $cat = '';
            } 
        } 
    } 
    
/// Printing the page header
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } 
    
    $strglossaries = get_string("modulenameplural", "glossary");
    $strglossary = get_string("modulename", "glossary");
    $strallcategories = get_string("allcategories", "glossary");
    $straddentry = get_string("addentry", "glossary");
    $strnoentries = get_string("noentries", "glossary");
    $strsearchconcept = get_string("searchconcept", "glossary");
    $strsearchindefinition = get_string("searchindefinition", "glossary");
    $strsearch = get_string("search");
    
    print_header(strip_tags("$course->shortname: $glossary->name"), "$course->fullname",
        "$navigation <A HREF=index.php?id=$course->id>$strglossaries</A> -> $glossary->name",
        "", "", true, update_module_button($cm->id, $course->id, $strglossary),
        navmenu($course, $cm));
    
    echo '<p align="center"><font size="3"><b>' . stripslashes_safe($glossary->name);
    if ($tab == GLOSSARY_CATEGORY_VIEW | $tab == GLOSSARY_STANDARD_VIEW |
        $tab == GLOSSARY_DATE_VIEW) {
    /// the "Print" icon
        echo " <a title =\"". get_string("printerfriendly","glossary") . "\" target=\"_blank\" href=\"print.php?id=$cm->id&tab=$tab&cat=$cat&l=$l&eid=$eid&sortkey=$sortkey&sortorder=$sortorder\">";
        echo '<img border=0 src="print.gif"/></a>';
    }
    echo '</b></font></p>';

/// Info box

    if ( $glossary->intro ) {
        print_simple_box_start('center','70%');
        echo format_text($glossary->intro);
        print_simple_box_end();
    }

/// Search box

    echo '<p>';
    print_simple_box_start("center", "", $THEME->cellheading);
    echo '<p>';
    echo '<form method="POST" action="view.php">';
    echo '<input type="submit" value="'.$strsearch.'" name="searchbutton"> ';
    echo '<input type="text" name="search" size="20" value=""> ';
    echo '<input type="checkbox" name="includedefinition" value="1">';
    echo $strsearchindefinition;
    echo '<input type="hidden" name="id" value="'.$cm->id.'">';
    echo '</form>';
    echo '</p>';
    print_simple_box_end();


/// Tabbed browsing sections
    include("tabs.html");
    
/// Printing the entries

    switch ($sortkey) {
        case 'CREATION':
            $orderby = "timecreated $sortorder";
        break;
        case 'UPDATE':
            $orderby = "timemodified $sortorder";
        break;
		default:
            $orderby .= "$sortkey $sortorder";
    }
    
    switch ($tab) {
        case GLOSSARY_CATEGORY_VIEW:
            if ($cat == GLOSSARY_SHOW_ALL_CATEGORIES) { 
                $sqlselect = "SELECT gec.id gecid, gc.name, gc.id CID, ge.*";
                $sqlfrom   = "FROM {$CFG->prefix}glossary_entries ge,
                             {$CFG->prefix}glossary_entries_categories gec,
                             {$CFG->prefix}glossary_categories gc";
                $sqlwhere  = "WHERE (ge.glossaryid = '$glossary->id' or ge.sourceglossaryid = '$glossary->id') AND
                              gec.entryid = ge.id AND
                              gc.id = gec.categoryid";

                if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
                    $sqlorderby = ' ORDER BY gc.name, ge.timecreated';
                } else {
                    $sqlorderby = ' ORDER BY gc.name, ge.concept';
                }
                $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
                $sqllimit = " LIMIT $offset, $entriesbypage";

                $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
            } else {
                if ( $cat == GLOSSARY_SHOW_NOT_CATEGORISED ) {
                    $sqlselect  = "SELECT *";
                    $sqlfrom    = "FROM {$CFG->prefix}glossary_entries";
                    $sqlwhere   = "WHERE (glossaryid = $glossary->id or sourceglossaryid = $glossary->id)";
                    $sqlorderby = "ORDER BY $orderby";

                    $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
                    $sqllimit   = " LIMIT $offset, $entriesbypage";
                    $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
                } else {
                    $sqlselect  = "SELECT *";
                    $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}glossary_entries_categories c";
                    $sqlwhere   = "WHERE (ge.id = c.entryid and c.categoryid = $cat) and
                                  (ge.glossaryid = $glossary->id or ge.sourceglossaryid = $glossary->id)";
                    $sqlorderby = "ORDER BY $orderby";

                    $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
                    $sqllimit   = " LIMIT $offset, $entriesbypage";
                    $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
                }
            }
            $currentcategory = "";
        break;
        case GLOSSARY_APPROVAL_VIEW:
            $allentries = glossary_get_entries_sorted($glossary, 'approved = 0',$orderby);
            $currentletter = '';
        break;
        case GLOSSARY_DATE_VIEW:
            $l = 'ALL';
        case GLOSSARY_STANDARD_VIEW:
        default:
            if ($search) { // looking for a term
                $allentries = glossary_search_entries($searchterms, $glossary, $includedefinition);
            } elseif ($eid) { // looking for an entry
                $allentries = get_records_select("glossary_entries", "id = $eid");
            } elseif ( $l or $sortkey ) {
                $where = '';
                if ($l != 'ALL' and $l != 'SPECIAL') {
                    switch ($CFG->dbtype) {
                        case 'postgres7':
                            $where = 'and substr(ucase(concept),1,' .  strlen($l) . ') = \'' . strtoupper($l) . '\'';
                        break;
                        case 'mysql':
                            $where = 'and left(ucase(concept),' .  strlen($l) . ") = '$l'";
                        break;
                        default:
                            $where = '';
                    }
                }
                $sqlselect  = "SELECT *";
                $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge";
                $sqlwhere   = "WHERE (ge.glossaryid = $glossary->id or ge.sourceglossaryid = $glossary->id) $where";
                $sqlorderby = "ORDER BY $orderby";
                $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
                $sqllimit   = " LIMIT $offset, $entriesbypage";
                $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
            }
            $currentletter = '';
        break;
    } 
    
    $dumpeddefinitions = 0;
    if ($allentries) {
        $paging = '';
        if ($count > $entriesbypage ) {
            for ($i = 0; ($i*$entriesbypage) < $count  ; $i++   ) {
                if ( $paging != '' ) {
                    if ($i % 20 == 0) {
                        $paging .= '<br>';
                    } else {
                        $paging .= ' | ';
                    }
                }
                if ($offset / $entriesbypage == $i) {
                    $paging .= '<strong>' . ($i + 1 ) . '</strong>';
                } else {
                    $paging .= "<a href=\"view.php?id=$id&l=$l&search=$search&tab=$tab&cat=$cat&includedefinition=$includedefinition&sortkey=$sortkey&sortored=$sortorder&offset=" . ($i*$entriesbypage) . "\">" . ($i+1) . '</a>';
                }
            }
            $paging  = "<font size=1><center>" . get_string ("jumpto") . " $paging</center></font>";
        }
        echo "$paging<p>";
        if ($glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS) {
            echo '<table border=0 cellspacing=0 width=95% valign=top cellpadding=5><tr><td align=left bgcolor="#FFFFFF">';
        }
        foreach ($allentries as $entry) {
            $dumptoscreen = 0;
            $firstletter = strtoupper(substr(ltrim($entry->concept), 0, strlen($l)));
            if ($l) {
                if ($l == 'ALL' or $sortkey == 'CREATION' or $sortkey == 'UPDATE' or $firstletter == $l) {
                    if ($currentletter != $firstletter[0]) {
                        if ($entry->approved or ($USER->id == $entry->userid and !isteacher($course->id)) or $tab == GLOSSARY_APPROVAL_VIEW) {
                            $currentletter = $firstletter[0];
    
                            if ($glossary->displayformat == GLOSSARY_FORMAT_SIMPLE) {
                                if ($dumpeddefinitions > 0) {
                                    echo '</table></center><p>';
                                } 
                                echo "\n<center><table border=0 cellspacing=0 width=95% valign=top cellpadding=5><tr><td align=center bgcolor=\"$THEME->cellheading2\">";
                            }
                            if ($l == 'ALL' and $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS) {
                                if ($tab != GLOSSARY_DATE_VIEW) {
                                    echo "<b>$currentletter</b>";
                                } 
                            } 
    
                            if ($glossary->displayformat == GLOSSARY_FORMAT_SIMPLE) {
                                echo '</center></td></tr></table></center>';
                                    if ($dumpeddefinitions > 0) {
                                        echo '<center><table border=0 cellspacing=0 width=95% valign=top cellpadding=5>';
                                } 
                            } 
                        } 
                    }
                    $dumptoscreen = 1;
                } elseif ($l == 'SPECIAL' and ord($firstletter) != ord('Ñ') and 
                         (ord($firstletter) < ord('A') or ord($firstletter) > ord('Z'))) {
                    $dumptoscreen = 1;
                } 
            } else {
                if ($tab == GLOSSARY_CATEGORY_VIEW) {
                    if ($category) {   // if we are browsing a category
                            $dumptoscreen = 1;
                    } else { 
                        if ($cat == GLOSSARY_SHOW_NOT_CATEGORISED) { // Not categorized
                            if (! record_exists("glossary_entries_categories", "entryid", $entry->id)) {
                                $dumptoscreen = 1;
                            } 
                        } else { // All categories
                            if ($currentcategory != $entry->CID) {
                                if ($entry->approved or ($USER->id == $entry->userid and !isteacher($course->id)) or $tab == GLOSSARY_APPROVAL_VIEW) {
                                    $currentcategory = $entry->CID;
                                    if ($glossary->displayformat == GLOSSARY_FORMAT_SIMPLE) {
                                        if ($dumpeddefinitions > 0) {
                                            echo '</table></center><p>';
                                        } 
                                        echo "\n<center><table border=0 cellspacing=0 width=95% valign=top cellpadding=10><tr><td align=center bgcolor=\"$THEME->cellheading2\">";
                                    } 
                                    if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
                                        echo '<center>';
                                    }
                                    echo "<b>$entry->name</b>";
                                    if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
                                        echo '</center><p>';
                                    }
                                }
                            } 
                            $dumptoscreen = 1;
    
                            if ($glossary->displayformat == GLOSSARY_FORMAT_SIMPLE) {
                                echo '</center></td></tr></table></center>';
                                if ($dumpeddefinitions > 0) {
                                    echo '<center><table border=1 cellspacing=0 width=95% valign=top cellpadding=10>';
                                } 
                            } 
    
                            $dumptoscreen = 1;
                        } 
                    } 
                } else {
                    $dumptoscreen = 1;
                } 
            } 
    
            if ($dumptoscreen) {
                $dumpeddefinitions++;
    
                $concept = $entry->concept;
                $definition = $entry->definition;
    
                if ($dumpeddefinitions == 1) {
                    if ($glossary->displayformat == GLOSSARY_FORMAT_SIMPLE) {
                        echo '<center><table border=1 cellspacing=0 width=95% valign=top cellpadding=10>';
                    } 
                } 
                if ($search) {
                    $entry->concept = highlight($search, $concept);
                    $entry->definition = highlight($search, $definition);
                } 
    
                glossary_print_entry($course, $cm, $glossary, $entry, $tab, $cat);
                if ($glossary->displayformat != GLOSSARY_FORMAT_SIMPLE) {
                    echo '<p>';
                } 
            } 
        } 
    } 
    if (! $dumpeddefinitions) {
        print_simple_box_start("center", "70%", "$THEME->cellheading2");
        if (!$search) {
            echo "<center>$strnoentries</center>";
        } else {
            echo '<center>';
            print_string("searchhelp");
            echo '</center>';
        } 
        print_simple_box_end();
    } else {
        if ( $paging ) {
            echo "<table border=0><tr><td>$paging</td></tr></table>";
        }
        switch ($glossary->displayformat) {
            case GLOSSARY_FORMAT_CONTINUOUS:
                echo '</td></tr></table><p>';
            break;
            case GLOSSARY_FORMAT_SIMPLE:
                echo '</table></center><p>';
            break;
        } 
    } 
    
    echo '</center>';
    glossary_print_tabbed_table_end();

/// Finish the page
    print_footer($course);
    
?>
