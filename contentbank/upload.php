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
 * Upload a file to content bank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/contentbank/files_form.php");

use core\output\notification;

require_login();

$contextid = optional_param('contextid', \context_system::instance()->id, PARAM_INT);
$context = context::instance_by_id($contextid, MUST_EXIST);

$cb = new \core_contentbank\contentbank();
if (!$cb->is_context_allowed($context)) {
    print_error('contextnotallowed', 'core_contentbank');
}

require_capability('moodle/contentbank:upload', $context);

$id = optional_param('id', null, PARAM_INT);
if ($id) {
    $content = $cb->get_content_from_id($id);
    $contenttype = $content->get_content_type_instance();
    if (!$contenttype->can_manage($content) || !$contenttype->can_upload()) {
        print_error('nopermissions', 'error', $returnurl, get_string('replacecontent', 'contentbank'));
    }
}

$title = get_string('contentbank');
\core_contentbank\helper::get_page_ready($context, $title, true);
if ($PAGE->course) {
    require_login($PAGE->course->id);
}
$returnurl = new \moodle_url('/contentbank/index.php', ['contextid' => $contextid]);

$PAGE->set_url('/contentbank/upload.php');
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('upload', 'contentbank'));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagetype('contentbank');

$maxbytes = $CFG->userquota;
$maxareabytes = $CFG->userquota;
if (has_capability('moodle/user:ignoreuserquota', $context)) {
    $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;
    $maxareabytes = FILE_AREA_MAX_BYTES_UNLIMITED;
}

if ($id) {
    $extensions = $contenttype->get_manageable_extensions();
    $accepted = implode(',', $extensions);
} else {
    $accepted = $cb->get_supported_extensions_as_string($context);
}

$data = new stdClass();
$options = array(
    'subdirs' => 1,
    'maxbytes' => $maxbytes,
    'maxfiles' => -1,
    'accepted_types' => $accepted,
    'areamaxbytes' => $maxareabytes
);
file_prepare_standard_filemanager($data, 'files', $options, $context, 'contentbank', 'public', 0);

$mform = new contentbank_files_form(null, ['contextid' => $contextid, 'data' => $data, 'options' => $options, 'id' => $id]);

$error = '';

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    require_sesskey();
    // Get the file and create the content based on it.
    $usercontext = \context_user::instance($USER->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $formdata->file, 'itemid, filepath, filename', false);
    if (!empty($files)) {
        $file = reset($files);
        if ($id) {
            $content = $contenttype->replace_content($file, $content);
        } else {
            $content = $cb->create_content_from_file($context, $USER->id, $file);
        }
        $viewurl = new \moodle_url('/contentbank/view.php', ['id' => $content->get_id(), 'contextid' => $contextid]);
        redirect($viewurl);
    } else {
        $error = get_string('errornofile', 'contentbank');
    }
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');

if (!empty($error)) {
    echo $OUTPUT->notification($error, notification::NOTIFY_ERROR);
}

$mform->display();

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
