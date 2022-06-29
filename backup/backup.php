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
 * This script is used to configure and execute the backup proccess.
 *
 * @package    core
 * @subpackage backup
 * @copyright  Moodle
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/moodle2/backup_plan_builder.class.php');

// Backup of large courses requires extra memory. Use the amount configured
// in admin settings.
raise_memory_limit(MEMORY_EXTRA);

$courseid = required_param('id', PARAM_INT);
$sectionid = optional_param('section', null, PARAM_INT);
$cmid = optional_param('cm', null, PARAM_INT);
$cancel      = optional_param('cancel', '', PARAM_ALPHA);
$previous = optional_param('previous', false, PARAM_BOOL);
/**
 * Part of the forms in stages after initial, is POST never GET
 */
$backupid = optional_param('backup', false, PARAM_ALPHANUM);

// Determine if we are performing realtime for asynchronous backups.
$backupmode = backup::MODE_GENERAL;
if (async_helper::is_async_enabled()) {
    $backupmode = backup::MODE_ASYNC;
}

$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
$url = new moodle_url('/backup/backup.php', array('id'=>$courseid));
if ($sectionid !== null) {
    $url->param('section', $sectionid);
}
if ($cmid !== null) {
    $url->param('cm', $cmid);
}
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$id = $courseid;
$cm = null;
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
$coursecontext = context_course::instance($course->id);
$contextid = $coursecontext->id;
$type = backup::TYPE_1COURSE;
if (!is_null($sectionid)) {
    $section = $DB->get_record('course_sections', array('course'=>$course->id, 'id'=>$sectionid), '*', MUST_EXIST);
    $type = backup::TYPE_1SECTION;
    $id = $sectionid;
}
if (!is_null($cmid)) {
    $cm = get_coursemodule_from_id(null, $cmid, $course->id, false, MUST_EXIST);
    $type = backup::TYPE_1ACTIVITY;
    $id = $cmid;
}
require_login($course, false, $cm);

switch ($type) {
    case backup::TYPE_1COURSE :
        require_capability('moodle/backup:backupcourse', $coursecontext);
        $heading = get_string('backupcourse', 'backup', $course->shortname);
        $PAGE->set_secondary_active_tab('coursereuse');
        break;
    case backup::TYPE_1SECTION :
        require_capability('moodle/backup:backupsection', $coursecontext);
        if ((string)$section->name !== '') {
            $sectionname = format_string($section->name, true, array('context' => $coursecontext));
            $heading = get_string('backupsection', 'backup', $sectionname);
            $PAGE->navbar->add($sectionname);
        } else {
            $heading = get_string('backupsection', 'backup', $section->section);
            $PAGE->navbar->add(get_string('section').' '.$section->section);
        }
        break;
    case backup::TYPE_1ACTIVITY :
        $activitycontext = context_module::instance($cm->id);
        require_capability('moodle/backup:backupactivity', $activitycontext);
        $contextid = $activitycontext->id;
        $heading = get_string('backupactivity', 'backup', $cm->name);
        break;
    default :
        print_error('unknownbackuptype');
}

$PAGE->set_title($heading);
$PAGE->set_heading($heading);
$PAGE->activityheader->disable();

if (empty($cancel)) {
    // Do not print the header if user cancelled the process, as we are going to redirect the user.
    echo $OUTPUT->header();
}

