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

/**
 * This file is used to browse repositories in non-javascript mode
 *
 * @since 2.0
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once('lib.php');
/// Wait as long as it takes for this script to finish
set_time_limit(0);

require_login();

// disable blocks in this page
$PAGE->set_pagelayout('embedded');

// general parameters
$action      = optional_param('action', '',        PARAM_ALPHA);
$client_id   = optional_param('client_id', SITEID, PARAM_RAW);    // client ID
$itemid      = optional_param('itemid', '',        PARAM_INT);

// parameters for repository
$callback    = optional_param('callback', '',      PARAM_CLEANHTML);
$contextid   = optional_param('ctx_id',    SYSCONTEXTID, PARAM_INT);    // context ID
$courseid    = optional_param('course',    SITEID, PARAM_INT);    // course ID
$env         = optional_param('env', 'filepicker', PARAM_ALPHA);  // opened in file picker, file manager or html editor
$filename    = optional_param('filename', '',      PARAM_FILE);
$fileurl     = optional_param('fileurl', '',       PARAM_FILE);
$filearea    = optional_param('filearea', '',      PARAM_TEXT);
$thumbnail   = optional_param('thumbnail', '',     PARAM_RAW);
$targetpath  = optional_param('targetpath', '',    PARAM_PATH);
$repo_id     = optional_param('repo_id', 0,        PARAM_INT);    // repository ID
$req_path    = optional_param('p', '',             PARAM_RAW);    // the path in repository
$curr_page   = optional_param('page', '',          PARAM_RAW);    // What page in repository?
$search_text = optional_param('s', '',             PARAM_CLEANHTML);

// draft area
$newdirname  = optional_param('newdirname', '',    PARAM_FILE);
$newfilename = optional_param('newfilename', '',   PARAM_FILE);
// path in draft area
$draftpath   = optional_param('draftpath', '/',    PARAM_PATH);


// user context
$user_context    = get_context_instance(CONTEXT_USER, $USER->id);

$PAGE->set_url('/repository/filepicker.php');
if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourseid');
}
$PAGE->set_course($course);

// init repository plugin
//
$sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i '.
       'WHERE i.id=? AND i.typeid=r.id';
if ($repository = $DB->get_record_sql($sql, array($repo_id))) {
    $type = $repository->type;
    if (file_exists($CFG->dirroot.'/repository/'.$type.'/repository.class.php')) {
        require_once($CFG->dirroot.'/repository/'.$type.'/repository.class.php');
        $classname = 'repository_' . $type;
        try {
            $repo = new $classname($repo_id, $contextid, array('ajax'=>false, 'name'=>$repository->name));
        } catch (repository_exception $e){
            print_error('pluginerror', 'repository');
        }
    } else {
        print_error('invalidplugin', 'repository');
    }
}

$url = new moodle_url($CFG->httpswwwroot."/repository/filepicker.php", array('ctx_id' => $contextid, 'itemid' => $itemid, 'env' => $env, 'course'=>$courseid));
$home_url = new moodle_url($url, array('action' => 'browse'));

switch ($action) {
case 'upload':
    // The uploaded file has been processed in plugin construct function
    // redirect to default page
    $repo->upload();
    redirect($url, get_string('uploadsucc','repository'));
    break;

case 'deletedraft':
    $contextid = $user_context->id;
    $fs = get_file_storage();
    if ($file = $fs->get_file($contextid, 'user_draft', $itemid, $draftpath, $filename)) {
        if ($file->is_directory()) {
            if ($file->get_parent_directory()) {
                $draftpath = $file->get_parent_directory()->get_filepath();
            } else {
                $draftpath = '/';
            }
        }
        if($result = $file->delete()) {
            $url->param('draftpath', $draftpath);
            $url->param('action', 'browse');
            redirect($url);
        } else {
            print_error('cannotdelete', 'repository');
        }
    }
    break;

case 'search':
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    try {
        $search_result = $repo->search($search_text);
        $search_result['search_result'] = true;
        $search_result['repo_id'] = $repo_id;

        // TODO: need a better solution
        $purl = new moodle_ulr($url, array('search_paging' => 1, 'action' => 'search', 'repo_id' => $repo_id));
        $pagingbar = new paging_bar($search_result['total'], $search_result['page'] - 1, $search_result['perpage'], $purl, 'p');
        echo $OUTPUT->render($pagingbar);

        echo '<table>';
        foreach ($search_result['list'] as $item) {
            echo '<tr>';
            echo '<td><img src="'.$item['thumbnail'].'" />';
            echo '</td><td>';
            if (!empty($item['url'])) {
                echo '<a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a>';
            } else {
                echo $item['title'];
            }
            echo '</td>';
            echo '<td>';
            echo '<form method="post">';
            echo '<input type="hidden" name="fileurl" value="'.$item['source'].'"/>';
            echo '<input type="hidden" name="action" value="confirm"/>';
            echo '<input type="hidden" name="filename" value="'.$item['title'].'"/>';
            echo '<input type="hidden" name="thumbnail" value="'.$item['thumbnail'].'"/>';
            echo '<input type="submit" value="'.get_string('select','repository').'" />';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } catch (repository_exception $e) {
    }
    break;

case 'list':
case 'sign':
    echo $OUTPUT->header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    if ($repo->check_login()) {
        $list = $repo->get_listing($req_path, $curr_page);
        $dynload = !empty($list['dynload'])?true:false;
        if (!empty($list['upload'])) {
            echo '<form action="'.$url->out().'" method="post" enctype="multipart/form-data" style="display:inline">';
            echo '<label>'.$list['upload']['label'].': </label>';
            echo '<input type="file" name="repo_upload_file" /><br />';
            echo '<input type="hidden" name="action" value="upload" /><br />';
            echo '<input type="hidden" name="draftpath" value="'.$draftpath.'" /><br />';
            echo '<input type="hidden" name="repo_id" value="'.$repo_id.'" /><br />';
            echo '<input type="submit" value="'.get_string('upload', 'repository').'" />';
            echo '</form>';
        } else {
            if (!empty($list['path'])) {
                foreach ($list['path'] as $p) {
                    echo '<form method="post" style="display:inline">';
                    echo '<input type="hidden" name="p" value="'.$p['path'].'"';
                    echo '<input type="hidden" name="action" value="list"';
                    echo '<input type="hidden" name="draftpath" value="'.$draftpath.'" /><br />';
                    echo '<input type="submit" value="'.$p['name'].'" />';
                    echo '</form>';
                    echo '<strong> / </strong>';
                }
            }
            if (!empty($list['page'])) {
                // TODO: need a better solution
                $pagingurl = new moodle_url("$CFG->httpswwwroot/repository/filepicker.php?action=list&itemid=$itemid&ctx_id=$contextid&repo_id=$repo_id&course=$courseid");
                echo $OUTPUT->paging_bar($list['total'], $list['page'] - 1, $list['perpage'], $pagingurl);
            }
            echo '<table>';
            foreach ($list['list'] as $item) {
                echo '<tr>';
                echo '<td><img src="'.$item['thumbnail'].'" />';
                echo '</td><td>';
                if (!empty($item['url'])) {
                    echo '<a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a>';
                } else {
                    echo $item['title'];
                }
                echo '</td>';
                echo '<td>';
                if (!isset($item['children'])) {
                    echo '<form method="post">';
                    echo '<input type="hidden" name="fileurl" value="'.$item['source'].'"/>';
                    echo '<input type="hidden" name="action" value="confirm"/>';
                    echo '<input type="hidden" name="draftpath" value="'.$draftpath.'" /><br />';
                    echo '<input type="hidden" name="filename" value="'.$item['title'].'"/>';
                    echo '<input type="hidden" name="thumbnail" value="'.$item['thumbnail'].'"/>';
                    echo '<input type="submit" value="'.get_string('select','repository').'" />';
                    echo '</form>';
                } else {
                    echo '<form method="post">';
                    echo '<input type="hidden" name="p" value="'.$item['path'].'"/>';
                    echo '<input type="submit" value="'.get_string('enter', 'repository').'" />';
                    echo '</form>';
                }
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    } else {
        echo '<form method="post">';
        echo '<input type="hidden" name="action" value="sign" />';
        echo '<input type="hidden" name="repo_id" value="'.$repo_id.'" />';
        $repo->print_login();
        echo '</form>';
    }
    echo $OUTPUT->footer();
    break;

case 'download':
    $filepath = $repo->get_file($fileurl, $filename, $itemid);
    if (!empty($filepath)) {
        if (!is_array($filepath)) {
            $info = repository::move_to_filepool($filepath, $filename, $itemid, $draftpath);
        }
        redirect($url, get_string('downloadsucc','repository'));
    } else {
        print_error('cannotdownload', 'repository');
    }

    break;

case 'downloaddir':
    $zipper = new zip_packer();
    $fs = get_file_storage();

    $file = $fs->get_file($user_context->id, 'user_draft', $itemid, $draftpath, '.');
    if ($file->get_parent_directory()) {
        $parent_path = $file->get_parent_directory()->get_filepath();
        $filename = trim($draftpath, '/').'.zip';
    } else {
        $parent_path = '/';
        $filename = 'draft_area.zip';
    }

    if ($newfile = $zipper->archive_to_storage(array($file), $user_context->id, 'user_draft', $itemid, $parent_path, $filename, $USER->id)) {
        $fileurl  = $CFG->wwwroot . '/draftfile.php/' . $user_context->id .'/user_draft/'.$itemid.$parent_path.$filename;
        header('Location: ' . $fileurl );
    } else {
        print_error('cannotdownloaddir', 'repository');
    }
    break;

case 'confirm':
    echo $OUTPUT->header();
    echo '<div><a href="'.me().'">'.get_string('back', 'repository').'</a></div>';
    echo '<img src="'.$thumbnail.'" />';
    echo '<form method="post">';
    echo '<table>';
    echo '  <tr>';
    echo '    <td><label>'.get_string('filename', 'repository').'</label></td>';
    echo '    <td><input type="text" name="filename" value="'.$filename.'" /></td>';
    echo '    <td><input type="hidden" name="fileurl" value="'.$fileurl.'" /></td>';
    echo '    <td><input type="hidden" name="action" value="download" /></td>';
    echo '    <td><input type="hidden" name="itemid" value="'.$itemid.'" /></td>';
    echo '  </tr>';
    echo '</table>';
    echo '<div>';
    // the save path
    echo ' <input name="draftpath" type="hidden" value="'.$draftpath.'" />';
    echo ' <input type="submit" value="'.get_string('download', 'repository').'" />';
    echo '</div>';
    echo '</form>';
    echo $OUTPUT->footer();
    break;

case 'plugins':
    $user_context = get_context_instance(CONTEXT_USER, $USER->id);
    $params = array();
    $params['context'] = array($user_context, get_system_context());
    $params['currentcontext'] = $PAGE->context;
    $params['returntypes'] = 2;
    $repos = repository::get_instances($params);
    echo $OUTPUT->header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    echo '<div>';
    echo '<ul>';
    foreach($repos as $repo) {
        $info = $repo->get_meta();

        $aurl = clone($url);
        $aurl->params(array('action' => 'list', 'repo_id' => $info->id, 'draftpath'=>$draftpath));

        if ($env == 'filemanager' && $info->type == 'draft') {
            continue;
        }
        echo '<li>' . $OUTPUT->action_icon($aurl, new pix_icon($info->icon, $info->name)) . '</li>'; // no hardcoded styles!
    }
    echo '</ul>';
    echo '</div>';
    echo $OUTPUT->footer();
    break;

case 'zip':
    $zipper = new zip_packer();
    $fs = get_file_storage();

    $file = $fs->get_file($user_context->id, 'user_draft', $itemid, $draftpath, '.');
    if (!$file->get_parent_directory()) {
        $parent_path = '/';
    } else {
        $parent_path = $file->get_parent_directory()->get_filepath();
    }

    $newfile = $zipper->archive_to_storage(array($file), $user_context->id, 'user_draft', $itemid, $parent_path, $file->get_filepath().'.zip', $USER->id);

    $url->param('action', 'browse');
    $url->param('draftpath', $parent_path);
    redirect($url, get_string('ziped','repository'));
    break;

case 'unzip':
    $zipper = new zip_packer();
    $fs = get_file_storage();
    $file = $fs->get_file($user_context->id, 'user_draft', $itemid, $draftpath, $filename);

    if ($newfile = $file->extract_to_storage($zipper, $user_context->id, 'user_draft', $itemid, $draftpath, $USER->id)) {
        $str = get_string('unziped','repository');
    } else {
        $str = get_string('cannotunzip', 'repository');
    }
    $url->param('action', 'browse');
    $url->param('draftpath', $draftpath);
    redirect($url, $str);
    break;

case 'movefile':
    if (!empty($targetpath)) {
        $fb = get_file_browser();
        $file = $fb->get_file_info($user_context, 'user_draft', $itemid, $draftpath, $filename);
        $file->copy_to_storage($user_context->id, 'user_draft', $itemid, $targetpath, $filename);
        if ($file->delete()) {
            $url->param('action', 'browse');
            $url->param('draftpath', $targetpath);
            redirect($url, '');
            exit;
        }
    }
    echo $OUTPUT->header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    $data = new stdclass;
    $url->param('action', 'movefile');
    $url->param('draftpath', $draftpath);
    $url->param('filename', $filename);
    file_get_draft_area_folders($itemid, '/', $data);
    print_draft_area_tree($data, true, $url);
    echo $OUTPUT->footer();
    break;
case 'mkdirform':
    echo $OUTPUT->header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    $url->param('draftpath', $draftpath);
    $url->param('action', 'mkdir');
    echo ' <form method="post" action="'.$url->out().'">';
    echo '  <input name="newdirname" type="text" />';
    echo '  <input name="draftpath" type="hidden" value="'.$draftpath.'" />';
    echo '  <input type="submit" value="'.get_string('makeafolder', 'moodle').'" />';
    echo ' </form>';
    echo $OUTPUT->footer();
    break;

case 'mkdir':
    $fs = get_file_storage();
    $fs->create_directory($user_context->id, 'user_draft', $itemid, file_correct_filepath(file_correct_filepath($draftpath).trim($newdirname, '/')));
    $url->param('action', 'browse');
    $url->param('draftpath', $draftpath);
    if (!empty($newdirname)) {
        $str = get_string('createfoldersuccess', 'repository');
    } else {
        $str = get_string('createfolderfail', 'repository');
    }
    redirect($url, $str);
    break;

case 'rename':
    $fs = get_file_storage();
    if ($file = $fs->get_file($user_context->id, 'user_draft', $itemid, $draftpath, $filename)) {
        if ($file->is_directory()) {
            if ($file->get_parent_directory()) {
                $draftpath = $file->get_parent_directory()->get_filepath();
            } else {
                $draftpath = '/';
            }
            // use file storage to create new folder
            $newdir = $draftpath . trim($newfilename , '/') . '/';
            $fs->create_directory($user_context->id, 'user_draft', $itemid, $newdir);
        } else {
            // use file browser to copy file
            $fb = get_file_browser();
            $file = $fb->get_file_info($user_context, 'user_draft', $itemid, $draftpath, $filename);
            $file->copy_to_storage($user_context->id, 'user_draft', $itemid, $draftpath, $newfilename);
        }
    }
    $file->delete();
    $url->param('action', 'browse');
    $url->param('draftpath', $draftpath);
    redirect($url);
    break;

case 'renameform':
    echo $OUTPUT->header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    $url->param('draftpath', $draftpath);
    $url->param('action', 'rename');
    echo ' <form method="post" action="'.$url->out().'">';
    echo '  <input name="newfilename" type="text" value="'.$filename.'" />';
    echo '  <input name="filename" type="hidden" value="'.$filename.'" />';
    echo '  <input name="draftpath" type="hidden" value="'.$draftpath.'" />';
    echo '  <input type="submit" value="'.get_string('rename', 'moodle').'" />';
    echo ' </form>';
    echo $OUTPUT->footer();
    break;

case 'browse':
default:
    $user_context = get_context_instance(CONTEXT_USER, $USER->id);
    $params = array();
    $params['context'] = array($user_context, get_system_context());
    $params['currentcontext'] = $PAGE->context;
    $params['returntypes'] = 2;
    $repos = repository::get_instances($params);
    $fs = get_file_storage();
    $files = $fs->get_directory_files($user_context->id, $filearea, $itemid, $draftpath, false);

    echo $OUTPUT->header();
    if ((!empty($files) or $draftpath != '/') and $env == 'filemanager') {
        echo '<div class="fm-breadcrumb">';
        $url->param('action', 'browse');
        $url->param('draftpath', '/');
        echo '<a href="'.$url->out().'">'.'Files</a> ▶';
        $trail = '';
        if ($draftpath !== '/') {
            $path = file_correct_filepath($draftpath);
            $parts = explode('/', $path);
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $trail .= ('/'.$part.'/');
                    $data->path[] = array('name'=>$part, 'path'=>$trail);
                    $url->param('draftpath', $trail);
                    echo ' <a href="'.$url->out().'">'.$part.'</a> ▶ ';
                }
            }
        }
        echo '</div>';
    }

    $url->param('draftpath', $draftpath);
    $url->param('action', 'plugins');
    echo '<div class="filemanager-toolbar">';
    if ($env == 'filepicker' and sizeof($files) > 0) {
    } else {
        echo ' <a href="'.$url->out().'">'.get_string('addfile', 'repository').'</a>';
    }
    if ($env == 'filemanager') {
        $url->param('action', 'mkdirform');
        echo ' <a href="'.$url->out().'">'.get_string('makeafolder', 'moodle').'</a>';
        $url->param('action', 'downloaddir');
        echo ' <a href="'.$url->out().'" target="_blank">'.get_string('downloadfolder', 'repository').'</a>';
    }
    echo '</div>';

    if (!empty($files)) {
        echo '<ul>';
        foreach ($files as $file) {
            $drafturl = new moodle_url($CFG->httpswwwroot.'/draftfile.php/'.$user_context->id.'/user_draft/'.$itemid.'/'.$file->get_filename());
            if ($file->get_filename() != '.') {
                // a file
                $fileicon = $CFG->wwwroot.'/pix/'.(file_extension_icon($file->get_filename()));
                $type = str_replace('.gif', '', mimeinfo('icon', $file->get_filename()));
                echo '<li>';
                echo '<img src="'.$fileicon. '" class="iconsmall" />';
                echo ' <a href="'.$drafturl->out().'">'.$file->get_filename().'</a> ';

                $url->param('filename', $file->get_filename());

                $url->param('action', 'deletedraft');
                $url->param('draftpath', $file->get_filepath());
                echo ' [<a href="'.$url->out().'" class="fm-operation">'.get_string('delete').'</a>]';

                $url->param('action', 'movefile');
                echo ' [<a href="'.$url->out().'" class="fm-operation">'.get_string('move').'</a>]';

                $url->param('action', 'renameform');
                echo ' [<a href="'.$url->out().'" class="fm-operation">'.get_string('rename').'</a>]';

                if ($type == 'zip') {
                    $url->param('action', 'unzip');
                    $url->param('draftpath', $file->get_filepath());
                    echo ' [<a href="'.$url->out().'" class="fm-operation">'.get_string('unzip').'</a>]';
                }

                echo '</li>';
            } else {
                // a folder
                echo '<li>';
                echo '<img src="'.$OUTPUT->pix_url('f/folder') . '" class="iconsmall" />';

                $url->param('action', 'browse');
                $url->param('draftpath', $file->get_filepath());
                $foldername = trim(array_pop(explode('/', trim($file->get_filepath(), '/'))), '/');
                echo ' <a href="'.$url->out().'">'.$foldername.'</a>';

                $url->param('draftpath', $file->get_filepath());
                $url->param('filename',  $file->get_filename());
                $url->param('action', 'deletedraft');
                echo ' [<a href="'.$url->out().'" class="fm-operation">'.get_string('delete').'</a>]';

                // file doesn't support rename yet
                // for folder with existing files, we need to move these files one by one
                //$url->param('action', 'renameform');
                //echo ' [<a href="'.$url->out().'" class="fm-operation">'.get_string('rename').'</a>]';

                $url->param('action', 'zip');
                echo ' [<a href="'.$url->out().'" class="fm-operation">Zip</a>]';
                echo '</li>';
            }
        }
        echo '</ul>';
    } else {
        //echo get_string('nofilesattached', 'repository');
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

