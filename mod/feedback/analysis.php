<?php // $Id$
/**
* shows an analysed view of feedback
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
    $courseid = optional_param('courseid', false, PARAM_INT);

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

    if($course->id == SITEID) {
        require_login($course->id, true);
    }else{
        require_login($course->id, true, $cm);
    }

    //check whether the given courseid exists
    if($courseid AND $courseid != SITEID) {
        if($course2 = $DB->get_record('course', array('id'=>$courseid))){
            require_course_login($course2); //this overwrites the object $course :-(
            $course = $DB->get_record("course", array("id"=>$cm->course)); // the workaround
        }else {
            print_error('invalidcourseid');
        }
    }

    if( !( ((intval($feedback->publish_stats) == 1) AND $capabilities->viewanalysepage) || $capabilities->viewreports)) {
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


    //print analysed items
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

    //get the groupid
    $groupselect = groups_print_activity_menu($cm, 'analysis.php?id=' . $cm->id.'&do_show=analysis', true);
    $mygroupid = groups_get_activity_group($cm);

    if( $capabilities->viewreports ) {

        echo isset($groupselect) ? $groupselect : '';
        echo '<div class="clearer"></div>';

        //button "export to excel"
        //echo '<div class="mdl-align">';
        // echo '<div class="feedback_centered_button">';
        echo '<div class="form-buttons">';
        $export_button_link = 'analysis_to_excel.php';
        $export_button_options = array('sesskey'=>sesskey(), 'id'=>$id);
        $export_button_label = get_string('export_to_excel', 'feedback');
        print_single_button($export_button_link, $export_button_options, $export_button_label, 'post');
        echo '</div>';
    }

    //get completed feedbacks
    $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid);

    //show the group, if available
    if($mygroupid and $group = $DB->get_record('groups', array('id'=>$mygroupid))) {
        echo '<b>'.get_string('group').': '.$group->name. '</b><br />';
    }
    //show the count
    echo '<b>'.get_string('completed_feedbacks', 'feedback').': '.$completedscount. '</b><br />';

    // get the items of the feedback
    $items = $DB->get_records('feedback_item', array('feedback'=>$feedback->id, 'hasvalue'=>1), 'position');
    //show the count
    if(is_array($items)){
        echo '<b>'.get_string('questions', 'feedback').': ' .sizeof($items). ' </b><hr />';
    } else {
        $items=array();
    }
    $check_anonymously = true;
    if($mygroupid > 0 AND $feedback->anonymous == FEEDBACK_ANONYMOUS_YES) {
        if($completedscount < FEEDBACK_MIN_ANONYMOUS_COUNT_IN_GROUP) {
            $check_anonymously = false;
        }
    }
    // echo '<div class="mdl-align"><table width="80%" cellpadding="10"><tr><td>';
    echo '<div><table width="80%" cellpadding="10"><tr><td>';
    if($check_anonymously) {
        $itemnr = 0;
        //print the items in an analysed form
        foreach($items as $item) {
            if($item->hasvalue == 0) continue;
            echo '<table width="100%" class="generalbox">';
            //get the class of item-typ
            $itemclass = 'feedback_item_'.$item->typ;
            //get the instance of the item-class
            $itemobj = new $itemclass();
            $itemnr++;
            if($feedback->autonumbering) {
                $printnr = $itemnr.'.';
            } else {
                $printnr = '';
            }
            $itemobj->print_analysed($item, $printnr, $mygroupid);
            // $itemnr = $itemobj->print_analysed($item, $itemnr, $mygroupid);
            echo '</table>';
        }
    }else {
        print_heading_with_help(get_string('insufficient_responses_for_this_group', 'feedback'), 'insufficient_responses', 'feedback');
    }
    echo '</td></tr></table></div>';
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();

?>