// Only let user perform a backup if we aren't in async mode, or if we are
// and there are no pending backups for this item for this user.
if (!async_helper::is_async_pending($id, 'course', 'backup')) {

    // The mix of business logic and display elements below makes me sad.
    // This needs to refactored into the renderer and seperated out.

    if (!($bc = backup_ui::load_controller($backupid))) {
        $bc = new backup_controller($type, $id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, $backupmode, $USER->id, backup::RELEASESESSION_YES);
        // The backup id did not relate to a valid controller so we made a new controller.
        // Now we need to reset the backup id to match the new controller.
        $backupid = $bc->get_backupid();
    }

    // Prepare a progress bar which can display optionally during long-running
    // operations while setting up the UI.
    $slowprogress = new \core\progress\display_if_slow(get_string('preparingui', 'backup'));
    $renderer = $PAGE->get_renderer('core', 'backup');
    $backup = new backup_ui($bc);

    if ($backup->get_stage() == backup_ui::STAGE_SCHEMA && !$previous) {
        // After schema stage, we are probably going to get to the confirmation stage,
        // The confirmation stage has 2 sets of progress, so this is needed to prevent
        // it showing 2 progress bars.
        $twobars = true;
        $slowprogress->start_progress('', 2);
    } else {
        $twobars = false;
    }
    $backup->get_controller()->set_progress($slowprogress);
    $backup->process();

    if ($backup->enforce_changed_dependencies()) {
        debugging('Your settings have been altered due to unmet dependencies', DEBUG_DEVELOPER);
    }

    $loghtml = '';
    if ($backup->get_stage() == backup_ui::STAGE_FINAL) {

        // Before we perform the backup check settings to see if user
        // or setting defaults are set to exclude files from the backup.
        if ($backup->get_setting_value('files') == 0) {
            $renderer->set_samesite_notification();
        }

        if ($backupmode != backup::MODE_ASYNC) {
            // Synchronous backup handling.

            // Display an extra backup step bar so that we can show the 'processing' step first.
            echo html_writer::start_div('', array('id' => 'executionprogress'));
            echo $renderer->progress_bar($backup->get_progress_bar());
            $backup->get_controller()->set_progress(new \core\progress\display());

            // Prepare logger and add to end of chain.
            $logger = new core_backup_html_logger($CFG->debugdeveloper ? backup::LOG_DEBUG : backup::LOG_INFO);
            $backup->get_controller()->add_logger($logger);

            // Carry out actual backup.
            $backup->execute();

            // Backup controller gets saved/loaded so the logger object changes and we
            // have to retrieve it.
            $logger = $backup->get_controller()->get_logger();
            while (!is_a($logger, 'core_backup_html_logger')) {
                $logger = $logger->get_next();
            }

            // Get HTML from logger.
            if ($CFG->debugdisplay) {
                $loghtml = $logger->get_html();
            }

            // Hide the progress display and first backup step bar (the 'finished' step will show next).
            echo html_writer::end_div();
            echo html_writer::script('document.getElementById("executionprogress").style.display = "none";');

        } else {
            // Async backup handling.
            $backup->get_controller()->finish_ui();

            echo html_writer::start_div('', array('id' => 'executionprogress'));
            echo $renderer->progress_bar($backup->get_progress_bar());
            echo html_writer::end_div();

            // Create adhoc task for backup.
            $asynctask = new \core\task\asynchronous_backup_task();
            $asynctask->set_blocking(false);
            $asynctask->set_custom_data(array('backupid' => $backupid));
            $asynctask->set_userid($USER->id);
            \core\task\manager::queue_adhoc_task($asynctask);

            // Add ajax progress bar and initiate ajax via a template.
            $restoreurl = new moodle_url('/backup/restorefile.php', array('contextid' => $contextid));
            $progresssetup = array(
                    'backupid' => $backupid,
                    'contextid' => $contextid,
                    'courseurl' => $courseurl->out(),
                    'restoreurl' => $restoreurl->out(),
                    'headingident' => 'backup'
            );

            echo $renderer->render_from_template('core/async_backup_status', $progresssetup);
        }

    } else {
        $backup->save_controller();
    }

    if ($backup->get_stage() != backup_ui::STAGE_FINAL) {

        // Displaying UI can require progress reporting, so do it here before outputting
        // the backup stage bar (as part of the existing progress bar, if required).
        $ui = $backup->display($renderer);
        if ($twobars) {
            $slowprogress->end_progress();
        }

        echo $renderer->progress_bar($backup->get_progress_bar());
        echo $ui;

        // Display log data if there was any.
        if ($loghtml != '' && $backupmode != backup::MODE_ASYNC) {
            echo $renderer->log_display($loghtml);
        }
    }

    $backup->destroy();
    unset($backup);

} else { // User has a pending async operation.
    echo $OUTPUT->notification(get_string('pendingasyncerror', 'backup'), 'error');
    echo $OUTPUT->container(get_string('pendingasyncdetail', 'backup'));
    echo $OUTPUT->continue_button($courseurl);
}

echo $OUTPUT->footer();
