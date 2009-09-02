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

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once('lib.php');
/// Wait as long as it takes for this script to finish
set_time_limit(0);

require_login();

// disable blocks in this page
$PAGE->set_generaltype('form');

// general parameters
$action      = optional_param('action', '',        PARAM_ALPHA);
$client_id   = optional_param('client_id', SITEID, PARAM_RAW);    // client ID
$itemid      = optional_param('itemid', '',        PARAM_INT);

// parameters for repository
$callback    = optional_param('callback', '',      PARAM_CLEANHTML);
$contextid   = optional_param('ctx_id',    SITEID, PARAM_INT);    // context ID
$env         = optional_param('env', 'filepicker', PARAM_ALPHA);  // opened in file picker, file manager or html editor
$filename    = optional_param('filename', '',      PARAM_FILE);
$fileurl     = optional_param('fileurl', '',       PARAM_FILE);
$thumbnail   = optional_param('thumbnail', '',     PARAM_RAW);
$repo_id     = optional_param('repo_id', 0,        PARAM_INT);    // repository ID
$req_path    = optional_param('p', '',             PARAM_RAW);    // the path in repository
$page        = optional_param('page', '',          PARAM_RAW);    // What page in repository?
$search_text = optional_param('s', '',             PARAM_CLEANHTML);

// draft area
$newdirname  = optional_param('newdirname', '',    PARAM_FILE);
// path in draft area
$draftpath   = optional_param('draftpath', '/',    PARAM_PATH);


// user context
$user_context    = get_context_instance(CONTEXT_USER, $USER->id);

$PAGE->set_url('/repository/filepicker.php');

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

$url = new moodle_url($CFG->httpswwwroot."/repository/filepicker.php", array('ctx_id' => $contextid, 'itemid' => $itemid));
$home_url = new moodle_url($url, array('action' => 'embedded'));

