<?php
    //This script is used to configure and execute the restore proccess.

require_once('../config.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

$contextid   = required_param('contextid', PARAM_INT);
$stage       = optional_param('stage', restore_ui::STAGE_CONFIRM, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

navigation_node::override_active_url(new moodle_url('/backup/restorefile.php', array('contextid'=>$contextid)));
$PAGE->set_url(new moodle_url('/backup/restore.php', array('contextid'=>$contextid)));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

require_login($course, null, $cm);
require_capability('moodle/restore:restorecourse', $context);

if ($stage & restore_ui::STAGE_CONFIRM + restore_ui::STAGE_DESTINATION) {
    $restore = restore_ui::engage_independent_stage($stage, $contextid);
} else {
    $restoreid = optional_param('restore', false, PARAM_ALPHANUM);
    $rc = restore_ui::load_controller($restoreid);
    if (!$rc) {
        $restore = restore_ui::engage_independent_stage($stage/2, $contextid);
        if ($restore->process()) {
            $rc = new restore_controller($restore->get_filepath(), $restore->get_course_id(), backup::INTERACTIVE_YES,
                                backup::MODE_GENERAL, $USER->id, $restore->get_target());
        }
    }
    if ($rc) {
        // check if the format conversion must happen first
        if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
            $rc->convert();
        }

        $restore = new restore_ui($rc, array('contextid'=>$context->id));
    }
}

$outcome = $restore->process();
if (!$restore->is_independent()) {
    if ($restore->get_stage() == restore_ui::STAGE_PROCESS && !$restore->requires_substage()) {
        try {
            $restore->execute();
        } catch(Exception $e) {
            $restore->cleanup();
            throw $e;
        }
    } else {
        $restore->save_controller();
    }
}
$heading = $course->fullname;

$PAGE->set_title($heading.': '.$restore->get_stage_name());
$PAGE->set_heading($heading);
$PAGE->navbar->add($restore->get_stage_name());

$renderer = $PAGE->get_renderer('core','backup');
echo $OUTPUT->header();
if (!$restore->is_independent() && $restore->enforce_changed_dependencies()) {
    debugging('Your settings have been altered due to unmet dependencies', DEBUG_DEVELOPER);
}
echo $renderer->progress_bar($restore->get_progress_bar());
echo $restore->display($renderer);
$restore->destroy();
unset($restore);
echo $OUTPUT->footer();
