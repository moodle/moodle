<?php   // $Id$

    global $CFG;

    require_once("../../config.php");
    require_once("lib.php");
    
    require_variable($id);                         // Course Module ID
    require_variable($mode,"letter");              // mode to show the entries
    optional_variable($hook,"ALL");                // what to show
    optional_variable($sortkey,"UPDATE");          // Sorting key 
    optional_variable($sortorder,"asc");           // Sorting order 
    optional_variable($offset);                    // number of entries to bypass
    optional_variable($displayformat,-1); 

    $mode = strip_tags(urldecode($mode));  //XSS
    $hook = strip_tags(urldecode($hook));  //XSS
    $sortkey = strip_tags(urldecode($sortkey));  //XSS

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    } 
    
    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    } 
    
    if (! $glossary = get_record("glossary", "id", $cm->instance)) {
        error("Course module is incorrect");
    } 
    
    if ( !$entriesbypage = $glossary->entbypage ) {
        $entriesbypage = $CFG->glossary_entbypage;
    }

    print_header_simple(strip_tags("$glossary->name"));

    if ($CFG->forcelogin) {
        require_login();
    }

    require_course_login($course);

/// setting the default values for the display mode of the current glossary
/// only if the glossary is viewed by the first time
    if ( $dp = get_record('glossary_formats','name', $glossary->displayformat) ) {
        $printpivot = $dp->showgroup;
        if ( $mode == '' and $hook == '' and $show == '') {
            $mode      = $dp->defaultmode;
            $hook      = $dp->defaulthook;
            $sortkey   = $dp->sortkey;
            $sortorder = $dp->sortorder;
        }
    } else {
        $printpivot = 1;
        if ( $mode == '' and $hook == '' and $show == '') {
            $mode = 'letter';
            $hook = 'ALL';
        }
    }

    if ( $displayformat == -1 ) {
         $displayformat = $glossary->displayformat;
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

    $site = get_record("course","id",1);
    echo '<p align="right"><font size=-1>' . userdate(time()) . '</font></p>';
    echo '<strong>' . $site->fullname . '</strong><br>';
    echo get_string("course") . ': <strong>' . $course->fullname . ' ('. $course->shortname . ')</strong><br />';
    echo get_string("modulename","glossary") . ': <strong>' . $glossary->name . '</strong><p>';
    if ( $allentries ) {
        foreach ($allentries as $entry) {
        /// Setting the pivot for the current entry
            $pivot = $entry->pivot;
            if ( !$fullpivot ) {
                $pivot = $pivot[0];
            }            
            
            if ( $currentpivot != strtoupper($pivot) ) {  
                // print the group break if apply
                if ( $printpivot )  {
                    $currentpivot = strtoupper($pivot);

                    $pivottoshow = $currentpivot;
                    if ( isset($entry->uid) ) {
                        $user = get_record("user","id",$entry->uid);
                        $pivottoshow = fullname($user, isteacher($course->id));
                    }

                    echo "<p align=\"center\"><strong><i>$pivottoshow</i></strong></p>" ;
                }
            }

            echo '<b>'. strip_tags($entry->concept) . ': </b>';
            $options->para = false;
            $definition = format_text('<nolink>' . strip_tags($entry->definition) . '</nolink>', $entry->format,$options);
    
            echo ($definition);
        
            echo '<br><br>';
        }
    }

    echo '</body></html>';
?>
