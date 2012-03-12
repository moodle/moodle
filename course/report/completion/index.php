<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Course completion progress report
 *
 * @package   moodlecore
 * @copyright 2009 Catalyst IT Ltd
 * @author    Aaron Barnes <aaronb@catalyst.net.nz>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir.'/completionlib.php');

/**
 * Configuration
 */
define('COMPLETION_REPORT_PAGE',        25);
define('COMPLETION_REPORT_COL_TITLES',  true);

/**
 * Setup page, check permissions
 */

// Get course
$courseid = required_param('course', PARAM_INT);
$format = optional_param('format','',PARAM_ALPHA);
$sort = optional_param('sort','',PARAM_ALPHA);
$edituser = optional_param('edituser', 0, PARAM_INT);


$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$url = new moodle_url('/course/report/completion/index.php', array('course'=>$course->id));
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

$firstnamesort = ($sort == 'firstname');
$excel = ($format == 'excelcsv');
$csv = ($format == 'csv' || $excel);

// Paging
$start   = optional_param('start', 0, PARAM_INT);
$sifirst = optional_param('sifirst', 'all', PARAM_ALPHA);
$silast  = optional_param('silast', 'all', PARAM_ALPHA);

// Whether to show idnumber
$idnumbers = $CFG->grade_report_showuseridnumber;

// Function for quoting csv cell values
function csv_quote($value) {
    global $excel;
    if($excel) {
        $tl=textlib_get_instance();
        return $tl->convert('"'.str_replace('"',"'",$value).'"','UTF-8','UTF-16LE');
    } else {
        return '"'.str_replace('"',"'",$value).'"';
    }
}


// Check permissions
require_login($course);

$context=get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('coursereport/completion:view', $context);

// Get group mode
$group = groups_get_course_group($course, true); // Supposed to verify group
if($group === 0 && $course->groupmode == SEPARATEGROUPS) {
    require_capability('moodle/site:accessallgroups',$context);
}

/**
 * Load data
 */

// Retrieve course_module data for all modules in the course
$modinfo = get_fast_modinfo($course);

// Get criteria for course
$completion = new completion_info($course);

if (!$completion->has_criteria()) {
    print_error('err_nocriteria', 'completion', $CFG->wwwroot.'/course/report.php?id='.$course->id);
}

// Get criteria and put in correct order
$criteria = array();

foreach ($completion->get_criteria(COMPLETION_CRITERIA_TYPE_COURSE) as $criterion) {
    $criteria[] = $criterion;
}

foreach ($completion->get_criteria(COMPLETION_CRITERIA_TYPE_ACTIVITY) as $criterion) {
    $criteria[] = $criterion;
}

foreach ($completion->get_criteria() as $criterion) {
    if (!in_array($criterion->criteriatype, array(
            COMPLETION_CRITERIA_TYPE_COURSE, COMPLETION_CRITERIA_TYPE_ACTIVITY))) {
        $criteria[] = $criterion;
    }
}

// Can logged in user mark users as complete?
// (if the logged in user has a role defined in the role criteria)
$allow_marking = false;
$allow_marking_criteria = null;

if (!$csv) {
    // Get role criteria
    $rcriteria = $completion->get_criteria(COMPLETION_CRITERIA_TYPE_ROLE);

    if (!empty($rcriteria)) {

        foreach ($rcriteria as $rcriterion) {
            $users = get_role_users($rcriterion->role, $context, true);

            // If logged in user has this role, allow marking complete
            if ($users && in_array($USER->id, array_keys($users))) {
                $allow_marking = true;
                $allow_marking_criteria = $rcriterion->id;
                break;
            }
        }
    }
}

/**
 * Setup page header
 */
if ($csv) {
    $shortname = format_string($course->shortname, true, array('context' => $context));
    $textlib = textlib_get_instance();
    header('Content-Disposition: attachment; filename=progress.'.
        preg_replace('/[^a-z0-9-]/','_',$textlib->strtolower(strip_tags($shortname))).'.csv');
    // Unicode byte-order mark for Excel
    if($excel) {
        header('Content-Type: text/csv; charset=UTF-16LE');
        print chr(0xFF).chr(0xFE);
        $sep="\t".chr(0);
        $line="\n".chr(0);
    } else {
        header('Content-Type: text/csv; charset=UTF-8');
        $sep=",";
        $line="\n";
    }

} else {
    // Navigation and header
    $strcompletion = get_string('coursecompletion');

    $PAGE->set_title($strcompletion);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

    $PAGE->requires->yui2_lib(
        array(
            'yahoo',
            'dom',
            'element',
            'event',
        )
    );

    $PAGE->requires->js('/course/report/completion/textrotate.js');

    // Handle groups (if enabled)
    groups_print_course_menu($course, $CFG->wwwroot.'/course/report/completion/?course='.$course->id);
}


