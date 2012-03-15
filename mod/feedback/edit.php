<?php

/**
* prints the form to edit the feedback items such moving, deleting and so on
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

require_once("../../config.php");
require_once("lib.php");
require_once('edit_form.php');

feedback_init_feedback_session();

$id = required_param('id', PARAM_INT);

if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

$do_show = optional_param('do_show', 'edit', PARAM_ALPHA);
$moveupitem = optional_param('moveupitem', false, PARAM_INT);
$movedownitem = optional_param('movedownitem', false, PARAM_INT);
$moveitem = optional_param('moveitem', false, PARAM_INT);
$movehere = optional_param('movehere', false, PARAM_INT);
$switchitemrequired = optional_param('switchitemrequired', false, PARAM_INT);

// $SESSION->feedback->current_tab = $do_show;
$current_tab = $do_show;

$url = new moodle_url('/mod/feedback/edit.php', array('id'=>$id, 'do_show'=>$do_show));

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

require_capability('mod/feedback:edititems', $context);

//move up/down items
if($moveupitem){
    $item = $DB->get_record('feedback_item', array('id'=>$moveupitem));
    feedback_moveup_item($item);
}
if($movedownitem){
    $item = $DB->get_record('feedback_item', array('id'=>$movedownitem));
    feedback_movedown_item($item);
}

//moving of items
if($movehere && isset($SESSION->feedback->moving->movingitem)){
    $item = $DB->get_record('feedback_item', array('id'=>$SESSION->feedback->moving->movingitem));
    feedback_move_item($item, intval($movehere));
    $moveitem = false;
}
if($moveitem){
    $item = $DB->get_record('feedback_item', array('id'=>$moveitem));
    $SESSION->feedback->moving->shouldmoving = 1;
    $SESSION->feedback->moving->movingitem = $moveitem;
} else {
    unset($SESSION->feedback->moving);
}

if($switchitemrequired) {
    $item = $DB->get_record('feedback_item', array('id'=>$switchitemrequired));
    @feedback_switch_item_required($item);
    redirect($url->out(false));
    exit;
}

//the create_template-form
$create_template_form = new feedback_edit_create_template_form();
$create_template_form->set_feedbackdata(array('context' => $context));
$create_template_form->set_form_elements();
$create_template_form->set_data(array('id'=>$id, 'do_show'=>'templates'));
$create_template_formdata = $create_template_form->get_data();
if(isset($create_template_formdata->savetemplate) && $create_template_formdata->savetemplate == 1) {
    //check the capabilities to create templates
    if(!has_capability('mod/feedback:createprivatetemplate', $context) AND
        !has_capability('mod/feedback:createpublictemplate', $context)) {
        print_error('cannotsavetempl', 'feedback');
    }
    if(trim($create_template_formdata->templatename) == '')
    {
        $savereturn = 'notsaved_name';
    }else {
        //public templates are currently deaktivated
        // if(has_capability('mod/feedback:createpublictemplate', $context)) {
            // $create_template_formdata->ispublic = isset($create_template_formdata->ispublic) ? 1 : 0;
        // }else {
            $create_template_formdata->ispublic = 0;
        // }
        if(!feedback_save_as_template($feedback, $create_template_formdata->templatename, $create_template_formdata->ispublic))
        {
            $savereturn = 'failed';
        }else {
            $savereturn = 'saved';
        }
    }
}

//get the feedbackitems
$lastposition = 0;
$feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
if(is_array($feedbackitems)){
    $feedbackitems = array_values($feedbackitems);
    if(count($feedbackitems) > 0) {
        $lastitem = $feedbackitems[count($feedbackitems)-1];
        $lastposition = $lastitem->position;
    }else {
        $lastposition = 0;
    }
}
$lastposition++;


//the add_item-form
$add_item_form = new feedback_edit_add_question_form('edit_item.php');
$add_item_form->set_data(array('cmid'=>$id, 'position'=>$lastposition));

//the use_template-form
$use_template_form = new feedback_edit_use_template_form('use_templ.php');
$use_template_form->set_feedbackdata(array('course' => $course));
$use_template_form->set_form_elements();
$use_template_form->set_data(array('id'=>$id));

//the create_template-form
//$create_template_form = new feedback_edit_create_template_form('use_templ.php');

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_url('/mod/feedback/edit.php', array('id'=>$cm->id, 'do_show'=>$do_show));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

/// print the tabs
include('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

$savereturn=isset($savereturn)?$savereturn:'';

//print the messages
if($savereturn == 'notsaved_name') {
    echo '<p align="center"><b><font color="red">'.get_string('name_required','feedback').'</font></b></p>';
}

if($savereturn == 'saved') {
    echo '<p align="center"><b><font color="green">'.get_string('template_saved','feedback').'</font></b></p>';
}

if($savereturn == 'failed') {
    echo '<p align="center"><b><font color="red">'.get_string('saving_failed','feedback').'</font></b></p>';
}

feedback_print_errors();

///////////////////////////////////////////////////////////////////////////
///print the template-section
///////////////////////////////////////////////////////////////////////////
if($do_show == 'templates') {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    $use_template_form->display();

    if(has_capability('mod/feedback:createprivatetemplate', $context) OR
                has_capability('mod/feedback:createpublictemplate', $context)) {
        $deleteurl = new moodle_url('/mod/feedback/delete_template.php', array('id'=>$id));
        $create_template_form->display();
        echo '<p><a href="'.$deleteurl->out().'">'.get_string('delete_templates', 'feedback').'</a></p>';
    }else {
        echo '&nbsp;';
    }

    if(has_capability('mod/feedback:edititems', $context)) {
        $exporturl = new moodle_url('/mod/feedback/export.php', array('action'=>'exportfile', 'id'=>$id));
        $importurl = new moodle_url('/mod/feedback/import.php', array('id'=>$id));
        echo '<p>
            <a href="'.$exporturl->out().'">'.get_string('export_questions', 'feedback').'</a>/
            <a href="'.$importurl->out().'">'.get_string('import_questions', 'feedback').'</a>
        </p>';
    }
    echo $OUTPUT->box_end();
}
///////////////////////////////////////////////////////////////////////////
///print the Item-Edit-section
///////////////////////////////////////////////////////////////////////////
if($do_show == 'edit') {

    $add_item_form->display();

    if(is_array($feedbackitems)){
        $itemnr = 0;
        
        $align = right_to_left() ? 'right' : 'left';

        $helpbutton = $OUTPUT->help_icon('preview', 'feedback');

        echo $OUTPUT->heading($helpbutton . get_string('preview', 'feedback'));
        if(isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->shouldmoving == 1) {
            echo $OUTPUT->heading('<a href="edit.php?id='.$id.'">'.get_string('cancel_moving', 'feedback').'</a>');
        }
        // echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

        //check, if there exists required-elements
        $countreq = $DB->count_records('feedback_item', array('feedback'=>$feedback->id, 'required'=> 1));
        if($countreq > 0) {
            // echo '<font color="red">(*)' . get_string('items_are_required', 'feedback') . '</font>';
            echo '<span class="feedback_required_mark">(*)' . get_string('items_are_required', 'feedback') . '</span>';
        }

        //use list instead a table
        echo $OUTPUT->box_start('feedback_items');
            if(isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->shouldmoving == 1) {
                $moveposition = 1;
                $movehereurl = new moodle_url($url, array('movehere'=>$moveposition));
                echo $OUTPUT->box_start('feedback_item_box_'.$align.' clipboard'); //only shown if shouldmoving = 1
                    $buttonlink = $movehereurl->out();
                    echo '<a title="'.get_string('move_here','feedback').'" href="'.$buttonlink.'">
                            <img class="movetarget" alt="'.get_string('move_here','feedback').'" src="'.$OUTPUT->pix_url('movehere') . '" />
                          </a>';
                echo $OUTPUT->box_end();
            }
            //print the inserted items
            $itempos = 0;
            foreach($feedbackitems as $feedbackitem){
                $itempos++;
                if(isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->movingitem == $feedbackitem->id){ //hiding the item to move
                    continue;
                }
                if($feedbackitem->dependitem > 0) {
                    $dependstyle = ' feedback_depend';
                }else {
                    $dependstyle = '';
                }
                echo $OUTPUT->box_start('feedback_item_box_'.$align.$dependstyle);
                    //items without value only are labels
                    if($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                        $itemnr++;
                        echo $OUTPUT->box_start('feedback_item_number_'.$align) . $itemnr . $OUTPUT->box_end();
                    }
                    echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                        echo $OUTPUT->box_start('feedback_item_commands_'.$align);
                            echo '<span class="feedback_item_commands">('.get_string('position', 'feedback').':'.$itempos .')</span>';
                            //print the moveup-button
                            if($feedbackitem->position > 1){
                                echo '<span class="feedback_item_command_moveup">';
                                $moveupurl = new moodle_url($url, array('moveupitem'=>$feedbackitem->id));
                                $buttonlink = $moveupurl->out();
                                echo '<a class="icon up" title="'.get_string('moveup_item','feedback').'" href="'.$buttonlink.'">
                                        <img alt="'.get_string('moveup_item','feedback').'" src="'.$OUTPUT->pix_url('t/up') . '" />
                                      </a>';
                                echo '</span>';
                            }
                            //print the movedown-button
                            if($feedbackitem->position < $lastposition - 1){
                                echo '<span class="feedback_item_command_movedown">';
                                $movedownurl = new moodle_url($url, array('movedownitem'=>$feedbackitem->id));
                                $buttonlink = $movedownurl->out();
                                echo '<a class="icon down" title="'.get_string('movedown_item','feedback').'" href="'.$buttonlink.'">
                                        <img alt="'.get_string('movedown_item','feedback').'" src="'.$OUTPUT->pix_url('t/down') . '" />
                                      </a>';
                                echo '</span>';
                            }
                            //print the move-button
                            echo '<span class="feedback_item_command_move">';
                            $moveurl = new moodle_url($url, array('moveitem'=>$feedbackitem->id));
                            $buttonlink = $moveurl->out();
                            echo '<a class="editing_move" title="'.get_string('move_item','feedback').'" href="'.$buttonlink.'">
                                    <img alt="'.get_string('move_item','feedback').'" src="'.$OUTPUT->pix_url('t/move') . '" />
                                  </a>';
                            echo '</span>';
                            //print the button to edit the item
                            if($feedbackitem->typ != 'pagebreak') {
                                echo '<span class="feedback_item_command_edit">';
                                $editurl = new moodle_url('/mod/feedback/edit_item.php');
                                $editurl->params(array('do_show'=>$do_show,
                                                         'cmid'=>$id,
                                                         'id'=>$feedbackitem->id,
                                                         'typ'=>$feedbackitem->typ));
                                
                                // in edit_item.php the param id is used for the itemid and the cmid is the id to get the module
                                $buttonlink = $editurl->out();
                                echo '<a class="editing_update" title="'.get_string('edit_item','feedback').'" href="'.$buttonlink.'">
                                        <img alt="'.get_string('edit_item','feedback').'" src="'.$OUTPUT->pix_url('t/edit') . '" />
                                      </a>';
                                echo '</span>';
                            }

                            //print the toggle-button to switch required yes/no
                            if($feedbackitem->hasvalue == 1) {
                                echo '<span class="feedback_item_command_toggle">';
                                // echo '<form action="edit.php" method="post"><fieldset>';
                                if($feedbackitem->required == 1) {
                                    // echo '<input title="'.get_string('switch_item_to_not_required','feedback').'" type="image" src="pics/required.gif" hspace="1" height="11" width="11" border="0" />';
                                    // echo '<input class="feedback_required_button" title="'.get_string('switch_item_to_not_required','feedback').'" type="image" src="pics/required.gif" />';
                                    $buttontitle = get_string('switch_item_to_not_required','feedback');
                                    $buttonimg = $OUTPUT->pix_url('required', 'feedback');
                                } else {
                                    // echo '<input title="'.get_string('switch_item_to_required','feedback').'" type="image" src="pics/notrequired.gif" hspace="1" height="11" width="11" border="0" />';
                                    // echo '<input class="feedback_required_button" title="'.get_string('switch_item_to_required','feedback').'" type="image" src="pics/notrequired.gif" />';
                                    $buttontitle = get_string('switch_item_to_required','feedback');
                                    $buttonimg = $OUTPUT->pix_url('notrequired', 'feedback');
                                }
                                $requiredurl = new moodle_url($url, array('switchitemrequired'=>$feedbackitem->id));
                                $buttonlink = $requiredurl->out();
                                echo '<a class="icon feedback_switchrequired" title="'.$buttontitle.'" href="'.$buttonlink.'">
                                        <img alt="'.$buttontitle.'" src="'.$buttonimg.'" />
                                      </a>';
                                echo '</span>';
                            }
                            
                            //print the delete-button
                            echo '<span class="feedback_item_command_toggle">';
                            $deleteitemurl = new moodle_url('/mod/feedback/delete_item.php');
                            $deleteitemurl->params(array('id'=>$id,
                                                         'do_show'=>$do_show,
                                                         'deleteitem'=>$feedbackitem->id));
                                                         
                            $buttonlink = $deleteitemurl->out();
                            echo '<a class="icon delete" title="'.get_string('delete_item','feedback').'" href="'.$buttonlink.'">
                                    <img alt="'.get_string('delete_item','feedback').'" src="'.$OUTPUT->pix_url('t/delete') . '" />
                                  </a>';
                            echo '</span>';
                        echo $OUTPUT->box_end();
                    if($feedbackitem->typ != 'pagebreak') {
                        feedback_print_item_preview($feedbackitem);
                    }else {
                        echo $OUTPUT->box_start('feedback_pagebreak');
                        echo get_string('pagebreak', 'feedback').'<hr class="feedback_pagebreak" />';
                        echo $OUTPUT->box_end();
                    }
                    echo $OUTPUT->box_end();
                echo $OUTPUT->box_end();
                if(isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->shouldmoving == 1) {
                    $moveposition++;
                    $movehereurl->param('movehere', $moveposition);
                    echo $OUTPUT->box_start('clipboard'); //only shown if shouldmoving = 1
                            $buttonlink = $movehereurl->out();
                            echo '<a title="'.get_string('move_here','feedback').'" href="'.$buttonlink.'">
                                    <img class="movetarget" alt="'.get_string('move_here','feedback').'" src="'.$OUTPUT->pix_url('movehere') . '" />
                                  </a>';
                    echo $OUTPUT->box_end();
                }
                echo '<div class="clearer">&nbsp;</div>';
            }
        echo $OUTPUT->box_end();
    }else{
        echo $OUTPUT->box(get_string('no_items_available_yet','feedback'),'generalbox boxaligncenter');
    }
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();
