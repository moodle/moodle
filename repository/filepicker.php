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

$page        = optional_param('page', '',          PARAM_RAW);    // page
$client_id   = optional_param('client_id', SITEID, PARAM_RAW);    // client ID
$env         = optional_param('env', 'filepicker', PARAM_ALPHA);  // opened in editor or moodleform
$file        = optional_param('file', '',          PARAM_RAW);    // file to download
$title       = optional_param('title', '',         PARAM_FILE);   // new file name
$itemid      = optional_param('itemid', '',        PARAM_INT);
$icon        = optional_param('icon', '',          PARAM_RAW);
$action      = optional_param('action', '',        PARAM_ALPHA);
$ctx_id      = optional_param('ctx_id', SITEID,    PARAM_INT);    // context ID
$repo_id     = optional_param('repo_id', 1,        PARAM_INT);    // repository ID
$req_path    = optional_param('p', '',             PARAM_RAW);          // path
$callback    = optional_param('callback', '',      PARAM_CLEANHTML);
$search_text = optional_param('s', '',             PARAM_CLEANHTML);

// init repository plugin
$sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i '.
       'WHERE i.id=? AND i.typeid=r.id';
if (!$repository = $DB->get_record_sql($sql, array($repo_id))) {
    print_error('invalidrepositoryid', 'repository');
} else {
    $type = $repository->type;
}
$url = $CFG->httpswwwroot."/repository/filepicker.php?ctx_id=$ctx_id&itemid=$itemid";

if (file_exists($CFG->dirroot.'/repository/'.$type.'/repository.class.php')) {
    require_once($CFG->dirroot.'/repository/'.$type.'/repository.class.php');
    $classname = 'repository_' . $type;
    try {
        $repo = new $classname($repo_id, $ctx_id, array('ajax'=>false, 'name'=>$repository->name, 'client_id'=>$client_id));
    } catch (repository_exception $e){
        print_error('pluginerror', 'repository');
    }
} else {
    print_error('invalidplugin', 'repository');
}
//$context = get_context_instance_by_id($ctx_id);
//$PAGE->set_course($context);

switch ($action) {
case 'upload':
    redirect($url, get_string('uploadsucc','repository'));
    break;
case 'deletedraft':
    if (!$context = get_context_instance(CONTEXT_USER, $USER->id)) {
        print_error('wrongcontextid', 'error');
    }
    $contextid = $context->id;
    $fs = get_file_storage();
    if ($file = $fs->get_file($contextid, 'user_draft', $itemid, '/', $title)) {
        if($result = $file->delete()) {
            header("Location: $CFG->httpswwwroot/repository/filepicker.php?action=embedded&itemid=$itemid&ctx_id=$ctx_id");
        } else {
            print_error('cannotdelete', 'repository');
        }
    }
    exit;
    break;
case 'search':
    try {
        $search_result = $repo->search($search_text);
        $search_result['search_result'] = true;
        $search_result['repo_id'] = $repo_id;
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
            if (!isset($item['children'])) {
                echo '<form method="post">';
                echo '<input type="hidden" name="file" value="'.$item['source'].'"/>';
                echo '<input type="hidden" name="action" value="confirm"/>';
                echo '<input type="hidden" name="title" value="'.$item['title'].'"/>';
                echo '<input type="hidden" name="icon" value="'.$item['thumbnail'].'"/>';
                echo '<input type="submit" value="'.get_string('select','repository').'" />';
                echo '</form>';
            } else {
                echo '<form method="post">';
                echo '<input type="hidden" name="p" value="'.$item['path'].'"/>';
                echo '<input type="submit" value="'.get_string('enter', 'repository').'" />';
                echo '</form>';
            }
            echo '</td>';
            echo '<td width="100px" align="center">';
            echo '</td>';
            echo '</td></tr>';
        }
        echo '</table>';
    } catch (repository_exception $e) {
    }
    break;
