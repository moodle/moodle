<?php   // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);                         // Course Module ID
    require_variable($mode,"letter");              // mode to show the entries
    optional_variable($hook,"ALL");                // what to show
    optional_variable($sortkey,"UPDATE");          // Sorting key 
    optional_variable($sortorder,"asc");           // Sorting order 
    optional_variable($offset);                    // number of entries to bypass

    print_header();

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 
    
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 
    
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
    global $CFG;
    if ( !$entriesbypage = $glossary->entbypage ) {
        $entriesbypage = $CFG->glossary_entbypage;
    }

    if ($CFG->forcelogin) {
        require_login();
    }

    if ($course->category) {
        require_login($course->id);    
        if (isguest()) {
            error("You must be logged to use this page.");
        } 
    }

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

    include_once("sql.php");
    
    $entriesshown = 0;
    $currentpivot = '';
    if ( $hook == 'SPECIAL' ) {
        $alphabet = explode(",", get_string("alphabet"));
    }
    $tableisopen = 0;

    $site = get_record("course","id",1);
    echo '<p align="right"><font size=-1>' . userdate(time()) . '</font></p>';
    echo '<strong>' . $site->fullname . '</strong><br>';
    echo get_string("course") . ': <strong>' . $course->fullname . '</strong><br />';
    echo get_string("modulename","glossary") . ': <strong>' . $glossary->name . '</strong><p>';
    if ( $allentries ) {
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
    
                if ( !$tableisopen ) {
                    echo '<table align="center" width="95%" bgcolor="#FFFFFF" style="border-style: solid; border-width: 1px;">';
                    $tableisopen = 1;
                }
                if ( $currentpivot != strtoupper($pivot) ) {  
                    // print the group break if apply
                    if ( $printpivot )  {
                        $currentpivot = strtoupper($pivot);
    
                        echo '<tr>';
                        $pivottoshow = $currentpivot;
                        if ( isset($entry->uid) ) {
                        // printing the user icon if defined (only when browsing authors)
                            echo '<td colspan="2" align="left" style="border-style: solid; border-width: 1px;">';
                            $user = get_record("user","id",$entry->uid);
                            $pivottoshow = fullname($user, isteacher($course->id));
                        } else {
                            echo '<td colspan="2" align="center" style="border-style: solid; border-width: 1px;">';
                        }
    
                        echo "<strong><i>$pivottoshow</i></strong>" ;
                        echo '</td>';
                        echo '</tr>';
                    }
                }
    
                echo '<tr>';
                echo '<td width="25%" align="right" valign="top"><b>'. $entry->concept . ': </b></td>';
                echo '<td width="75%" style="border-style: solid; border-width: 1px;">';
        
                if ( $entry->attachment) {
                    glossary_print_entry_attachment($entry);
                }
                echo strip_tags($entry->definition);
        
                echo '<br><br></tr>';
            }
        }
    }
    if ($tableisopen) {
        echo '</table>';
    }
    echo '<center><font size=-1>' . userdate(time()) . '</font></center>';

    echo '</body></html>';
?>
