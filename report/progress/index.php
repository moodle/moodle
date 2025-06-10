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
 * Activity progress reports
 *
 * @package    report
 * @subpackage progress
 * @copyright  2008 Sam Marshall
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\report_helper;
use \report_progress\local\helper;

require('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

// Get course
$id = required_param('course',PARAM_INT);
$course = $DB->get_record('course',array('id'=>$id));
if (!$course) {
    throw new \moodle_exception('invalidcourseid');
}
$context = context_course::instance($course->id);

// Sort (default lastname, optionally firstname)
$sort = optional_param('sort','',PARAM_ALPHA);
$firstnamesort = $sort == 'firstname';

// CSV format
$format = optional_param('format','',PARAM_ALPHA);
$excel = $format == 'excelcsv';
$csv = $format == 'csv' || $excel;

// Paging, sorting and filtering.
$page   = optional_param('page', 0, PARAM_INT);
$sifirst = optional_param('sifirst', 'all', PARAM_NOTAGS);
$silast  = optional_param('silast', 'all', PARAM_NOTAGS);
$groupid = optional_param('group', 0, PARAM_INT);
$activityinclude = optional_param('activityinclude', 'all', PARAM_TEXT);
$activityorder = optional_param('activityorder', 'orderincourse', PARAM_TEXT);

// Whether to show extra user identity information
$userfields = \core_user\fields::for_identity($context);
$extrafields = $userfields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);
$leftcols = 1 + count($extrafields);

function csv_quote($value) {
    global $excel;
    if ($excel) {
        return core_text::convert('"'.str_replace('"',"'",$value).'"','UTF-8','UTF-16LE');
    } else {
        return '"'.str_replace('"',"'",$value).'"';
    }
}

$url = new moodle_url('/report/progress/index.php', array('course'=>$id));
if ($sort !== '') {
    $url->param('sort', $sort);
}
if ($format !== '') {
    $url->param('format', $format);
}
if ($page !== 0) {
    $url->param('page', $page);
}
if ($sifirst !== 'all') {
    $url->param('sifirst', $sifirst);
}
if ($silast !== 'all') {
    $url->param('silast', $silast);
}
if ($groupid !== 0) {
    $url->param('group', $groupid);
}
if ($activityinclude !== '') {
    $url->param('activityinclude', $activityinclude);
}
if ($activityorder !== '') {
    $url->param('activityorder', $activityorder);
}

$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

require_login($course);

// Check basic permission
require_capability('report/progress:view',$context);

// Get group mode
$group = groups_get_course_group($course,true); // Supposed to verify group
if ($group===0 && $course->groupmode==SEPARATEGROUPS) {
    require_capability('moodle/site:accessallgroups',$context);
}

// Get data on activities and progress of all users, and give error if we've
// nothing to display (no users or no activities).
$completion = new completion_info($course);
list($activitytypes, $activities) = helper::get_activities_to_show($completion, $activityinclude, $activityorder);
$output = $PAGE->get_renderer('report_progress');

if ($sifirst !== 'all') {
    set_user_preference('ifirst', $sifirst);
}
if ($silast !== 'all') {
    set_user_preference('ilast', $silast);
}

if (!empty($USER->preference['ifirst'])) {
    $sifirst = $USER->preference['ifirst'];
} else {
    $sifirst = 'all';
}

if (!empty($USER->preference['ilast'])) {
    $silast = $USER->preference['ilast'];
} else {
    $silast = 'all';
}

// Generate where clause
$where = array();
$where_params = array();

if ($sifirst !== 'all') {
    $where[] = $DB->sql_like('u.firstname', ':sifirst', false, false);
    $where_params['sifirst'] = $sifirst.'%';
}

if ($silast !== 'all') {
    $where[] = $DB->sql_like('u.lastname', ':silast', false, false);
    $where_params['silast'] = $silast.'%';
}

// Get user match count
$total = $completion->get_num_tracked_users(implode(' AND ', $where), $where_params, $group);

// Total user count
$grandtotal = $completion->get_num_tracked_users('', array(), $group);

// Get user data
$progress = array();

if ($total) {
    $progress = $completion->get_progress_all(
        implode(' AND ', $where),
        $where_params,
        $group,
        $firstnamesort ? 'u.firstname ASC, u.lastname ASC' : 'u.lastname ASC, u.firstname ASC',
        $csv ? 0 : helper::COMPLETION_REPORT_PAGE,
        $csv ? 0 : $page * helper::COMPLETION_REPORT_PAGE,
        $context
    );
}

