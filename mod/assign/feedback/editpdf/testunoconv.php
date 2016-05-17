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
 * Test that unoconv is configured correctly
 *
 * @package   assignfeedback_editpdf
 * @copyright 2016 Simey Lameze
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/filelib.php');

$sendpdf = optional_param('sendpdf', 0, PARAM_BOOL);

$PAGE->set_url(new moodle_url('/mod/assign/feedback/editpdf/testunoconv.php'));
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/site:config', context_system::instance());

$strheading = get_string('test_unoconv', 'assignfeedback_editpdf');
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('assignmentplugins', 'mod_assign'));
$PAGE->navbar->add(get_string('feedbackplugins', 'mod_assign'));
$PAGE->navbar->add(get_string('pluginname', 'assignfeedback_editpdf'),
        new moodle_url('/admin/settings.php', array('section' => 'assignfeedback_editpdf')));
$PAGE->navbar->add($strheading);
$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);
if ($sendpdf) {
    require_sesskey();
    // Serve the generated test pdf.
    file_storage::send_test_pdf();
    die();
}

$result = file_storage::test_unoconv_path();
switch ($result->status) {
    case file_storage::UNOCONVPATH_OK:
        $msg = $OUTPUT->notification(get_string('test_unoconvok', 'assignfeedback_editpdf'), 'success');
        $pdflink = new moodle_url($PAGE->url, array('sendpdf' => 1, 'sesskey' => sesskey()));
        $msg .= html_writer::link($pdflink, get_string('test_unoconvdownload', 'assignfeedback_editpdf'));
        $msg .= html_writer::empty_tag('br');
        break;

    case file_storage::UNOCONVPATH_ERROR:
        $msg = $OUTPUT->notification($result->message, 'warning');
        break;

    default:
        $msg = $OUTPUT->notification(get_string("test_unoconv{$result->status}", 'assignfeedback_editpdf'), 'warning');
        break;
}
$returl = new moodle_url('/admin/settings.php', array('section' => 'assignfeedback_editpdf'));
$msg .= $OUTPUT->continue_button($returl);

echo $OUTPUT->header();
echo $OUTPUT->box($msg, 'generalbox');
echo $OUTPUT->footer();
