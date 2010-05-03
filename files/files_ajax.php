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
 * File manager
 *
 * @package    moodlecore
 * @subpackage file
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/adminlib.php');

require_login();

$err = new stdclass;

if (isguestuser()) {
    $err->error = get_string('noguest');
    die(json_encode($err));
}

if (!confirm_sesskey()) {
    $err->error = get_string('invalidsesskey');
    die(json_encode($err));
}

$action     = optional_param('action', 'list', PARAM_ALPHA);
$filename   = optional_param('filename', '', PARAM_FILE);
$filearea   = optional_param('filearea', 'user_draft', PARAM_ALPHAEXT);
$filepath   = optional_param('filepath', '/', PARAM_PATH);
$itemid     = optional_param('itemid', -1, PARAM_INT);
$newfilepath = optional_param('newfilepath', '/', PARAM_PATH);
$newdirname  = optional_param('newdirname', '', PARAM_FILE);
$newfilename = optional_param('newfilename', '', PARAM_FILE);

$user_context = get_context_instance(CONTEXT_USER, $USER->id);

switch ($action) {
case 'dir':
    $data = new stdclass;
    file_get_user_area_folders($itemid, $filepath, $data, $filearea);
    echo json_encode($data);
    break;

case 'list':
    $data = file_get_user_area_files($itemid, $filepath, $filearea);
    echo json_encode($data);
    break;

case 'mkdir':
    $fs = get_file_storage();
    $fs->create_directory($user_context->id, $filearea, $itemid, file_correct_filepath(file_correct_filepath($filepath).$newdirname));
    $return = new stdclass;
    $return->filepath = $filepath;
    echo json_encode($return);
    break;

case 'delete':
    $fs = get_file_storage();
    $filepath = file_correct_filepath($filepath);
    $return = new stdclass;
    if ($stored_file = $fs->get_file($user_context->id, $filearea, $itemid, $filepath, $filename)) {
        $parent_path = $stored_file->get_parent_directory()->get_filepath();
        if($result = $stored_file->delete()) {
            $return->filepath = $parent_path;
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
    } else {
        echo json_encode(false);
    }
    break;

case 'renamedir':
    $fs = get_file_storage();
    $fb = get_file_browser();
    $return = new stdclass;
    $fileinfo = $fb->get_file_info($user_context, $filearea, $itemid, $filepath, '.');
    if ($result = $fileinfo->delete()) {
        $newdir = $fs->create_directory($user_context->id, $filearea, $itemid, file_correct_filepath($newfilename));
        $return->filepath = $newdir->get_parent_directory()->get_filepath();
        echo json_encode($return);
    } else {
        echo json_encode(false);
    }
    break;

case 'rename':
    $fb = get_file_browser();
    $file = $fb->get_file_info($user_context, $filearea, $itemid, $filepath, $filename);
    $file->copy_to_storage($user_context->id, $filearea, $itemid, $filepath, $newfilename);
    if ($file->delete()) {
        $return = new stdclass;
        $return->filepath = $filepath;
        echo json_encode($return);
    } else {
        echo json_encode(false);
    }
    break;

case 'movefile':
case 'movedir':
    $fb = get_file_browser();
    $return = new stdclass;
    if ($filepath != $newfilepath) {
        $file = $fb->get_file_info($user_context, $filearea, $itemid, $filepath, $filename);
        $file->copy_to_storage($user_context->id, $filearea, $itemid, $newfilepath, $filename);
        if ($file->delete()) {
            $return->filepath = $newfilepath;
        }
    }
    if (!isset($return->filepath)) {
        $return->filepath = '/';
    }
    echo json_encode($return);
    break;

case 'zip':
    $zipper = new zip_packer();
    $fs = get_file_storage();

    $file = $fs->get_file($user_context->id, $filearea, $itemid, $filepath, '.');

    $parent_path = $file->get_parent_directory()->get_filepath();

    if ($newfile = $zipper->archive_to_storage(array($file), $user_context->id, $filearea, $itemid, $parent_path, $filepath.'.zip', $USER->id)) {
        $return = new stdclass;
        $return->filepath = $parent_path;
        echo json_encode($return);
    } else {
        echo json_encode(false);
    }
    break;

case 'downloaddir':
    $zipper = new zip_packer();
    $fs = get_file_storage();
    $draftarea = file_get_draft_area_info($itemid);
    if ($draftarea['filecount'] == 0) {
        echo json_encode(false);
        die;
    }

    $stored_file = $fs->get_file($user_context->id, $filearea, $itemid, $filepath, '.');
    if ($filepath === '/') {
        $parent_path = '/';
        $filename = get_string('files').'.zip';
    } else {
        $parent_path = $stored_file->get_parent_directory()->get_filepath();
        $filename = trim($filepath, '/').'.zip';
    }

    // archive compressed file to an unused draft area
    $newdraftitemid = file_get_unused_draft_itemid();
    if ($newfile = $zipper->archive_to_storage(array($stored_file), $user_context->id, $filearea, $newdraftitemid, '/', $filename, $USER->id)) {
        $return = new stdclass;
        $return->fileurl  = $CFG->wwwroot . '/draftfile.php/' . $user_context->id .'/'.$filearea.'/'.$newdraftitemid.'/'.$filename;
        $return->filepath = $parent_path;
        echo json_encode($return);
    } else {
        echo json_encode(false);
    }
    break;

case 'unzip':
    $zipper = new zip_packer();

    $fs = get_file_storage();

    $file = $fs->get_file($user_context->id, $filearea, $itemid, $filepath, $filename);

    if ($newfile = $file->extract_to_storage($zipper, $user_context->id, $filearea, $itemid, $filepath, $USER->id)) {
        $return = new stdclass;
        $return->filepath = $filepath;
        echo json_encode($return);
    } else {
        echo json_encode(false);
    }
    break;

case 'upload':
    break;

default:
    break;
}
