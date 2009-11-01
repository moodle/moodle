<?php
/**
* deletes an item of the feedback
*
* @version $Id$
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

    require_once("../../config.php");
    require_once("lib.php");
    require_once('delete_item_form.php');

    $id = required_param('id', PARAM_INT);
    $deleteitem = required_param('deleteitem', PARAM_INT);

    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/feedback/delete_item.php', array('id'=>$id, 'deleteitem'=>$deleteitem)));

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

    $mform = new mod_feedback_delete_item_form();
    $newformdata = array('id'=>$id,
                        'deleteitem'=>$deleteitem,
                        'confirmdelete'=>'1');
    $mform->set_data($newformdata);
    $formdata = $mform->get_data();

    if ($mform->is_cancelled()) {
        redirect('edit.php?id='.$id);
    }

    if(isset($formdata->confirmdelete) AND $formdata->confirmdelete == 1){
        feedback_delete_item($formdata->deleteitem);
        redirect('edit.php?id=' . $id);
    }


    /// Print the page header
    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");

    $PAGE->navbar->add($strfeedbacks, new moodle_url($CFG->wwwroot.'/mod/feedback/index.php', array('id'=>$course->id)));
    $PAGE->navbar->add(format_string($feedback->name));

    $PAGE->set_title(format_string($feedback->name));
    $PAGE->set_button($OUTPUT->update_module_button($cm->id, 'feedback'));
    echo $OUTPUT->header();

    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    echo $OUTPUT->heading(format_text($feedback->name));
    echo $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
    echo $OUTPUT->heading(get_string('confirmdeleteitem', 'feedback'));
    print_string('relateditemsdeleted','feedback');
    $mform->display();
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();