switch ($action) {
case 'upload':
    // The uploaded file has been processed in plugin construct function
    // redirect to default page
    redirect($url, get_string('uploadsucc','repository'));
    break;

case 'deletedraft':
    $contextid = $user_context->id;
    $fs = get_file_storage();
    if ($file = $fs->get_file($contextid, 'user_draft', $itemid, $draftpath, $filename)) {
        if($result = $file->delete()) {
            header('Location: ' . $home_url->out(false, array(), false));
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
        $pagingbar = new moodle_paging_bar();
        $pagingbar->totalcount = $search_result['total'];
        $pagingbar->page = $search_result['page'] - 1;
        $pagingbar->perpage = $search_result['perpage'];
        $pagingbar->baseurl = clone($url);
        $pagingbar->baseurl->params(array('search_paging' => 1, 'action' => 'search', 'repo_id' => $repo_id));
        $pagingbar->pagevar = 'p';
        echo $OUTPUT->paging_bar($pagingbar);

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
    print_header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    if ($repo->check_login()) {
        $list = $repo->get_listing($req_path, $page);
        $dynload = !empty($list['dynload'])?true:false;
        if (!empty($list['upload'])) {
            echo '<form action="'.$url->out(false).'" method="post" enctype="multipart/form-data" style="display:inline">';
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
                $pagingurl = new moodle_url("$CFG->httpswwwroot/repository/filepicker.php?action=list&itemid=$itemid&ctx_id=$contextid&repo_id=$repo_id");
                echo $OUTPUT->paging_bar(moodle_paging_bar::make($list['total'], $list['page'] - 1, $list['perpage'], $pagingurl));
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
        $info = repository::move_to_filepool($filepath, $filename, $itemid, $draftpath);
        redirect($url, get_string('downloadsucc','repository'));
    } else {
        print_error('cannotdownload', 'repository');
    }

    break;

case 'confirm':
    print_header();
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
    $repos = repository::get_instances(array($user_context, get_system_context()), null, true, null, '*', 'ref_id');
    print_header();
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    echo '<div>';
    echo '<ul>';
    foreach($repos as $repo) {
        $info = $repo->get_meta();
        $icon = new moodle_action_icon();
        $icon->image->src = $info->icon;
        $icon->image->style = 'height: 16px; width: 16px;';
        $icon->link->url = clone($url);
        $icon->link->url->params(array('action' => 'list', 'repo_id' => $info->id, 'draftpath'=>$draftpath));
        $icon->linktext = $info->name;
        echo '<li>' . $OUTPUT->action_icon($icon) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo $OUTPUT->footer();
    break;

case 'mkdir':
    $fs = get_file_storage();
    $fs->create_directory($user_context->id, 'user_draft', $itemid, trim_path(trim_path($draftpath).$newdirname));
    $url->param('action', 'browse');
    $url->param('draftpath', $draftpath);
    redirect($url, get_string('Created folder success!','repository'));
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

case 'browse':
default:
    $user_context = get_context_instance(CONTEXT_USER, $USER->id);
    $repos = repository::get_instances(array($user_context, get_system_context()), null, true, null, '*', 'ref_id');
    print_header();

    echo '<div>';
    $url->param('draftpath', '/');
    echo '<a href="'.$url->out().'">'.'Files</a> ▶';
    $trail = '';
    if ($draftpath !== '/') {
        $path = trim_path($draftpath);
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


    $fs = get_file_storage();
    $files = $fs->get_directory_files($user_context->id, 'user_draft', $itemid, $draftpath, false);

    $iconzip  = $CFG->wwwroot . '/pix/f/zip.gif';

    if (empty($files)) {
        echo get_string('nofilesattached', 'repository');
    } else {
        echo '<ul>';
        foreach ($files as $file) {
            $drafturl = new moodle_url($CFG->httpswwwroot.'/draftfile.php/'.$user_context->id.'/user_draft/'.$itemid.'/'.$file->get_filename());
            if ($file->get_filename()!='.') {
                // a file
                $fileicon = $CFG->wwwroot.'/pix/'.(file_extension_icon($file->get_filename()));
                $type = str_replace('.gif', '', mimeinfo('icon', $file->get_filename()));
                echo '<li>';
                echo '<img src="'.$fileicon. '" class="iconsmall" />';
                echo ' <a href="'.$drafturl->out().'">'.$file->get_filename().'</a> ';

                $url->param('filename', $file->get_filename());
                $url->param('action', 'deletedraft');
                echo ' <a href="'.$url->out().'"><img src="'.$OUTPUT->old_icon_url('t/delete') . '" class="iconsmall" /></a>';

                if ($type == 'zip') {
                    $url->param('action', 'unzip');
                    $url->param('draftpath', $file->get_filepath());
                    echo ' [<a href="'.$url->out().'">Unzip</a>]';
                }

                echo '</li>';
            } else {
                // a folder
                echo '<li>';
                echo '<img src="'.$OUTPUT->old_icon_url('f/folder') . '" class="iconsmall" />';

                $url->param('action', 'browse');
                $url->param('draftpath', $file->get_filepath());
                $foldername = trim(array_pop(explode('/', trim($file->get_filepath(), '/'))), '/');
                echo ' <a href="'.$url->out().'">'.$foldername.'</a>';

                $url->param('draftpath', $file->get_filepath());
                $url->param('filename',  $file->get_filename());
                $url->param('action', 'deletedraft');
                echo ' <a href="'.$url->out().'"><img src="'.$OUTPUT->old_icon_url('t/delete') . '" class="iconsmall" /></a>';

                $url->param('action', 'zip');
                echo ' [<a href="'.$url->out().'">Zip</a>]';
                echo '</li>';
            }
        }
        echo '</ul>';
    }
    $url->param('draftpath', $draftpath);
    $url->param('action', 'plugins');

    echo '<div>';
    echo ' <a href="'.$url->out().'">'.get_string('addfile', 'repository').'</a>';
    $url->param('action', 'mkdir');
    echo ' <form method="post" action="'.$url->out().'">';
    echo '  <input name="newdirname" type="text" /><input type="submit" value="Make a folder" /></a>';
    echo '  <input name="draftpath" type="hidden" value="'.$draftpath.'" />';
    echo ' </form>';
    echo '</div>';

    echo $OUTPUT->footer();
    break;
}

/**
 * trim filepath, and add slash to it
 * @param string $str
 * @return string path
 */
function trim_path($str) {
    return '/'.trim(trim($str), './@#$').'/';
}
