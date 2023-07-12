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
 * Manage files in folder in private area.
 *
 * @package   report_trainingsessions
 * @category  report
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/trainingsessions/files_form.php');
require_once($CFG->dirroot.'/repository/lib.php');

// Security.

require_login();

$view = required_param('view', PARAM_TEXT);
$id = required_param('id', PARAM_INT);

$url = new moodle_url('/report/trainingsessions/filearea.php', array('id' => $id, 'view' => $view));

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}
$context = context_course::instance($course->id);

require_login($course);
require_capability('report/trainingsessions:downloadreports', $context);

$returnurl = new moodle_url('/report/trainingsessions/index.php', array('id' => $id, 'view' => $view));

$title = get_string('reports', 'report_trainingsessions');

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add(get_string('pluginname', 'report_trainingsessions'), $url);
$PAGE->navbar->add(get_string('reports', 'report_trainingsessions'));
$PAGE->set_pagelayout('admin');

$data = new stdClass();
$data->returnurl = $returnurl;

$options = array('subdirs' => 1, 'maxbytes' => -1, 'maxfiles' => -1, 'accepted_types' => '*', 'areamaxbytes' => -1);

file_prepare_standard_filemanager($data, 'files', $options, $context, 'report_trainingsessions', 'reports', $course->id);

$mform = new trainingsessions_files_form(null, array('data' => $data, 'options' => $options));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'report_trainingsessions', 'reports', $course->id);
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

$formdata = new StdClass();
$formdata->id = $id;
$formdata->view = $view;
$mform->set_data($formdata);

$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
