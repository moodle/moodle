<?php  // $Id$
/// This page prints a particular instance of glossary
    require_once("../../config.php");
    require_once("lib.php");
    require_once("$CFG->libdir/rsslib.php");

    $id = optional_param('id', 0, PARAM_INT);           // Course Module ID
    $g  = optional_param('g', 0, PARAM_INT);            // Glossary ID

    $tab  = optional_param('tab', GLOSSARY_NO_VIEW, PARAM_ALPHA);    // browsing entries by categories?
    $displayformat = optional_param('displayformat',-1, PARAM_INT);  // override of the glossary display format

    $mode       = optional_param('mode', '', PARAM_ALPHA);           // term entry cat date letter search author approval
    $hook       = optional_param('hook', '', PARAM_CLEAN);           // the term, entry, cat, etc... to look for based on mode
    $fullsearch = optional_param('fullsearch', 0,PARAM_INT);         // full search (concept and definition) when searching?
    $sortkey    = optional_param('sortkey', '', PARAM_ALPHA);// Sorted view: CREATION | UPDATE | FIRSTNAME | LASTNAME...
    $sortorder  = optional_param('sortorder', 'ASC', PARAM_ALPHA);   // it defines the order of the sorting (ASC or DESC)
    $offset     = optional_param('offset', 0,PARAM_INT);             // entries to bypass (for paging purposes)
    $page       = optional_param('page', 0,PARAM_INT);               // Page to show (for paging purposes)
    $show       = optional_param('show', '', PARAM_ALPHA);           // [ concept | alias ] => mode=term hook=$show

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('glossary', $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $glossary = get_record("glossary", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($g)) {
        if (! $glossary = get_record("glossary", "id", $g)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $glossary->course)) {
            error("Could not determine which course this belonged to!");
        }
        if (!$cm = get_coursemodule_from_instance("glossary", $glossary->id, $course->id)) {
            error("Could not determine which course module this belonged to!");
        }
        $id = $cm->id;
    } else {
        error("Must specify glossary ID or course module ID");
    }

    if ($CFG->forcelogin) {
        require_login();
    }

/// Loading the textlib singleton instance. We are going to need it.
    $textlib = textlib_get_instance();

/// redirecting if adding a new entry
    if ($tab == GLOSSARY_ADDENTRY_VIEW ) {
        redirect("edit.php?id=$cm->id&amp;mode=$mode");
    }

/// setting the defaut number of entries per page if not set

    if ( !$entriesbypage = $glossary->entbypage ) {
        $entriesbypage = $CFG->glossary_entbypage;
    }

/// If we have received a page, recalculate offset
    if ($page != 0 && $offset == 0) {
        $offset = $page * $entriesbypage;
    }

/// setting the default values for the display mode of the current glossary
/// only if the glossary is viewed by the first time
    if ( $dp = get_record('glossary_formats','name', addslashes($glossary->displayformat)) ) {
    /// Based on format->defaultmode, we build the defaulttab to be showed sometimes
        switch ($dp->defaultmode) {
            case 'cat':
                $defaulttab = GLOSSARY_CATEGORY_VIEW;
                break;
            case 'date':
                $defaulttab = GLOSSARY_DATE_VIEW;
                break;
            case 'author':
                $defaulttab = GLOSSARY_AUTHOR_VIEW;
                break;
            default:
                $defaulttab = GLOSSARY_STANDARD_VIEW;
        }
    /// Fetch the rest of variables
        $printpivot = $dp->showgroup;
        if ( $mode == '' and $hook == '' and $show == '') {
            $mode      = $dp->defaultmode;
            $hook      = $dp->defaulthook;
            $sortkey   = $dp->sortkey;
            $sortorder = $dp->sortorder;
        }
    } else {
        $defaulttab = GLOSSARY_STANDARD_VIEW;
        $printpivot = 1;
        if ( $mode == '' and $hook == '' and $show == '') {
            $mode = 'letter';
            $hook = 'ALL';
        }
    }

    if ( $displayformat == -1 ) {
         $displayformat = $glossary->displayformat;
    }

    if ( $show ) {
        $mode = 'term';
        $hook = $show;
        $show = '';
    }
