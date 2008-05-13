<?php  // $Id$
include '../../../../config.php';
include $CFG->dirroot."/lib/graphlib.php";
include $CFG->dirroot."/mod/quiz/report/reportlib.php";

$quizid = required_param('id', PARAM_INT);

$quiz = get_record('quiz', 'id', $quizid);
$course = get_record('course', 'id', $quiz->course);
require_login($course);
$cm = get_coursemodule_from_instance('quiz', $quizid);
$currentgroup = groups_get_activity_group($cm);

$modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/quiz:viewreports', $modcontext);

$line = new graph(640,480);
$line->parameter['title']   = '';
$line->parameter['y_label_left'] = $course->students;
$line->parameter['x_label'] = get_string('grade');
$line->parameter['y_label_angle'] = 90;
$line->parameter['x_label_angle'] = 0;
$line->parameter['x_axis_angle'] = 60;

//following two lines seem to silence notice warnings from graphlib.php
$line->y_tick_labels = null;
$line->offset_relation = null;

$line->parameter['bar_size']    = 1.5; // make size > 1 to get overlap effect
$line->parameter['bar_spacing'] = 30; // don't forget to increase spacing so that graph doesn't become one big block of colour

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
$bandlabels = array();
for ($i=0;$i < $quiz->grade;$i += $bandwidth){
    $bandlabels[] = number_format($i, $quiz->decimalpoints).' - '.number_format($i+$bandwidth, $quiz->decimalpoints);
} 
$line->x_data          = $bandlabels;

$useridlist = join(',',array_keys(get_users_by_capability($modcontext, 'mod/quiz:attempt','','','','','','',false)));
$line->y_data['allusers'] = quiz_report_grade_bands($bands, $quizid, $useridlist);
if ($currentgroup){
    //only turn on legends if there is more than one set of bars
    $line->parameter['legend']        = 'outside-top';
    $line->parameter['legend_border'] = 'black';
    $line->parameter['legend_offset'] = 4;
    $useridingrouplist = join(',',array_keys(get_users_by_capability($modcontext, 'mod/quiz:attempt','','','','',$currentgroup,'',false)));
    $line->y_data['groupusers'] = quiz_report_grade_bands($bands, $quizid, $useridingrouplist);
    $line->y_format['groupusers'] =
        array('colour' => 'green', 'bar' => 'fill', 'shadow_offset' => 1, 'legend' => groups_get_group_name($currentgroup));
    $line->y_order = array('allusers', 'groupusers');
} else {
    $line->y_order = array('allusers');
}


$line->y_format['allusers'] =
  array('colour' => 'red', 'bar' => 'fill', 'shadow_offset' => 1, 'legend' => get_string('allparticipants'));


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
