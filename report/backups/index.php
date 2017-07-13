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
 * A report to display the outcome of scheduled backups
 *
 * @package    report
 * @subpackage backups
 * @copyright  2007 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Required for backup::xxx constants.
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT); // This represents which backup we are viewing.

// Required for constants in backup_cron_automated_helper
require_once($CFG->dirroot.'/backup/util/helper/backup_cron_helper.class.php');

admin_externalpage_setup('reportbackups', '', null, '', array('pagelayout'=>'report'));

$strftimedatetime = get_string('strftimerecent');
$strerror = get_string('error');
$strok = get_string('ok');
$strunfinished = get_string('unfinished');
$strskipped = get_string('skipped');
$strwarning = get_string('warning');
$strnotyetrun = get_string('backupnotyetrun');

if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), 'id, fullname', MUST_EXIST);

    // Get the automated backups that have been performed for this course.
    $params = array('operation' => backup::OPERATION_BACKUP,
                    'type' => backup::TYPE_1COURSE,
                    'itemid' => $course->id,
                    'interactive' => backup::INTERACTIVE_NO);
    if ($backups = $DB->get_records('backup_controllers', $params, 'timecreated DESC',
        'id, backupid, status, timecreated', $page, 1)) {
        // Get the backup we want to use.
        $backup = reset($backups);

        // Get the backup status.
        if ($backup->status == backup::STATUS_FINISHED_OK) {
            $status = $strok;
            $statusclass = 'backup-ok'; // Green.
        } else if ($backup->status == backup::STATUS_AWAITING || $backup->status == backup::STATUS_EXECUTING) {
            $status = $strunfinished;
            $statusclass = 'backup-unfinished'; // Red.
        } else { // Else show error.
            $status = $strerror;
            $statusclass = 'backup-error'; // Red.
        }

        $table = new html_table();
        $table->head = array('');
        $table->data = array();
        $statusrow = get_string('status') . ' - ' . html_writer::tag('span', $status, array('class' => $statusclass));
        $table->data[] = array($statusrow);

        // Get the individual logs for this backup.
        if ($logs = $DB->get_records('backup_logs', array('backupid' => $backup->backupid), 'timecreated ASC',
            'id, message, timecreated')) {
            foreach ($logs as $log) {
                $table->data[] = array(userdate($log->timecreated, get_string('strftimetime', 'report_backups')) .
                    ' - ' . $log->message);
            }
        } else {
            $table->data[] = array(get_string('nologsfound', 'report_backups'));
        }
    }

    // Set the course name to display.
    $coursename = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('backupofcourselogs', 'report_backups', $coursename));
    if (isset($backup)) {
        // We put this logic down here as we may be viewing a backup that was performed which there were no logs
        // recorded for. We still want to display the pagination so the user can still navigate to other backups,
        // and we also display a message so they are aware that the backup happened but there were no logs.
        $baseurl = new moodle_url('/report/backups/index.php', array('courseid' => $courseid));
        $numberofbackups = $DB->count_records('backup_controllers', $params);
        $pagingbar = new paging_bar($numberofbackups, $page, 1, $baseurl);

        echo $OUTPUT->render($pagingbar);
        echo $OUTPUT->heading(get_string('logsofbackupexecutedon', 'report_backups', userdate($backup->timecreated)), 3);
        echo html_writer::table($table);
        echo $OUTPUT->render($pagingbar);
    } else {
        echo $OUTPUT->box(get_string('nobackupsfound', 'report_backups'), 'center');
    }
    echo $OUTPUT->footer();
    exit();
}

$table = new html_table;
$table->head = array(
    get_string("course"),
    get_string("timetaken", "backup"),
    get_string("status"),
    get_string("backupnext")
);
$table->headspan = array(1, 3, 1, 1);
$table->attributes = array('class' => 'generaltable backup-report');
$table->data = array();

$select = ', ' . context_helper::get_preload_record_columns_sql('ctx');
$join = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
$sql = "SELECT bc.*, c.id as courseid, c.fullname $select
          FROM {backup_courses} bc
          JOIN {course} c ON c.id = bc.courseid
               $join";
$rs = $DB->get_recordset_sql($sql, array('contextlevel' => CONTEXT_COURSE));
foreach ($rs as $backuprow) {

    // Cache the course context
    context_helper::preload_from_record($backuprow);

    // Prepare a cell to display the status of the entry.
    if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_OK) {
        $status = $strok;
        $statusclass = 'backup-ok'; // Green.
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_UNFINISHED) {
        $status = $strunfinished;
        $statusclass = 'backup-unfinished'; // Red.
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_SKIPPED) {
        $status = $strskipped;
        $statusclass = 'backup-skipped'; // Green.
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_WARNING) {
        $status = $strwarning;
        $statusclass = 'backup-warning'; // Orange.
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_NOTYETRUN) {
        $status = $strnotyetrun;
        $statusclass = 'backup-notyetrun';
    } else {
        $status = $strerror;
        $statusclass = 'backup-error'; // Red.
    }
    $status = new html_table_cell($status);
    $status->attributes = array('class' => $statusclass);

    // Create the row and add it to the table
    $backuprowname = format_string($backuprow->fullname, true, array('context' => context_course::instance($backuprow->courseid)));
    $backuplogsurl = new moodle_url('/report/backups/index.php', array('courseid' => $backuprow->courseid));
    $backuplogsicon = new pix_icon('t/viewdetails', get_string('viewlogs', 'report_backups'));
    $cells = array(
        $backuprowname . ' ' . $OUTPUT->action_icon($backuplogsurl, $backuplogsicon),
        userdate($backuprow->laststarttime, $strftimedatetime),
        '-',
        userdate($backuprow->lastendtime, $strftimedatetime),
        $status,
        userdate($backuprow->nextstarttime, $strftimedatetime)
    );
    $table->data[] = new html_table_row($cells);
}
$rs->close();

// Check if we have any results and if not add a no records notification
if (empty($table->data)) {
    $cell = new html_table_cell($OUTPUT->notification(get_string('nologsfound')));
    $cell->colspan = 6;
    $table->data[] = new html_table_row(array($cell));
}

$automatedbackupsenabled = get_config('backup', 'backup_auto_active');

// Display the backup report
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("backuploglaststatus"));
echo $OUTPUT->box_start();
if (empty($automatedbackupsenabled)) {
    // Automated backups aren't active, display a notification.
    // Not we don't stop because of this as perhaps scheduled backups are being run
    // automatically, or were enabled in the page.
    echo $OUTPUT->notification(get_string('automatedbackupsinactive', 'backup'));
}
echo html_writer::table($table);
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