// Generate where clause
$where = array();
$where_params = array();

if ($sifirst !== 'all') {
    $where[] = $DB->sql_like('u.firstname', ':sifirst', false);
    $where_params['sifirst'] = $sifirst.'%';
}

if ($silast !== 'all') {
    $where[] = $DB->sql_like('u.lastname', ':silast', false);
    $where_params['silast'] = $silast.'%';
}

// Get user match count
$total = $completion->get_num_tracked_users(implode(' AND ', $where), $where_params, $group);

// Total user count
$grandtotal = $completion->get_num_tracked_users('', array(), $group);

// If no users in this course what-so-ever
if (!$grandtotal) {
    echo $OUTPUT->container(get_string('err_nousers', 'completion'), 'errorbox errorboxcontent');
    echo $OUTPUT->footer();
    exit;
}

// Get user data
$progress = array();

if ($total) {
    $progress = $completion->get_progress_all(
        implode(' AND ', $where),
        $where_params,
        $group,
        $firstnamesort ? 'u.firstname ASC' : 'u.lastname ASC',
        $csv ? 0 : COMPLETION_REPORT_PAGE,
        $csv ? 0 : $start
    );
}


// Build link for paging
$link = $CFG->wwwroot.'/course/report/completion/?course='.$course->id;
if (strlen($sort)) {
    $link .= '&amp;sort='.$sort;
}
$link .= '&amp;start=';

// Build the the page by Initial bar
$initials = array('first', 'last');
$alphabet = explode(',', get_string('alphabet', 'langconfig'));

$pagingbar = '';
foreach ($initials as $initial) {
    $var = 'si'.$initial;

    $othervar = $initial == 'first' ? 'silast' : 'sifirst';
    $othervar = $$othervar != 'all' ? "&amp;{$othervar}={$$othervar}" : '';

    $pagingbar .= ' <div class="initialbar '.$initial.'initial">';
    $pagingbar .= get_string($initial.'name').':&nbsp;';

    if ($$var == 'all') {
        $pagingbar .= '<strong>'.get_string('all').'</strong> ';
    }
    else {
        $pagingbar .= "<a href=\"{$link}{$othervar}\">".get_string('all').'</a> ';
    }

    foreach ($alphabet as $letter) {
        if ($$var === $letter) {
            $pagingbar .= '<strong>'.$letter.'</strong> ';
        }
        else {
            $pagingbar .= "<a href=\"$link&amp;$var={$letter}{$othervar}\">$letter</a> ";
        }
    }

    $pagingbar .= '</div>';
}

// Do we need a paging bar?
if($total > COMPLETION_REPORT_PAGE) {

    // Paging bar
    $pagingbar .= '<div class="paging">';
    $pagingbar .= get_string('page').': ';

    $sistrings = array();
    if ($sifirst != 'all') {
        $sistrings[] =  "sifirst={$sifirst}";
    }
    if ($silast != 'all') {
        $sistrings[] =  "silast={$silast}";
    }
    $sistring = !empty($sistrings) ? '&amp;'.implode('&amp;', $sistrings) : '';

    // Display previous link
    if ($start > 0) {
        $pstart = max($start - COMPLETION_REPORT_PAGE, 0);
        $pagingbar .= "(<a class=\"previous\" href=\"{$link}{$pstart}{$sistring}\">".get_string('previous').'</a>)&nbsp;';
    }

    // Create page links
    $curstart = 0;
    $curpage = 0;
    while ($curstart < $total) {
        $curpage++;

        if ($curstart == $start) {
            $pagingbar .= '&nbsp;'.$curpage.'&nbsp;';
        }
        else {
            $pagingbar .= "&nbsp;<a href=\"{$link}{$curstart}{$sistring}\">$curpage</a>&nbsp;";
        }

        $curstart += COMPLETION_REPORT_PAGE;
    }

    // Display next link
    $nstart = $start + COMPLETION_REPORT_PAGE;
    if ($nstart < $total) {
        $pagingbar .= "&nbsp;(<a class=\"next\" href=\"{$link}{$nstart}{$sistring}\">".get_string('next').'</a>)';
    }

    $pagingbar .= '</div>';
}


