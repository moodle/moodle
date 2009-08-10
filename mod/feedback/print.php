<?php // $Id$
/**
* print a printview of feedback-items
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);

    $formdata = data_submitted();

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

    if(!$capabilities->edititems){
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


    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    echo $OUTPUT->heading(format_text($feedback->name));

    feedback_print_errors();

    $feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
    if(is_array($feedbackitems)){
        $itemnr = 0;

        // print_simple_box_start('center', '80%');
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo '<div class="mdl-align printview"><table>';
        //print the inserted items
        $itempos = 0;
        foreach($feedbackitems as $feedbackitem){
            $itempos++;
            echo '<tr>';
            //Items without value only are labels
            if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo '<td valign="top">' . $itemnr . '.&nbsp;</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            if($feedbackitem->typ != 'pagebreak') {
                feedback_print_item($feedbackitem, false, false, true);
            }else {
                echo '<td class="feedback_print_pagebreak" colspan="2">&nbsp;</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '<font color="red">(*)' . get_string('items_are_required', 'feedback') . '</font>';
        echo '</div>';
        // print_simple_box_end();
        echo $OUTPUT->box_end();
    }else{
        // print_simple_box(get_string('no_items_available_yet','feedback'),"center");
        echo $OUTPUT->box(get_string('no_items_available_yet','feedback'),'generalbox boxaligncenter boxwidthwide');
    }
    print_continue('view.php?id='.$id);
    /// Finish the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    echo $OUTPUT->footer();

?>
