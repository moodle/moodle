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
    feedback_delete_template($formdata->deletetempl);
    redirect($deleteurl->out(false));
}

/// Print the page header
$strfeedbacks = get_string("modulenameplural", "feedback");
$strfeedback  = get_string("modulename", "feedback");

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($feedback->name));
echo $OUTPUT->header();

/// print the tabs
include('tabs.php');

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
echo $OUTPUT->heading(get_string('delete_template','feedback'));
if($shoulddelete == 1) {

    echo $OUTPUT->box_start('generalbox errorboxcontent boxaligncenter boxwidthnormal');
    echo $OUTPUT->heading(get_string('confirmdeletetemplate', 'feedback'));
    $mform->display();
    echo $OUTPUT->box_end();
}else {
    $templates = feedback_get_template_list($course, true);
    echo '<div class="mdl-align">';
    if(!is_array($templates)) {
        echo $OUTPUT->box(get_string('no_templates_available_yet', 'feedback'), 'generalbox boxaligncenter');
    }else {
        echo '<table width="30%">';
        echo '<tr><th>'.get_string('templates', 'feedback').'</th><th>&nbsp;</th></tr>';
        foreach($templates as $template) {
            echo '<tr><td align="center">'.$template->name.'</td>';
            echo '<td align="center">';
            echo '<form action="delete_template.php" method="post">';
            echo '<input title="'.get_string('delete_template','feedback').'" type="image" src="'.$OUTPUT->pix_url('t/delete') . '" hspace="1" height="11" width="11" border="0" />';
            echo '<input type="hidden" name="deletetempl" value="'.$template->id.'" />';
            echo '<input type="hidden" name="shoulddelete" value="1" />';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo '</form>';
            echo '</td></tr>';
        }
        echo '</table>';
    }
?>
        <form name="frm" action="delete_template.php" method="post">
            <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="canceldelete" value="0" />
            <button type="button" onclick="this.form.canceldelete.value=1;this.form.submit();"><?php print_string('cancel');?></button>
        </form>
        </div>
<?php
}

echo $OUTPUT->footer();

