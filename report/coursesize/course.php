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
require_once($CFG->dirroot.'/theme/lsu.php');
require_once(dirname(__FILE__) . '/locallib.php');



$courseid = required_param('id', PARAM_INT);

$csv = new csvtool();
$stamped = time();
$dirready = $csv->add_upload_dir($courseid, $stamped);

// Check to see if we are allowing special access to this page.
admin_externalpage_setup('reportcoursesize');

$course = $DB->get_record('course', array('id' => $courseid));

$context = context_course::instance($course->id);
$contextcheck = $context->path . '/%';

// Old query.
// $sizesql = "SELECT a.component, a.filearea, SUM(a.filesize) as filesize
//               FROM (SELECT DISTINCT f.contenthash, f.component, f.filesize, f.filearea
//                     FROM {files} f
//                     JOIN {context} ctx ON f.contextid = ctx.id
//                     WHERE ".$DB->sql_concat('ctx.path', "'/'")." LIKE ?
//                        AND f.filename != '.') a
//              GROUP BY a.component, a.filearea";

$sizesql = "SELECT mcs.section, mcs.name AS sectionname, mm.name as modname, ff.filename,
        ff.filesize, ff.filearea AS filearea, ff.component AS filecomp
    FROM (
        SELECT ctx.id, ctx.instanceid, f.filename, f.filesize, f.filearea, f.component
        FROM {files} f
        JOIN {context} ctx ON f.contextid = ctx.id
        WHERE ".$DB->sql_concat('ctx.path', "'/'")." LIKE ? AND f.filename != '.'

    ) AS ff

    LEFT JOIN {course_modules} mcm ON mcm.id = ff.instanceid AND mcm.course=?
    LEFT JOIN {modules} mm ON mm.id = mcm.module
    LEFT JOIN {course_sections} mcs ON mcs.id = mcm.section
    LEFT JOIN {course} mc ON mc.id = mcm.course

    ORDER BY mcs.section, ff.filesize DESC";

$cxsizes = $DB->get_recordset_sql($sizesql, array($contextcheck, $courseid));

$coursetable = new html_table();
$coursetable->attributes['class'] = 'table';
$coursetable->responsive = true;

// TODO: Remove these and add to lang file.
$headertitles = array(
    'Section Name',
    'Activity Type',
    'File name',
    get_string('size'),
);


// Headers for the Table.
$headerlist = array();
foreach ($headertitles as $title) {
    $headerlist[] = new html_table_cell($title);
}

// Add the CSV headers to the file
$dirready ? $csv->add_csv_row($headertitles) : null;

$tableheader = new html_table_row($headerlist);

$tableheader->header = true;
$tableheader->attributes['class'] = 'table-primary bold';
$coursetable->data[] = $tableheader;

$sectionstart = true;
$currentsection = 0;
$sizetotal = 0;

foreach ($cxsizes as $cxdata) {
    $activitytype = $cxdata->modname;
    
    if ($cxdata->section == null) {
        $activitytype = $cxdata->filearea;
        $sectionlink = '';
    } else {
        // Each time the loop hits a new section let's reset and then create a header.
        if ($currentsection != $cxdata->section) {
            $sectionstart = true;
            $currentsection = $cxdata->section;
        }
        
        $sectionlink = '#section-'.$cxdata->section;
        if ($sectionstart) {
            // Make the rest of the rows for the course section regular.
            $header = new html_table_cell(html_writer::tag('span', $cxdata->sectionname, array('id'=>'coursesize_header')));
            $header->header = true;
            $header->colspan = count($headerlist);
            // $header->colclasses = array ('centeralign'); 
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'table-info';

            $sectionstart = false;
            $coursetable->data[] = $header;
        }
    }

    $row = $csvrow = array();
    
    $row[] = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid. $sectionlink.'">'.$cxdata->sectionname.'</a>';
    $csvrow[] = $cxdata->sectionname;

    $row[] = $csvrow[] = $activitytype;
    $row[] = $csvrow[] = $cxdata->filename;
    
    $csvrow[] = $cxdata->filesize;
    $row[] = display_size($cxdata->filesize);

    // Add to the csv.
    $dirready ? $csv->add_csv_row($csvrow) : null;

    // Add to display table.
    $coursetable->data[] = $row;

    $sizetotal += $cxdata->filesize;
}

// Now the final total row.
$footertitle = new html_table_cell(html_writer::tag('span', "Total Size: ", array()));
$footersize = new html_table_cell(html_writer::tag('span', display_size($sizetotal), array()));
$footertitle->colspan = count($headerlist) - 1;
$footer = new html_table_row(array(
    $footertitle,
    $footersize
));
$footer->cells[0]->style = 'text-align: right;';
// $footer->cells[1]->style = 'text-align: right;';
$footer->attributes['class'] = 'table-primary bold';
$coursetable->data[] = $footer;

if ($dirready) {
    // Download a CSV button
    // $csvtitle = new html_table_cell(html_writer::tag('span', "Download CSV: ", array()));
    $downloadurl = $CFG->wwwroot.'/report/coursesize/download.php'; 
    $downbtn = new html_table_cell(
        html_writer::tag(
            'a',
            "Download CSV",
            array(
                'href' => new moodle_url(
                    $downloadurl, 
                    array('id' => $courseid, 'timestamp' => $stamped)
                ),
                'class' => 'btn btn-success'
            )
        )
    );

    $downbtn->colspan = count($headerlist);
    $btnrow = new html_table_row(array(
        $downbtn
    ));
    $btnrow->cells[0]->style = 'text-align: right;';
    $btnrow->attributes['class'] = 'table-primary bold';
    $coursetable->data[] = $btnrow;


    // Close the CSV Tool.
    $csv->close_handle();
}
// Close the table.
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
