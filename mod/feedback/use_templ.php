<?php // $Id$
/**
* print the confirm dialog to use template and create new items from template
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    require_once('use_templ_form.php');

    $id = required_param('id', PARAM_INT); 
    $templateid = optional_param('templateid', false, PARAM_INT);
    $deleteolditems = optional_param('deleteolditems', 0, PARAM_INT);
   
    if(!$templateid) {
        redirect('edit.php?id='.$id);
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
    
    if(!$capabilities->edititems){
        print_error('error');
    }
    
    $mform = new mod_feedback_use_templ_form();
    $newformdata = array('id'=>$id,
                        'templateid'=>$templateid,
                        'confirmadd'=>'1',
                        'deleteolditems'=>'1',
                        'do_show'=>'edit');
    $mform->set_data($newformdata);
    $formdata = $mform->get_data();
    
    if ($mform->is_cancelled()) {
        redirect('edit.php?id='.$id.'&do_show=templates');
    }
    
    if(isset($formdata->confirmadd) AND $formdata->confirmadd == 1){
        feedback_items_from_template($feedback, $templateid, $deleteolditems);
        redirect('edit.php?id=' . $id);
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
    
    // print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
    print_box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
    echo $OUTPUT->heading(get_string('confirmusetemplate', 'feedback'));
    
    $mform->display();

    // print_simple_box_end();
    print_box_end();

    $templateitems = $DB->get_records('feedback_item', array('template'=>$templateid), 'position');
    if(is_array($templateitems)){
        $templateitems = array_values($templateitems);
    }

    if(is_array($templateitems)){
        $itemnr = 0;
        echo '<p align="center">'.get_string('preview', 'feedback').'</p>';
        // print_simple_box_start('center', '75%');
        print_box_start('generalbox boxaligncenter boxwidthwide');
        echo '<div class="mdl-align"><table>';
        foreach($templateitems as $templateitem){
            echo '<tr>';
            if($templateitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo '<td valign="top">' . $itemnr . '.&nbsp;</td>';
            } else {
                echo '<td>&nbsp;</td>';
            }
            if($templateitem->typ != 'pagebreak') {
                feedback_print_item($templateitem);
            }else {
                echo '<td><hr /></td><td>'.get_string('pagebreak', 'feedback').'</td>';
            }
            echo '</tr>';
            echo '<tr><td>&nbsp;</td></tr>';
        }
        echo '</table></div>';
        // print_simple_box_end();
        print_box_end();
    }else{
        // print_simple_box(get_string('no_items_available_at_this_template','feedback'),"center");
        print_box(get_string('no_items_available_at_this_template','feedback'),'generalbox boxaligncenter boxwidthwide');
    }

    echo $OUTPUT->footer();

?>