case 'list':
case 'sign':
    print_header();
    echo "<div><a href='$CFG->httpswwwroot/repository/filepicker.php?action=embedded&itemid=$itemid&ctx_id=$ctx_id'>".get_string('back', 'repository')."</a></div>";
    if ($repo->check_login()) {
        $list = $repo->get_listing($req_path);
        $dynload = !empty($list['dynload'])?true:false;
        if (!empty($list['upload'])) {
            echo '<form action="'.$url.'" method="post" enctype="multipart/form-data" style="display:inline">';
            echo '<label>'.$list['upload']['label'].': </label>';
            echo '<input type="file" name="repo_upload_file" /><br />';
            echo '<input type="hidden" name="action" value="upload" /><br />';
            echo '<input type="hidden" name="repo_id" value="'.$repo_id.'" /><br />';
            echo '<input type="submit" value="'.get_string('upload', 'repository').'" />';
            echo '</form>';
        } else {
            if (!empty($list['path'])) {
                foreach ($list['path'] as $p) {
                    echo '<form method="post" style="display:inline">';
                    echo '<input type="hidden" name="p" value="'.$p['path'].'"';
                    echo '<input type="hidden" name="action" value="list"';
                    echo '<input type="submit" value="'.$p['name'].'" />';
                    echo '</form>';
                    echo '<strong> / </strong>';
                }
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
                    echo '<input type="hidden" name="file" value="'.$item['source'].'"/>';
                    echo '<input type="hidden" name="action" value="confirm"/>';
                    echo '<input type="hidden" name="title" value="'.$item['title'].'"/>';
                    echo '<input type="hidden" name="icon" value="'.$item['thumbnail'].'"/>';
                    echo '<input type="submit" value="'.get_string('select','repository').'" />';
                    echo '</form>';
                } else {
                    echo '<form method="post">';
                    echo '<input type="hidden" name="p" value="'.$item['path'].'"/>';
                    echo '<input type="submit" value="'.get_string('enter', 'repository').'" />';
                    echo '</form>';
                }
                echo '</td>';
                echo '<td width="100px" align="center">';
                echo '</td>';
                echo '</td></tr>';
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
    print_footer('empty');
    break;
case 'download':
    $filepath = $repo->get_file($file, $title, $itemid);
    if (preg_match('#(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)#', $filepath)) {
        // youtube plugin return a url instead a file path
        $url = $filepath;
        echo json_encode(array(
                    /* File picker need to know this is a link
                     * in order to attach title to url
                     */
                    'type'=>'link',
                    'client_id'=>$client_id,
                    'url'=>$url,
                    'id'=>$url,
                    'file'=>$url
                    )
                );
    } else if (is_array($filepath)) {
        // file api don't have real file path, so we need more file api specific info for "local" plugin
        $fileinfo = $filepath;
        $info = array();
        $info['file'] = $fileinfo['title'];
        $info['id'] = $itemid;
        $info['url'] = $CFG->httpswwwroot.'/draftfile.php/'.$fileinfo['contextid'].'/user_draft/'.$itemid.'/'.$fileinfo['title'];
        echo json_encode($info);
    } else {
        // normal file path name
        $info = repository::move_to_filepool($filepath, $title, $itemid);
        //echo json_encode($info);
        redirect($url, get_string('downloadsucc','repository'));
    }

    break;
case 'confirm':
    print_header();
    echo '<div><a href="'.me().'">'.get_string('back', 'repository').'</a></div>';
    echo '<img src="'.$icon.'" />';
    echo '<form method="post"><table>';
    echo '<tr>';
    echo '<td><label>'.get_string('filename', 'repository').'</label></td>';
    echo '<td><input type="text" name="title" value="'.$title.'" /></td>';
    echo '<td><input type="hidden" name="file" value="'.$file.'" /></td>';
    echo '<td><input type="hidden" name="action" value="download" /></td>';
    echo '<td><input type="hidden" name="itemid" value="'.$itemid.'" /></td>';
    echo '</tr>';
    echo '</table>';
    echo '<div>';
    echo '<input type="submit" value="'.get_string('download', 'repository').'" />';
    echo '</div>';
    echo '</form>';
    print_footer('empty');
    break;
case 'plugins':
    $user_context = get_context_instance(CONTEXT_USER, $USER->id);
    $repos = repository::get_instances(array($user_context, get_system_context()), null, true, null, '*', 'ref_id');
    print_header();
    echo '<div><ul>';
    foreach($repos as $repo) {
        $info = $repo->get_meta();
        echo '<li><img src="'.$info->icon.'" width="16px" height="16px"/> <a href="'.$url.'&action=list&repo_id='.$info->id.'">'.$info->name.'</a></li>';
    }
    echo '</ul></div>';
    break;
default:
    $user_context = get_context_instance(CONTEXT_USER, $USER->id);
    $repos = repository::get_instances(array($user_context, get_system_context()), null, true, null, '*', 'ref_id');
    print_header();
    $fs = get_file_storage();
    $context = get_context_instance(CONTEXT_USER, $USER->id);
    $files = $fs->get_area_files($context->id, 'user_draft', $itemid);
    if (empty($files)) {
        echo get_string('nofilesattached', 'repository');
    } else {
        echo '<ul>';
        foreach ($files as $file) {
            if ($file->get_filename()!='.') {
                $drafturl = $CFG->httpswwwroot.'/draftfile.php/'.$context->id.'/user_draft/'.$itemid.'/'.$file->get_filename();
                echo '<li><a href="'.$drafturl.'">'.$file->get_filename().'</a> ';
                echo '<a href="'.$CFG->httpswwwroot.'/repository/filepicker.php?action=deletedraft&itemid='.$itemid.'&ctx_id='.$ctx_id.'&title='.$file->get_filename().'"><img src="'.$CFG->httpswwwroot.'/pix/t/delete.gif" class="iconsmall" /></a></li>';
            }
        }
        echo '</ul>';
    }
    echo '<div><a href="'.$url.'&action=plugins">'.get_string('add', 'repository').'</a></div>';
    print_footer('empty');
    break;
}
