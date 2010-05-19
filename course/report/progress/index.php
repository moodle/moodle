<?php
require_once('../../../config.php');
require_once($CFG->libdir . '/completionlib.php');

define('COMPLETION_REPORT_PAGE',50);

// Get course
$id = required_param('course',PARAM_INT);
$course=$DB->get_record('course',array('id'=>$id));
if(!$course) {
    print_error('invalidcourseid');
}

// Sort (default lastname, optionally firstname)
$sort=optional_param('sort','',PARAM_ALPHA);
$firstnamesort=$sort=='firstname';

// CSV format
$format=optional_param('format','',PARAM_ALPHA);
$excel=$format=='excelcsv';
$csv=$format=='csv' || $excel;

// Whether to start at a particular position
$start=optional_param('start',0,PARAM_INT);

// Whether to show idnumber
// TODO: This should really not be using a config option 'intended' for
// gradebook, but that option is also used in quiz reports as well. There ought
// to be a generic option somewhere.
$idnumbers=$CFG->grade_report_showuseridnumber;

function csv_quote($value) {
    global $excel;
    if($excel) {
        $tl=textlib_get_instance();
        return $tl->convert('"'.str_replace('"',"'",$value).'"','UTF-8','UTF-16LE');
    } else {
        return '"'.str_replace('"',"'",$value).'"';
    }
}

