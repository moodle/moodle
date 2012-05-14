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
 * Draft file ajax file manager
 *
 * @package    core
 * @subpackage repository
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/repository/lib.php');
$PAGE->set_context(get_system_context());
require_login();
if (isguestuser()) {
    print_error('noguest');
}
require_sesskey();

$action  = required_param('action', PARAM_ALPHA);
$draftid = required_param('itemid', PARAM_INT);
$filepath = optional_param('filepath', '/', PARAM_PATH);

$user_context = get_context_instance(CONTEXT_USER, $USER->id);

echo $OUTPUT->header(); // send headers

//
//NOTE TO ALL DEVELOPERS: this script must deal only with draft area of current user, it has to use only file_storage and no file_browser!!
//

switch ($action) {
    case 'dir':
        $data = new stdClass();
        file_get_drafarea_folders($draftid, $filepath, $data);
        echo json_encode($data);
        die;

    case 'list':
        $filepath = optional_param('filepath', '/', PARAM_PATH);

        $data = repository::prepare_listing(file_get_drafarea_files($draftid, $filepath));
        $info = file_get_draft_area_info($draftid);
        $data->filecount = $info['filecount'];
        $data->filesize = $info['filesize'];
        $data->tree = new stdClass();
        file_get_drafarea_folders($draftid, '/', $data->tree);
        echo json_encode($data);
        die;

    case 'mkdir':
        $filepath   = required_param('filepath', PARAM_PATH);
        $newdirname = required_param('newdirname', PARAM_FILE);

        $fs = get_file_storage();
        $fs->create_directory($user_context->id, 'user', 'draft', $draftid, file_correct_filepath(file_correct_filepath($filepath).$newdirname));
        $return = new stdClass();
        $return->filepath = $filepath;
        echo json_encode($return);
        die;

    case 'delete':
        $filename   = required_param('filename', PARAM_FILE);
        $filepath   = required_param('filepath', PARAM_PATH);

        $fs = get_file_storage();
        $filepath = file_correct_filepath($filepath);
        $return = new stdClass();
        if ($stored_file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, $filename)) {
            $parent_path = $stored_file->get_parent_directory()->get_filepath();
            if ($stored_file->is_directory()) {
                $files = $fs->get_directory_files($user_context->id, 'user', 'draft', $draftid, $filepath, true);
                foreach ($files as $file) {
                    $file->delete();
                }
                $stored_file->delete();
                $return->filepath = $parent_path;
                echo json_encode($return);
            } else {
                if($result = $stored_file->delete()) {
                    $return->filepath = $parent_path;
                    echo json_encode($return);
                } else {
                    echo json_encode(false);
                }
            }
        } else {
            echo json_encode(false);
        }
        die;

    case 'setmainfile':
        $filename   = required_param('filename', PARAM_FILE);
        $filepath   = required_param('filepath', PARAM_PATH);

        $filepath = file_correct_filepath($filepath);
        // reset sort order
        file_reset_sortorder($user_context->id, 'user', 'draft', $draftid);
        // set main file
        $return = file_set_sortorder($user_context->id, 'user', 'draft', $draftid, $filepath, $filename, 1);
        echo json_encode($return);
        die;

    case 'updatefile':
        // Allows to Rename file, move it to another directory, change it's license and author information in one request
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);

        $fs = get_file_storage();
        if (!($file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, $filename))) {
            die(json_encode((object)array('error' => get_string('filenotfound', 'error'))));
        }

        $updatedata = array();
        $updatedata['filename'] = $newfilename = optional_param('newfilename', $file->get_filename(), PARAM_FILE);
        $updatedata['filepath'] = $newfilepath = optional_param('newfilepath', $file->get_filepath(), PARAM_PATH);
        $updatedata['license'] = optional_param('newlicense', $file->get_license(), PARAM_TEXT);
        $updatedata['author'] = optional_param('newauthor', $file->get_author(), PARAM_TEXT);
        foreach ($updatedata as $key => $value) {
            if (''.$value === ''.$file->{'get_'.$key}()) {
                unset($updatedata[$key]);
            }
        }

        if (!empty($updatedata)) {
            if (array_key_exists('filename', $updatedata) || array_key_exists('filepath', $updatedata)) {
                // check that target file name does not exist
                if ($fs->file_exists($user_context->id, 'user', 'draft', $draftid, $newfilepath, $newfilename)) {
                    die(json_encode((object)array('error' => get_string('fileexists', 'repository'))));
                }
                $file->rename($newfilepath, $newfilename);
            }
            if (array_key_exists('license', $updatedata)) {
                $file->set_license($updatedata['license']);
            }
            if (array_key_exists('author', $updatedata)) {
                $file->set_license($updatedata['author']);
            }
            $changes = array_diff(array_keys($updatedata), array('filepath'));
            if (!empty($changes)) {
                // any change except for the moving to another folder alters 'Date modified' of the file
                $file->set_timemodified(time());
            }
        }

        die(json_encode((object)array('filepath' => $newfilepath)));

    case 'rename':
        // TODO deprecate this, use 'updatefile' instead
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);
        $newfilename = required_param('newfilename', PARAM_FILE);

        $fs = get_file_storage();
        if ($fs->file_exists($user_context->id, 'user', 'draft', $draftid, $filepath, $newfilename)) {
            //bad luck, we can not rename!
            echo json_encode(false);
        } else if ($file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, $filename)) {
            $return = new stdClass();
            $newfile = $fs->create_file_from_storedfile(array('filename'=>$newfilename), $file);
            $file->delete();
            $return->filepath = $newfile->get_filepath();
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
        die;

    case 'updatedir':
        $filepath = required_param('filepath', PARAM_PATH);
        $fs = get_file_storage();
        if (!$dir = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, '.')) {
            die(json_encode((object)array('error' => get_string('foldernotfound', 'repository'))));
        }
        $parts = explode('/', trim($dir->get_filepath(), '/'));
        $dirname = end($parts);
        $newdirname = required_param('newdirname', PARAM_FILE);
        $parent = required_param('newfilepath', PARAM_PATH);
        $newfilepath = clean_param($parent . '/' . $newdirname . '/', PARAM_PATH);
        //we must update directory and all children too
        if ($fs->get_directory_files($user_context->id, 'user', 'draft', $draftid, $newfilepath, true)) {
            //bad luck, we can not rename if something already exists there
            die(json_encode((object)array('error' => get_string('folderexists', 'repository'))));
        }
        $xfilepath = preg_quote($filepath, '|');
        if ($newfilepath !== $filepath && preg_match("|^$xfilepath|", $parent)) {
            // we can not move folder to it's own subfolder
            die(json_encode((object)array('error' => get_string('folderrecurse', 'repository'))));
        }

        $files = $fs->get_area_files($user_context->id, 'user', 'draft', $draftid);
        $moved = array();
        foreach ($files as $file) {
            if (!preg_match("|^$xfilepath|", $file->get_filepath())) {
                continue;
            }
            // move one by one
            $path = preg_replace("|^$xfilepath|", $newfilepath, $file->get_filepath());
            $updatedata = array('filepath' => $path, 'timemodified' => $file->get_timemodified());
            if ($dirname !== $newdirname && $file->get_filepath() === $filepath) {
                // this is the main directory we move/rename AND it has actually been renamed
                $updatedata['timemodified'] = time();
            }
            $fs->create_file_from_storedfile($updatedata, $file);
            $moved[] = $file;
        }
        foreach ($moved as $file) {
            // delete all old
            $file->delete();
        }

        $return = new stdClass();
        $return->filepath = $parent;
        echo json_encode($return);
        die;

    case 'renamedir':
    case 'movedir':
        // TODO deprecate this, use 'renamemovedir' instead

        $filepath = required_param('filepath', PARAM_PATH);
        $fs = get_file_storage();

        if (!$dir = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, '.')) {
            echo json_encode(false);
            die;
        }
        if ($action === 'renamedir') {
            $newdirname = required_param('newdirname', PARAM_FILE);
            $parent = clean_param(dirname($filepath) . '/', PARAM_PATH);
            $newfilepath = $parent . $newdirname . '/';
        } else {
            $newfilepath = required_param('newfilepath', PARAM_PATH);
            $parts = explode('/', trim($dir->get_filepath(), '/'));
            $dirname = end($parts);
            $newfilepath = clean_param($newfilepath . '/' . $dirname . '/', PARAM_PATH);
        }

        //we must update directory and all children too
        if ($fs->get_directory_files($user_context->id, 'user', 'draft', $draftid, $newfilepath, true)) {
            //bad luck, we can not rename if something already exists there
            echo json_encode(false);
            die;
        }

        $xfilepath = preg_quote($filepath, '|');

        $files = $fs->get_area_files($user_context->id, 'user', 'draft', $draftid);
        $moved = array();
        foreach ($files as $file) {
            if (!preg_match("|^$xfilepath|", $file->get_filepath())) {
                continue;
            }
            // move one by one
            $path = preg_replace("|^$xfilepath|", $newfilepath, $file->get_filepath());
            $fs->create_file_from_storedfile(array('filepath'=>$path), $file);
            $moved[] = $file;
        }
        foreach ($moved as $file) {
            // delete all old
            $file->delete();
        }

        $return = new stdClass();
        if ($action === 'renamedir') {
            $return->filepath = $parent;
        } else {
            $return->filepath = $newfilepath;
        }
        echo json_encode($return);
        die;

    case 'movefile':
        // TODO deprecate this, use 'updatefile' instead
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);
        $newfilepath = required_param('newfilepath', PARAM_PATH);

        $fs = get_file_storage();
        if ($fs->file_exists($user_context->id, 'user', 'draft', $draftid, $newfilepath, $filename)) {
            //bad luck, we can not rename!
            echo json_encode(false);
        } else if ($file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, $filename)) {
            $return = new stdClass();
            $newfile = $fs->create_file_from_storedfile(array('filepath'=>$newfilepath), $file);
            $file->delete();
            $return->filepath = $newfile->get_filepath();
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
        die;

    case 'zip':
        $filepath = required_param('filepath', PARAM_PATH);

        $zipper = get_file_packer('application/zip');
        $fs = get_file_storage();

        $file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, '.');

        $parent_path = $file->get_parent_directory()->get_filepath();

        if ($newfile = $zipper->archive_to_storage(array($file), $user_context->id, 'user', 'draft', $draftid, $parent_path, $filepath.'.zip', $USER->id)) {
            $return = new stdClass();
            $return->filepath = $parent_path;
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
        die;

    case 'downloaddir':
        $filepath = required_param('filepath', PARAM_PATH);

        $zipper = get_file_packer('application/zip');
        $fs = get_file_storage();
        $area = file_get_draft_area_info($draftid);
        if ($area['filecount'] == 0) {
            echo json_encode(false);
            die;
        }

        $stored_file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, '.');
        if ($filepath === '/') {
            $parent_path = '/';
            $filename = get_string('files').'.zip';
        } else {
            $parent_path = $stored_file->get_parent_directory()->get_filepath();
            $filename = trim($filepath, '/').'.zip';
        }

        // archive compressed file to an unused draft area
        $newdraftitemid = file_get_unused_draft_itemid();
        if ($newfile = $zipper->archive_to_storage(array($stored_file), $user_context->id, 'user', 'draft', $newdraftitemid, '/', $filename, $USER->id)) {
            $return = new stdClass();
            $return->fileurl  = moodle_url::make_draftfile_url($newdraftitemid, '/', $filename)->out();
            $return->filepath = $parent_path;
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
        die;

    case 'unzip':
        $filename = required_param('filename', PARAM_FILE);
        $filepath = required_param('filepath', PARAM_PATH);

        $zipper = get_file_packer('application/zip');

        $fs = get_file_storage();

        $file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, $filename);

        if ($newfile = $file->extract_to_storage($zipper, $user_context->id, 'user', 'draft', $draftid, $filepath, $USER->id)) {
            $return = new stdClass();
            $return->filepath = $filepath;
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
        die;

    case 'getoriginal':
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);

        $fs = get_file_storage();
        $file = $fs->get_file($user_context->id, 'user', 'draft', $draftid, $filepath, $filename);
        if (!$file) {
            echo json_encode(false);
        } else {
            $return = array('filename' => $filename, 'filepath' => $filepath, 'original' => $file->get_reference_details());
            echo json_encode((object)$return);
        }
        die;

    default:
        // no/unknown action?
        echo json_encode(false);
        die;
}
