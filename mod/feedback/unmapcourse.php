<?php

/**
 * drops records from feedback_sitecourse_map
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package feedback
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/feedback/lib.php');

$id = required_param('id', PARAM_INT);
$cmapid = required_param('cmapid', PARAM_INT);

$url = new moodle_url('/mod/feedback/unmapcourse.php', array('id'=>$id));
if ($cmapid !== '') {
    $url->param('cmapid', $cmapid);
}
$PAGE->set_url($url);

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

require_capability('mod/feedback:mapcourse', $context);

// cleanup all lost entries after deleting courses or feedbacks
feedback_clean_up_sitecourse_map();

if ($DB->delete_records('feedback_sitecourse_map', array('id'=>$cmapid))) {
    $mapurl = new moodle_url('/mod/feedback/mapcourse.php', array('id'=>$id));
    redirect ($mapurl->out(false));
} else {
    print_error('cannotunmap', 'feedback');
}

