<?php
/// This page prints a particular instance of glossary
    require_once("../../config.php");
    require_once("lib.php");
    $debug = 0;
        
    require_variable($id);           // Course Module ID
    optional_variable($tab,GLOSSARY_NO_VIEW); // browsing entries by categories?

    optional_variable($mode,"letter");  // [ "term"   | "entry"  | "cat"     | "date" | 
                                        //   "letter" | "search" | "author"  | "approval" ]
    optional_variable($hook,"");  // the term, entry, cat, etc... to look for based on mode

    optional_variable($fullsearch,0); // full search (concept and definition) when searching?

    optional_variable($sortkey,"");    // Sorted view: 
                                       //    [ CREATION | UPDATE | FIRSTNAME | LASTNAME |
                                       //      concept | timecreated | ... ]
    optional_variable($sortorder,"");  // it defines the order of the sorting (ASC or DESC)

    optional_variable($offset,0);    // entries to bypass (for paging purpouses)

    optional_variable($show,"");     // [ concept | alias ] => mode=term hook=$show

    if ( $show ) {
        $mode = 'term';
        $hook = $show;
        $show = '';
    }
    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }     
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }     
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
/// redirecting if adding a new entry
    if ($tab == GLOSSARY_ADDENTRY_VIEW ) {
        redirect("edit.php?id=$cm->id&mode=$mode");
    }

/// setting the defaut number of entries per page if not set
    global $CFG, $THEME, $USER;
    
    if ( !$entriesbypage = $glossary->entbypage ) {
        $entriesbypage = 10;
    }

/// setting the right fram for a "Continuous" glossary
    if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
        $mode = 'date';
    }

/// Processing standard security processes
    $navigation = "";
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        require_login($course->id);
    }
    if (!$cm->visible and !isteacher($course->id)) {
        notice(get_string("activityiscurrentlyhidden"));
    } 
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id&tab=$tab", "$glossary->id");

/// stablishing flag variables
    if ( $sortorder = strtolower($sortorder) ) {
        if ($sortorder != 'asc' and $sortorder != 'desc') {
            $sortorder = '';
        }
    }
    if ( $sortkey = strtoupper($sortkey) ) {
        if ($sortkey != 'CREATION' and 
            $sortkey != 'UPDATE' and 
            $sortkey != 'FIRSTNAME' and 
            $sortkey != 'LASTNAME'
            ) {
            $sortkey = '';
        }
    }

    switch ( $mode = strtolower($mode) ) {
    case 'search': /// looking for terms containing certain word(s)
        $tab = GLOSSARY_STANDARD_VIEW;

        $searchterms = explode(' ', $hook); // Search for words independently
        foreach ($searchterms as $key => $searchterm) {
            if (strlen($searchterm) < 2) {
                unset($searchterms[$key]);
            } 
        } 
        $hook = trim(implode(' ', $searchterms));
    break;
    
    case 'entry':  /// Looking for a certain entry id
        $tab = GLOSSARY_STANDARD_VIEW;
    break;
    
    case 'cat':    /// Looking for a certain cat
        $tab = GLOSSARY_CATEGORY_VIEW;
        if ( $hook > 0 ) {
            $category = get_record("glossary_categories","id",$hook);
        }
    break;

    case 'approval':    /// Looking for entries waiting for approval
        $tab = GLOSSARY_APPROVAL_VIEW;
        if ( !$hook and !$sortkey and !$sortorder) {
            $hook = 'ALL';
        }
    break;

    case 'term':   /// Looking for entries that include certain term in its concept, definition or aliases
        $tab = GLOSSARY_STANDARD_VIEW;
    break;

    case 'date':
        $tab = GLOSSARY_DATE_VIEW;
        if ( !$sortkey ) {
            $sortkey = 'UPDATE';
        } 
        if ( !$sortorder ) {
            $sortorder = 'desc';
        }
    break;
    
    case 'author':  /// Looking for entries, browsed by author
        $tab = GLOSSARY_AUTHOR_VIEW;
        if ( !$hook ) {
            $hook = 'ALL';
        } 
        if ( !$sortkey ) {
            $sortkey = 'FIRSTNAME';
        } 
        if ( !$sortorder ) {
            $sortorder = 'asc';
        }
    break;

    case 'letter':  /// Looking for entries that begin with a certain letter, ALL or SPECIAL characters
    default:
        $tab = GLOSSARY_STANDARD_VIEW;
        if ( !$hook ) {
            $hook = 'ALL';  
        } 
    break;
    }  

    switch ( $tab ) {
    case GLOSSARY_IMPORT_VIEW: 
    case GLOSSARY_EXPORT_VIEW: 
    case GLOSSARY_APPROVAL_VIEW:
        $isuserframe = 0;
    break;
    
    default:
        $isuserframe = 1;
    break;
    }

