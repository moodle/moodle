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
 * The Web service script that is called from the filepicker upload.
 *
 * @package    repository_googledocs
 * @copyright  2025 Raquel Ortega <raquel.ortega@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');


$action    = required_param('action', PARAM_ALPHA);
$repoid    = optional_param('repo_id', 0, PARAM_INT);
$contextid = optional_param('contextid', 0, PARAM_INT);
$itemid    = optional_param('itemid', 0, PARAM_INT);

require_login();
if (!confirm_sesskey()) {
    die(json_encode(['error' => get_string('invalidsesskey', 'error')]));
}

$context = context::instance_by_id($contextid, true);

$repo = repository::get_repository_by_id($repoid, $contextid);
if (!$repo) {
    die(json_encode(['error' => get_string('invalidrepositoryid', 'repository')]));
}
// Check permissions.
$repo->check_capability();
$repo->check_login();

$fs = get_file_storage();
$usercontext = context_user::instance($USER->id);

switch ($action) {
    case 'upload':
        // Save the files in the draft area.
        $draftid = !empty($itemid) ? $itemid : file_get_unused_draft_itemid();
        $file = $_FILES['repo_upload_file'];

        if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => get_string('erroruploadfailed', 'repository')]);
            break;
        }

        $filerecord = [
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftid,
            'filepath'  => '/',
            'filename'  => clean_param($file['name'], PARAM_FILE),
        ];

        $fs->create_file_from_pathname($filerecord, $file['tmp_name']);

        echo json_encode([
            'draftid' => $draftid,
            'file'    => $filerecord['filename'],
        ]);
        break;

    case 'commit':
        // Upload the files to Google Drive.
        if (empty($itemid)) {
            die(json_encode(['erroruploadfailed' => get_string('error', 'repository')]));
        }

        // Grab files from draft area with the same itemid.
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $itemid, "id", false);
        if (empty($draftfiles)) {
            die(json_encode(['erroruploadfailed' => get_string('error', 'repository')]));
        }

        // Upload the file to Google Drive repository.
        $tmp = make_request_directory();
        $ha = new repository_googledocs($repoid, $context);
        $userauth = $ha->get_user_oauth_client();
        $userservice = new repository_googledocs\rest($userauth);
        foreach ($draftfiles as $draftfile) {
            $tempfile = $tmp . '/' . rand();
            $filename = $draftfile->get_filename();
            $draftfile->copy_content_to($tempfile);
            $ha->upload_file($userservice, $tempfile, $filename, 'download', "root");
        }

        // Clear drafts after upload them to Google drive.
        $fs->delete_area_files($usercontext->id, 'user', 'draft', $itemid);

        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => get_string('error', 'repository')]);
}
