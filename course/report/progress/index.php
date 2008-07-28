<?php

require_once('../../../config.php');
global $DB;

// Get course
$course=$DB->get_record('course',array('id'=>required_param('course',PARAM_INT)));
if(!$course) {
    error('Specified course not found');    
}

// Sort (default lastname, optionally firstname)
$sort=optional_param('sort','',PARAM_ALPHA);
$firstnamesort=$sort=='firstname';

// CSV format
$csv=optional_param('format','',PARAM_ALPHA)==='csv';

function csv_quote($value) {
    $tl=textlib_get_instance();
    
    $value=$tl->specialtoascii($value);
    return '"'.str_replace('"',"'",$value).'"';
}

require_login($course->id);

// Check basic permission
$context=get_context_instance(CONTEXT_COURSE,$course->id);
require_capability('moodle/course:viewprogress',$context);

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
$progress=$completion->get_progress_all($firstnamesort,$group);

if($csv) {
    header('Content-Type: text/csv; charset=ISO-8859-1');
    header('Content-Disposition: attachment; filename=progress.'.
        preg_replace('/[^a-z0-9-]/','_',strtolower($course->shortname)).'.csv');
} else {
    // Use SVG to draw sideways text if supported
    $svgcleverness=check_browser_version('Firefox',2.0) && !$USER->screenreader;

    // Navigation and header
    $strreports = get_string("reports");
    $strcompletion = get_string('completionreport','completion');
    $navlinks = array();
    $navlinks[] = array('name' => $strreports, 'link' => "../../report.php?id=$course->id", 'type' => 'misc');
    $navlinks[] = array('name' => $strcompletion, 'link' => null, 'type' => 'misc');
    if($svgcleverness) {
        require_js(array('yui_yahoo','yui_event','yui_dom'));
    }
    print_header($strcompletion,$course->fullname,build_navigation($navlinks));
    if($svgcleverness) {
        print '<script type="text/javascript" src="textrotate.js"></script>';
    }

    // Handle groups (if enabled)
    groups_print_course_menu($course,$CFG->wwwroot.'/course/report/progress/?course='.$course->id);
}

// Okay, let's draw the table of progress info,

// Start of table  
if(!$csv) {
    print '<br class="clearer"/>'; // ugh
    if(count($progress)==0) {
        print '<p class="nousers">'.get_string('err_nousers','completion').'</p>';
        print '<p><a href="'.$reportsurl.'">'.get_string('continue').'</a></p>';
        print_footer($course);
        exit;
    }
    print '<table id="completion-progress" class="generaltable flexible boxaligncenter" style="text-align:left"><tr style="vertical-align:top">';

    // User heading / sort option
    print '<th scope="col" class="completion-sortchoice">';
    if($firstnamesort) {
        print 
            get_string('firstname').' / <a href="./?course='.$course->id.'">'.
            get_string('lastname').'</a>';
    } else {
        print '<a href="./?course='.$course->id.'&sort=firstname">'.
            get_string('firstname').'</a> / '.
            get_string('lastname');
    }
    print '</th>';
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

    if($csv) {
        print ','.csv_quote($activity->name).','.csv_quote($datetext);
    } else {
        print '<th scope="col" class="'.$activity->datepassedclass.'">'.
            '<a href="'.$CFG->wwwroot.'/mod/'.$activity->modname.
            '/view.php?id='.$activity->id.'">'.
            '<img src="'.$CFG->pixpath.'/mod/'.$activity->modname.'/icon.gif" alt="'.
            get_string('modulename',$activity->modname).'" /> <span class="completion-activityname">'.
            format_string($activity->name).'</span></a>';
        if($activity->completionexpected) {
            print '<div class="completion-expected"><span>'.$datetext.'</span></div>';
        }
        print '</th>';
    }
}

if($csv) {
    print "\n";
} else {
    print '</tr>';
}

// Row for each user
foreach($progress as $user) {
    // User name
    if($csv) {
        print csv_quote(fullname($user));
    } else {
        print '<tr><th scope="row"><a href="'.$CFG->wwwroot.'/user/view.php?id='.
            $user->id.'&amp;course='.$course->id.'">'.fullname($user).'</a></th>';
    }

    // Progress for each activity
    foreach($activities as $activity) {

        // Get progress information and state
        if(array_key_exists($activity->id,$user->progress)) {
            $progress=$user->progress[$activity->id];
            $state=$progress->completionstate;
            $date=userdate($progress->timemodified);
        } else {
            $state=COMPLETION_INCOMPLETE;
            $date='';
        }

        // Work out how it corresponds to an icon
        $completiontype=
            ($activity->completion==COMPLETION_TRACKING_AUTOMATIC ? 'auto' : 'manual').
            '-';
        switch($state) {
            case COMPLETION_INCOMPLETE : $completiontype.='n'; break;
            case COMPLETION_COMPLETE : $completiontype.='y'; break;
            case COMPLETION_COMPLETE_PASS : $completiontype.='pass'; break;
            case COMPLETION_COMPLETE_FAIL : $completiontype.='fail'; break;
        }        

        $describe=get_string('completion-alt-'.$completiontype,'completion');
        $a=new StdClass;
        $a->state=$describe;
        $a->date=$date;
        $a->user=fullname($user);
        $a->activity=$activity->name;
        $fulldescribe=get_string('progress-title','completion',$a);

        if($csv) {
            print ','.csv_quote($describe).','.csv_quote($date);
        } else {
            print '<td class="completion-progresscell '.$activity->datepassedclass.'">'.
                '<img src="'.$CFG->pixpath.'/i/completion-'.$completiontype.
                '.gif" alt="'.$describe.'" title="'.$fulldescribe.'" /></td>';
        }
    }

    if($csv) {
        print "\n";
    } else {
        print '</tr>';
    }
}

if($csv) {
    exit;
}
print '</table>';

print '<ul class="progress-actions"><li><a href="index.php?course='.$course->id.
    '&amp;format=csv">'.get_string('csvdownload','completion').'</a></li></ul>';

print_footer($course);
?>
