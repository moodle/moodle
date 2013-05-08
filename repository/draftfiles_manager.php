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
//


//
// NOTE TO ALL DEVELOPERS: this script must deal only with draft area of current user, it has to use only file_storage and no file_browser!!
//


/**
 * This file is used to manage draft files in non-javascript browsers
 *
 * @since 2.0
 * @package    core
 * @subpackage repository
 * @copyright  2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once('lib.php');

require_sesskey();
require_login();

// disable blocks in this page
$PAGE->set_pagelayout('embedded');

// general parameters
$action      = optional_param('action', '',        PARAM_ALPHA);
$itemid      = optional_param('itemid', '',        PARAM_INT);

// parameters for repository
$contextid   = optional_param('ctx_id',    SYSCONTEXTID, PARAM_INT);    // context ID
$courseid    = optional_param('course',    SITEID, PARAM_INT);    // course ID
$env         = optional_param('env', 'filepicker', PARAM_ALPHA);  // opened in file picker, file manager or html editor
$filename    = optional_param('filename', '',      PARAM_FILE);
$targetpath  = optional_param('targetpath', '',    PARAM_PATH);
$maxfiles    = optional_param('maxfiles', -1, PARAM_INT);    // maxfiles
$maxbytes    = optional_param('maxbytes',  0, PARAM_INT);    // maxbytes
$subdirs     = optional_param('subdirs',  0, PARAM_INT);    // maxbytes
$areamaxbytes = optional_param('areamaxbytes', FILE_AREA_MAX_BYTES_UNLIMITED, PARAM_INT);    // Area maxbytes.

// draft area
$newdirname  = optional_param('newdirname', '',    PARAM_FILE);
$newfilename = optional_param('newfilename', '',   PARAM_FILE);
// path in draft area
$draftpath   = optional_param('draftpath', '/',    PARAM_PATH);

// user context
$user_context = context_user::instance($USER->id);


$PAGE->set_context($user_context);

$fs = get_file_storage();

$params = array('ctx_id' => $contextid, 'itemid' => $itemid, 'env' => $env, 'course'=>$courseid, 'maxbytes'=>$maxbytes, 'areamaxbytes'=>$areamaxbytes, 'maxfiles'=>$maxfiles, 'subdirs'=>$subdirs, 'sesskey'=>sesskey());
$PAGE->set_url('/repository/draftfiles_manager.php', $params);
$filepicker_url = new moodle_url($CFG->httpswwwroot."/repository/filepicker.php", $params);

$params['action'] = 'browse';
$home_url = new moodle_url('/repository/draftfiles_manager.php', $params);

switch ($action) {

    // delete draft files
case 'deletedraft':
    if ($file = $fs->get_file($user_context->id, 'user', 'draft', $itemid, $draftpath, $filename)) {
        if ($file->is_directory()) {
            $pathname = $draftpath;
            if ($file->get_parent_directory()) {
                $draftpath = $file->get_parent_directory()->get_filepath();
            } else {
                $draftpath = '/';
            }

            // delete files in folder
            $files = $fs->get_directory_files($user_context->id, 'user', 'draft', $itemid, $pathname, true);
            foreach ($files as $storedfile) {
                $storedfile->delete();
            }
            $file->delete();
        } else {
            $file->delete();
        }
        $home_url->param('draftpath', $draftpath);
        $home_url->param('action', 'browse');
        redirect($home_url);
    }
    break;

case 'renameform':
    echo $OUTPUT->header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    $home_url->param('draftpath', $draftpath);
    $home_url->param('action', 'rename');
    echo ' <form method="post" action="'.$home_url->out().'">';
    echo html_writer::label(get_string('enternewname', 'repository'), 'newfilename', array('class' => 'accesshide'));
    echo '  <input id="newfilename" name="newfilename" type="text" value="'.s($filename).'" />';
    echo '  <input name="filename" type="hidden" value="'.s($filename).'" />';
    echo '  <input name="draftpath" type="hidden" value="'.s($draftpath).'" />';
    echo '  <input type="submit" value="'.s(get_string('rename', 'moodle')).'" />';
    echo ' </form>';
    echo $OUTPUT->footer();
    break;

case 'rename':
    repository::update_draftfile($itemid, $draftpath, $filename, array('filename' => $newfilename));
    $home_url->param('action', 'browse');
    $home_url->param('draftpath', $draftpath);
    redirect($home_url);
    break;

case 'downloaddir':
    $zipper = new zip_packer();

    $file = $fs->get_file($user_context->id, 'user', 'draft', $itemid, $draftpath, '.');
    if ($draftpath === '/') {
        $filename = get_string('files').'.zip';
    } else {
        $filename = explode('/', trim($draftpath, '/'));
        $filename = array_pop($filename) . '.zip';
    }

    $newdraftitemid = file_get_unused_draft_itemid();
    if ($newfile = $zipper->archive_to_storage(array('/' => $file), $user_context->id, 'user', 'draft', $newdraftitemid, '/', $filename, $USER->id)) {
        $fileurl = moodle_url::make_draftfile_url($newdraftitemid, '/', $filename)->out();
        header('Location: ' . $fileurl);
    } else {
        print_error('cannotdownloaddir', 'repository');
    }
    break;

case 'zip':
    $zipper = new zip_packer();

    $file = $fs->get_file($user_context->id, 'user', 'draft', $itemid, $draftpath, '.');
    if (!$file->get_parent_directory()) {
        $parent_path = '/';
        $filepath = '/';
        $filename = get_string('files').'.zip';
    } else {
        $parent_path = $file->get_parent_directory()->get_filepath();
        $filepath = explode('/', trim($file->get_filepath(), '/'));
        $filepath = array_pop($filepath);
        $filename = $filepath.'.zip';
    }

    $filename = repository::get_unused_filename($itemid, $parent_path, $filename);
    $newfile = $zipper->archive_to_storage(array($filepath => $file), $user_context->id, 'user', 'draft', $itemid, $parent_path, $filename, $USER->id);

    $home_url->param('action', 'browse');
    $home_url->param('draftpath', $parent_path);
    redirect($home_url, get_string('ziped', 'repository'));
    break;

case 'unzip':
    $zipper = new zip_packer();
    $file = $fs->get_file($user_context->id, 'user', 'draft', $itemid, $draftpath, $filename);

    if ($newfile = $file->extract_to_storage($zipper, $user_context->id, 'user', 'draft', $itemid, $draftpath, $USER->id)) {
        $str = get_string('unzipped', 'repository');
    } else {
        $str = get_string('cannotunzip', 'error');
    }
    $home_url->param('action', 'browse');
    $home_url->param('draftpath', $draftpath);
    redirect($home_url, $str);
    break;

case 'movefile':
    if (!empty($targetpath)) {
        repository::update_draftfile($itemid, $draftpath, $filename, array('filepath' => $targetpath));
        $home_url->param('action', 'browse');
        $home_url->param('draftpath', $targetpath);
        redirect($home_url);
    }
    echo $OUTPUT->header();

    echo $OUTPUT->container_start();
    echo html_writer::link($home_url, get_string('back', 'repository'));
    echo $OUTPUT->container_end();

    $data = new stdClass();
    $home_url->param('action', 'movefile');
    $home_url->param('draftpath', $draftpath);
    $home_url->param('filename', $filename);
    file_get_drafarea_folders($itemid, '/', $data);
    print_draft_area_tree($data, true, $home_url);
    echo $OUTPUT->footer();
    break;

case 'mkdirform':
    echo $OUTPUT->header();

    echo $OUTPUT->container_start();
    echo html_writer::link($home_url, get_string('back', 'repository'));
    echo $OUTPUT->container_end();

    $home_url->param('draftpath', $draftpath);
    $home_url->param('action', 'mkdir');
    echo ' <form method="post" action="'.$home_url->out().'">';
    echo html_writer::label(get_string('entername', 'repository'), 'newdirname', array('class' => 'accesshide'));
    echo '  <input name="newdirname" id="newdirname" type="text" />';
    echo '  <input name="draftpath" type="hidden" value="'.s($draftpath).'" />';
    echo '  <input type="submit" value="'.s(get_string('makeafolder', 'moodle')).'" />';
    echo ' </form>';
    echo $OUTPUT->footer();
    break;

case 'mkdir':

    $newfolderpath = $draftpath . trim($newdirname, '/') . '/';
    $fs->create_directory($user_context->id, 'user', 'draft', $itemid, $newfolderpath);
    $home_url->param('action', 'browse');
    if (!empty($newdirname)) {
        $home_url->param('draftpath', $newfolderpath);
        $str = get_string('createfoldersuccess', 'repository');
    } else {
        $home_url->param('draftpath', $draftpath);
        $str = get_string('createfolderfail', 'repository');
    }
    redirect($home_url, $str);
    break;

case 'browse':
default:
    $files = file_get_drafarea_files($itemid, $draftpath);
    $info = file_get_draft_area_info($itemid);
    $filecount = $info['filecount'];

    echo $OUTPUT->header();
    if ((!empty($files) or $draftpath != '/') and $env == 'filemanager') {
        echo '<div class="fm-breadcrumb">';
        $home_url->param('action', 'browse');
        $home_url->param('draftpath', '/');
        echo '<a href="'.$home_url->out().'">' . get_string('files') . '</a> ▶';
        $trail = '';
        if ($draftpath !== '/') {
            $path = '/' . trim($draftpath, '/') . '/';
            $parts = explode('/', $path);
            foreach ($parts as $part) {
                if ($part != '') {
                    $trail .= ('/'.$part.'/');
                    $data->path[] = array('name'=>$part, 'path'=>$trail);
                    $home_url->param('draftpath', $trail);
                    echo ' <a href="'.$home_url->out().'">'.$part.'</a> ▶ ';
                }
            }
        }
        echo '</div>';
    }

    $filepicker_url->param('draftpath', $draftpath);
    $filepicker_url->param('savepath', $draftpath);
    $filepicker_url->param('action', 'plugins');
    echo '<div class="filemanager-toolbar">';
    if ($env == 'filepicker') {
        $maxfiles = 1;
    }
    if ($filecount < $maxfiles || $maxfiles == -1) {
        echo ' <a href="'.$filepicker_url->out().'">'.get_string('addfile', 'repository').'</a>';
    }
    if ($env == 'filemanager') {
        if (!empty($subdirs)) {
            $home_url->param('action', 'mkdirform');
            echo ' <a href="'.$home_url->out().'">'.get_string('makeafolder', 'moodle').'</a>';
        }
        if (!empty($files->list)) {
            $home_url->param('action', 'downloaddir');
            echo ' ' . html_writer::link($home_url, get_string('downloadfolder', 'repository'), array('target'=>'_blank'));
        }
    }
    echo '</div>';

    if (!empty($files->list)) {
        echo '<ul>';
        foreach ($files->list as $file) {
            if ($file->type != 'folder') {
                $drafturl = $file->url;
                // a file
                echo '<li>';
                echo $OUTPUT->pix_icon(file_file_icon($file), '', 'moodle', array('class' => 'iconsmall'));
                echo html_writer::link($drafturl, $file->filename);

                $home_url->param('filename', $file->filename);
                $home_url->param('draftpath', $file->filepath);

                $home_url->param('action', 'deletedraft');
                echo ' [<a href="'.$home_url->out().'" class="fm-operation">'.get_string('delete').'</a>]';

                $home_url->param('action', 'movefile');
                echo ' [<a href="'.$home_url->out().'" class="fm-operation">'.get_string('move').'</a>]';

                $home_url->param('action', 'renameform');
                echo ' [<a href="'.$home_url->out().'" class="fm-operation">'.get_string('rename').'</a>]';

                if (file_extension_in_typegroup($file->filename, 'archive', true)) {
                    $home_url->param('action', 'unzip');
                    $home_url->param('draftpath', $file->filepath);
                    echo ' [<a href="'.$home_url->out().'" class="fm-operation">'.get_string('unzip').'</a>]';
                }

                echo '</li>';
            } else {
                // a folder
                echo '<li>';
                echo '<img src="'.$OUTPUT->pix_url(file_folder_icon()) . '" class="iconsmall" />';

                $home_url->param('action', 'browse');
                $home_url->param('draftpath', $file->filepath);
                $filepathchunks = explode('/', trim($file->filepath, '/'));
                $foldername = trim(array_pop($filepathchunks), '/');
                echo html_writer::link($home_url, $foldername);

                $home_url->param('draftpath', $file->filepath);
                $home_url->param('filename',  $file->filename);
                $home_url->param('action', 'deletedraft');
                echo ' [<a href="'.$home_url->out().'" class="fm-operation">'.get_string('delete').'</a>]';

                $home_url->param('action', 'zip');
                echo ' [<a href="'.$home_url->out().'" class="fm-operation">Zip</a>]';
                echo '</li>';
            }
        }
        echo '</ul>';
    } else {
        echo get_string('nofilesavailable', 'repository');
    }
    echo $OUTPUT->footer();
    break;
}

function print_draft_area_tree($tree, $root, $url) {
    echo '<ul>';
    if ($root) {
        $url->param('targetpath', '/');
        if ($url->param('draftpath') == '/') {
            echo '<li>'.get_string('files').'</li>';
        } else {
            echo '<li><a href="'.$url->out().'">'.get_string('files').'</a></li>';
        }
        echo '<ul>';
        if (isset($tree->children)) {
            $tree = $tree->children;
        }
    }

    if (!empty($tree)) {
        foreach ($tree as $node) {
            echo '<li>';
            $url->param('targetpath', $node->filepath);
            if ($url->param('draftpath') != $node->filepath) {
                echo '<a href="'.$url->out().'">'.$node->fullname.'</a>';
            } else {
                echo $node->fullname;
            }
            echo '</li>';
            if (!empty($node->children)) {
                print_draft_area_tree($node->children, false, $url);
            }
        }
    }
    if ($root) {
        echo '</ul>';
    }
    echo '</ul>';
}