if ($csv && $grandtotal && count($activities)>0) { // Only show CSV if there are some users/actvs

    $shortname = format_string($course->shortname, true, array('context' => $context));
    header('Content-Disposition: attachment; filename=progress.'.
        preg_replace('/[^a-z0-9-]/','_',core_text::strtolower(strip_tags($shortname))).'.csv');
    // Unicode byte-order mark for Excel
    if ($excel) {
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
    $strreports = get_string("reports");
    $strcompletion = get_string('activitycompletion', 'completion');

    $PAGE->set_title($strcompletion);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    // Print the selected dropdown.
    $pluginname = get_string('pluginname', 'report_progress');
    report_helper::print_report_selector($pluginname);
    $PAGE->requires->js_call_amd('report_progress/completion_override', 'init', [fullname($USER)]);

    // Handle groups (if enabled).
    echo $output->render_groups_select($url, $course);

    // Display include activity filter.
    echo $output->render_include_activity_select($url, $activitytypes, $activityinclude);

    // Display activity order options.
    echo $output->render_activity_order_select($url, $activityorder);

}

if (count($activities)==0) {
    echo $OUTPUT->container(get_string('err_noactivities', 'completion'), 'errorbox errorboxcontent');
    echo $OUTPUT->footer();
    exit;
}

// If no users in this course what-so-ever
if (!$grandtotal) {
    echo $OUTPUT->container(get_string('err_nousers', 'completion'), 'errorbox errorboxcontent');
    echo $OUTPUT->footer();
    exit;
}

// Build link for paging
$link = $CFG->wwwroot.'/report/progress/?course='.$course->id;
if (strlen($sort)) {
    $link .= '&amp;sort='.$sort;
}
$link .= '&amp;start=';

$pagingbar = '';

// Initials bar.
$prefixfirst = 'sifirst';
$prefixlast = 'silast';

// The URL used in the initials bar should reset the 'start' parameter.
$initialsbarurl = fullclone($url);
$initialsbarurl->remove_params('page');

$pagingbar .= $OUTPUT->initials_bar($sifirst, 'firstinitial mt-2', get_string('firstname'), $prefixfirst, $initialsbarurl);
$pagingbar .= $OUTPUT->initials_bar($silast, 'lastinitial', get_string('lastname'), $prefixlast, $initialsbarurl);
$pagingbar .= $OUTPUT->paging_bar($total, $page, helper::COMPLETION_REPORT_PAGE, $url);

// Okay, let's draw the table of progress info,

// Start of table
if (!$csv) {
    print '<br class="clearer"/>'; // ugh

    print $pagingbar;

    if (!$total) {
        echo $OUTPUT->heading(get_string('nothingtodisplay'));
        echo $OUTPUT->footer();
        exit;
    }

    print '<div id="completion-progress-wrapper" class="no-overflow">';
    print '<table id="completion-progress" class="generaltable flexible boxaligncenter" style="text-align:left"><thead><tr style="vertical-align:top">';

    // User heading / sort option
    print '<th scope="col" class="completion-sortchoice">';

    $sorturl = fullclone($url);
    if ($firstnamesort) {
        $sorturl->param('sort', 'lastname');
        $sortlink = html_writer::link($sorturl, get_string('lastname'));
        print
            get_string('firstname') . " / $sortlink";
    } else {
        $sorturl->param('sort', 'firstname');
        $sortlink = html_writer::link($sorturl, get_string('firstname'));
        print "$sortlink / " . get_string('lastname');
    }
    print '</th>';

    // Print user identity columns
    foreach ($extrafields as $field) {
        echo '<th scope="col" class="completion-identifyfield">' .
                \core_user\fields::get_display_name($field) . '</th>';
    }
} else {
    foreach ($extrafields as $field) {
        echo $sep . csv_quote(\core_user\fields::get_display_name($field));
    }
}

// Activities
$formattedactivities = array();
foreach($activities as $activity) {
    $datepassed = $activity->completionexpected && $activity->completionexpected <= time();
    $datepassedclass = $datepassed ? 'completion-expired' : '';

    if ($activity->completionexpected) {
        if ($csv) {
            $datetext = userdate($activity->completionexpected, "%F %T");
        } else {
            $datetext = userdate($activity->completionexpected, get_string('strftimedate', 'langconfig'));
        }
    } else {
        $datetext='';
    }

    // Some names (labels) come URL-encoded and can be very long, so shorten them
    $displayname = format_string($activity->name, true, array('context' => $activity->context));

    if ($csv) {
        print $sep.csv_quote($displayname).$sep.csv_quote($datetext);
    } else {
        $shortenedname = shorten_text($displayname);
        print '<th scope="col" class="completion-header '.$datepassedclass.'">'.
            '<a href="'.$CFG->wwwroot.'/mod/'.$activity->modname.
            '/view.php?id='.$activity->id.'" title="' . s($displayname) . '">'.
            '<div class="rotated-text-container"><span class="rotated-text">'.$shortenedname.'</span></div>'.
            '<div class="modicon">'.
            $OUTPUT->image_icon('monologo', get_string('modulename', $activity->modname), $activity->modname) .
            '</div>'.
            '</a>';
        if ($activity->completionexpected) {
            print '<div class="completion-expected"><span>'.$datetext.'</span></div>';
        }
        print '</th>';
    }
    $formattedactivities[$activity->id] = (object)array(
        'datepassedclass' => $datepassedclass,
        'displayname' => $displayname,
    );
}

if ($csv) {
    print $line;
} else {
    print '</tr></thead><tbody>';
}

// Row for each user
foreach($progress as $user) {
    // User name
    if ($csv) {
        print csv_quote(fullname($user, has_capability('moodle/site:viewfullnames', $context)));
        foreach ($extrafields as $field) {
            echo $sep . csv_quote($user->{$field});
        }
    } else {
        print '<tr><th scope="row"><a href="' . $CFG->wwwroot . '/user/view.php?id=' .
            $user->id . '&amp;course=' . $course->id . '">' .
            fullname($user, has_capability('moodle/site:viewfullnames', $context)) . '</a></th>';
        foreach ($extrafields as $field) {
            echo '<td>' . s($user->{$field}) . '</td>';
        }
    }

    // Progress for each activity
    foreach($activities as $activity) {

        // Get progress information and state
        if (array_key_exists($activity->id, $user->progress)) {
            $thisprogress = $user->progress[$activity->id];
            $state = $thisprogress->completionstate;
            $overrideby = $thisprogress->overrideby;
            $date = userdate($thisprogress->timemodified);
        } else {
            $state = COMPLETION_INCOMPLETE;
            $overrideby = 0;
            $date = '';
        }

        // Work out how it corresponds to an icon
        switch($state) {
            case COMPLETION_INCOMPLETE :
                $completiontype = 'n'.($overrideby ? '-override' : '');
                break;
            case COMPLETION_COMPLETE :
                $completiontype = 'y'.($overrideby ? '-override' : '');
                break;
            case COMPLETION_COMPLETE_PASS :
                $completiontype = 'pass';
                break;
            case COMPLETION_COMPLETE_FAIL :
                $completiontype = 'fail';
                break;
        }
        $completiontrackingstring = $activity->completion == COMPLETION_TRACKING_AUTOMATIC ? 'auto' : 'manual';
        $completionicon = 'completion-' . $completiontrackingstring. '-' . $completiontype;

        if ($overrideby) {
            $overridebyuser = \core_user::get_user($overrideby, '*', MUST_EXIST);
            $describe = get_string('completion-' . $completiontype, 'completion', fullname($overridebyuser));
        } else {
            $describe = get_string('completion-' . $completiontype, 'completion');
        }
        $a=new StdClass;
        $a->state=$describe;
        $a->date=$date;
        $a->user = fullname($user, has_capability('moodle/site:viewfullnames', $context));
        $a->activity = $formattedactivities[$activity->id]->displayname;
        $fulldescribe=get_string('progress-title','completion',$a);

        if ($csv) {
            if ($date != '') {
                $date = userdate($thisprogress->timemodified, "%F %T");
            }
            print $sep.csv_quote($describe).$sep.csv_quote($date);
        } else {
            $celltext = $OUTPUT->pix_icon('i/' . $completionicon, s($fulldescribe));
            if (has_capability('moodle/course:overridecompletion', $context) &&
                    $state != COMPLETION_COMPLETE_PASS && $state != COMPLETION_COMPLETE_FAIL) {
                $newstate = ($state == COMPLETION_COMPLETE) ? COMPLETION_INCOMPLETE : COMPLETION_COMPLETE;
                $changecompl = $user->id . '-' . $activity->id . '-' . $newstate;
                $url = new moodle_url($PAGE->url, ['sesskey' => sesskey()]);
                $celltext = html_writer::link($url, $celltext, array('class' => 'changecompl', 'data-changecompl' => $changecompl,
                                                                     'data-activityname' => $a->activity,
                                                                     'data-userfullname' => $a->user,
                                                                     'data-completiontracking' => $completiontrackingstring,
                                                                     'role' => 'button'));
            }
            print '<td class="completion-progresscell '.$formattedactivities[$activity->id]->datepassedclass.'">'.
                $celltext . '</td>';
        }
    }

    if ($csv) {
        print $line;
    } else {
        print '</tr>';
    }
}

if ($csv) {
    exit;
}
print '</tbody></table>';
print '</div>';

echo $output->render_download_buttons($url);

echo $OUTPUT->footer();