/// Processing standard security processes
    $navigation = "";
    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
        require_login($course->id);
    }
    if (!$cm->visible and !isteacher($course->id)) {
        print_header();
        notice(get_string("activityiscurrentlyhidden"));
    }
    add_to_log($course->id, "glossary", "view", "view.php?id=$cm->id&amp;tab=$tab", $glossary->id, $cm->id);

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

        //Clean a bit the search string
        $hook = trim(strip_tags($hook));

    break;

    case 'entry':  /// Looking for a certain entry id
        $tab = GLOSSARY_STANDARD_VIEW;
        if ( $dp = get_record("glossary_formats","name", $glossary->displayformat) ) {
            $displayformat = $dp->popupformatname;
        }
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

    $navigation = "<a href=\"index.php?id=$course->id\">$strglossaries</a> ->";

    print_header_simple(format_string($glossary->name), "",
                 "$navigation ".format_string($glossary->name), "", "", true, update_module_button($cm->id, $course->id, $strglossary), navmenu($course, $cm));

    //If rss are activated at site and glossary level and this glossary has rss defined, show link
        if (isset($CFG->enablerssfeeds) && isset($CFG->glossary_enablerssfeeds) &&
            $CFG->enablerssfeeds && $CFG->glossary_enablerssfeeds && $glossary->rsstype && $glossary->rssarticles) {
            echo '<table width="100%" border="0" cellpadding="3" cellspacing="0"><tr valign="top"><td align="right">';
            $tooltiptext = get_string("rsssubscriberss","glossary",format_string($glossary->name,true));
            if (empty($USER->id)) {
                $userid = 0;
            } else {
                $userid = $USER->id;
            }
            rss_print_link($course->id, $userid, "glossary", $glossary->id, $tooltiptext);
            echo '</td></tr></table>';
        }


    /// the "Print" icon
    $printicon = '';
    if ( $isuserframe and $mode != 'search') {
        if (isteacher($course->id) or $glossary->allowprintview) {
            $printicon = " <a title =\"". get_string("printerfriendly","glossary") . "\" target=\"printview\" href=\"print.php?id=$cm->id&amp;mode=$mode&amp;hook=$hook&amp;sortkey=$sortkey&amp;sortorder=$sortorder&amp;offset=$offset\"><img border=\"0\" src=\"print.gif\" alt=\"\" /></a>";
        }
    }
    print_heading(format_string($glossary->name).$printicon);


/// Info box
    if ( $glossary->intro ) {
        print_simple_box(format_text($glossary->intro), 'center', '70%', '', 5, 'generalbox', 'intro');
    }

/// Search box

    echo '<form method="post" action="view.php">';

    echo '<table align="center" width="70%" border="0">';
    echo '<tr><td align="center" class="glossarysearchbox">';

    echo '<input type="submit" value="'.$strsearch.'" name="searchbutton" /> ';
    if ($mode == 'search') {
        echo '<input type="text" name="hook" size="20" value="'.s($hook).'" alt="'.$strsearch.'" /> ';
    } else {
        echo '<input type="text" name="hook" size="20" value="" alt="'.$strsearch.'" /> ';
    }
    if ($fullsearch || $mode != 'search') {
        $fullsearchchecked = 'checked="checked"';
    } else {
        $fullsearchchecked = '';
    }
    echo '<input type="checkbox" name="fullsearch" value="1" '.$fullsearchchecked.' alt="'.$strsearchindefinition.'" />';
    echo '<input type="hidden" name="mode" value="search" />';
    echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
    echo $strsearchindefinition;
    echo '</td></tr></table>';

    echo '</form>';
    echo '<br />';

    include("tabs.html");

    include_once("sql.php");

