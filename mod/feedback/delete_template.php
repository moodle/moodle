<?php

/**
* deletes a template
*
* @author Andreas Grabs
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package feedback
*/

require_once("../../config.php");
require_once("lib.php");
require_once('delete_template_form.php');
require_once($CFG->libdir.'/tablelib.php');

// $SESSION->feedback->current_tab = 'templates';
$current_tab = 'templates';

$id = required_param('id', PARAM_INT);
$canceldelete = optional_param('canceldelete', false, PARAM_INT);
$shoulddelete = optional_param('shoulddelete', false, PARAM_INT);
$deletetempl = optional_param('deletetempl', false, PARAM_INT);
// $formdata = data_submitted();

$url = new moodle_url('/mod/feedback/delete_template.php', array('id'=>$id));
if ($canceldelete !== false) {
    $url->param('canceldelete', $canceldelete);
}
if ($shoulddelete !== false) {
    $url->param('shoulddelete', $shoulddelete);
}
if ($deletetempl !== false) {
    $url->param('deletetempl', $deletetempl);
}
$PAGE->set_url($url);

if(($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

if($canceldelete == 1){
    $editurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$id, 'do_show'=>'templates'));
    redirect($editurl->out(false));
}

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

require_capability('mod/feedback:deletetemplate', $context);

$mform = new mod_feedback_delete_template_form();
$newformdata = array('id'=>$id,
                    'deletetempl'=>$deletetempl,
                    'confirmdelete'=>'1');

$mform->set_data($newformdata);
$formdata = $mform->get_data();

$deleteurl = new moodle_url('/mod/feedback/delete_template.php', array('id'=>$id));

if ($mform->is_cancelled()) {
    redirect($deleteurl->out(false));
}

if(isset($formdata->confirmdelete) AND $formdata->confirmdelete == 1){
    if(!$template = $DB->get_record("feedback_template", array("id"=>$deletetempl))) {
        print_error('error');
    }
    
    if($template->ispublic) {
        $systemcontext = get_system_context();
        require_capability('mod/feedback:createpublictemplate', $systemcontext);
        require_capability('mod/feedback:deletetemplate', $systemcontext);
    }
    
    feedback_delete_template($template);
    redirect($deleteurl->out(false));
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");
$str_delete_feedback = get_string('delete_template','feedback');

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

/// print the tabs
include('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading($str_delete_feedback);
if($shoulddelete == 1) {

    echo $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
    echo $OUTPUT->heading(get_string('confirmdeletetemplate', 'feedback'));
    $mform->display();
    echo $OUTPUT->box_end();
}else {
    //first we get the own templates
    $templates = feedback_get_template_list($course, 'own');
    if(!is_array($templates)) {
        echo $OUTPUT->box(get_string('no_templates_available_yet', 'feedback'), 'generalbox boxaligncenter');
    }else {
        echo $OUTPUT->heading(get_string('course'), 3);
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');
        $tablecolumns = array('template', 'action');
        $tableheaders = array(get_string('template', 'feedback'), '');
        $table_course = new flexible_table('feedback_template_course_table');

        $table_course->define_columns($tablecolumns);
        $table_course->define_headers($tableheaders);
        $table_course->define_baseurl($deleteurl);
        $table_course->column_style('action', 'width', '10%');

        $table_course->sortable(false);
        $table_course->set_attribute('width', '100%');
        $table_course->set_attribute('class', 'generaltable');
        $table_course->setup();

        foreach($templates as $template) {
            $data = array();
            $data[] = $template->name;
            $url = new moodle_url($deleteurl, array(
                                            'id'=>$id,
                                            'deletetempl'=>$template->id,
                                            'shoulddelete'=>1,
                                            ));
                                                                                  
            $data[] = $OUTPUT->single_button($url, $str_delete_feedback, 'post');
            $table_course->add_data($data);
        }
        $table_course->finish_output();
        echo $OUTPUT->box_end();
    }
    //now we get the public templates if it is permitted
    $systemcontext = get_system_context();
    if(has_capability('mod/feedback:createpublictemplate', $systemcontext) AND
        has_capability('mod/feedback:deletetemplate', $systemcontext)) {
        $templates = feedback_get_template_list($course, 'public');
        if(!is_array($templates)) {
            echo $OUTPUT->box(get_string('no_templates_available_yet', 'feedback'), 'generalbox boxaligncenter');
        }else {
            echo $OUTPUT->heading(get_string('public', 'feedback'), 3);
            echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');
            $tablecolumns = $tablecolumns = array('template', 'action');
            $tableheaders = array(get_string('template', 'feedback'), '');
            $table_public = new flexible_table('feedback_template_public_table');

            $table_public->define_columns($tablecolumns);
            $table_public->define_headers($tableheaders);
            $table_public->define_baseurl($deleteurl);
            $table_public->column_style('action', 'width', '10%');

            $table_public->sortable(false);
            $table_public->set_attribute('width', '100%');
            $table_public->set_attribute('class', 'generaltable');
            $table_public->setup();

            
            // echo $OUTPUT->heading(get_string('public', 'feedback'), 3);
            // echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
            foreach($templates as $template) {
                $data = array();
                $data[] = $template->name;
                $url = new moodle_url($deleteurl, array(
                                                'id'=>$id,
                                                'deletetempl'=>$template->id,
                                                'shoulddelete'=>1,
                                                ));
                                                                                      
                $data[] = $OUTPUT->single_button($url, $str_delete_feedback, 'post');
                $table_public->add_data($data);
            }
            $table_public->finish_output();
            echo $OUTPUT->box_end();
        }
    }
    
    echo $OUTPUT->box_start('boxaligncenter boxwidthnormal');
    $url = new moodle_url($deleteurl, array(
                                    'id'=>$id,
                                    'canceldelete'=>1,
                                    ));
                                                                          
    echo $OUTPUT->single_button($url, get_string('back'), 'post');
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();