/// Printing the heading
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
    if ($isuserframe ) {
    /// the "Print" icon
        echo " <a title =\"". get_string("printerfriendly","glossary") . "\" target=\"_blank\" href=\"print.php?id=$cm->id&tab=$tab&mode=$mode&hook=$hook&sortkey=$sortkey&sortorder=$sortorder\">";
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
    echo '<input type="text" name="hook" size="20" value=""> ';
    echo '<input type="checkbox" name="fullsearch" value="1">';
    echo '<input type="hidden" name="mode" value="search">';
    echo '<input type="hidden" name="id" value="'.$cm->id.'">';
    echo $strsearchindefinition;
    echo '</form>';
    echo '</p>';
    print_simple_box_end();    

    include("tabs.html");

    switch ( $sortkey ) {    
    case "CREATION": 
        $sortkey = "timecreated";
    break;
    
    case "UPDATE": 
        $sortkey = "timemodified";
    default:
    break;
    }
    
/// Creating the SQL statements

/// Pivot is the field that set the break by groups (category, initial, author name, etc)

/// fullpivot indicate if the whole pivot should be compared agasint the db or just the first letter
/// printpivot indicate if the pivot should be printed or not
    $fullpivot = 1;
    $printpivot = 1;

//    global $db;
//    $db->debug = true;

    switch ($tab) {
    case GLOSSARY_CATEGORY_VIEW:
        if ($hook == GLOSSARY_SHOW_ALL_CATEGORIES  ) { 

            $sqlselect = "SELECT gec.id, gc.name pivot, ge.*";
            $sqlfrom   = "FROM {$CFG->prefix}glossary_entries ge,
                         {$CFG->prefix}glossary_entries_categories gec,
                         {$CFG->prefix}glossary_categories gc";
            $sqlwhere  = "WHERE (ge.glossaryid = '$glossary->id' OR ge.sourceglossaryid = '$glossary->id') AND
                          ge.id = gec.entryid AND gc.id = gec.categoryid AND
                          (ge.approved != 0 OR ge.userid = $USER->id)";

            if ( $glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS ) {
                $sqlorderby = ' ORDER BY gc.name, ge.timecreated';
            } else {
                $sqlorderby = ' ORDER BY gc.name, ge.concept';
            }

        } elseif ($hook == GLOSSARY_SHOW_NOT_CATEGORISED ) { 

            $printpivot = 0;
            $sqlselect = "SELECT concept pivot, ge.*";
            $sqlfrom   = "FROM {$CFG->prefix}glossary_entries ge";
            $sqlwhere  = "WHERE (glossaryid = '$glossary->id' OR sourceglossaryid = '$glossary->id') AND
                          (ge.approved != 0 OR ge.userid = $USER->id)";


            $sqlorderby = ' ORDER BY concept';

        } else {

            $printpivot = 0;
            $sqlselect  = "SELECT ce.id, c.name pivot, ge.*";
            $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}glossary_entries_categories ce, {$CFG->prefix}glossary_categories c";
            $sqlwhere   = "WHERE ge.id = ce.entryid AND ce.categoryid = $hook AND
                                 ce.categoryid = c.id AND ge.approved != 0 AND
                                 (ge.glossaryid = $glossary->id OR ge.sourceglossaryid = $glossary->id) AND
                          (ge.approved != 0 OR ge.userid = $USER->id)";

            $sqlorderby = ' ORDER BY c.name, ge.concept';

        }
        $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
        $sqllimit = " LIMIT $offset, $entriesbypage";
        $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
    break;
    case GLOSSARY_AUTHOR_VIEW:

        $where = '';
        switch ($CFG->dbtype) {
        case 'postgres7':
            $usernametoshow = "u.firstname || ' ' || u.lastname";
            if ( $sortkey == 'FIRSTNAME' ) {
                $usernamefield = "u.firstname || ' ' || u.lastname";
            } else {
                $usernamefield = "u.lastname || ' ' || u.firstname";
            }
            $where = "AND substr(ucase($usernamefield),1," .  strlen($hook) . ") = '" . strtoupper($hook) . "'";
        break;
        case 'mysql':
            $usernametoshow = "CONCAT(CONCAT(u.firstname,' '), u.lastname)";
            if ( $sortkey == 'FIRSTNAME' ) {
                $usernamefield = "CONCAT(CONCAT(u.firstname,' '), u.lastname)";
            } else {
                $usernamefield = "CONCAT(CONCAT(u.lastname,' '), u.firstname)";
            }
            $where = "AND left(ucase($usernamefield)," .  strlen($hook) . ") = '$hook'";
        break;
        }
        if ( $hook == 'ALL' ) {
            $where = '';
        }

        $sqlselect  = "SELECT ge.id, $usernamefield pivot, $usernametoshow uname, u.id uid, ge.*";
        $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge, {$CFG->prefix}user u";
        $sqlwhere   = "WHERE ge.userid = u.id  AND
                             ge.approved != 0
                             $where AND 
                             (ge.glossaryid = $glossary->id OR ge.sourceglossaryid = $glossary->id)";
        $sqlorderby = "ORDER BY $usernamefield $sortorder, ge.concept";

        $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
        $sqllimit = " LIMIT $offset, $entriesbypage";
        $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
    break;
    case GLOSSARY_APPROVAL_VIEW:
        $fullpivot = 0;
        $printpivot = 0;

        $where = '';
        if ($hook != 'ALL' and $hook != 'SPECIAL') {
            switch ($CFG->dbtype) {
            case 'postgres7':
                $where = 'AND substr(ucase(concept),1,' .  strlen($hook) . ') = \'' . strtoupper($hook) . '\'';
            break;
            case 'mysql':
                $where = 'AND left(ucase(concept),' .  strlen($hook) . ") = '$hook'";
            break;
            }
        }

        $sqlselect  = "SELECT ge.concept pivot, ge.*";
        $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge";
        $sqlwhere   = "WHERE (ge.glossaryid = $glossary->id OR ge.sourceglossaryid = $glossary->id) AND
                             ge.approved = 0 $where";
                             
        if ( $sortkey ) {
            $sqlorderby = "ORDER BY $sortkey $sortorder";
        } else {
            $sqlorderby = "ORDER BY ge.concept";
        }
        
        $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
        $sqllimit   = " LIMIT $offset, $entriesbypage";
        $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");
    break;
    case GLOSSARY_DATE_VIEW:
    case GLOSSARY_STANDARD_VIEW:
    default:
        $sqlselect  = "SELECT ge.concept pivot, ge.*";
        $sqlfrom    = "FROM {$CFG->prefix}glossary_entries ge";

        $where = '';
        $fullpivot = 0;
        if ($CFG->dbtype == "postgres7") {
            $LIKE = "ILIKE";   // case-insensitive
        } else {
            $LIKE = "LIKE";
        }

        switch ( $mode ) {
        case 'search': 
            $printpivot = 0;
            $where = "AND ( ge.concept $LIKE '%$hook%'";
            if ( $fullsearch ) {
                $where .= "OR ge.definition $LIKE '%$hook%')";
            } else {
                $where .= ")";
            }
        break;
        
        case 'term': 
            $printpivot = 0;
            $sqlfrom .= ", {$CFG->prefix}glossary_alias ga";
            $where = "AND ge.id = ga.entryid AND                            
                          (ge.concept = '$hook' OR ga.alias = '$hook' )
                     ";
//            $where = "AND ge.id = ga.entryid AND (
//                           (ge.casesensitive != 0 and ( ge.concept LIKE BINARY '$hook' OR ga.alias LIKE BINARY '$hook' ) ) or
//                           (ge.casesensitive = 0 and ( ucase(ge.concept) = ucase('$hook') OR ucase(ga.alias) = ucase('$hook') ) )
//                       )";
        break;

        case 'entry': 
            $printpivot = 0;
            $where = "AND ge.id = $hook";
        break;

        case 'letter': 
            if ($hook != 'ALL' and $hook != 'SPECIAL') {
                switch ($CFG->dbtype) {
                case 'postgres7':
                    $where = 'AND substr(ucase(concept),1,' .  strlen($hook) . ') = \'' . strtoupper($hook) . '\'';
                break;
                case 'mysql':
                    $where = 'AND left(ucase(concept),' .  strlen($hook) . ") = '$hook'";
                break;
                }
            }
        break;
        }
        
        $sqlwhere   = "WHERE (ge.glossaryid = $glossary->id or ge.sourceglossaryid = $glossary->id) AND
                             (ge.approved != 0 OR ge.userid = $USER->id)
                              $where";
        switch ( $tab ) {
        case GLOSSARY_DATE_VIEW: 
            $sqlorderby = "ORDER BY $sortkey $sortorder";
        break;
        
        case GLOSSARY_STANDARD_VIEW: 
            $sqlorderby = "ORDER BY ge.concept";
        default:
        break;
        }

        $count = count_records_sql("select count(*) $sqlfrom $sqlwhere");
        $sqllimit   = " LIMIT $offset, $entriesbypage";
        $allentries = get_records_sql("$sqlselect $sqlfrom $sqlwhere $sqlorderby $sqllimit");

    break;
    } 
