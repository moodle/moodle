<?php

// Require both the backup and restore libs
require_once('../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/ui/import_extensions.php');

// The courseid we are importing to
$courseid = required_param('id', PARAM_INT);
// The id of the course we are importing FROM (will only be set if past first stage
$importcourseid = optional_param('importid', false, PARAM_INT);
// The target method for the restore (adding or deleting)
$restoretarget = optional_param('target', backup::TARGET_CURRENT_ADDING, PARAM_INT);

// Load the course and context
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);

// Must pass login
require_login($course);
// Must hold restoretargetimport in the current course
require_capability('moodle/restore:restoretargetimport', $context);

$heading = get_string('import');

// Set up the page
$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->set_url(new moodle_url('/backup/import.php', array('id'=>$courseid)));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

// Prepare the backup renderer
$renderer = $PAGE->get_renderer('core','backup');

// Check if we already have a import course id
if ($importcourseid === false) {
    // Obviously not... show the selector so one can be chosen
    $url = new moodle_url('/backup/import.php', array('id'=>$courseid));
    $search = new import_course_search(array('url'=>$url));

    // show the course selector
    echo $OUTPUT->header();
    echo $renderer->import_course_selector($url, $search);
    echo $OUTPUT->footer();
    die();
}

// Load the course +context to import from
$importcourse = $DB->get_record('course', array('id'=>$importcourseid), '*', MUST_EXIST);
$importcontext = context_course::instance($importcourseid);

// Make sure the user can backup from that course
require_capability('moodle/backup:backuptargetimport', $importcontext);

// Attempt to load the existing backup controller (backupid will be false if there isn't one)
$backupid = optional_param('backup', false, PARAM_ALPHANUM);
if (!($bc = backup_ui::load_controller($backupid))) {
    $bc = new backup_controller(backup::TYPE_1COURSE, $importcourse->id, backup::FORMAT_MOODLE,
                            backup::INTERACTIVE_YES, backup::MODE_IMPORT, $USER->id);
    $bc->get_plan()->get_setting('users')->set_status(backup_setting::LOCKED_BY_CONFIG);
    $settings = $bc->get_plan()->get_settings();

    // For the initial stage we want to hide all locked settings and if there are
    // no visible settings move to the next stage
    $visiblesettings = false;
    foreach ($settings as $setting) {
        if ($setting->get_status() !== backup_setting::NOT_LOCKED) {
            $setting->set_visibility(backup_setting::HIDDEN);
        } else {
            $visiblesettings = true;
        }
    }
    import_ui::skip_current_stage(!$visiblesettings);
}

// Prepare the import UI
$backup = new import_ui($bc, array('importid'=>$importcourse->id, 'target'=>$restoretarget));
// Process the current stage
$backup->process();

// If this is the confirmation stage remove the filename setting
if ($backup->get_stage() == backup_ui::STAGE_CONFIRMATION) {
    $backup->get_setting('filename')->set_visibility(backup_setting::HIDDEN);
}

// If it's the final stage process the import
if ($backup->get_stage() == backup_ui::STAGE_FINAL) {
    // First execute the backup
    $backup->execute();
    $backup->destroy();
    unset($backup);

    // Check whether the backup directory still exists. If missing, something
    // went really wrong in backup, throw error. Note that backup::MODE_IMPORT
    // backups don't store resulting files ever
    $tempdestination = $CFG->tempdir . '/backup/' . $backupid;
    if (!file_exists($tempdestination) || !is_dir($tempdestination)) {
        print_error('unknownbackupexporterror'); // shouldn't happen ever
    }

    // Prepare the restore controller. We don't need a UI here as we will just use what
    // ever the restore has (the user has just chosen).
    $rc = new restore_controller($backupid, $course->id, backup::INTERACTIVE_YES, backup::MODE_IMPORT, $USER->id, $restoretarget);
    // Convert the backup if required.... it should NEVER happed
    if ($rc->get_status() == backup::STATUS_REQUIRE_CONV) {
        $rc->convert();
    }
    // Mark the UI finished.
    $rc->finish_ui();
    // Execute prechecks
    if (!$rc->execute_precheck()) {
        $precheckresults = $rc->get_precheck_results();
        if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
            fulldelete($tempdestination);

            echo $OUTPUT->header();
            echo $renderer->precheck_notices($precheckresults);
            echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id'=>$course->id)));
            echo $OUTPUT->footer();
            die();
        }
    } else {
        if ($restoretarget == backup::TARGET_CURRENT_DELETING || $restoretarget == backup::TARGET_EXISTING_DELETING) {
            restore_dbops::delete_course_content($course->id);
        }
        // Execute the restore
        $rc->execute_plan();
    }

    // Delete the temp directory now
    fulldelete($tempdestination);

    // Display a notification and a continue button
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('importsuccess', 'backup'),'notifysuccess');
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id'=>$course->id)));
    echo $OUTPUT->footer();

    die();

} else {
    // Otherwise save the controller and progress
    $backup->save_controller();
}

// Adjust the page for the stage
$PAGE->set_title($heading.': '.$backup->get_stage_name());
$PAGE->set_heading($heading.': '.$backup->get_stage_name());
$PAGE->navbar->add($backup->get_stage_name());

// Display the current stage
echo $OUTPUT->header();
if ($backup->enforce_changed_dependencies()) {
    debugging('Your settings have been altered due to unmet dependencies', DEBUG_DEVELOPER);
}
echo $renderer->progress_bar($backup->get_progress_bar());
echo $backup->display($renderer);
$backup->destroy();
unset($backup);
echo $OUTPUT->footer();