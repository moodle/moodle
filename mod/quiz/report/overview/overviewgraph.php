<?php  // $Id$
include '../../../../config.php';
include $CFG->dirroot."/lib/graphlib.php";
include $CFG->dirroot."/mod/quiz/report/reportlib.php";
function graph_get_new_colour(){
    static $colourindex = 0;
    $colours = array('red', 'green', 'yellow', 'orange', 'purple', 'black', 'maroon', 'blue', 'ltgreen', 'navy', 'ltred', 'ltltgreen', 'ltltorange', 'olive', 'gray', 'ltltred', 'ltorange', 'lime', 'ltblue', 'ltltblue');
    $colour = $colours[$colourindex];
    $colourindex++;
    if ($colourindex > (count($colours)-1)){
        $colourindex =0;
    }
    return $colour;
}
define('QUIZ_REPORT_MAX_PARTICIPANTS_TO_SHOW_ALL_GROUPS', 500);
$quizid = required_param('id', PARAM_INT);

$quiz = get_record('quiz', 'id', $quizid);
$course = get_record('course', 'id', $quiz->course);
require_login($course);
$cm = get_coursemodule_from_instance('quiz', $quizid);
if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
    $groups = groups_get_activity_allowed_groups($cm);
} else {
    $groups = false;
}
$modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/quiz:viewreports', $modcontext);

$line = new graph(800,600);
$line->parameter['title']   = '';
$line->parameter['y_label_left'] = $course->students;
$line->parameter['x_label'] = get_string('grade');
$line->parameter['y_label_angle'] = 90;
$line->parameter['x_label_angle'] = 0;
$line->parameter['x_axis_angle'] = 60;

//following two lines seem to silence notice warnings from graphlib.php
$line->y_tick_labels = null;
$line->offset_relation = null;

$line->parameter['bar_size']    = 1; // will make size > 1 to get overlap effect when showing groups
$line->parameter['bar_spacing'] = 10; // don't forget to increase spacing so that graph doesn't become one big block of colour

//pick a sensible number of bands depending on quiz maximum grade.
$bands = $quiz->grade;
while ($bands >= 20 || $bands < 10){
    if ($bands >= 50){
        $bands = $bands /5;
    } else if ($bands >= 20) {
        $bands = $bands /2;
    }
    if ($bands < 4){
        $bands = $bands * 5;
    } else if ($bands < 10){
        $bands = $bands * 2;
    }
}

$bandwidth = $quiz->grade / $bands;
$bands = ceil($bands);
$bandlabels = array();
for ($i=0;$i < $quiz->grade;$i += $bandwidth){
    $label = number_format($i, $quiz->decimalpoints).' - ';
    if ($quiz->grade > $i+$bandwidth){
        $label .= number_format($i+$bandwidth, $quiz->decimalpoints);
    } else {
        $label .= number_format($quiz->grade, $quiz->decimalpoints);
    }
    $bandlabels[] = $label;
} 
$line->x_data          = $bandlabels;

$line->y_format['allusers'] =
  array('colour' => graph_get_new_colour(), 'bar' => 'fill', 'shadow_offset' => 1, 'legend' => get_string('allparticipants'));
$line->y_data['allusers'] = quiz_report_grade_bands($bandwidth, $bands, $quizid);
if (array_sum($line->y_data['allusers'])>QUIZ_REPORT_MAX_PARTICIPANTS_TO_SHOW_ALL_GROUPS ||
        count($groups)>4){
    if ($groups){
        if ($currentgroup = groups_get_activity_group($cm)){
            $groups = array($currentgroup=>'');
        } else {
            $groups = false;//all participants mode
        }
    }
}
$line->y_order = array('allusers');
if ($groups){
    foreach (array_keys($groups) as $group){
        $useridingroup = get_users_by_capability($modcontext, array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),'','','','',$group,'',false);
        if ($useridingroup){
            $useridingrouplist = join(',',array_keys($useridingroup));
            $groupdata = quiz_report_grade_bands($bandwidth, $bands, $quizid, $useridingrouplist);
            if ($groupdata){
                $line->parameter['bar_size']    = 1.2;
                $line->y_data['groupusers'.$group] = $groupdata;
                //only turn on legends if there is more than one set of bars
                $line->parameter['legend']        = 'outside-top';
                $line->parameter['legend_border'] = 'black';
                $line->parameter['legend_offset'] = 4;
                $line->y_format['groupusers'.$group] =
                    array('colour' => graph_get_new_colour(), 'bar' => 'fill', 'shadow_offset' => 1, 'legend' => groups_get_group_name($group));
                $line->y_order[] ='groupusers'.$group;
            }
        }
    }
}




$line->parameter['y_min_left'] = 0;  // start at 0
$line->parameter['y_max_left'] = max($line->y_data['allusers']); 
$line->parameter['y_decimal_left'] = 0; // 2 decimal places for y axis.


//pick a sensible number of gridlines depending on max value on graph.
$gridlines = max($line->y_data['allusers']);
while ($gridlines >= 10){
    if ($gridlines >= 50){
        $gridlines = $gridlines /5;
    } else {
        $gridlines = $gridlines /2;
    }
}

$line->parameter['y_axis_gridlines'] = $gridlines+1; 
$line->draw();

?>
