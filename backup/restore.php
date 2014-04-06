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
$PAGE->set_pagelayout('admin');

require_login($course, null, $cm);
require_capability('moodle/restore:restorecourse', $context);

if (is_null($course)) {
    $coursefullname = $SITE->fullname;
    $courseshortname = $SITE->shortname;
} else {
    $coursefullname = $course->fullname;
    $courseshortname = $course->shortname;
}

// Show page header.
$PAGE->set_title($courseshortname . ': ' . get_string('restore'));
$PAGE->set_heading($coursefullname);

$renderer = $PAGE->get_renderer('core','backup');
echo $OUTPUT->header();

// Prepare a progress bar which can display optionally during long-running
// operations while setting up the UI.
$slowprogress = new \core\progress\display_if_slow(get_string('preparingui', 'backup'));

// Overall, allow 10 units of progress.
$slowprogress->start_progress('', 10);

// This progress section counts for loading the restore controller.
$slowprogress->start_progress('', 1, 1);

// Restore of large courses requires extra memory. Use the amount configured
// in admin settings.
raise_memory_limit(MEMORY_EXTRA);

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

// End progress section for loading restore controller.
$slowprogress->end_progress();

// This progress section is for the 'process' function below.
$slowprogress->start_progress('', 1, 9);

// Depending on the code branch above, $restore may be a restore_ui or it may
// be a restore_ui_independent_stage. Either way, this function exists.
$restore->set_progress_reporter($slowprogress);
$outcome = $restore->process();

if (!$restore->is_independent() && $restore->enforce_changed_dependencies()) {
    debugging('Your settings have been altered due to unmet dependencies', DEBUG_DEVELOPER);
}

$loghtml = '';
// Finish the 'process' progress reporting section, and the overall count.
$slowprogress->end_progress();
$slowprogress->end_progress();

if (!$restore->is_independent()) {
    // Use a temporary (disappearing) progress bar to show the precheck progress if any.
    $precheckprogress = new \core\progress\display_if_slow(get_string('preparingdata', 'backup'));
    $restore->get_controller()->set_progress($precheckprogress);
    if ($restore->get_stage() == restore_ui::STAGE_PROCESS && !$restore->requires_substage()) {
        try {
            // Div used to hide the 'progress' step once the page gets onto 'finished'.
            echo html_writer::start_div('', array('id' => 'executionprogress'));
            // Show the current restore state (header with bolded item).
            echo $renderer->progress_bar($restore->get_progress_bar());
            // Start displaying the actual progress bar percentage.
            $restore->get_controller()->set_progress(new \core\progress\display());
            // Prepare logger.
            $logger = new core_backup_html_logger($CFG->debugdeveloper ? backup::LOG_DEBUG : backup::LOG_INFO);
            $restore->get_controller()->add_logger($logger);
            // Do actual restore.
            $restore->execute();
            // Get HTML from logger.
            $loghtml = $logger->get_html();
            // Hide this section because we are now going to make the page show 'finished'.
            echo html_writer::end_div();
            echo html_writer::script('document.getElementById("executionprogress").style.display = "none";');
        } catch(Exception $e) {
            $restore->cleanup();
            throw $e;
        }
    } else {
        $restore->save_controller();
    }
}

echo $renderer->progress_bar($restore->get_progress_bar());
echo $restore->display($renderer);
$restore->destroy();
unset($restore);

// Display log data if there was any.
if ($loghtml != '') {
    echo $renderer->log_display($loghtml);
}

echo $OUTPUT->footer();
