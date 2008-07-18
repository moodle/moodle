<?php // $Id$
/**
* prints an analysed excel-spreadsheet of the feedback
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    require_once('easy_excel.php');
 
    $id = required_param('id', PARAM_INT);  //the POST dominated the GET
    $coursefilter = optional_param('coursefilter', '0', PARAM_INT);    
    
    $formdata = data_submitted();
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('feedback', $id)) {
            error("Course Module ID was incorrect");
        }
     
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            error("Course is misconfigured");
        }
     
        if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
            error("Course module is incorrect");
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);

    require_login($course->id, true, $cm);
    
    if(!$capabilities->viewreports){
        error(get_string('error'));
    }

    //buffering any output
    //this prevents some output before the excel-header will be send
    ob_start();
    $fstring = new object();
    $fstring->bold = get_string('bold', 'feedback');
    $fstring->page = get_string('page', 'feedback');
    $fstring->of = get_string('of', 'feedback');
    $fstring->modulenameplural = get_string('modulenameplural', 'feedback');
    $fstring->questions = get_string('questions', 'feedback');
    $fstring->question = get_string('question', 'feedback');
    $fstring->responses = get_string('responses', 'feedback');
    $fstring->idnumber = get_string('idnumber');
    $fstring->username = get_string('username');
    $fstring->fullname = get_string('fullname');
    $fstring->courseid = get_string('courseid', 'feedback');
    $fstring->course = get_string('course');
    $fstring->anonymous_user = get_string('anonymous_user','feedback');
    ob_end_clean();
    
    //get the questions (item-names)
    if(!$items = $DB->get_records('feedback_item', array('feedback'=>$feedback->id, 'hasvalue'=>1), 'position')) {
        error(get_string('no_items_available_yet', 'feedback'), $CFG->wwwroot.'/mod/feedback/view.php?id='.$id);
        exit;
    }

    $filename = "feedback.xls";
    
    //get the groupid for this module
    //get the groupid
    $mygroupid = $SESSION->feedback->lstgroupid;

    // Creating a workbook
    $workbook = new EasyWorkbook("-");
    $workbook->setTempDir($CFG->dataroot.'/temp');
    $workbook->send($filename);
    $workbook->setVersion(8);
    // Creating the worksheets
    $sheetname = clean_param($feedback->name, PARAM_ALPHANUM);
    error_reporting(0);
    $worksheet1 =& $workbook->addWorksheet(substr($sheetname, 0, 31));
    $worksheet1->set_workbook($workbook);
    $worksheet2 =& $workbook->addWorksheet('detailed');
    $worksheet2->set_workbook($workbook);
    error_reporting($CFG->debug);
    $worksheet1->setPortrait();
    $worksheet1->setPaper(9);
    $worksheet1->centerHorizontally();
    $worksheet1->hideGridlines();
    $worksheet1->setHeader("&\"Arial," . $fstring->bold . "\"&14".$feedback->name);
    $worksheet1->setFooter($fstring->page." &P " . $fstring->of . " &N");
    $worksheet1->setColumn(0, 0, 30);
    $worksheet1->setColumn(1, 20, 15);
    $worksheet1->setMargins_LR(0.10);

    $worksheet2->setLandscape();
    $worksheet2->setPaper(9);
    $worksheet2->centerHorizontally();

    //writing the table header
    $rowOffset1 = 0;
    $worksheet1->setFormat("<f>",12,false);
    $worksheet1->write_string($rowOffset1, 0, UserDate(time()));

    ////////////////////////////////////////////////////////////////////////
    //print the analysed sheet
    ////////////////////////////////////////////////////////////////////////
    //get the completeds
    $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid, $coursefilter);
    if($completedscount > 0){
        //write the count of completeds
        $rowOffset1++;
        $worksheet1->write_string($rowOffset1, 0, $fstring->modulenameplural.': '.strval($completedscount));
    }

    if(is_array($items)){
        $rowOffset1++;
        $worksheet1->write_string($rowOffset1, 0, $fstring->questions.': '. strval(sizeof($items)));
    }
    
    $rowOffset1 += 2;
    $worksheet1->write_string($rowOffset1, 0, $fstring->question);
    $worksheet1->write_string($rowOffset1, 1, $fstring->responses);
    $rowOffset1++ ;

    if (empty($items)) {
         $items=array();
    }
    foreach($items as $item) {
        //get the class of item-typ
        $itemclass = 'feedback_item_'.$item->typ;
        //get the instance of the item-class
        $itemobj = new $itemclass();
        $rowOffset1 = $itemobj->excelprint_item($worksheet1, $rowOffset1, $item, $mygroupid, $coursefilter);
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
    $rowOffset2 = feedback_excelprint_detailed_head($worksheet2, $items, $rowOffset2);
    
    
    if(is_array($completeds)){
        foreach($completeds as $completed) {
            $rowOffset2 = feedback_excelprint_detailed_items($worksheet2, $completed, $items, $rowOffset2);
        }
    }
    
    
    $workbook->close();
    exit;
////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////    
//functions
////////////////////////////////////////////////////////////////////////////////    

    
    function feedback_excelprint_detailed_head(&$worksheet, $items, $rowOffset) {
        global $fstring, $feedback;
        
        if(!$items) return;
        $colOffset = 0;
        
        $worksheet->setFormat('<l><f><ru2>');

        $worksheet->write_string($rowOffset, $colOffset, $fstring->idnumber);
        $colOffset++;

        $worksheet->write_string($rowOffset, $colOffset, $fstring->username);
        $colOffset++;

        $worksheet->write_string($rowOffset, $colOffset, $fstring->fullname);
        $colOffset++;
        
        foreach($items as $item) {
            $worksheet->setFormat('<l><f><ru2>');
            $worksheet->write_string($rowOffset, $colOffset, $item->name);
            $colOffset++;
        }

        $worksheet->setFormat('<l><f><ru2>');
        $worksheet->write_string($rowOffset, $colOffset, $fstring->courseid);
        $colOffset++;

        $worksheet->setFormat('<l><f><ru2>');
        $worksheet->write_string($rowOffset, $colOffset, $fstring->course);
        $colOffset++;

        return $rowOffset + 1;
    }
    
    function feedback_excelprint_detailed_items(&$worksheet, $completed, $items, $rowOffset) {
        global $DB, $fstring;
        
        if(!$items) return;
        $colOffset = 0;
        $courseid = 0;
        
        $feedback = $DB->get_record('feedback', array('id'=>$completed->feedback));
        //get the username
        //anonymous users are separated automatically because the userid in the completed is "0"
        $worksheet->setFormat('<l><f><ru2>');
        if($user = $DB->get_record('user', array('id'=>$completed->userid))) {
            if ($completed->anonymous_response == FEEDBACK_ANONYMOUS_NO) {
                $worksheet->write_string($rowOffset, $colOffset, $user->idnumber);
                $colOffset++;
                $userfullname = fullname($user);
                $worksheet->write_string($rowOffset, $colOffset, $user->username);
                $colOffset++;
            } else {
                $userfullname = $fstring->anonymous_user;
                $worksheet->write_string($rowOffset, $colOffset, '-');
                $colOffset++;
                $worksheet->write_string($rowOffset, $colOffset, '-');
                $colOffset++;
            }
        }else {
            $userfullname = $fstring->anonymous_user;
            $worksheet->write_string($rowOffset, $colOffset, '-');
            $colOffset++;
            $worksheet->write_string($rowOffset, $colOffset, '-');
            $colOffset++;
        }
        
        $worksheet->write_string($rowOffset, $colOffset, $userfullname);
        
        $colOffset++;
        foreach($items as $item) {
            $value = $DB->get_record('feedback_value', array('item'=>$item->id, 'completed'=>$completed->id));
            
            $itemclass = 'feedback_item_'.$item->typ;
            $itemobj = new $itemclass();
            $printval = $itemobj->get_printval($item, $value);

            $worksheet->setFormat('<l><vo>');
            if(is_numeric($printval)) {
                $worksheet->write_number($rowOffset, $colOffset, trim($printval));
            } else {
                $worksheet->write_string($rowOffset, $colOffset, trim($printval));
            }
            $printval = '';
            $colOffset++;
            $courseid = isset($value->course_id) ? $value->course_id : 0;
            if($courseid == 0) $courseid = $feedback->course;
        }
        $worksheet->write_number($rowOffset, $colOffset, $courseid);
        $colOffset++;
        if(isset($courseid) AND $course = $DB->get_record('course', array('id'=>$courseid))) {
            $worksheet->write_string($rowOffset, $colOffset, $course->shortname);
        }
        return $rowOffset + 1;
    }
?>
