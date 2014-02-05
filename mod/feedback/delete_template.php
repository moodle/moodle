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

$current_tab = 'templates';

$id = required_param('id', PARAM_INT);
$canceldelete = optional_param('canceldelete', false, PARAM_INT);
$shoulddelete = optional_param('shoulddelete', false, PARAM_INT);
$deletetempl = optional_param('deletetempl', false, PARAM_INT);

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

if (($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

if ($canceldelete == 1) {
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

$context = context_module::instance($cm->id);

require_login($course, true, $cm);

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

if (isset($formdata->confirmdelete) AND $formdata->confirmdelete == 1) {
    if (!$template = $DB->get_record("feedback_template", array("id"=>$deletetempl))) {
        print_error('error');
    }

    if ($template->ispublic) {
        $systemcontext = context_system::instance();
        require_capability('mod/feedback:createpublictemplate', $systemcontext);
        require_capability('mod/feedback:deletetemplate', $systemcontext);
    }

    feedback_delete_template($template);
    redirect($deleteurl->out(false));
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");
$strdeletefeedback = get_string('delete_template', 'feedback');

$PAGE->set_heading($course->fullname);
$PAGE->set_title($feedback->name);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($feedback->name));
/// print the tabs
require('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading($strdeletefeedback, 3);
if ($shoulddelete == 1) {

    echo $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
    echo html_writer::tag('p', get_string('confirmdeletetemplate', 'feedback'), array('class' => 'bold'));
    $mform->display();
    echo $OUTPUT->box_end();
} else {
    //first we get the own templates
    $templates = feedback_get_template_list($course, 'own');
    if (!is_array($templates)) {
        echo $OUTPUT->box(get_string('no_templates_available_yet', 'feedback'),
                         'generalbox boxaligncenter');
    } else {
        echo $OUTPUT->heading(get_string('course'), 4);
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');
        $tablecolumns = array('template', 'action');
        $tableheaders = array(get_string('template', 'feedback'), '');
        $tablecourse = new flexible_table('feedback_template_course_table');

        $tablecourse->define_columns($tablecolumns);
        $tablecourse->define_headers($tableheaders);
        $tablecourse->define_baseurl($deleteurl);
        $tablecourse->column_style('action', 'width', '10%');

        $tablecourse->sortable(false);
        $tablecourse->set_attribute('width', '100%');
        $tablecourse->set_attribute('class', 'generaltable');
        $tablecourse->setup();

        foreach ($templates as $template) {
            $data = array();
            $data[] = $template->name;
            $url = new moodle_url($deleteurl, array(
                                            'id'=>$id,
                                            'deletetempl'=>$template->id,
                                            'shoulddelete'=>1,
                                            ));

            $data[] = $OUTPUT->single_button($url, $strdeletefeedback, 'post');
            $tablecourse->add_data($data);
        }
        $tablecourse->finish_output();
        echo $OUTPUT->box_end();
    }
    //now we get the public templates if it is permitted
    $systemcontext = context_system::instance();
    if (has_capability('mod/feedback:createpublictemplate', $systemcontext) AND
        has_capability('mod/feedback:deletetemplate', $systemcontext)) {
        $templates = feedback_get_template_list($course, 'public');
        if (!is_array($templates)) {
            echo $OUTPUT->box(get_string('no_templates_available_yet', 'feedback'),
                              'generalbox boxaligncenter');
        } else {
            echo $OUTPUT->heading(get_string('public', 'feedback'), 4);
            echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal');
            $tablecolumns = array('template', 'action');
            $tableheaders = array(get_string('template', 'feedback'), '');
            $tablepublic = new flexible_table('feedback_template_public_table');

            $tablepublic->define_columns($tablecolumns);
            $tablepublic->define_headers($tableheaders);
            $tablepublic->define_baseurl($deleteurl);
            $tablepublic->column_style('action', 'width', '10%');

            $tablepublic->sortable(false);
            $tablepublic->set_attribute('width', '100%');
            $tablepublic->set_attribute('class', 'generaltable');
            $tablepublic->setup();

            foreach ($templates as $template) {
                $data = array();
                $data[] = $template->name;
                $url = new moodle_url($deleteurl, array(
                                                'id'=>$id,
                                                'deletetempl'=>$template->id,
                                                'shoulddelete'=>1,
                                                ));

                $data[] = $OUTPUT->single_button($url, $strdeletefeedback, 'post');
                $tablepublic->add_data($data);
            }
            $tablepublic->finish_output();
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

