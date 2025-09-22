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

switch ($action) {
    case 'upload':
        $files = $_FILES['files'];
        $tmp = make_request_directory();
        $savedfiles = [];
        if (is_array($files['name'])) {
            // Multiple files.
            foreach ($files['name'] as $idx => $name) {
                $dest = $tmp . '/' . basename($name);
                move_uploaded_file($files['tmp_name'][$idx], $dest);
                $savedfiles[] = $dest;
            }
        } else {
            // Single file.
            $dest = $tmp . '/' . basename($files['name']);
            move_uploaded_file($files['tmp_name'], $dest);
            $savedfiles[] = $dest;
        }

        // Upload the file to Google Drive repository.
        $ha = new repository_googledocs($repoid, $context);
        $userauth = $ha->get_user_oauth_client();
        $userservice = new repository_googledocs\rest($userauth);
        foreach ($savedfiles as $file) {
            $filename = basename($file);
            $ha->upload_file($userservice, $file, $filename, 'download', "root");
        }

        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => get_string('error', 'repository')]);
}