/// printing the entries
    $entriesshown = 0;
    $currentpivot = '';
    $ratingsmenuused = NULL;
    $paging = NULL;

    if ($allentries) {

        //Decide if we must show the ALL link in the pagebar
        $specialtext = '';
        if ($glossary->showall) {
            $specialtext = get_string("allentries","glossary");
        }

        //Build paging bar
        $paging = glossary_get_paging_bar($count, $page, $entriesbypage, "view.php?id=$id&mode=$mode&hook=$hook&sortkey=$sortkey&sortorder=$sortorder&fullsearch=$fullsearch&",9999,10,'&nbsp;&nbsp;', $specialtext, -1);

        echo '<div class="paging">';
        echo $paging;
        echo '</div>';

        $ratings = NULL;
        $ratingsmenuused = false;
        if ($glossary->assessed and !empty($USER->id)) {
            if ($ratings->scale = make_grades_menu($glossary->scale)) {
                $ratings->assesstimestart = $glossary->assesstimestart;
                $ratings->assesstimefinish = $glossary->assesstimefinish;
            }
            if ($glossary->assessed == 2 and !isteacher($course->id)) {
                $ratings->allow = false;
            } else {
                $ratings->allow = true;
            }

            echo "<form name=\"form\" method=\"post\" action=\"rate.php\">";
            echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
        }

        foreach ($allentries as $entry) {

            // Setting the pivot for the current entry
            $pivot = $entry->pivot;
            $upperpivot = $textlib->strtoupper($pivot, current_charset());
            // Reduce pivot to 1cc if necessary
            if ( !$fullpivot ) {
                $upperpivot = $textlib->substr($upperpivot, 0, 1, current_charset());
            }            
            
            // if there's a group break
            if ( $currentpivot != $upperpivot ) {

                // print the group break if apply
                if ( $printpivot )  {
                    $currentpivot = $upperpivot;

                    echo '<div>';
                    echo '<table cellspacing="0" class="categoryheader">';

                    echo '<tr>';
                    $pivottoshow = $currentpivot;
                    if ( isset($entry->uid) ) {
                    // printing the user icon if defined (only when browsing authors)
                        echo '<td align="left">';

                        $user = get_record("user","id",$entry->uid);
                        print_user_picture($user->id, $course->id, $user->picture);
                        $pivottoshow = fullname($user, isteacher($course->id));;
                    } else {
                        echo '<td align="center">';
                    }

                    echo "<strong> $pivottoshow</strong>" ;
                    echo '</td></tr></table></div>';

                }
            }

            $concept = $entry->concept;
            $definition = $entry->definition;

            /// highlight the term if necessary
            if ($mode == 'search') {
                //We have to strip any word starting by + and take out words starting by -
                //to make highlight works properly
                $searchterms = explode(' ', $hook);    // Search for words independently
                foreach ($searchterms as $key => $searchterm) {
                    if (preg_match('/^\-/',$searchterm)) {
                        unset($searchterms[$key]);
                    } else {
                        $searchterms[$key] = preg_replace('/^\+/','',$searchterm);
                    }
                    //Avoid highlight of <2 len strings. It's a well known hilight limitation.
                    if (strlen($searchterm) < 2) {
                        unset($searchterms[$key]);
                    }
                }
                $strippedsearch = implode(' ', $searchterms);    // Rebuild the string
                $entry->highlight = $strippedsearch;
            }

            /// and finally print the entry.

            if ( glossary_print_entry($course, $cm, $glossary, $entry, $mode, $hook,1,$displayformat,$ratings) ) {
                $ratingsmenuused = true;
            }

            $entriesshown++;
        }
    }
    if ( !$entriesshown ) {
        print_simple_box('<center>' . get_string("noentries","glossary") . '</center>',"center","95%");
    }


    if ($ratingsmenuused) {
        echo "<center><input type=\"submit\" value=\"".get_string("sendinratings", "glossary")."\" />";
        if ($glossary->scale < 0) {
            if ($scale = get_record("scale", "id", abs($glossary->scale))) {
                print_scale_menu_helpbutton($course->id, $scale );
            }
        }
        echo "</center>";
        echo "</form>";
    }

    if ( $paging ) {
        echo '<hr />';
        echo '<div class="paging">';
        echo $paging;
        echo '</div>';
    }
    echo '<br />';
    echo '</center>';
    glossary_print_tabbed_table_end();
    if ( !empty($debug) and isadmin() ) {
        echo '<p>';
        print_simple_box("$sqlselect<br /> $sqlfrom<br /> $sqlwhere<br /> $sqlorderby<br /> $sqllimit","center","85%");

        echo "<p align=\"right\"><font size=\"-3\">";
        echo microtime_diff($CFG->startpagetime, microtime());
        echo "</font></p>";
    }

/// Finish the page

    print_footer($course);

?>
