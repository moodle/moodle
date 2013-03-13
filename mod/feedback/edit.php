<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * prints the form to edit the feedback items such moving, deleting and so on
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once('../../config.php');
require_once('lib.php');
require_once('edit_form.php');

feedback_init_feedback_session();

$id = required_param('id', PARAM_INT);

if (($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

$do_show = optional_param('do_show', 'edit', PARAM_ALPHA);
$moveupitem = optional_param('moveupitem', false, PARAM_INT);
$movedownitem = optional_param('movedownitem', false, PARAM_INT);
$moveitem = optional_param('moveitem', false, PARAM_INT);
$movehere = optional_param('movehere', false, PARAM_INT);
$switchitemrequired = optional_param('switchitemrequired', false, PARAM_INT);

$current_tab = $do_show;

$url = new moodle_url('/mod/feedback/edit.php', array('id'=>$id, 'do_show'=>$do_show));

if (! $cm = get_coursemodule_from_id('feedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $feedback = $DB->get_record('feedback', array('id'=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

require_capability('mod/feedback:edititems', $context);

//Move up/down items
if ($moveupitem) {
    $item = $DB->get_record('feedback_item', array('id'=>$moveupitem));
    feedback_moveup_item($item);
}
if ($movedownitem) {
    $item = $DB->get_record('feedback_item', array('id'=>$movedownitem));
    feedback_movedown_item($item);
}

//Moving of items
if ($movehere && isset($SESSION->feedback->moving->movingitem)) {
    $item = $DB->get_record('feedback_item', array('id'=>$SESSION->feedback->moving->movingitem));
    feedback_move_item($item, intval($movehere));
    $moveitem = false;
}
if ($moveitem) {
    $item = $DB->get_record('feedback_item', array('id'=>$moveitem));
    $SESSION->feedback->moving->shouldmoving = 1;
    $SESSION->feedback->moving->movingitem = $moveitem;
} else {
    unset($SESSION->feedback->moving);
}

if ($switchitemrequired) {
    $item = $DB->get_record('feedback_item', array('id'=>$switchitemrequired));
    @feedback_switch_item_required($item);
    redirect($url->out(false));
    exit;
}

//The create_template-form
$create_template_form = new feedback_edit_create_template_form();
$create_template_form->set_feedbackdata(array('context'=>$context, 'course'=>$course));
$create_template_form->set_form_elements();
$create_template_form->set_data(array('id'=>$id, 'do_show'=>'templates'));
$create_template_formdata = $create_template_form->get_data();
if (isset($create_template_formdata->savetemplate) && $create_template_formdata->savetemplate == 1) {
    //Check the capabilities to create templates.
    if (!has_capability('mod/feedback:createprivatetemplate', $context) AND
            !has_capability('mod/feedback:createpublictemplate', $context)) {
        print_error('cannotsavetempl', 'feedback');
    }
    if (trim($create_template_formdata->templatename) == '') {
        $savereturn = 'notsaved_name';
    } else {
        //If the feedback is located on the frontpage then templates can be public.
        if (has_capability('mod/feedback:createpublictemplate', get_system_context())) {
            $create_template_formdata->ispublic = isset($create_template_formdata->ispublic) ? 1 : 0;
        } else {
            $create_template_formdata->ispublic = 0;
        }
        if (!feedback_save_as_template($feedback,
                                      $create_template_formdata->templatename,
                                      $create_template_formdata->ispublic)) {
            $savereturn = 'failed';
        } else {
            $savereturn = 'saved';
        }
    }
}

//Get the feedbackitems
$lastposition = 0;
$feedbackitems = $DB->get_records('feedback_item', array('feedback'=>$feedback->id), 'position');
if (is_array($feedbackitems)) {
    $feedbackitems = array_values($feedbackitems);
    if (count($feedbackitems) > 0) {
        $lastitem = $feedbackitems[count($feedbackitems)-1];
        $lastposition = $lastitem->position;
    } else {
        $lastposition = 0;
    }
}
$lastposition++;


//The add_item-form
$add_item_form = new feedback_edit_add_question_form('edit_item.php');
$add_item_form->set_data(array('cmid'=>$id, 'position'=>$lastposition));

//The use_template-form
$use_template_form = new feedback_edit_use_template_form('use_templ.php');
$use_template_form->set_feedbackdata(array('course' => $course));
$use_template_form->set_form_elements();
$use_template_form->set_data(array('id'=>$id));

//Print the page header.
$strfeedbacks = get_string('modulenameplural', 'feedback');
$strfeedback  = get_string('modulename', 'feedback');

$PAGE->set_url('/mod/feedback/edit.php', array('id'=>$cm->id, 'do_show'=>$do_show));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));

//Adding the javascript module for the items dragdrop.
if (count($feedbackitems) > 1) {
    if ($do_show == 'edit' and $CFG->enableajax) {
        $PAGE->requires->strings_for_js(array(
               'pluginname',
               'move_item',
               'position',
            ), 'feedback');
        $PAGE->requires->yui_module('moodle-mod_feedback-dragdrop', 'M.mod_feedback.init_dragdrop',
                array(array('cmid' => $cm->id)));
    }
}

echo $OUTPUT->header();

/// print the tabs
require('tabs.php');

/// Print the main part of the page.
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

$savereturn=isset($savereturn)?$savereturn:'';

//Print the messages.
if ($savereturn == 'notsaved_name') {
    echo '<p align="center"><b><font color="red">'.
          get_string('name_required', 'feedback').
          '</font></b></p>';
}

if ($savereturn == 'saved') {
    echo '<p align="center"><b><font color="green">'.
          get_string('template_saved', 'feedback').
          '</font></b></p>';
}

if ($savereturn == 'failed') {
    echo '<p align="center"><b><font color="red">'.
          get_string('saving_failed', 'feedback').
          '</font></b></p>';
}

///////////////////////////////////////////////////////////////////////////
///Print the template-section.
///////////////////////////////////////////////////////////////////////////
if ($do_show == 'templates') {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    $use_template_form->display();

    if (has_capability('mod/feedback:createprivatetemplate', $context) OR
                has_capability('mod/feedback:createpublictemplate', $context)) {
        $deleteurl = new moodle_url('/mod/feedback/delete_template.php', array('id' => $id));
        $create_template_form->display();
        echo '<p><a href="'.$deleteurl->out().'">'.
             get_string('delete_templates', 'feedback').
             '</a></p>';
    } else {
        echo '&nbsp;';
    }

    if (has_capability('mod/feedback:edititems', $context)) {
        $urlparams = array('action'=>'exportfile', 'id'=>$id);
        $exporturl = new moodle_url('/mod/feedback/export.php', $urlparams);
        $importurl = new moodle_url('/mod/feedback/import.php', array('id'=>$id));
        echo '<p>
            <a href="'.$exporturl->out().'">'.get_string('export_questions', 'feedback').'</a>/
            <a href="'.$importurl->out().'">'.get_string('import_questions', 'feedback').'</a>
        </p>';
    }
    echo $OUTPUT->box_end();
}
///////////////////////////////////////////////////////////////////////////
///Print the Item-Edit-section.
///////////////////////////////////////////////////////////////////////////
if ($do_show == 'edit') {

    $add_item_form->display();

    if (is_array($feedbackitems)) {
        $itemnr = 0;

        $align = right_to_left() ? 'right' : 'left';

        $helpbutton = $OUTPUT->help_icon('preview', 'feedback');

        echo $OUTPUT->heading($helpbutton . get_string('preview', 'feedback'));
        if (isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->shouldmoving == 1) {
            $anker = '<a href="edit.php?id='.$id.'">';
            $anker .= get_string('cancel_moving', 'feedback');
            $anker .= '</a>';
            echo $OUTPUT->heading($anker);
        }

        //Check, if there exists required-elements.
        $params = array('feedback' => $feedback->id, 'required' => 1);
        $countreq = $DB->count_records('feedback_item', $params);
        if ($countreq > 0) {
            echo '<span class="feedback_required_mark">(*)';
            echo get_string('items_are_required', 'feedback');
            echo '</span>';
        }

        //Use list instead a table
        echo $OUTPUT->box_start('feedback_items');
        if (isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->shouldmoving == 1) {
            $moveposition = 1;
            $movehereurl = new moodle_url($url, array('movehere'=>$moveposition));
            //Only shown if shouldmoving = 1
            echo $OUTPUT->box_start('feedback_item_box_'.$align.' clipboard');
            $buttonlink = $movehereurl->out();
            $strbutton = get_string('move_here', 'feedback');
            $src = $OUTPUT->pix_url('movehere');
            echo '<a title="'.$strbutton.'" href="'.$buttonlink.'">
                    <img class="movetarget" alt="'.$strbutton.'" src="'.$src.'" />
                  </a>';
            echo $OUTPUT->box_end();
        }
        //Print the inserted items
        $itempos = 0;
        echo '<div id="feedback_dragarea">'; //The container for the dragging area
        echo '<ul id="feedback_draglist">'; //The list what includes the draggable items
        foreach ($feedbackitems as $feedbackitem) {
            $itempos++;
            //Hiding the item to move
            if (isset($SESSION->feedback->moving)) {
                if ($SESSION->feedback->moving->movingitem == $feedbackitem->id) {
                    continue;
                }
            }
            //Here come the draggable items, each one in a single li-element.
            echo '<li class="feedback_itemlist generalbox" id="feedback_item_'.$feedbackitem->id.'">';
            echo '<span class="spinnertest"> </span>';
            if ($feedbackitem->dependitem > 0) {
                $dependstyle = ' feedback_depend';
            } else {
                $dependstyle = '';
            }
            echo $OUTPUT->box_start('feedback_item_box_'.$align.$dependstyle,
                                    'feedback_item_box_'.$feedbackitem->id);
            //Items without value only are labels
            if ($feedbackitem->hasvalue == 1 AND $feedback->autonumbering) {
                $itemnr++;
                echo $OUTPUT->box_start('feedback_item_number_'.$align);
                echo $itemnr;
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_start('box boxalign_'.$align);
            echo $OUTPUT->box_start('feedback_item_commands_'.$align);
            echo '<span class="feedback_item_commands position">';
            echo '('.get_string('position', 'feedback').':'.$itempos .')';
            echo '</span>';
            //Print the moveup-button
            if ($feedbackitem->position > 1) {
                echo '<span class="feedback_item_command_moveup">';
                $moveupurl = new moodle_url($url, array('moveupitem'=>$feedbackitem->id));
                $buttonlink = $moveupurl->out();
                $strbutton = get_string('moveup_item', 'feedback');
                echo '<a class="icon up" title="'.$strbutton.'" href="'.$buttonlink.'">
                        <img alt="'.$strbutton.'" src="'.$OUTPUT->pix_url('t/up') . '" />
                      </a>';
                echo '</span>';
            }
            //Print the movedown-button
            if ($feedbackitem->position < $lastposition - 1) {
                echo '<span class="feedback_item_command_movedown">';
                $urlparams = array('movedownitem'=>$feedbackitem->id);
                $movedownurl = new moodle_url($url, $urlparams);
                $buttonlink = $movedownurl->out();
                $strbutton = get_string('movedown_item', 'feedback');
                echo '<a class="icon down" title="'.$strbutton.'" href="'.$buttonlink.'">
                        <img alt="'.$strbutton.'" src="'.$OUTPUT->pix_url('t/down') . '" />
                      </a>';
                echo '</span>';
            }
            //Print the move-button
            if (count($feedbackitems) > 1) {
                echo '<span class="feedback_item_command_move">';
                $moveurl = new moodle_url($url, array('moveitem'=>$feedbackitem->id));
                $buttonlink = $moveurl->out();
                $strbutton = get_string('move_item', 'feedback');
                echo '<a class="editing_move" title="'.$strbutton.'" href="'.$buttonlink.'">
                        <img alt="'.$strbutton.'" src="'.$OUTPUT->pix_url('t/move') . '" />
                      </a>';
                echo '</span>';
            }
            //Print the button to edit the item
            if ($feedbackitem->typ != 'pagebreak') {
                echo '<span class="feedback_item_command_edit">';
                $editurl = new moodle_url('/mod/feedback/edit_item.php');
                $editurl->params(array('do_show'=>$do_show,
                                         'cmid'=>$id,
                                         'id'=>$feedbackitem->id,
                                         'typ'=>$feedbackitem->typ));

                // In edit_item.php the param id is used for the itemid
                // and the cmid is the id to get the module.
                $buttonlink = $editurl->out();
                $strbutton = get_string('edit_item', 'feedback');
                echo '<a class="editing_update" title="'.$strbutton.'" href="'.$buttonlink.'">
                        <img alt="'.$strbutton.'" src="'.$OUTPUT->pix_url('t/edit') . '" />
                      </a>';
                echo '</span>';
            }

            //Print the toggle-button to switch required yes/no
            if ($feedbackitem->hasvalue == 1) {
                echo '<span class="feedback_item_command_toggle">';
                if ($feedbackitem->required == 1) {
                    $buttontitle = get_string('switch_item_to_not_required', 'feedback');
                    $buttonimg = $OUTPUT->pix_url('required', 'feedback');
                } else {
                    $buttontitle = get_string('switch_item_to_required', 'feedback');
                    $buttonimg = $OUTPUT->pix_url('notrequired', 'feedback');
                }
                $urlparams = array('switchitemrequired'=>$feedbackitem->id);
                $requiredurl = new moodle_url($url, $urlparams);
                $buttonlink = $requiredurl->out();
                echo '<a class="icon '.
                        'feedback_switchrequired" '.
                        'title="'.$buttontitle.'" '.
                        'href="'.$buttonlink.'">'.
                        '<img alt="'.$buttontitle.'" src="'.$buttonimg.'" />'.
                        '</a>';
                echo '</span>';
            }

            //Print the delete-button
            echo '<span class="feedback_item_command_toggle">';
            $deleteitemurl = new moodle_url('/mod/feedback/delete_item.php');
            $deleteitemurl->params(array('id'=>$id,
                                         'do_show'=>$do_show,
                                         'deleteitem'=>$feedbackitem->id));

            $buttonlink = $deleteitemurl->out();
            $strbutton = get_string('delete_item', 'feedback');
            $src = $OUTPUT->pix_url('t/delete');
            echo '<a class="icon delete" title="'.$strbutton.'" href="'.$buttonlink.'">
                    <img alt="'.$strbutton.'" src="'.$src.'" />
                  </a>';
            echo '</span>';
            echo $OUTPUT->box_end();
            if ($feedbackitem->typ != 'pagebreak') {
                feedback_print_item_preview($feedbackitem);
            } else {
                echo $OUTPUT->box_start('feedback_pagebreak');
                echo get_string('pagebreak', 'feedback').'<hr class="feedback_pagebreak" />';
                echo $OUTPUT->box_end();
            }
            echo $OUTPUT->box_end();
            echo $OUTPUT->box_end();
            echo '<div class="clearer">&nbsp;</div>';
            echo '</li>';
            //Print out the target box if we ar moving an item
            if (isset($SESSION->feedback->moving) AND $SESSION->feedback->moving->shouldmoving == 1) {
                echo '<li>';
                $moveposition++;
                $movehereurl->param('movehere', $moveposition);
                echo $OUTPUT->box_start('clipboard'); //Only shown if shouldmoving = 1
                $buttonlink = $movehereurl->out();
                $strbutton = get_string('move_here', 'feedback');
                $src = $OUTPUT->pix_url('movehere');
                echo '<a title="'.$strbutton.'" href="'.$buttonlink.'">
                        <img class="movetarget" alt="'.$strbutton.'" src="'.$src.'" />
                      </a>';
                echo $OUTPUT->box_end();
                echo '</li>';
            }
        }
        echo $OUTPUT->box_end();
        echo '</ul>';
        echo '</div>';
    } else {
        echo $OUTPUT->box(get_string('no_items_available_yet', 'feedback'),
                         'generalbox boxaligncenter');
    }
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();
