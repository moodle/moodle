<?php

/// This page prints a particular instance of glossary
require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');
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
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $glossary = $DB->get_record("glossary", array("id"=>$cm->instance))) {
        print_error('invalidid', 'glossary');
    }

} else if (!empty($g)) {
    if (! $glossary = $DB->get_record("glossary", array("id"=>$g))) {
        print_error('invalidid', 'glossary');
    }
    if (! $course = $DB->get_record("course", array("id"=>$glossary->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("glossary", $glossary->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
    $id = $cm->id;
} else {
    print_error('invalidid', 'glossary');
}
$cm = cm_info::create($cm);

require_course_login($course->id, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/glossary:view', $context);

// Prepare format_string/text options
$fmtoptions = array(
    'context' => $context);

require_once($CFG->dirroot . '/comment/lib.php');
comment::init();

/// redirecting if adding a new entry
if ($tab == GLOSSARY_ADDENTRY_VIEW ) {
    redirect("edit.php?cmid=$cm->id&amp;mode=$mode");
}

/// setting the defaut number of entries per page if not set
if ( !$entriesbypage = $glossary->entbypage ) {
    $entriesbypage = $CFG->glossary_entbypage;
}

// If we have received a page, recalculate offset and page size.
$pagelimit = $entriesbypage;
if ($page > 0 && $offset == 0) {
    $offset = $page * $entriesbypage;
} else if ($page < 0) {
    $offset = 0;
    $pagelimit = 0;
}

/// setting the default values for the display mode of the current glossary
/// only if the glossary is viewed by the first time
if ( $dp = $DB->get_record('glossary_formats', array('name'=>$glossary->displayformat)) ) {
/// Based on format->defaultmode, we build the defaulttab to be showed sometimes
    $showtabs = glossary_get_visible_tabs($dp);
    switch ($dp->defaultmode) {
        case 'cat':
            $defaulttab = GLOSSARY_CATEGORY_VIEW;

            // Handle defaultmode if 'category' tab is disabled. Fallback to 'standard' tab.
            if (!in_array(GLOSSARY_CATEGORY, $showtabs)) {
                $defaulttab = GLOSSARY_STANDARD_VIEW;
            }

            break;
        case 'date':
            $defaulttab = GLOSSARY_DATE_VIEW;

            // Handle defaultmode if 'date' tab is disabled. Fallback to 'standard' tab.
            if (!in_array(GLOSSARY_DATE, $showtabs)) {
                $defaulttab = GLOSSARY_STANDARD_VIEW;
            }

            break;
        case 'author':
            $defaulttab = GLOSSARY_AUTHOR_VIEW;

            // Handle defaultmode if 'author' tab is disabled. Fallback to 'standard' tab.
            if (!in_array(GLOSSARY_AUTHOR, $showtabs)) {
                $defaulttab = GLOSSARY_STANDARD_VIEW;
            }

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
    $showtabs = array($defaulttab);
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
    if ( $dp = $DB->get_record("glossary_formats", array("name"=>$glossary->displayformat)) ) {
        $displayformat = $dp->popupformatname;
    }
break;

case 'cat':    /// Looking for a certain cat
    $tab = GLOSSARY_CATEGORY_VIEW;

    // Validation - we don't want to display 'category' tab if it is disabled.
    if (!in_array(GLOSSARY_CATEGORY, $showtabs)) {
        $tab = GLOSSARY_STANDARD_VIEW;
    }

    if ( $hook > 0 ) {
        $category = $DB->get_record("glossary_categories", array("id"=>$hook));
    }
break;

case 'approval':    /// Looking for entries waiting for approval
    $tab = GLOSSARY_APPROVAL_VIEW;
    // Override the display format with the approvaldisplayformat
    if ($glossary->approvaldisplayformat !== 'default' && ($df = $DB->get_record("glossary_formats",
            array("name" => $glossary->approvaldisplayformat)))) {
        $displayformat = $df->popupformatname;
    }
    if ( !$hook and !$sortkey and !$sortorder) {
        $hook = 'ALL';
    }
break;

case 'term':   /// Looking for entries that include certain term in its concept, definition or aliases
    $tab = GLOSSARY_STANDARD_VIEW;
break;

case 'date':
    $tab = GLOSSARY_DATE_VIEW;

    // Validation - we dont want to display 'date' tab if it is disabled.
    if (!in_array(GLOSSARY_DATE, $showtabs)) {
        $tab = GLOSSARY_STANDARD_VIEW;
    }

    if ( !$sortkey ) {
        $sortkey = 'UPDATE';
    }
    if ( !$sortorder ) {
        $sortorder = 'desc';
    }
break;

case 'author':  /// Looking for entries, browsed by author
    $tab = GLOSSARY_AUTHOR_VIEW;

    // Validation - we dont want to display 'author' tab if it is disabled.
    if (!in_array(GLOSSARY_AUTHOR, $showtabs)) {
        $tab = GLOSSARY_STANDARD_VIEW;
    }

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
    $showcommonelements = 0;
break;

default:
    $showcommonelements = 1;
break;
}

// Trigger module viewed event.
glossary_view($glossary, $course, $cm, $context, $mode);

/// Printing the heading
$strglossaries = get_string("modulenameplural", "glossary");
$strglossary = get_string("modulename", "glossary");
$strallcategories = get_string("allcategories", "glossary");
$straddentry = get_string("addentry", "glossary");
$strnoentries = get_string("noentries", "glossary");
$strsearchindefinition = get_string("searchindefinition", "glossary");
$strsearch = get_string("search");
$strwaitingapproval = get_string('waitingapproval', 'glossary');

/// If we are in approval mode, prit special header
$PAGE->set_title($glossary->name);
$PAGE->set_heading($course->fullname);
$url = new moodle_url('/mod/glossary/view.php', array('id'=>$cm->id));
if (isset($mode)) {
    $url->param('mode', $mode);
}
$PAGE->set_url($url);
$PAGE->force_settings_menu();

if (!empty($CFG->enablerssfeeds) && !empty($CFG->glossary_enablerssfeeds)
    && $glossary->rsstype && $glossary->rssarticles) {

    $rsstitle = format_string($course->shortname, true, array('context' => context_course::instance($course->id))) . ': '. format_string($glossary->name);
    rss_add_http_header($context, 'mod_glossary', $glossary, $rsstitle);
}

if ($tab == GLOSSARY_APPROVAL_VIEW) {
    require_capability('mod/glossary:approve', $context);
    $PAGE->navbar->add($strwaitingapproval);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($strwaitingapproval);
} else { /// Print standard header
    echo $OUTPUT->header();
}
echo $OUTPUT->heading(format_string($glossary->name), 2);

// Render the activity information.
$completiondetails = \core_completion\cm_completion_details::get_instance($cm, $USER->id);
$activitydates = \core\activity_dates::get_dates_for_module($cm, $USER->id);
echo $OUTPUT->activity_information($cm, $completiondetails, $activitydates);

/// All this depends if whe have $showcommonelements
if ($showcommonelements) {
/// To calculate available options
    $availableoptions = '';

/// Decide about to print the import link
    /*if (has_capability('mod/glossary:import', $context)) {
        $availableoptions = '<span class="helplink">' .
                            '<a href="' . $CFG->wwwroot . '/mod/glossary/import.php?id=' . $cm->id . '"' .
                            '  title="' . s(get_string('importentries', 'glossary')) . '">' .
                            get_string('importentries', 'glossary') . '</a>' .
                            '</span>';
    }
/// Decide about to print the export link
    if (has_capability('mod/glossary:export', $context)) {
        if ($availableoptions) {
            $availableoptions .= '&nbsp;/&nbsp;';
        }
        $availableoptions .='<span class="helplink">' .
                            '<a href="' . $CFG->wwwroot . '/mod/glossary/export.php?id=' . $cm->id .
                            '&amp;mode='.$mode . '&amp;hook=' . urlencode($hook) . '"' .
                            '  title="' . s(get_string('exportentries', 'glossary')) . '">' .
                            get_string('exportentries', 'glossary') . '</a>' .
                            '</span>';
    }*/

/// Decide about to print the approval link
    if (has_capability('mod/glossary:approve', $context)) {
    /// Check we have pending entries
        if ($hiddenentries = $DB->count_records('glossary_entries', array('glossaryid'=>$glossary->id, 'approved'=>0))) {
            if ($availableoptions) {
                $availableoptions .= '<br />';
            }
            $availableoptions .='<span class="helplink">' .
                                '<a href="' . $CFG->wwwroot . '/mod/glossary/view.php?id=' . $cm->id .
                                '&amp;mode=approval' . '"' .
                                '  title="' . s(get_string('waitingapproval', 'glossary')) . '">' .
                                get_string('waitingapproval', 'glossary') . ' ('.$hiddenentries.')</a>' .
                                '</span>';
        }
    }

/// Start to print glossary controls
//        print_box_start('glossarycontrol clearfix');
    echo '<div class="glossarycontrol" style="text-align: right">';
    echo $availableoptions;

/// The print icon
    if ( $showcommonelements and $mode != 'search') {
        if (has_capability('mod/glossary:manageentries', $context) or $glossary->allowprintview) {
            $params = array(
                'id'        => $cm->id,
                'mode'      => $mode,
                'hook'      => $hook,
                'sortkey'   => $sortkey,
                'sortorder' => $sortorder,
                'offset'    => $offset,
                'pagelimit' => $pagelimit
            );
            $printurl = new moodle_url('/mod/glossary/print.php', $params);
            $printtitle = get_string('printerfriendly', 'glossary');
            $printattributes = array(
                'class' => 'printicon',
                'title' => $printtitle
            );
            echo html_writer::link($printurl, $printtitle, $printattributes);
        }
    }
/// End glossary controls
//        print_box_end(); /// glossarycontrol
    echo '</div><br />';

//        print_box('&nbsp;', 'clearer');
}

/// Info box
if ($glossary->intro && $showcommonelements) {
    echo $OUTPUT->box(format_module_intro('glossary', $glossary, $cm->id), 'generalbox', 'intro');
}

/// Search box
if ($showcommonelements ) {
    $fullsearchchecked = false;
    if ($fullsearch || $mode != 'search') {
        $fullsearchchecked = true;
    }

    $check = [
        'name' => 'fullsearch',
        'id' => 'fullsearch',
        'value' => '1',
        'checked' => $fullsearchchecked,
        'label' => $strsearchindefinition
    ];

    $checkbox = $OUTPUT->render_from_template('core/checkbox', $check);

    $hiddenfields = [
        (object) ['name' => 'id', 'value' => $cm->id],
        (object) ['name' => 'mode', 'value' => 'search'],
    ];
    $data = [
        'action' => new moodle_url('/mod/glossary/view.php'),
        'hiddenfields' => $hiddenfields,
        'otherfields' => $checkbox,
        'inputname' => 'hook',
        'query' => ($mode == 'search') ? s($hook) : '',
        'searchstring' => get_string('search'),
        'extraclasses' => 'my-2'
    ];
    echo $OUTPUT->render_from_template('core/search_input', $data);
}

/// Show the add entry button if allowed
if (has_capability('mod/glossary:write', $context) && $showcommonelements ) {
    echo '<div class="singlebutton glossaryaddentry">';
    echo "<form class=\"form form-inline mb-1\" id=\"newentryform\" method=\"get\" action=\"$CFG->wwwroot/mod/glossary/edit.php\">";
    echo '<div>';
    echo "<input type=\"hidden\" name=\"cmid\" value=\"$cm->id\" />";
    echo '<input type="submit" value="'.get_string('addentry', 'glossary').'" class="btn btn-secondary" />';
    echo '</div>';
    echo '</form>';
    echo "</div>\n";
}


require("tabs.php");

require("sql.php");

/// printing the entries
$entriesshown = 0;
$currentpivot = '';
$paging = NULL;

if ($allentries) {

    //Decide if we must show the ALL link in the pagebar
    $specialtext = '';
    if ($glossary->showall) {
        $specialtext = get_string("allentries","glossary");
    }

    //Build paging bar
    $baseurl = new moodle_url('/mod/glossary/view.php', ['id' => $id, 'mode' => $mode, 'hook' => $hook,
        'sortkey' => $sortkey, 'sortorder' => $sortorder, 'fullsearch' => $fullsearch]);
    $paging = glossary_get_paging_bar($count, $page, $entriesbypage, $baseurl->out() . '&amp;',
        9999, 10, '&nbsp;&nbsp;', $specialtext, -1);

    echo '<div class="paging">';
    echo $paging;
    echo '</div>';

    //load ratings
    require_once($CFG->dirroot.'/rating/lib.php');
    if ($glossary->assessed != RATING_AGGREGATE_NONE) {
        $ratingoptions = new stdClass;
        $ratingoptions->context = $context;
        $ratingoptions->component = 'mod_glossary';
        $ratingoptions->ratingarea = 'entry';
        $ratingoptions->items = $allentries;
        $ratingoptions->aggregate = $glossary->assessed;//the aggregation method
        $ratingoptions->scaleid = $glossary->scale;
        $ratingoptions->userid = $USER->id;
        $ratingoptions->returnurl = $CFG->wwwroot.'/mod/glossary/view.php?id='.$cm->id;
        $ratingoptions->assesstimestart = $glossary->assesstimestart;
        $ratingoptions->assesstimefinish = $glossary->assesstimefinish;

        $rm = new rating_manager();
        $allentries = $rm->get_ratings($ratingoptions);
    }

    foreach ($allentries as $entry) {

        // Setting the pivot for the current entry
        if ($printpivot) {
            $pivot = $entry->{$pivotkey};
            $upperpivot = core_text::strtoupper($pivot);
            $pivottoshow = core_text::strtoupper(format_string($pivot, true, $fmtoptions));

            // Reduce pivot to 1cc if necessary.
            if (!$fullpivot) {
                $upperpivot = core_text::substr($upperpivot, 0, 1);
                $pivottoshow = core_text::substr($pivottoshow, 0, 1);
            }

            // If there's a group break.
            if ($currentpivot != $upperpivot) {
                $currentpivot = $upperpivot;

                // print the group break if apply

                echo '<div>';
                echo '<table cellspacing="0" class="glossarycategoryheader">';

                echo '<tr>';
                if ($userispivot) {
                // printing the user icon if defined (only when browsing authors)
                    echo '<th align="left">';
                    $user = mod_glossary_entry_query_builder::get_user_from_record($entry);
                    echo $OUTPUT->user_picture($user, array('courseid'=>$course->id));
                    $pivottoshow = fullname($user, has_capability('moodle/site:viewfullnames', context_course::instance($course->id)));
                } else {
                    echo '<th >';
                }

                echo $OUTPUT->heading($pivottoshow, 3);
                echo "</th></tr></table></div>\n";
            }
        }

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
        glossary_print_entry($course, $cm, $glossary, $entry, $mode, $hook,1,$displayformat);
        $entriesshown++;
    }
    // The all entries value may be a recordset or an array.
    if ($allentries instanceof moodle_recordset) {
        $allentries->close();
    }
}
if ( !$entriesshown ) {
    echo $OUTPUT->box(get_string("noentries","glossary"), "generalbox boxaligncenter boxwidthwide");
}

if (!empty($formsent)) {
    // close the form properly if used
    echo "</div>";
    echo "</form>";
}

if ( $paging ) {
    echo '<hr />';
    echo '<div class="paging">';
    echo $paging;
    echo '</div>';
}
echo '<br />';
glossary_print_tabbed_table_end();

/// Finish the page
echo $OUTPUT->footer();
