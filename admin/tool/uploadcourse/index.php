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
 * Bulk course registration script from a comma separated file
 *
 * @package    tool_uploadcourse
 * @copyright  2004 onwards Martin Dougiamas (http://dougiamas.com)
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->libdir . '/filelib.php');
require_once('locallib.php');
require_once('course_form.php');

$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
require_login();
admin_externalpage_setup('tooluploadcourse');

$returnurl = new moodle_url('/admin/tool/uploadcourse/index.php');
$bulknurl  = new moodle_url('/admin/tool/uploadcourse/index.php');
$std_fields = tool_uploadcourse_std_fields();


if (empty($iid)) {
    $mform1 = new admin_uploadcourse_form1();

    if ($formdata = $mform1->get_data()) {
        $iid = csv_import_reader::get_new_iid('uploadcourse');
        $cir = new csv_import_reader($iid, 'uploadcourse');

        $content = $mform1->get_file_content('coursefile');

        $readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
        unset($content);

        if ($readcount === false) {
            print_error('csvfileerror', 'tool_uploadcourse', $returnurl, $cir->get_error());
        } else if ($readcount == 0) {
            print_error('csvemptyfile', 'error', $returnurl, $cir->get_error());
        }
        // Test if columns ok.
        $filecolumns = tool_uploadcourse_validate_course_upload_columns($cir, $std_fields, $returnurl);
        // Continue to form2.

    } else {
        echo $OUTPUT->header();

        echo $OUTPUT->heading_with_help(get_string('uploadcourses', 'tool_uploadcourse'), 'uploadcourses', 'tool_uploadcourse');

        $mform1->display();
        echo $OUTPUT->footer();
        die;
    }
} else {
    $cir = new csv_import_reader($iid, 'uploadcourse');
    $filecolumns = tool_uploadcourse_validate_course_upload_columns($cir, $std_fields, $returnurl);
}

$frontpagecontext = context_course::instance(SITEID);
$mform2 = new admin_uploadcourse_form2(null,
                                       array('contextid' => $frontpagecontext->id,
                                             'columns' => $filecolumns,
                                             'data' => array('iid'=>$iid, 'previewrows'=>$previewrows)));

// If a file has been uploaded, then process it.
if ($formdata = $mform2->is_cancelled()) {
    $cir->cleanup(true);
    redirect($returnurl);
} else if ($formdata = $mform2->get_data()) {
    // Print the header.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadcoursesresult', 'tool_uploadcourse'));

    $tmpdir = $CFG->tempdir . '/backup';
    if (!check_dir_exists($tmpdir, true, true)) {
        throw new restore_controller_exception('cannot_create_backup_temp_dir');
    }
    $filename = restore_controller::get_tempdir_name(SITEID, $USER->id);
    $restorefile = $tmpdir . '/' . $filename;
    if (!$mform2->save_file('restorefile', $restorefile)) {
        $restorefile = null;
    }
    $bulk              = isset($formdata->ccbulk) ? $formdata->ccbulk : 0;

    tool_uploadcourse_process_course_upload($formdata, $cir, $filecolumns, $restorefile);

    echo $OUTPUT->box_end();

    if ($bulk) {
        echo $OUTPUT->continue_button($bulknurl);
    } else {
        echo $OUTPUT->continue_button($returnurl);
    }
    echo $OUTPUT->footer();
    die;
}

// Print the header.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('uploadcoursespreview', 'tool_uploadcourse'));

// NOTE: this is JUST csv processing preview, we must not prevent import from here if there is something in the file!!
//       this was intended for validation of csv formatting and encoding, not filtering the data!!!!
//       we definitely must not process the whole file!

// Preview table data.
$data = array();
$cir->init();
$linenum = 1; // Column header is first line.
while ($linenum <= $previewrows and $fields = $cir->next()) {
    $linenum++;
    $rowcols = array();
    $rowcols['line'] = $linenum;
    foreach ($fields as $key => $field) {
        $rowcols[$filecolumns[$key]] = s($field);
    }
    $rowcols['status'] = array();

    if (isset($rowcols['shortname'])) {
        $stdshortname = clean_param($rowcols['shortname'], PARAM_MULTILANG);
        if ($rowcols['shortname'] !== $stdshortname) {
            $rowcols['status'][] = get_string('invalidshortnameupload');
        }
        if ($courseid = $DB->get_field('course', 'id', array('shortname'=>$stdshortname))) {
            $rowcols['shortname'] = html_writer::link(new moodle_url('/course/view.php',
                                                                     array('id' => $courseid)),
                                                                     $rowcols['shortname']);
        }
    } else {
        $rowcols['status'][] = get_string('missingshortname');
    }

    $rowcols['status'] = implode('<br />', $rowcols['status']);
    $data[] = $rowcols;
}
if ($fields = $cir->next()) {
    $data[] = array_fill(0, count($fields) + 2, '...');
}
$cir->close();

$table = new html_table();
$table->id = "ccpreview";
$table->attributes['class'] = 'generaltable';
$table->tablealign = 'center';
$table->summary = get_string('uploadcoursespreview', 'tool_uploadcourse');
$table->head = array();
$table->data = $data;

$table->head[] = get_string('cccsvline', 'tool_uploadcourse');
foreach ($filecolumns as $column) {
    $table->head[] = $column;
}
$table->head[] = get_string('status');

echo html_writer::tag('div', html_writer::table($table), array('class'=>'flexible-wrap'));

// Print the form.
$mform2->display();
echo $OUTPUT->footer();
die;

