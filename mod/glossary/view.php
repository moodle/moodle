<?php // $Id$

/// This page prints a particular instance of glossary
    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);           // Course Module ID
    optional_variable($l,"");           // letter to look for
    optional_variable($eid);         // Entry ID
    optional_variable($search, "");  // search string
    optional_variable($includedefinition); // include definition in search function?
    
    optional_variable($tab); // browsing entries by categories?
    optional_variable($cat);         // categoryID

    optional_variable($sortkey,"");  // Sorted view: CREATION or UPDATE
    optional_variable($sortorder,"");  // it define the order of the sorting (ASC or DESC)

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
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
        $search = '';
    } 

    $alphabet = explode('|', get_string("alphabet","glossary"));
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
    echo ' <a title ="' . get_string("printerfriendly","glossary") . '" target="_blank" href="print.php?id=' . $cm->id . '&tab=' . $tab . '&sortkey=' . $sortkey . '&sortorder=' . $sortorder . '">';
    echo '<img border=0 src="print.gif"/></a>';
    echo '</b></font></p>';

/// Info box

    if ( $glossary->intro ) {
        print_simple_box_start('center','70%');
        echo format_text($glossary->intro);
        print_simple_box_end();
    }

/// Add buttons

    if (!isguest()) {
        if ( isteacher($course->id) or $glossary->studentcanpost  ) {
            unset($options);
            $options = array ("id" => "$cm->id");
            echo '<center><p>';
            print_single_button("edit.php", $options, $straddentry);
            echo '</p></center>';
        }
    } 

/*
    unset($options);
    $options = array ("id" => "$cm->id");
    echo '<center><p>';
    print_single_button("export.php", $options, get_string("exportglossary","glossary"));
    echo '</p></center>';
*/

/// Search box

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
    echo '<p align="center">';
    $glossary_tCFG->TabTableBGColor = $THEME->cellheading;
    $glossary_tCFG->ActiveTabColor = $THEME->cellheading;
    $glossary_tCFG->InactiveTabColor = $THEME->cellcontent2;
    $glossary_tCFG->InactiveFontColor= $THEME->hidden;
    $glossary_tCFG->TabTableWidth = "70%";
    $glossary_tCFG->TabsPerRow = 4;
    $glossary_tCFG->TabSeparation = 4;

    $data[GLOSSARY_STANDARD_VIEW]->caption = get_string("standardview", "glossary");
    $data[GLOSSARY_CATEGORY_VIEW]->caption = get_string("categoryview", "glossary");
    $data[GLOSSARY_DATE_VIEW]->caption = get_string("dateview", "glossary");

    $data[GLOSSARY_DATE_VIEW]->link = "view.php?id=$id&tab=".GLOSSARY_DATE_VIEW;
    if ( $glossary->displayformat != GLOSSARY_FORMAT_CONTINUOUS ) {
        $data[GLOSSARY_STANDARD_VIEW]->link = "view.php?id=$id";
        $data[GLOSSARY_CATEGORY_VIEW]->link = "view.php?id=$id&tab=".GLOSSARY_CATEGORY_VIEW;
    }

    if (isteacher($course->id)) {
        $data[GLOSSARY_APPROVAL_VIEW]->caption = get_string("waitingapproval", "glossary");
        $data[GLOSSARY_APPROVAL_VIEW]->link = "";

        $hiddenentries = get_records_select("glossary_entries","glossaryid  = $glossary->id and approved = 0");
        if ($hiddenentries) {
            $data[GLOSSARY_APPROVAL_VIEW]->caption .= "<br><font size=1>(" . count($hiddenentries) . " " . get_string("entries","glossary") . ")</font>";
            $data[GLOSSARY_APPROVAL_VIEW]->link = "view.php?id=$id&tab=".GLOSSARY_APPROVAL_VIEW;
        } elseif ( $tab == GLOSSARY_APPROVAL_VIEW ) {
            $tab = $framebydefault;
        }
    } elseif ( $tab == GLOSSARY_APPROVAL_VIEW ) {
        $tab = $framebydefault;
    }

/// printing header of the current tab
    echo '<center>';
    glossary_print_tabbed_table_start($data, $tab, $glossary_tCFG);
    switch ($tab) {
        case GLOSSARY_CATEGORY_VIEW:
            glossary_print_categories_menu($course, $cm, $glossary, $cat, $category);
        break;
        case GLOSSARY_APPROVAL_VIEW:
            glossary_print_approval_menu($cm, $glossary, $l, $sortkey, $sortorder,$tab);
        break;
        case GLOSSARY_DATE_VIEW:
            if (!$sortkey) {
                $sortkey = 'UPDATE';
            }
            if (!$sortorder) {
                $sortorder = 'desc';
            }
        case GLOSSARY_STANDARD_VIEW:
        default:
            glossary_print_alphabet_menu($cm, $glossary, $l, $sortkey, $sortorder,$tab);
            if ($search) {
                echo "<h3>$strsearch: $search</h3>";
            } 
        break;
    } 
    echo '<hr>';
    
/// Printing the entries

    switch ($sortkey) {
        case 'CREATION':
            $orderby = "timecreated $sortorder";
        break;
        case 'UPDATE':
            $orderby = "timemodified $sortorder";
        break;
		default:
            $orderby = "$sortkey $sortorder";
    }
    
    switch ($tab) {
        case GLOSSARY_CATEGORY_VIEW:
            if ($cat == GLOSSARY_SHOW_ALL_CATEGORIES) { 
                $sql = "SELECT gec.id gecid, gc.name, gc.id CID, ge.*
                        FROM {$CFG->prefix}glossary_entries ge,
                             {$CFG->prefix}glossary_entries_categories gec,
                             {$CFG->prefix}glossary_categories gc
                        WHERE (ge.glossaryid = '$glossary->id' or ge.sourceglossaryid = '$glossary->id') AND
                              gec.entryid = ge.id AND
                              gc.id = gec.categoryid";

                if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
                    $sql .= ' ORDER BY gc.name, ge.timecreated';
                } else {
                    $sql .= ' ORDER BY gc.name, ge.concept';
                }
                $allentries = get_records_sql($sql);
            } else {
                if ( $cat == GLOSSARY_SHOW_NOT_CATEGORISED ) {
                    $allentries = glossary_get_entries_sorted($glossary, '',$orderby);
                } else {
                    $allentries = glossary_get_entries_by_category($glossary, $cat, '',$orderby);
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
                            $where = 'substr(ucase(concept),1,' .  strlen($l) . ') = \'' . strtoupper($l) . '\'';
                        break;
                        case 'mysql':
                            $where = 'left(ucase(concept),' .  strlen($l) . ") = '$l'";
                        break;
                        default:
                            $where = '';
                    }
                }
                $allentries = glossary_get_entries_sorted($glossary, $where,$orderby);
            }
            $currentletter = '';
        break;
    } 
    
    $dumpeddefinitions = 0;
    if ($allentries) {
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
                                echo "<b>$currentletter</b>";
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
