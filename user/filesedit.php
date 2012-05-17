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
 * @package   moodle
 * @copyright 2010 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once("$CFG->dirroot/user/filesedit_form.php");
require_once("$CFG->dirroot/repository/lib.php");

require_login();
if (isguestuser()) {
    die();
}

$returnurl = optional_param('returnurl', '', PARAM_URL);

if (empty($returnurl)) {
    if (!empty($_SERVER["HTTP_REFERER"])) {
        $returnurl = $_SERVER["HTTP_REFERER"];
    } else {
        $returnurl = new moodle_url('/user/files.php');
    }
}

$context = get_context_instance(CONTEXT_USER, $USER->id);
require_capability('moodle/user:manageownfiles', $context);

$title = get_string('myfiles');
$struser = get_string('user');

$PAGE->set_url('/user/filesedit.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('user-files');

$data = new stdClass();
$data->returnurl = $returnurl;
$options = array('subdirs'=>1, 'maxbytes'=>$CFG->userquota, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
file_prepare_standard_filemanager($data, 'files', $options, $context, 'user', 'private', 0);

$mform = new user_filesedit_form(null, array('data'=>$data, 'options'=>$options));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'user', 'private', 0);
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
