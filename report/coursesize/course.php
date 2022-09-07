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
 * Course breakdown.
 *
 * @package    report_coursesize
 * @copyright  2017 Catalyst IT {@link http://www.catalyst.net.nz}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$courseid = required_param('id', PARAM_INT);

admin_externalpage_setup('reportcoursesize');

$course = $DB->get_record('course', array('id' => $courseid));

$context = context_course::instance($course->id);
$contextcheck = $context->path . '/%';

$sizesql = "SELECT a.component, a.filearea, SUM(a.filesize) as filesize
              FROM (SELECT DISTINCT f.contenthash, f.component, f.filesize, f.filearea
                    FROM {files} f
                    JOIN {context} ctx ON f.contextid = ctx.id
                    WHERE ".$DB->sql_concat('ctx.path', "'/'")." LIKE ?
                       AND f.filename != '.') a
             GROUP BY a.component, a.filearea";

$cxsizes = $DB->get_recordset_sql($sizesql, array($contextcheck));

$coursetable = new html_table();
$coursetable->align = array('right', 'right', 'right');
$coursetable->head = array(get_string('plugin'), get_string('filearea', 'report_coursesize'), get_string('size'));
$coursetable->data = array();

foreach ($cxsizes as $cxdata) {
    $row = array();
    $row[] = $cxdata->component;
    $row[] = $cxdata->filearea;
    $row[] = display_size($cxdata->filesize);

    $coursetable->data[] = $row;
}
$cxsizes->close();

// Calculate filesize shared with other courses.
$sizesql = "SELECT SUM(filesize) FROM (SELECT DISTINCT contenthash, filesize
            FROM {files} f
            JOIN {context} ctx ON f.contextid = ctx.id
            WHERE ".$DB->sql_concat('ctx.path', "'/'")." NOT LIKE ?
                AND f.contenthash IN (SELECT DISTINCT f.contenthash
                                      FROM {files} f
                                      JOIN {context} ctx ON f.contextid = ctx.id
                                     WHERE ".$DB->sql_concat('ctx.path', "'/'")." LIKE ?
                                       AND f.filename != '.')) b";
$size = $DB->get_field_sql($sizesql, array($contextcheck, $contextcheck));
if (!empty($size)) {
    $size = display_size($size);
}


// All the processing done, the rest is just output stuff.

print $OUTPUT->header();

print $OUTPUT->heading(get_string('coursesize', 'report_coursesize'). " - ". format_string($course->fullname));
print $OUTPUT->box(get_string('coursereport', 'report_coursesize'));
if (!empty($size)) {
    print $OUTPUT->box(get_string('sharedusagecourse', 'report_coursesize', $size));
}

print html_writer::table($coursetable);
print $OUTPUT->footer();
