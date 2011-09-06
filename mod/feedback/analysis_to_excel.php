<?php

/**
* prints an analysed excel-spreadsheet of the feedback
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

require_once("../../config.php");
require_once("lib.php");
// require_once('easy_excel.php');
require_once("$CFG->libdir/excellib.class.php");

feedback_load_feedback_items();

$id = required_param('id', PARAM_INT);  //the POST dominated the GET
$coursefilter = optional_param('coursefilter', '0', PARAM_INT);

$url = new moodle_url('/mod/feedback/analysis_to_excel.php', array('id'=>$id));
if ($coursefilter !== '0') {
    $url->param('coursefilter', $coursefilter);
}
$PAGE->set_url($url);

$formdata = data_submitted();

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
}

require_login($course->id, true, $cm);

require_capability('mod/feedback:viewreports', $context);

//buffering any output
//this prevents some output before the excel-header will be send
ob_start();
$fstring = new stdClass();
$fstring->bold = get_string('bold', 'feedback');
$fstring->page = get_string('page', 'feedback');
$fstring->of = get_string('of', 'feedback');
$fstring->modulenameplural = get_string('modulenameplural', 'feedback');
$fstring->questions = get_string('questions', 'feedback');
$fstring->itemlabel = get_string('item_label', 'feedback');
$fstring->question = get_string('question', 'feedback');
$fstring->responses = get_string('responses', 'feedback');
$fstring->idnumber = get_string('idnumber');
$fstring->username = get_string('username');
$fstring->fullname = get_string('fullnameuser');
$fstring->courseid = get_string('courseid', 'feedback');
$fstring->course = get_string('course');
$fstring->anonymous_user = get_string('anonymous_user','feedback');
ob_end_clean();

//get the questions (item-names)
if(!$items = $DB->get_records('feedback_item', array('feedback'=>$feedback->id, 'hasvalue'=>1), 'position')) {
    print_error('no_items_available_yet', 'feedback', $CFG->wwwroot.'/mod/feedback/view.php?id='.$id);
    exit;
}

$filename = "feedback.xls";

$mygroupid = groups_get_activity_group($cm);

// Creating a workbook
// $workbook = new EasyWorkbook("-");
$workbook = new MoodleExcelWorkbook('-');
// $workbook->setTempDir($CFG->dataroot.'/temp');
$workbook->send($filename);
// $workbook->setVersion(8);

//creating the needed formats
$xlsFormats = new stdClass();
$xlsFormats->head1 = $workbook->add_format(array(
                        'bold'=>1,
                        'size'=>12));

$xlsFormats->head2 = $workbook->add_format(array(
                        'align'=>'left',
                        'bold'=>1,
                        'bottum'=>2));

$xlsFormats->default = $workbook->add_format(array(
                        'align'=>'left',
                        'v_align'=>'top'));

// $xlsFormats->head2_green = $workbook->add_format(array(
                        // 'align'=>'left',
                        // 'bold'=>1,
                        // 'v_align'=>'top',
                        // 'bottum'=>2,
                        // 'fg_color'=>'green'));

$xlsFormats->value_bold = $workbook->add_format(array(
                        'align'=>'left',
                        'bold'=>1,
                        'v_align'=>'top'));

// $xlsFormats->value_blue = $workbook->add_format(array(
                        // 'align'=>'left',
                        // 'bold'=>1,
                        // 'v_align'=>'top',
                        // 'top'=>2,
                        // 'fg_color'=>'blue'));

// $xlsFormats->value_red = $workbook->add_format(array(
                        // 'align'=>'left',
                        // 'bold'=>1,
                        // 'v_align'=>'top',
                        // 'top'=>2,
                        // 'fg_color'=>'red'));

$xlsFormats->procent = $workbook->add_format(array(
                        'align'=>'left',
                        'bold'=>1,
                        'v_align'=>'top',
                        'num_format'=>'#,##0.00%'));

// Creating the worksheets
$sheetname = clean_param($feedback->name, PARAM_ALPHANUM);
error_reporting(0);
$worksheet1 =& $workbook->add_worksheet(substr($sheetname, 0, 31));
// $worksheet1->set_workbook($workbook);
$worksheet2 =& $workbook->add_worksheet('detailed');
// $worksheet2->set_workbook($workbook);
error_reporting($CFG->debug);
// $worksheet1->pear_excel_worksheet->set_portrait();
// $worksheet1->pear_excel_worksheet->set_paper(9);
// $worksheet1->pear_excel_worksheet->center_horizontally();
$worksheet1->hide_gridlines();
// $worksheet1->pear_excel_worksheet->set_header("&\"Arial," . $fstring->bold . "\"&14".$feedback->name);
// $worksheet1->pear_excel_worksheet->set_footer($fstring->page." &P " . $fstring->of . " &N");
$worksheet1->set_column(0, 0, 10);
$worksheet1->set_column(1, 1, 30);
$worksheet1->set_column(2, 20, 15);
// $worksheet1->set_margins_LR(0.10);

// $worksheet2->pear_excel_worksheet->set_landscape();
// $worksheet2->pear_excel_worksheet->set_paper(9);
// $worksheet2->pear_excel_worksheet->center_horizontally();

//writing the table header
$rowOffset1 = 0;
// $worksheet1->setFormat("<f>",12,false);
$worksheet1->write_string($rowOffset1, 0, UserDate(time()), $xlsFormats->head1);

////////////////////////////////////////////////////////////////////////
//print the analysed sheet
////////////////////////////////////////////////////////////////////////
//get the completeds
$completedscount = feedback_get_completeds_group_count($feedback, $mygroupid, $coursefilter);
if($completedscount > 0){
    //write the count of completeds
    $rowOffset1++;
    $worksheet1->write_string($rowOffset1, 0, $fstring->modulenameplural.': '.strval($completedscount), $xlsFormats->head1);
}

if(is_array($items)){
    $rowOffset1++;
    $worksheet1->write_string($rowOffset1, 0, $fstring->questions.': '. strval(sizeof($items)), $xlsFormats->head1);
}

$rowOffset1 += 2;
$worksheet1->write_string($rowOffset1, 0, $fstring->itemlabel, $xlsFormats->head1);
$worksheet1->write_string($rowOffset1, 1, $fstring->question, $xlsFormats->head1);
$worksheet1->write_string($rowOffset1, 2, $fstring->responses, $xlsFormats->head1);
$rowOffset1++ ;

if (empty($items)) {
     $items=array();
}
foreach($items as $item) {
    //get the class of item-typ
    $itemobj = feedback_get_item_class($item->typ);
    $rowOffset1 = $itemobj->excelprint_item($worksheet1, $rowOffset1, $xlsFormats, $item, $mygroupid, $coursefilter);
}

////////////////////////////////////////////////////////////////////////
//print the detailed sheet
////////////////////////////////////////////////////////////////////////
//get the completeds

$completeds = feedback_get_completeds_group($feedback, $mygroupid, $coursefilter);
//important: for each completed you have to print each item, even if it is not filled out!!!
//therefor for each completed we have to iterate over all items of the feedback
//this is done by feedback_excelprint_detailed_items

$rowOffset2 = 0;
//first we print the table-header
$rowOffset2 = feedback_excelprint_detailed_head($worksheet2, $xlsFormats, $items, $rowOffset2);


if(is_array($completeds)){
    foreach($completeds as $completed) {
        $rowOffset2 = feedback_excelprint_detailed_items($worksheet2, $xlsFormats, $completed, $items, $rowOffset2);
    }
}


$workbook->close();
exit;
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//functions
////////////////////////////////////////////////////////////////////////////////


function feedback_excelprint_detailed_head(&$worksheet, $xlsFormats, $items, $rowOffset) {
    global $fstring, $feedback;

    if(!$items) return;
    $colOffset = 0;

    // $worksheet->setFormat('<l><f><ru2>');

    $worksheet->write_string($rowOffset + 1, $colOffset, $fstring->idnumber, $xlsFormats->head2);
    $colOffset++;

    $worksheet->write_string($rowOffset + 1, $colOffset, $fstring->username, $xlsFormats->head2);
    $colOffset++;

    $worksheet->write_string($rowOffset + 1, $colOffset, $fstring->fullname, $xlsFormats->head2);
    $colOffset++;

    foreach($items as $item) {
        // $worksheet->setFormat('<l><f><ru2>');
        $worksheet->write_string($rowOffset, $colOffset, $item->name, $xlsFormats->head2);
        $worksheet->write_string($rowOffset + 1, $colOffset, $item->label, $xlsFormats->head2);
        $colOffset++;
    }

    // $worksheet->setFormat('<l><f><ru2>');
    $worksheet->write_string($rowOffset + 1, $colOffset, $fstring->courseid, $xlsFormats->head2);
    $colOffset++;

    // $worksheet->setFormat('<l><f><ru2>');
    $worksheet->write_string($rowOffset + 1, $colOffset, $fstring->course, $xlsFormats->head2);
    $colOffset++;

    return $rowOffset + 2;
}

function feedback_excelprint_detailed_items(&$worksheet, $xlsFormats, $completed, $items, $rowOffset) {
    global $DB, $fstring;

    if(!$items) return;
    $colOffset = 0;
    $courseid = 0;

    $feedback = $DB->get_record('feedback', array('id'=>$completed->feedback));
    //get the username
    //anonymous users are separated automatically because the userid in the completed is "0"
    // $worksheet->setFormat('<l><f><ru2>');
    if($user = $DB->get_record('user', array('id'=>$completed->userid))) {
        if ($completed->anonymous_response == FEEDBACK_ANONYMOUS_NO) {
            $worksheet->write_string($rowOffset, $colOffset, $user->idnumber, $xlsFormats->head2);
            $colOffset++;
            $userfullname = fullname($user);
            $worksheet->write_string($rowOffset, $colOffset, $user->username, $xlsFormats->head2);
            $colOffset++;
        } else {
            $userfullname = $fstring->anonymous_user;
            $worksheet->write_string($rowOffset, $colOffset, '-', $xlsFormats->head2);
            $colOffset++;
            $worksheet->write_string($rowOffset, $colOffset, '-', $xlsFormats->head2);
            $colOffset++;
        }
    }else {
        $userfullname = $fstring->anonymous_user;
        $worksheet->write_string($rowOffset, $colOffset, '-', $xlsFormats->head2);
        $colOffset++;
        $worksheet->write_string($rowOffset, $colOffset, '-', $xlsFormats->head2);
        $colOffset++;
    }

    $worksheet->write_string($rowOffset, $colOffset, $userfullname, $xlsFormats->head2);

    $colOffset++;
    foreach($items as $item) {
        $value = $DB->get_record('feedback_value', array('item'=>$item->id, 'completed'=>$completed->id));

        $itemobj = feedback_get_item_class($item->typ);
        $printval = $itemobj->get_printval($item, $value);
        $printval = trim($printval);

        // $worksheet->setFormat('<l><vo>');
        if(is_numeric($printval)) {
            $worksheet->write_number($rowOffset, $colOffset, $printval, $xlsFormats->default);
        } elseif($printval != '') {
            $worksheet->write_string($rowOffset, $colOffset, $printval, $xlsFormats->default);
        }
        $printval = '';
        $colOffset++;
        $courseid = isset($value->course_id) ? $value->course_id : 0;
        if($courseid == 0) $courseid = $feedback->course;
    }
    $worksheet->write_number($rowOffset, $colOffset, $courseid, $xlsFormats->default);
    $colOffset++;
    if (isset($courseid) AND $course = $DB->get_record('course', array('id' => $courseid))) {
        $shortname = format_string($course->shortname, true, array('context' => get_context_instance(CONTEXT_COURSE, $courseid)));
        $worksheet->write_string($rowOffset, $colOffset, $shortname, $xlsFormats->default);
    }
    return $rowOffset + 1;
}
