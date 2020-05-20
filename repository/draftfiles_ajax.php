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
$PAGE->set_context(context_system::instance());
require_login();
if (isguestuser()) {
    print_error('noguest');
}
require_sesskey();

$action  = required_param('action', PARAM_ALPHA);
$draftid = required_param('itemid', PARAM_INT);
$filepath = optional_param('filepath', '/', PARAM_PATH);

$usercontext = context_user::instance($USER->id);

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
        $fs->create_directory($usercontext->id, 'user', 'draft', $draftid, file_correct_filepath(file_correct_filepath($filepath).$newdirname));
        $return = new stdClass();
        $return->filepath = $filepath;
        echo json_encode($return);
        die;

    case 'delete':
        $filename   = required_param('filename', PARAM_FILE);
        $filepath   = required_param('filepath', PARAM_PATH);
        $selectedfile = (object)[
            'filename' => $filename,
            'filepath' => $filepath
        ];
        $return = repository_delete_selected_files($usercontext, 'user', 'draft', $draftid, [$selectedfile]);

        if ($return) {
            $response = new stdClass();
            $response->filepath = array_keys($return)[0];
            echo json_encode($response);
            die;
        }

        echo json_encode(false);
        die;

    case 'deleteselected':
        $selected   = required_param('selected', PARAM_RAW);
        $return = [];
        $selectedfiles = json_decode($selected);
        $return = repository_delete_selected_files($usercontext, 'user', 'draft', $draftid, $selectedfiles);
        echo (json_encode($return ? array_keys($return) : false));
        die;

    case 'setmainfile':
        $filename   = required_param('filename', PARAM_FILE);
        $filepath   = required_param('filepath', PARAM_PATH);

        $filepath = file_correct_filepath($filepath);
        // reset sort order
        file_reset_sortorder($usercontext->id, 'user', 'draft', $draftid);
        // set main file
        $return = file_set_sortorder($usercontext->id, 'user', 'draft', $draftid, $filepath, $filename, 1);
        echo json_encode($return);
        die;

    case 'updatefile':
        // Allows to Rename file, move it to another directory, change it's license and author information in one request
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);
        $updatedata = array();
        $updatedata['filename'] = optional_param('newfilename', $filename, PARAM_FILE);
        $updatedata['filepath'] = $newfilepath = optional_param('newfilepath', $filepath, PARAM_PATH);
        if (($v = optional_param('newlicense', false, PARAM_TEXT)) !== false) {
            $updatedata['license'] = $v;
        }
        if (($v = optional_param('newauthor', false, PARAM_TEXT)) !== false) {
            $updatedata['author'] = $v;
        }
        try {
            repository::update_draftfile($draftid, $filepath, $filename, $updatedata);
        } catch (moodle_exception $e) {
            die(json_encode((object)array('error' => $e->getMessage())));
        }
        die(json_encode((object)array('filepath' => $newfilepath)));

    case 'updatedir':
        $filepath = required_param('filepath', PARAM_PATH);
        $newdirname = required_param('newdirname', PARAM_FILE);
        $parent = required_param('newfilepath', PARAM_PATH);
        $newfilepath = clean_param($parent . '/' . $newdirname . '/', PARAM_PATH);
        try {
            repository::update_draftfile($draftid, $filepath, '.', array('filepath' => $newfilepath));
        } catch (moodle_exception $e) {
            die(json_encode((object)array('error' => $e->getMessage())));
        }
        die(json_encode((object)array('filepath' => $parent)));

    case 'zip':
        $filepath = required_param('filepath', PARAM_PATH);

        $zipper = get_file_packer('application/zip');
        $fs = get_file_storage();

        $file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, $filepath, '.');

        $parent_path = $file->get_parent_directory()->get_filepath();

        $filepath = explode('/', trim($file->get_filepath(), '/'));
        $filepath = array_pop($filepath);
        $zipfile = repository::get_unused_filename($draftid, $parent_path, $filepath . '.zip');

        if ($newfile = $zipper->archive_to_storage([$filepath => $file], $usercontext->id, 'user', 'draft', $draftid, $parent_path, $zipfile, $USER->id)) {
            $return = new stdClass();
            $return->filepath = $parent_path;
            echo json_encode($return);
        } else {
            echo json_encode(false);
        }
        die;
    case 'downloadselected':
        $selected   = required_param('selected', PARAM_RAW);
        $selectedfiles = json_decode($selected);
        if (!count($selectedfiles)) {
            $filepath = required_param('filepath', PARAM_PATH);
            $selectedfiles = [(object)[
                'filename' => '',
                'filepath' => $filepath
            ]];
        }
        $return = repository_download_selected_files($usercontext, 'user', 'draft', $draftid, $selectedfiles);
        echo (json_encode($return));
        die;

    case 'downloaddir':
        $filepath = required_param('filepath', PARAM_PATH);

        $selectedfile = (object)[
            'filename' => '',
            'filepath' => $filepath
        ];
        $return = repository_download_selected_files($usercontext, 'user', 'draft', $draftid, [$selectedfile]);
        echo json_encode($return);
        die;

    case 'unzip':
        $filename = required_param('filename', PARAM_FILE);
        $filepath = required_param('filepath', PARAM_PATH);

        $zipper = get_file_packer('application/zip');

        $fs = get_file_storage();

        $file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, $filepath, $filename);

        // Find unused name for directory to extract the archive.
        $temppath = $fs->get_unused_dirname($usercontext->id, 'user', 'draft', $draftid, $filepath. pathinfo($filename, PATHINFO_FILENAME). '/');
        $donotremovedirs = array();
        $doremovedirs = array($temppath);
        // Extract archive and move all files from $temppath to $filepath
        if ($file->extract_to_storage($zipper, $usercontext->id, 'user', 'draft', $draftid, $temppath, $USER->id) !== false) {
            $extractedfiles = $fs->get_directory_files($usercontext->id, 'user', 'draft', $draftid, $temppath, true);
            $xtemppath = preg_quote($temppath, '|');
            foreach ($extractedfiles as $file) {
                $realpath = preg_replace('|^'.$xtemppath.'|', $filepath, $file->get_filepath());
                if (!$file->is_directory()) {
                    // Set the source to the extracted file to indicate that it came from archive.
                    $file->set_source(serialize((object)array('source' => $filepath)));
                }
                if (!$fs->file_exists($usercontext->id, 'user', 'draft', $draftid, $realpath, $file->get_filename())) {
                    // File or directory did not exist, just move it.
                    $file->rename($realpath, $file->get_filename());
                } else if (!$file->is_directory()) {
                    // File already existed, overwrite it
                    repository::overwrite_existing_draftfile($draftid, $realpath, $file->get_filename(), $file->get_filepath(), $file->get_filename());
                } else {
                    // Directory already existed, remove temporary dir but make sure we don't remove the existing dir
                    $doremovedirs[] = $file->get_filepath();
                    $donotremovedirs[] = $realpath;
                }
            }
            $return = new stdClass();
            $return->filepath = $filepath;
        } else {
            $return = false;
        }
        // Remove remaining temporary directories.
        foreach (array_diff($doremovedirs, $donotremovedirs) as $filepath) {
            if ($file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, $filepath, '.')) {
                $file->delete();
            }
        }
        die(json_encode($return));

    case 'getoriginal':
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);

        $fs = get_file_storage();
        $file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, $filepath, $filename);
        if (!$file) {
            echo json_encode(false);
        } else {
            $return = array('filename' => $filename, 'filepath' => $filepath, 'original' => $file->get_reference_details());
            echo json_encode((object)$return);
        }
        die;

    case 'getreferences':
        $filename    = required_param('filename', PARAM_FILE);
        $filepath    = required_param('filepath', PARAM_PATH);

        $fs = get_file_storage();
        $file = $fs->get_file($usercontext->id, 'user', 'draft', $draftid, $filepath, $filename);
        if (!$file) {
            echo json_encode(false);
        } else {
            $source = unserialize($file->get_source());
            $return = array('filename' => $filename, 'filepath' => $filepath, 'references' => array());
            $browser = get_file_browser();
            if (isset($source->original)) {
                $reffiles = $fs->search_references($source->original);
                foreach ($reffiles as $reffile) {
                    $refcontext = context::instance_by_id($reffile->get_contextid());
                    $fileinfo = $browser->get_file_info($refcontext, $reffile->get_component(), $reffile->get_filearea(), $reffile->get_itemid(), $reffile->get_filepath(), $reffile->get_filename());
                    if (empty($fileinfo)) {
                        $return['references'][] = get_string('undisclosedreference', 'repository');
                    } else {
                        $return['references'][] = $fileinfo->get_readable_fullname();
                    }
                }
            }
            echo json_encode((object)$return);
        }
        die;

    default:
        // no/unknown action?
        echo json_encode(false);
        die;
}
