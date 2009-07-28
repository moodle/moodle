<?php // $Id$
/**
* shows an analysed view of a feedback on the mainsite
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    
    // $SESSION->feedback->current_tab = 'analysis';
    $current_tab = 'analysis';
 
    $id = required_param('id', PARAM_INT);  //the POST dominated the GET
    $coursefilter = optional_param('coursefilter', '0', PARAM_INT);
    $courseitemfilter = optional_param('courseitemfilter', '0', PARAM_INT);
    $courseitemfiltertyp = optional_param('courseitemfiltertyp', '0', PARAM_ALPHANUM);
    // $searchcourse = optional_param('searchcourse', '', PARAM_ALPHAEXT);
    $searchcourse = optional_param('searchcourse', '', PARAM_RAW);
    $courseid = optional_param('courseid', false, PARAM_INT);
    
    if(($searchcourse OR $courseitemfilter OR $coursefilter) AND !confirm_sesskey()) {
        print_error('invalidsesskey');
    }
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('feedback', $id)) {
            print_error('invalidcoursemodule');
        }
     
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }
     
        if (! $feedback = $DB->get_record("feedback", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    }
    $capabilities = feedback_load_capabilities($cm->id);

    require_login($course->id, true, $cm);
    
    if( !( (intval($feedback->publish_stats) == 1) || $capabilities->viewreports)) {
        print_error('error');
    }
    
    /// Print the page header
    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");
    $buttontext = update_module_button($cm->id, $course->id, $strfeedback);
    
    $navlinks = array();
    $navlinks[] = array('name' => $strfeedbacks, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($feedback->name), 'link' => "", 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string($feedback->name), "",
                 $navigation, "", "", true, $buttontext, navmenu($course, $cm));

    /// print the tabs
    include('tabs.php');

    //print the analysed items
    // print_simple_box_start("center", '80%');
    print_box_start('generalbox boxaligncenter boxwidthwide');

    if( $capabilities->viewreports ) {
        //button "export to excel"
        echo '<div class="mdl-align">';
        $export_button_link = 'analysis_to_excel.php';
        $export_button_options = array('sesskey'=>sesskey(), 'id'=>$id, 'coursefilter'=>$coursefilter);
        $export_button_label = get_string('export_to_excel', 'feedback');
        print_single_button($export_button_link, $export_button_options, $export_button_label, 'post');
        echo '</div>';
    }
    
    //get the groupid
    //lstgroupid is the choosen id
    $mygroupid = false;
    //get completed feedbacks
    $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid, $coursefilter);
    
    //show the count
    echo '<b>'.get_string('completed_feedbacks', 'feedback').': '.$completedscount. '</b><br />';
    
    // get the items of the feedback
    $items = $DB->get_records('feedback_item', array('feedback'=>$feedback->id, 'hasvalue'=>1), 'position');
    //show the count
    if(is_array($items)){
    	echo '<b>'.get_string('questions', 'feedback').': ' .sizeof($items). ' </b><hr />';
        echo '<a href="analysis_course.php?id=' . $id . '&courseid='.$courseid.'">'.get_string('show_all', 'feedback').'</a>';
    } else {
        $items=array();
    }

    echo '<form name="report" method="post" id="analysis-form">';
    echo '<div class="mdl-align"><table width="80%" cellpadding="10">';
    if ($courseitemfilter > 0) {
        $avgvalue = 'avg(value)';
        if ($DB->get_dbfamily() == 'postgres') { // TODO: this should be moved to standard sql DML function ;-)
             $avgvalue = 'avg(cast (value as integer))';
        }
        if ($courses = $DB->get_records_sql ("SELECT fv.course_id, c.shortname, $avgvalue AS avgvalue
                                                FROM {feedback_value} fv, {course} c, {feedback_item} fi
                                               WHERE fv.course_id = c.id AND fi.id = fv.item AND fi.typ = ? AND fv.item = ?
                                            GROUP BY course_id, shortname
                                            ORDER BY avgvalue desc",
                                              array($courseitemfiltertyp, $courseitemfilter))) {
             $item = $DB->get_record('feedback_item', array('id'=>$courseitemfilter));
             echo '<tr><th colspan="2">'.$item->name.'</th></tr>';
             echo '<tr><td><table align="left">';
             echo '<tr><th>Course</th><th>Average</th></tr>';
             $sep_dec = get_string('separator_decimal', 'feedback');
             $sep_thous = get_string('separator_thousand', 'feedback');

             foreach ($courses as $c) {
                  echo '<tr><td>'.$c->shortname.'</td><td align="right">'.number_format(($c->avgvalue), 2, $sep_dec, $sep_thous).'</td></tr>';
             }
             echo '</table></td></tr>';
        } else {
             echo '<tr><td>'.get_string('noresults').'</td></tr>';
        }
    } else {

        echo get_string('search_course', 'feedback') . ': ';
        echo '<input type="text" name="searchcourse" value="'.s($searchcourse).'"/> <input type="submit" value="'.get_string('search').'"/>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '<input type="hidden" name="courseitemfilter" value="'.$courseitemfilter.'" />';
        echo '<input type="hidden" name="courseitemfiltertyp" value="'.$courseitemfiltertyp.'" />';
        echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
        echo $PAGE->requires->js('mod/feedback/feedback.js')->asap();
        $sql = 'select c.id, c.shortname from {course} c, '.
                                              '{feedback_value} fv, {feedback_item} fi '.
                                              'where c.id = fv.course_id and fv.item = fi.id '.
                                              'and fi.feedback = ?'.
                                              'and 
                                              (c.shortname '.$DB->sql_ilike().' ?
                                              OR c.fullname '.$DB->sql_ilike().' ?)';
        $params = array($feedback->id, "%$searchcourse%", "%$searchcourse%");
        
        if ($courses = $DB->get_records_sql_menu($sql, $params)) {

             echo ' ' . get_string('filter_by_course', 'feedback') . ': ';
             $selectmenu = new moodle_select_menu();
             $selectmenu->options = $courses;
             $selectmenu->name = 'coursefilter';
             $selectmenu->selectedvalue = $coursefilter;
             $selectmenu->add_action('change', 'submit_form_by_id', array('id' => 'analysis-form'));
             echo $OUTPUT->select_menu($selectmenu);
        }
        echo '<hr />';
        $itemnr = 0;
        //print the items in an analysed form
        echo '<tr><td>';
        foreach($items as $item) {
            if($item->hasvalue == 0) continue;
            echo '<table width="100%" class="generalbox">';	
            //get the class from item-typ
            $itemclass = 'feedback_item_'.$item->typ;
            //get the instance of the item-class
            $itemobj = new $itemclass();
            $itemnr++;
            if($feedback->autonumbering) {
                $printnr = $itemnr.'.';
            } else {
                $printnr = '';
            }
            $itemobj->print_analysed($item, $printnr, $mygroupid, $coursefilter);
            if (preg_match('/rated$/i', $item->typ)) {
                 echo '<tr><td colspan="2"><a href="#" onclick="setcourseitemfilter('.$item->id.',\''.$item->typ.'\'); return false;">'.
                    get_string('sort_by_course', 'feedback').'</a></td></tr>'; 
            }
            echo '</table>';
        }
        echo '</td></tr>';
    }
    echo '</table></div>';
    echo '</form>';
    print_box_end();
    
    print_footer($course);

?>