$url = new moodle_url('/course/report/progress/index.php', array('course'=>$id));
if ($sort !== '') {
    $url->param('sort', $sort);
}
if ($format !== '') {
    $url->param('format', $format);
}
if ($start !== 0) {
    $url->param('start', $start);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

require_login($course);

// Check basic permission
$context=get_context_instance(CONTEXT_COURSE,$course->id);
require_capability('coursereport/progress:view',$context);

// Get group mode
$group=groups_get_course_group($course,true); // Supposed to verify group
if($group===0 && $course->groupmode==SEPARATEGROUPS) {
    require_capability('moodle/site:accessallgroups',$context);
}

// Get data on activities and progress of all users, and give error if we've
// nothing to display (no users or no activities)
$reportsurl=$CFG->wwwroot.'/course/report.php?id='.$course->id;
$completion=new completion_info($course);
$activities=$completion->get_activities();
if(count($activities)==0) {
    print_error('err_noactivities','completion',$reportsurl);
}

$progress=$completion->get_progress_all($firstnamesort,$group,
    $csv ? 0 :COMPLETION_REPORT_PAGE,$csv ? 0 : $start);

if($csv) {
    header('Content-Disposition: attachment; filename=progress.'.
        preg_replace('/[^a-z0-9-]/','_',strtolower($course->shortname)).'.csv');
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
    // Use SVG to draw sideways text if supported
    $svgcleverness = can_use_rotated_text();

    // Navigation and header
    $strreports = get_string("reports");
    $strcompletion = get_string('completionreport','completion');

    $PAGE->set_title($strcompletion);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    if($svgcleverness) {
        $PAGE->requires->yui2_lib('event');
        $PAGE->requires->js('/course/report/progress/textrotate.js');
    }

    // Handle groups (if enabled)
    groups_print_course_menu($course,$CFG->wwwroot.'/course/report/progress/?course='.$course->id);
}

// Do we need a paging bar?
if($progress->total > COMPLETION_REPORT_PAGE) {
    $pagingbar='<div class="completion_pagingbar">';

    if($start>0) {
        $newstart=$start-COMPLETION_REPORT_PAGE;
        if($newstart<0) {
            $newstart=0;
        }
        $pagingbar.=link_arrow_left(get_string('previous'),'./?course='.$course->id.
            ($newstart ? '&amp;start='.$newstart : ''),false,'completion_prev');
    }

    $a=new StdClass;
    $a->from=$start+1;
    $a->to=$start+COMPLETION_REPORT_PAGE;
    $a->total=$progress->total;
    $pagingbar.='<p>'.get_string('reportpage','completion',$a).'</p>';

    if($start+COMPLETION_REPORT_PAGE < $progress->total) {
        $pagingbar.=link_arrow_right(get_string('next'),'./?course='.$course->id.
            '&amp;start='.($start+COMPLETION_REPORT_PAGE),false,'completion_next');
    }

    $pagingbar.='</div>';
} else {
    $pagingbar='';
}

// Okay, let's draw the table of progress info,

// Start of table
if(!$csv) {
    print '<br class="clearer"/>'; // ugh
    if(count($progress->users)==0) {
        print '<p class="nousers">'.get_string('err_nousers','completion').'</p>';
        print '<p><a href="'.$reportsurl.'">'.get_string('continue').'</a></p>';
        echo $OUTPUT->footer();
        exit;
    }
    print $pagingbar;
    print '<table id="completion-progress" class="generaltable flexible boxaligncenter" style="text-align:left"><tr style="vertical-align:top">';

    // User heading / sort option
    print '<th scope="col" class="completion-sortchoice">';
    if($firstnamesort) {
        print
            get_string('firstname').' / <a href="./?course='.$course->id.'">'.
            get_string('lastname').'</a>';
    } else {
        print '<a href="./?course='.$course->id.'&amp;sort=firstname">'.
            get_string('firstname').'</a> / '.
            get_string('lastname');
    }
    print '</th>';

    if($idnumbers) {
        print '<th>'.get_string('idnumber').'</th>';
    }

} else {
    if($idnumbers) {
        print $sep;
    }
}

// Activities
foreach($activities as $activity) {
    $activity->datepassed = $activity->completionexpected && $activity->completionexpected <= time();
    $activity->datepassedclass=$activity->datepassed ? 'completion-expired' : '';

    if($activity->completionexpected) {
        $datetext=userdate($activity->completionexpected,get_string('strftimedate','langconfig'));
    } else {
        $datetext='';
    }

    // Some names (labels) come URL-encoded and can be very long, so shorten them
    $activity->name = shorten_text($activity->name);

    if($csv) {
        print $sep.csv_quote(strip_tags($activity->name)).$sep.csv_quote($datetext);
    } else {
        print '<th scope="col" class="'.$activity->datepassedclass.'">'.
            '<a href="'.$CFG->wwwroot.'/mod/'.$activity->modname.
            '/view.php?id='.$activity->id.'">'.
            '<img src="'.$OUTPUT->pix_url('icon', $activity->modname).'/icon.gif" alt="'.
            get_string('modulename',$activity->modname).'" /> <span class="completion-activityname">'.
            format_string($activity->name).'</span></a>';
        if($activity->completionexpected) {
            print '<div class="completion-expected"><span>'.$datetext.'</span></div>';
        }
        print '</th>';
    }
}

if($csv) {
    print $line;
} else {
    print '</tr>';
}

// Row for each user
foreach($progress->users as $user) {
    // User name
    if($csv) {
        print csv_quote(fullname($user));
        if($idnumbers) {
            print $sep.csv_quote($user->idnumber);
        }
    } else {
        print '<tr><th scope="row"><a href="'.$CFG->wwwroot.'/user/view.php?id='.
            $user->id.'&amp;course='.$course->id.'">'.fullname($user).'</a></th>';
        if($idnumbers) {
            print '<td>'.htmlspecialchars($user->idnumber).'</td>';
        }
    }

    // Progress for each activity
    foreach($activities as $activity) {

        // Get progress information and state
        if(array_key_exists($activity->id,$user->progress)) {
            $thisprogress=$user->progress[$activity->id];
            $state=$thisprogress->completionstate;
            $date=userdate($thisprogress->timemodified);
        } else {
            $state=COMPLETION_INCOMPLETE;
            $date='';
        }

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
            print '<td class="completion-progresscell '.$activity->datepassedclass.'">'.
                '<img src="'.$OUTPUT->pix_url('i/'.$completionicon).
                '" alt="'.$describe.'" title="'.$fulldescribe.'" /></td>';
        }
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
print $pagingbar;

print '<ul class="progress-actions"><li><a href="index.php?course='.$course->id.
    '&amp;format=csv">'.get_string('csvdownload','completion').'</a></li>
    <li><a href="index.php?course='.$course->id.'&amp;format=excelcsv">'.
    get_string('excelcsvdownload','completion').'</a></li></ul>';

echo $OUTPUT->footer();

