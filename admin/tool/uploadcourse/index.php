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
 * Bulk course registration script from a comma separated file.
 *
 * @package    tool_uploadcourse
 * @copyright  2011 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');

admin_externalpage_setup('tooluploadcourse');

$importid         = optional_param('importid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);

$returnurl = new moodle_url('/admin/tool/uploadcourse/index.php');

if (empty($importid)) {
    $mform1 = new tool_uploadcourse_step1_form();
    if ($form1data = $mform1->get_data()) {
        $importid = csv_import_reader::get_new_iid('uploadcourse');
        $cir = new csv_import_reader($importid, 'uploadcourse');
        $content = $mform1->get_file_content('coursefile');
        $readcount = $cir->load_csv_content($content, $form1data->encoding, $form1data->delimiter_name);
        unset($content);
        if ($readcount === false) {
            throw new \moodle_exception('csvfileerror', 'tool_uploadcourse', $returnurl, $cir->get_error());
        } else if ($readcount == 0) {
            throw new \moodle_exception('csvemptyfile', 'error', $returnurl, $cir->get_error());
        }
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading_with_help(get_string('uploadcourses', 'tool_uploadcourse'), 'uploadcourses', 'tool_uploadcourse');
        $mform1->display();
        echo $OUTPUT->footer();
        die();
    }
} else {
    $cir = new csv_import_reader($importid, 'uploadcourse');
}

// Data to set in the form.
$data = array('importid' => $importid, 'previewrows' => $previewrows);
if (!empty($form1data)) {
    // Get options from the first form to pass it onto the second.
    foreach ($form1data->options as $key => $value) {
        $data["options[$key]"] = $value;
    }
}
$context = context_system::instance();
$mform2 = new tool_uploadcourse_step2_form(null, array('contextid' => $context->id, 'columns' => $cir->get_columns(),
    'data' => $data));

// If a file has been uploaded, then process it.
if ($form2data = $mform2->is_cancelled()) {
    $cir->cleanup(true);
    redirect($returnurl);
} else if ($form2data = $mform2->get_data()) {

    $options = (array) $form2data->options;
    $defaults = (array) $form2data->defaults;

    // Custom field defaults.
    $customfields = tool_uploadcourse_helper::get_custom_course_field_names();
    foreach ($customfields as $customfield) {
        $defaults[$customfield] = $form2data->{$customfield};
    }

    // Restorefile deserves its own logic because formslib does not really appreciate
    // when the name of a filepicker is an array...
    $options['restorefile'] = '';
    if (!empty($form2data->restorefile)) {
        $options['restorefile'] = $mform2->save_temp_file('restorefile');
    }
    $processor = new tool_uploadcourse_processor($cir, $options, $defaults);

    echo $OUTPUT->header();
    if (isset($form2data->showpreview)) {
        echo $OUTPUT->heading(get_string('uploadcoursespreview', 'tool_uploadcourse'));
        $processor->preview($previewrows, new tool_uploadcourse_tracker(tool_uploadcourse_tracker::OUTPUT_HTML));
        $mform2->display();
    } else {
        echo $OUTPUT->heading(get_string('uploadcoursesresult', 'tool_uploadcourse'));
        $processor->execute(new tool_uploadcourse_tracker(tool_uploadcourse_tracker::OUTPUT_HTML));
        echo $OUTPUT->continue_button($returnurl);
    }

    // Deleting the file after processing or preview.
    if (!empty($options['restorefile'])) {
        @unlink($options['restorefile']);
    }

} else {
    if (!empty($form1data)) {
        $options = $form1data->options;
    } else if ($submitteddata = $mform2->get_submitted_data()) {
        $options = (array)$submitteddata->options;
    } else {
        // Weird but we still need to provide a value, setting the default step1_form one.
        $options = array('mode' => tool_uploadcourse_processor::MODE_CREATE_NEW);
    }
    $processor = new tool_uploadcourse_processor($cir, $options, array());
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadcoursespreview', 'tool_uploadcourse'));
    $processor->preview($previewrows, new tool_uploadcourse_tracker(tool_uploadcourse_tracker::OUTPUT_HTML));
    $mform2->display();
}

echo $OUTPUT->footer();