/*    
    print_simple_box_start("center","85%");
    print_object($allentries);
    print_simple_box_end();
    $db->debug=false;
*/

/// printing the entries

    $entriesshown = 0;
    $currentpivot = '';
    if ( $hook == 'SPECIAL' ) {
        $alphabet = explode(",", get_string("alphabet"));
    }
    if ($allentries) {
        /// printing the paging links
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
                    $paging .= "<a href=\"view.php?id=$id&mode=$mode&hook=$hook&offset=" . ($i*$entriesbypage) . "\">" . ($i+1) . '</a>';
                }
            }
            $paging  = "<font size=1><center>" . get_string ("jumpto") . " $paging</center></font>";
        }
        echo "$paging";
        glossary_debug($debug,'<div align=right><font size=1>SELECT normal:' . count($allentries) . '</font></div>',0);
        glossary_debug($debug,'<div align=right><font size=1>SELECT count(*):' . $count . '</font></div>',0);

        if ($glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS) {
            $printpivot = 0;
        }
        
        foreach ($allentries as $entry) {
        /// Setting the pivot for the current entry
            $pivot = $entry->pivot;
            if ( !$fullpivot ) {
                $pivot = $pivot[0];
            }            
            
        /// 
        /// Validating special cases not covered by the SQL statement
        /// 

        /// if we're browsing by alphabet and the current concept does not begin with
        ///     the letter we are look for.
            $showentry = 1;
            if ( $mode == 'letter' and $hook != 'SPECIAL' and $hook != 'ALL' ) {
                if ( substr($entry->concept, 0, strlen($hook)) != $hook ) {
                    $showentry = 0;
                }
            } 
            
        /// if we're browsing for letter, looking for special characters not covered
        ///     in the alphabet 
            if ( $showentry and $hook == 'SPECIAL' ) {
                $initial = $entry->concept[0];
                for ($i = 0; $i < count($alphabet); $i++) {
                    $curletter = $alphabet[$i];
                    if ( $curletter == $initial ) {

                        $showentry = 0;
                        break;
                    }
                }
            } 

        /// if we're browsing categories, looking for entries not categorised.
            if ( $showentry and $mode == 'cat' and $hook == GLOSSARY_SHOW_NOT_CATEGORISED ) {
                if ( record_exists("glossary_entries_categories", "entryid", $entry->id)) {
                    $showentry = 0;
                } 
            }

        /// if the entry is not approved, deal with it based on the current view and
        ///     user.
            if ( $showentry and $mode != 'approval' ) {
                if ( !$entry->approved and isteacher($course->id, $entry->userid) ) {
                    $showentry = 0;
                }            
            }

            /// ok, if it's a valid entry.. Print it.
            if ( $showentry ) {
            
                /// if there's a group break
                if ( $currentpivot != strtoupper($pivot) ) {  

                    // print the group break if apply
                    if ( $printpivot )  {
                        if ( $tableisopen ) {
                            print_simple_box_end();
                            $tableisopen = 0;
                        }
                        $currentpivot = strtoupper($pivot);

                        echo '<p>';
                        echo '<table width="95%" border="0" class="generaltabselected" bgcolor="' . $THEME->cellheading2 . '">';

                        echo '<tr>';
                        $pivottoshow = $currentpivot;
                        if ( isset($entry->uid) ) {
                        // printing the user icon if defined (only when browsing authors)
                            echo '<td align="left">';
                            
                            $user = get_record("user","id",$entry->uid);
                            print_user_picture($user->id, $course->id, $user->picture);
                            $pivottoshow = $entry->uname;
                        } else {
                            echo '<td align="center">';
                        }

                        echo "<strong> $pivottoshow</strong>" ;
                        echo '</td></tr></table>';

                        if ($glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS OR 
                            $glossary->displayformat == GLOSSARY_FORMAT_SIMPLE ) {
                            print_simple_box_start("center","95%","#ffffff","5","generalbox");
                            $tableisopen = 1;
                        }
                    }
                }
                
                if ( !$tableisopen ) {
                    if ($glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS OR 
                        $glossary->displayformat == GLOSSARY_FORMAT_SIMPLE) {
                        print_simple_box_start("center","95%","#ffffff","5","generalbox");
                        $tableisopen = 1;
                    }
                }

                $concept = $entry->concept;
                $definition = $entry->definition;
    
                /// highligh the term if necessary
                if ($mode == 'search') {
                    $entry->concept = highlight($hook, $concept);
                    $entry->definition = highlight($hook, $definition);
                } 

                /// and finally print the entry.
                glossary_print_entry($course, $cm, $glossary, $entry, $mode, $hook);
                $entriesshown++;
//                echo '<p>';
            }
        }
        if ( $tableisopen ) {
            if ($glossary->displayformat == GLOSSARY_FORMAT_CONTINUOUS OR 
                $glossary->displayformat == GLOSSARY_FORMAT_SIMPLE ) {
                print_simple_box_end();
                $tableisopen = 0;
            }
        }
    }
    if ( !$entriesshown ) {
        print_simple_box('<center>' . get_string("noentries","glossary") . '</center>',"center","95%");
    }

    echo '</center>';
    glossary_print_tabbed_table_end();
    if ( $debug ) {
        echo '<p>';
        print_simple_box("$sqlselect<br> $sqlfrom<br> $sqlwhere<br> $sqlorderby<br> $sqllimit","center","85%");
    }

/// Finish the page
    print_footer($course);
?>