/**
 * Draw table header
 */

// Start of table
if(!$csv) {
    print '<br class="clearer"/>'; // ugh

    $total_header = ($total == $grandtotal) ? $total : "{$total}/{$grandtotal}";
    echo $OUTPUT->heading(get_string('allparticipants').": {$total_header}", 3);

    print $pagingbar;

    if (!$total) {
        echo $OUTPUT->heading(get_string('nothingtodisplay'), 2);
        echo $OUTPUT->footer();
        exit;
    }

    print '<div id="completion-progress-wrapper" class="no-overflow">';
    print '<table id="completion-progress" class="generaltable flexible boxaligncenter completionreport" style="text-align: left" cellpadding="5" border="1">';

    // Print criteria group names
    print PHP_EOL.'<tr style="vertical-align: top">';
    print '<th scope="row" colspan="'.($idnumbers ? 2 : 1).'" class="rowheader">'.get_string('criteriagroup', 'completion').'</th>';

    $current_group = false;
    $col_count = 0;
    for ($i = 0; $i <= count($criteria); $i++) {

        if (isset($criteria[$i])) {
            $criterion = $criteria[$i];

            if ($current_group && $criterion->criteriatype === $current_group->criteriatype) {
                ++$col_count;
                continue;
            }
        }

        // Print header cell
        if ($col_count) {
            print '<th scope="col" colspan="'.$col_count.'" class="colheader criteriagroup">'.$current_group->get_type_title().'</th>';
        }

        if (isset($criteria[$i])) {
            // Move to next criteria type
            $current_group = $criterion;
            $col_count = 1;
        }
    }

    // Overall course completion status
    print '<th style="text-align: center;">'.get_string('course').'</th>';

    print '</tr>';

    // Print aggregation methods
    print PHP_EOL.'<tr style="vertical-align: top">';
    print '<th scope="row" colspan="'.($idnumbers ? 2: 1).'" class="rowheader">'.get_string('aggregationmethod', 'completion').'</th>';

    $current_group = false;
    $col_count = 0;
    for ($i = 0; $i <= count($criteria); $i++) {

        if (isset($criteria[$i])) {
            $criterion = $criteria[$i];

            if ($current_group && $criterion->criteriatype === $current_group->criteriatype) {
                ++$col_count;
                continue;
            }
        }

        // Print header cell
        if ($col_count) {
            $has_agg = array(
                COMPLETION_CRITERIA_TYPE_COURSE,
                COMPLETION_CRITERIA_TYPE_ACTIVITY,
                COMPLETION_CRITERIA_TYPE_ROLE,
            );

            if (in_array($current_group->criteriatype, $has_agg)) {
                // Try load a aggregation method
                $method = $completion->get_aggregation_method($current_group->criteriatype);

                $method = $method == 1 ? get_string('all') : get_string('any');

            } else {
                $method = '-';
            }

            print '<th scope="col" colspan="'.$col_count.'" class="colheader aggheader">'.$method.'</th>';
        }

        if (isset($criteria[$i])) {
            // Move to next criteria type
            $current_group = $criterion;
            $col_count = 1;
        }
    }

    // Overall course aggregation method
    print '<th scope="col" class="colheader aggheader aggcriteriacourse">';

    // Get course aggregation
    $method = $completion->get_aggregation_method();

    print $method == 1 ? get_string('all') : get_string('any');
    print '</th>';

    print '</tr>';


    // Print criteria titles
    if (COMPLETION_REPORT_COL_TITLES) {

        print PHP_EOL.'<tr>';
        print '<th scope="row" colspan="'.($idnumbers ? 2 : 1).'" class="rowheader">'.get_string('criteria', 'completion').'</th>';

        foreach ($criteria as $criterion) {
            // Get criteria details
            $details = $criterion->get_title_detailed();
            print '<th scope="col" class="colheader criterianame">';
            print '<span class="completion-criterianame">'.$details.'</span>';
            print '</th>';
        }

        // Overall course completion status
        print '<th scope="col" class="colheader criterianame">';

        print '<span class="completion-criterianame">'.get_string('coursecomplete', 'completion').'</span>';

        print '</th></tr>';
    }

    // Print user heading and icons
    print '<tr>';

    // User heading / sort option
    print '<th scope="col" class="completion-sortchoice" style="clear: both;">';

    $sistring = "&amp;silast={$silast}&amp;sifirst={$sifirst}";

    if($firstnamesort) {
        print
            get_string('firstname')." / <a href=\"./?course={$course->id}{$sistring}\">".
            get_string('lastname').'</a>';
    } else {
        print "<a href=\"./?course={$course->id}&amp;sort=firstname{$sistring}\">".
            get_string('firstname').'</a> / '.
            get_string('lastname');
    }
    print '</th>';


    // Print user id number column
    if($idnumbers) {
        print '<th>'.get_string('idnumber').'</th>';
    }

    ///
    /// Print criteria icons
    ///
    foreach ($criteria as $criterion) {

        // Generate icon details
        $icon = '';
        $iconlink = '';
        $icontitle = ''; // Required if $iconlink set
        $iconalt = ''; // Required
        switch ($criterion->criteriatype) {

            case COMPLETION_CRITERIA_TYPE_ACTIVITY:
                // Display icon
                $icon = $OUTPUT->pix_url('icon', $criterion->module);
                $iconlink = $CFG->wwwroot.'/mod/'.$criterion->module.'/view.php?id='.$criterion->moduleinstance;
                $icontitle = $modinfo->cms[$criterion->moduleinstance]->name;
                $iconalt = get_string('modulename', $criterion->module);
                break;

            case COMPLETION_CRITERIA_TYPE_COURSE:
                // Load course
                $crs = $DB->get_record('course', array('id' => $criterion->courseinstance));

                // Display icon
                $iconlink = $CFG->wwwroot.'/course/view.php?id='.$criterion->courseinstance;
                $icontitle = format_string($crs->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $crs->id, MUST_EXIST)));
                $iconalt = format_string($crs->shortname, true, array('context' => get_context_instance(CONTEXT_COURSE, $crs->id)));
                break;

            case COMPLETION_CRITERIA_TYPE_ROLE:
                // Load role
                $role = $DB->get_record('role', array('id' => $criterion->role));

                // Display icon
                $iconalt = $role->name;
                break;
        }

        // Print icon and cell
        print '<th class="criteriaicon">';

        // Create icon if not supplied
        if (!$icon) {
            $icon = $OUTPUT->pix_url('i/'.$COMPLETION_CRITERIA_TYPES[$criterion->criteriatype]);
        }

        print ($iconlink ? '<a href="'.$iconlink.'" title="'.$icontitle.'">' : '');
        print '<img src="'.$icon.'" class="icon" alt="'.$iconalt.'" '.(!$iconlink ? 'title="'.$iconalt.'"' : '').' />';
        print ($iconlink ? '</a>' : '');

        print '</th>';
    }

    // Overall course completion status
    print '<th class="criteriaicon">';
    print '<img src="'.$OUTPUT->pix_url('i/course').'" class="icon" alt="'.get_string('course').'" title="'.get_string('coursecomplete', 'completion').'" />';
    print '</th>';

    print '</tr>';


} else {
    // TODO
    if($idnumbers) {
        print $sep;
    }
}


