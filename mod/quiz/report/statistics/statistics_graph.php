<?php
include '../../../../config.php';
include $CFG->dirroot."/lib/graphlib.php";
include $CFG->dirroot."/mod/quiz/locallib.php";
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
$quizstatisticsid = required_param('id', PARAM_INT);

$quizstatistics = $DB->get_record('quiz_statistics', array('id' => $quizstatisticsid));
$questionstatistics = $DB->get_records('quiz_question_statistics', array('quizstatisticsid' => $quizstatistics->id, 'subquestion' => 0));
$quiz = $DB->get_record('quiz', array('id' => $quizstatistics->quizid));
require_login($quiz->course);
$questions = quiz_report_load_questions($quiz);
$cm = get_coursemodule_from_instance('quiz', $quiz->id);
if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
    $groups = groups_get_activity_allowed_groups($cm);
} else {
    $groups = false;
}
if ($quizstatistics->groupid){
    if (!in_array($quizstatistics->groupid, array_keys($groups))){
        print_error('groupnotamember', 'group');
    }
}
$modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('quizreport/statistics:view', $modcontext);

$line = new graph(800,600);
$line->parameter['title']   = '';
$line->parameter['y_label_left'] = '%';
$line->parameter['x_label'] = get_string('position', 'quiz_statistics');
$line->parameter['y_label_angle'] = 90;
$line->parameter['x_label_angle'] = 0;
$line->parameter['x_axis_angle'] = 60;

$line->parameter['legend']        = 'outside-right';
$line->parameter['legend_border'] = 'black';
$line->parameter['legend_offset'] = 4;


$line->parameter['bar_size']    = 1;

$line->parameter['zero_axis']        = 'grayEE';


$fieldstoplot = array('facility' => get_string('facility', 'quiz_statistics'), 'discriminativeefficiency' => get_string('discriminative_efficiency', 'quiz_statistics'));
$fieldstoplotfactor = array('facility' => 100, 'discriminativeefficiency' => 1);

$line->x_data = array();
foreach (array_keys($fieldstoplot) as $fieldtoplot){
    $line->y_data[$fieldtoplot] = array();
    $line->y_format[$fieldtoplot] =
            array('colour' => graph_get_new_colour(), 'bar' => 'fill', 'shadow_offset' => 1, 'legend' => $fieldstoplot[$fieldtoplot]);
}
foreach ($questionstatistics as $questionstatistic){
    $line->x_data[$questions[$questionstatistic->questionid]->number] = $questions[$questionstatistic->questionid]->number;
    foreach (array_keys($fieldstoplot) as $fieldtoplot){
        $value = !is_null($questionstatistic->$fieldtoplot)?$questionstatistic->$fieldtoplot:0;
        $value = $value * $fieldstoplotfactor[$fieldtoplot];
        $line->y_data[$fieldtoplot][$questions[$questionstatistic->questionid]->number] = $value;
    }
}
foreach (array_keys($line->y_data) as $fieldtoplot){
    ksort($line->y_data[$fieldtoplot]);
    $line->y_data[$fieldtoplot] = array_values($line->y_data[$fieldtoplot]);
}
ksort($line->x_data);
$line->x_data = array_values($line->x_data);
$max = 0;
$min = 0;
foreach (array_keys($fieldstoplot) as $fieldtoplot){
    ksort($line->y_data[$fieldtoplot]);
    $line->y_data[$fieldtoplot] = array_values($line->y_data[$fieldtoplot]);
    $max = (max($line->y_data[$fieldtoplot])> $max)? max($line->y_data[$fieldtoplot]): $max;
    $min = (min($line->y_data[$fieldtoplot])< $min)? min($line->y_data[$fieldtoplot]): $min;
}
$line->y_order = array_keys($fieldstoplot);

$gridresolution = 10;

$max = ceil($max / $gridresolution) * $gridresolution;
$min = floor($min / $gridresolution) * $gridresolution;

$gridlines = ceil(($max - $min) / $gridresolution);


$line->parameter['y_axis_gridlines'] = $gridlines+1;

$line->parameter['y_min_left'] = $min;
$line->parameter['y_max_left'] = $max;
$line->parameter['y_decimal_left'] = 0;


$line->draw();

