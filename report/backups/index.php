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
require_once($CFG->libdir.'/adminlib.php');

// Required for constants in backup_cron_automated_helper
require_once($CFG->dirroot.'/backup/util/helper/backup_cron_helper.class.php');

admin_externalpage_setup('reportbackups', '', null, '', array('pagelayout'=>'report'));

$table = new html_table;
$table->head = array(
    get_string("course"),
    get_string("timetaken", "quiz"),
    get_string("status"),
    get_string("backupnext")
);
$table->headspan = array(1, 3, 1, 1);
$table->attributes = array('class' => 'generaltable backup-report');
$table->data = array();

$strftimedatetime = get_string("strftimerecent");
$strerror = get_string("error");
$strok = get_string("ok");
$strunfinished = get_string("unfinished");
$strskipped = get_string("skipped");
$strwarning = get_string("warning");

list($select, $join) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
$sql = "SELECT bc.*, c.fullname $select
          FROM {backup_courses} bc
          JOIN {course} c ON c.id = bc.courseid
               $join";
$rs = $DB->get_recordset_sql($sql);
foreach ($rs as $backuprow) {

    // Cache the course context
    context_instance_preload($backuprow);

    // Prepare a cell to display the status of the entry
    if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_OK) {
        $status = $strok;
        $statusclass = 'backup-ok'; // Green
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_UNFINISHED) {
        $status = $strunfinished;
        $statusclass = 'backup-unfinished'; // Red
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_SKIPPED) {
        $status = $strskipped;
        $statusclass = 'backup-skipped'; // Green
    } else if ($backuprow->laststatus == backup_cron_automated_helper::BACKUP_STATUS_WARNING) {
        $status = $strwarning;
        $statusclass = 'backup-warning'; // Orange
    } else {
        $status = $strerror;
        $statusclass = 'backup-error'; // Red
    }
    $status = new html_table_cell($status);
    $status->attributes = array('class' => $statusclass);

    // Create the row and add it to the table
    $cells = array(
        format_string($backuprow->fullname, true, array('context' => context_course::instance($backuprow->courseid))),
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