///
/// Display a row for each user
///
foreach ($progress as $user) {

    // User name
    if($csv) {
        print csv_quote(fullname($user));
        if($idnumbers) {
            print $sep.csv_quote($user->idnumber);
        }
    } else {
        print PHP_EOL.'<tr id="user-'.$user->id.'">';

        print '<th scope="row"><a href="'.$CFG->wwwroot.'/user/view.php?id='.
            $user->id.'&amp;course='.$course->id.'">'.fullname($user).'</a></th>';
        if($idnumbers) {
            print '<td>'.htmlspecialchars($user->idnumber).'</td>';
        }
    }

    // Progress for each course completion criteria
    foreach ($criteria as $criterion) {

        // Handle activity completion differently
        if ($criterion->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) {

            // Load activity
            $activity = $modinfo->cms[$criterion->moduleinstance];

            // Get progress information and state
            if(array_key_exists($activity->id,$user->progress)) {
                $thisprogress=$user->progress[$activity->id];
                $state=$thisprogress->completionstate;
                $date=userdate($thisprogress->timemodified);
            } else {
                $state=COMPLETION_INCOMPLETE;
                $date='';
            }

            $criteria_completion = $completion->get_user_completion($user->id, $criterion);

            // Work out how it corresponds to an icon
            switch($state) {
                case COMPLETION_INCOMPLETE : $completiontype='n'; break;
                case COMPLETION_COMPLETE : $completiontype='y'; break;
                case COMPLETION_COMPLETE_PASS : $completiontype='pass'; break;
                case COMPLETION_COMPLETE_FAIL : $completiontype='fail'; break;
            }

            $completionicon='completion-'.
                ($activity->completion==COMPLETION_TRACKING_AUTOMATIC ? 'auto' : 'manual').
                '-'.$completiontype;

            $describe=get_string('completion-alt-auto-'.$completiontype,'completion');
            $a=new StdClass;
            $a->state=$describe;
            $a->date=$date;
            $a->user=fullname($user);
            $a->activity=strip_tags($activity->name);
            $fulldescribe=get_string('progress-title','completion',$a);

            if($csv) {
                print $sep.csv_quote($describe).$sep.csv_quote($date);
            } else {
                print '<td class="completion-progresscell">';

                print '<img src="'.$OUTPUT->pix_url('i/'.$completionicon).
                      '" alt="'.$describe.'" class="icon" title="'.$fulldescribe.'" />';

                print '</td>';
            }

            continue;
        }

        // Handle all other criteria
        $criteria_completion = $completion->get_user_completion($user->id, $criterion);
        $is_complete = $criteria_completion->is_complete();

        $completiontype = $is_complete ? 'y' : 'n';
        $completionicon = 'completion-auto-'.$completiontype;

        $describe = get_string('completion-alt-auto-'.$completiontype, 'completion');

        $a = new stdClass();
        $a->state    = $describe;
        $a->date     = $is_complete ? userdate($criteria_completion->timecompleted) : '';
        $a->user     = fullname($user);
        $a->activity = strip_tags($criterion->get_title());
        $fulldescribe = get_string('progress-title', 'completion', $a);

        if ($csv) {
            print $sep.csv_quote($describe);
        } else {

            if ($allow_marking_criteria === $criterion->id) {
                $describe = get_string('completion-alt-auto-'.$completiontype,'completion');

                print '<td class="completion-progresscell">'.
                    '<a href="'.$CFG->wwwroot.'/course/togglecompletion.php?user='.$user->id.'&amp;course='.$course->id.'&amp;rolec='.$allow_marking_criteria.'&amp;sesskey='.sesskey().'">'.
                    '<img src="'.$OUTPUT->pix_url('i/completion-manual-'.($is_complete ? 'y' : 'n')).
                    '" alt="'.$describe.'" class="icon" title="'.get_string('markcomplete', 'completion').'" /></a></td>';
            } else {
                print '<td class="completion-progresscell">'.
                    '<img src="'.$OUTPUT->pix_url('i/'.$completionicon).
                    '" alt="'.$describe.'" class="icon" title="'.$fulldescribe.'" /></td>';
            }
        }
    }

    // Handle overall course completion

    // Load course completion
    $params = array(
        'userid'    => $user->id,
        'course'    => $course->id
    );

    $ccompletion = new completion_completion($params);
    $completiontype =  $ccompletion->is_complete() ? 'y' : 'n';

    $describe = get_string('completion-alt-auto-'.$completiontype, 'completion');

    $a = new StdClass;
    $a->state    = $describe;
    $a->date     = '';
    $a->user     = fullname($user);
    $a->activity = strip_tags(get_string('coursecomplete', 'completion'));
    $fulldescribe = get_string('progress-title', 'completion', $a);

    if ($csv) {
        print $sep.csv_quote($describe);
    } else {

        print '<td class="completion-progresscell">';

        // Display course completion status icon
        print '<img src="'.$OUTPUT->pix_url('i/completion-auto-'.$completiontype).
               '" alt="'.$describe.'" class="icon" title="'.$fulldescribe.'" />';

        print '</td>';
    }

    if($csv) {
        print $line;
    } else {
        print '</tr>';
    }
}

if($csv) {
    exit;
}
print '</table>';
print '</div>';
print $pagingbar;

print '<ul class="progress-actions"><li><a href="index.php?course='.$course->id.
    '&amp;format=csv">'.get_string('csvdownload','completion').'</a></li>
    <li><a href="index.php?course='.$course->id.'&amp;format=excelcsv">'.
    get_string('excelcsvdownload','completion').'</a></li></ul>';

echo $OUTPUT->footer($course);
