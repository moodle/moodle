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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user edit the properties of a particular email template.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/local/iomad/lib/company.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$classroomid = optional_param('id', 0, PARAM_INTEGER);

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

$urlparams = array('id' => $classroomid);
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$templatelist = new moodle_url('/blocks/iomad_company_admin/classroom_list.php', $urlparams);

$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);

if ($classroomid) {
    $isadding = false;
    $editoroptions['context'] = $companycontext;
    $editoroptions['subdirs'] = file_area_contains_subdirs($companycontext, 'classroom', 'description', 0);

    $classroomrecord = (object) $DB->get_record('classroom', array('id' => $classroomid), '*', MUST_EXIST);
    iomad::require_capability('block/iomad_company_admin:classrooms_edit', $companycontext);
    $classroomrecord = file_prepare_standard_editor($classroomrecord, 'description', $editoroptions, $companycontext, 'block_iomad_company_admin', 'classroom_description', 0);

    $title = 'classrooms_edit';
} else {
    $editoroptions['context'] = $companycontext;
    $editoroptions['subdirs'] = 0;
    $isadding = true;
    $classroomid = 0;
    $classroomrecord = new stdClass;
    iomad::require_capability('block/iomad_company_admin:classrooms_add', $companycontext);

    $title = 'classrooms_add';
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string($title, 'block_iomad_company_admin');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/classroom_edit_form.php');

// Print the page header.
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('myhome') . " - $linktext");
$PAGE->navbar->add($linktext, $linkurl);

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
// Get the form data.

// Set up the form.
$mform = new \block_iomad_company_admin\forms\classroom_edit_form($PAGE->url, $isadding, $companyid, $classroomid, $editoroptions);
$mform->set_data($classroomrecord);

if ($mform->is_cancelled()) {
    redirect($templatelist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;
    if (empty($data->isvirtual)) {
        $data->isvirtual = 0;
    } else {
        if (empty($data->address)) {
            $data->address = "";
        }
        if (empty($data->city)) {
            $data->city = "";
        }
        if (empty($data->postcode)) {
            $data->postcode = "";
        }
        if (empty($data->capacity)) {
            $data->capacity = 0;
        }
    }

    $data->description = "";
    $data->descriptionformat = $data->description_editor['format'];

    if ($isadding) {
        $data->companyid = $companyid;
        $classroomid = $DB->insert_record('classroom', $data);
        $data->id = $classroomid;
        $message = get_string('classroomaddedok', 'block_iomad_company_admin');
    } else {
        $data->id = $classroomid;
        $DB->update_record('classroom', $data);
        $message = get_string('classroomupdatedok', 'block_iomad_company_admin');
    }

    // Save the files used in the summary editor and store
    $data = file_postupdate_standard_editor($data, 'description', $editoroptions, $companycontext, 'block_iomad_company_admin', 'classroom_description', 0);
    $DB->set_field('classroom', 'description', $data->description, ['id' => $classroomid]);
    $DB->set_field('classroom', 'descriptionformat', $data->descriptionformat, ['id' => $classroomid]);

    redirect($templatelist, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
