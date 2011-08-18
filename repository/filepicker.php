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
 * @package    core
 * @subpackage repository
 * @copyright  2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once('lib.php');
/// Wait as long as it takes for this script to finish
set_time_limit(0);

require_sesskey();
require_login();

// disable blocks in this page
$PAGE->set_pagelayout('embedded');

// general parameters
$action      = optional_param('action', '',        PARAM_ALPHA);
$client_id   = optional_param('client_id', '', PARAM_RAW);    // client ID
$itemid      = optional_param('itemid', '',        PARAM_INT);

// parameters for repository
$callback    = optional_param('callback', '',      PARAM_CLEANHTML);
$contextid   = optional_param('ctx_id',    SYSCONTEXTID, PARAM_INT);    // context ID
$courseid    = optional_param('course',    SITEID, PARAM_INT);    // course ID
$env         = optional_param('env', 'filepicker', PARAM_ALPHA);  // opened in file picker, file manager or html editor
$filename    = optional_param('filename', '',      PARAM_FILE);
$fileurl     = optional_param('fileurl', '',       PARAM_RAW);
$thumbnail   = optional_param('thumbnail', '',     PARAM_RAW);
$targetpath  = optional_param('targetpath', '',    PARAM_PATH);
$repo_id     = optional_param('repo_id', 0,        PARAM_INT);    // repository ID
$req_path    = optional_param('p', '',             PARAM_RAW);    // the path in repository
$curr_page   = optional_param('page', '',          PARAM_RAW);    // What page in repository?
$search_text = optional_param('s', '',             PARAM_CLEANHTML);
$maxfiles    = optional_param('maxfiles', -1,      PARAM_INT);    // maxfiles
$maxbytes    = optional_param('maxbytes',  0,      PARAM_INT);    // maxbytes
$subdirs     = optional_param('subdirs',  0,       PARAM_INT);    // maxbytes

// the path to save files
$savepath = optional_param('savepath', '/',    PARAM_PATH);
// path in draft area
$draftpath = optional_param('draftpath', '/',    PARAM_PATH);


// user context
$user_context = get_context_instance(CONTEXT_USER, $USER->id);

$PAGE->set_context($user_context);
if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    print_error('invalidcourseid');
}
$PAGE->set_course($course);

// init repository plugin
$sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i '.
       'WHERE i.id=? AND i.typeid=r.id';
if ($repository = $DB->get_record_sql($sql, array($repo_id))) {
    $type = $repository->type;
    if (file_exists($CFG->dirroot.'/repository/'.$type.'/lib.php')) {
        require_once($CFG->dirroot.'/repository/'.$type.'/lib.php');
        $classname = 'repository_' . $type;
        try {
            $repo = new $classname($repo_id, $contextid, array('ajax'=>false, 'name'=>$repository->name, 'type'=>$type));
        } catch (repository_exception $e){
            print_error('pluginerror', 'repository');
        }
    } else {
        print_error('invalidplugin', 'repository');
    }
}

$moodle_maxbytes = get_max_upload_file_size();
// to prevent maxbytes greater than moodle maxbytes setting
if ($maxbytes == 0 || $maxbytes>=$moodle_maxbytes) {
    $maxbytes = $moodle_maxbytes;
}

$params = array('ctx_id' => $contextid, 'itemid' => $itemid, 'env' => $env, 'course'=>$courseid, 'maxbytes'=>$maxbytes, 'maxfiles'=>$maxfiles, 'subdirs'=>$subdirs, 'sesskey'=>sesskey());
$params['action'] = 'browse';
$params['draftpath'] = $draftpath;
$home_url = new moodle_url('/repository/draftfiles_manager.php', $params);

$params['savepath'] = $savepath;
$params['repo_id'] = $repo_id;
$url = new moodle_url($CFG->httpswwwroot."/repository/filepicker.php", $params);
$PAGE->set_url('/repository/filepicker.php', $params);

switch ($action) {
case 'upload':
    // The uploaded file has been processed in plugin construct function
    // redirect to default page
    try {
        $repo->upload('', $maxbytes);
        redirect($home_url, get_string('uploadsucc','repository'));
    } catch (moodle_exception $e) {
        // inject target URL
        $e->link = $PAGE->url->out();
        echo $OUTPUT->header(); // hack: we need the embedded header here, standard error printing would not use it
        throw $e;
    }
    break;

case 'search':
    echo '<div><a href="' . $home_url->out() . '">'.get_string('back', 'repository')."</a></div>";
    try {
        $search_result = $repo->search($search_text);
        $search_result['issearchresult'] = true;
        $search_result['repo_id'] = $repo_id;

        // TODO: need a better solution
        $purl = new moodle_url($url, array('search_paging' => 1, 'action' => 'search', 'repo_id' => $repo_id));
        $pagingbar = new paging_bar($search_result['total'], $search_result['page'] - 1, $search_result['perpage'], $purl, 'p');
        echo $OUTPUT->render($pagingbar);

        echo '<table>';
        foreach ($search_result['list'] as $item) {
            echo '<tr>';
            echo '<td><img src="'.$item['thumbnail'].'" />';
            echo '</td><td>';
            if (!empty($item['url'])) {
                echo html_writer::link($item['url'], $item['title'], array('target'=>'_blank'));
            } else {
                echo $item['title'];
            }
            echo '</td>';
            echo '<td>';
            echo '<form method="post">';
            echo '<input type="hidden" name="fileurl" value="'.s($item['source']).'"/>';
            echo '<input type="hidden" name="action" value="confirm"/>';
            echo '<input type="hidden" name="filename" value="'.s($item['title']).'"/>';
            echo '<input type="hidden" name="thumbnail" value="'.s($item['thumbnail']).'"/>';
            echo '<input type="submit" value="'.s(get_string('select','repository')).'" />';
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

    echo $OUTPUT->container_start();
    echo html_writer::link($url, get_string('back', 'repository'));
    echo $OUTPUT->container_end();

    if ($repo->check_login()) {
        $list = $repo->get_listing($req_path, $curr_page);
        $dynload = !empty($list['dynload'])?true:false;
        if (!empty($list['upload'])) {
            echo '<form action="'.$url->out().'" method="post" enctype="multipart/form-data" style="display:inline">';
            echo '<label>'.$list['upload']['label'].': </label>';
            echo '<input type="file" name="repo_upload_file" /><br />';
            echo '<input type="hidden" name="action" value="upload" /><br />';
            echo '<input type="hidden" name="draftpath" value="'.s($draftpath).'" /><br />';
            echo '<input type="hidden" name="savepath" value="'.s($savepath).'" /><br />';
            echo '<input type="hidden" name="repo_id" value="'.s($repo_id).'" /><br />';
            echo '<input type="submit" value="'.s(get_string('upload', 'repository')).'" />';
            echo '</form>';
        } else {
            if (!empty($list['path'])) {
                foreach ($list['path'] as $p) {
                    //echo '<form method="post" style="display:inline">';
                    //echo '<input type="hidden" name="p" value="'.s($p['path']).'"';
                    //echo '<input type="hidden" name="action" value="list"';
                    //echo '<input type="hidden" name="draftpath" value="'.s($draftpath).'" /><br />';
                    //echo '<input type="hidden" name="savepath" value="'.s($savepath).'" /><br />';
                    //echo '<input style="display:inline" type="submit" value="'.s($p['name']).'" />';
                    //echo '</form>';

                    $pathurl = new moodle_url($url, array(
                        'p'=>$p['path'],
                        'action'=>'list',
                        'draftpath'=>$draftpath,
                        'savepath'=>$savepath
                        ));
                    echo '<strong>' . html_writer::link($pathurl, $p['name']) . '</strong>';
                    echo '<span> / </span>';
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
                    echo html_writer::link($item['url'], $item['title'], array('target'=>'_blank'));
                } else {
                    echo $item['title'];
                }
                echo '</td>';
                echo '<td>';
                if (!isset($item['children'])) {
                    echo '<form method="post">';
                    echo '<input type="hidden" name="fileurl" value="'.s($item['source']).'"/>';
                    echo '<input type="hidden" name="action" value="confirm"/>';
                    echo '<input type="hidden" name="draftpath" value="'.s($draftpath).'" /><br />';
                    echo '<input type="hidden" name="savepath" value="'.s($savepath).'" /><br />';
                    echo '<input type="hidden" name="filename" value="'.s($item['title']).'"/>';
                    echo '<input type="hidden" name="thumbnail" value="'.s($item['thumbnail']).'"/>';
                    echo '<input type="submit" value="'.s(get_string('select','repository')).'" />';
                    echo '</form>';
                } else {
                    echo '<form method="post">';
                    echo '<input type="hidden" name="p" value="'.s($item['path']).'"/>';
                    echo '<input type="submit" value="'.s(get_string('enter', 'repository')).'" />';
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
        echo '<input type="hidden" name="repo_id" value="'.s($repo_id).'" />';
        $repo->print_login();
        echo '</form>';
    }
    echo $OUTPUT->footer();
    break;

case 'download':
    $thefile = $repo->get_file($fileurl, $filename);
    $filesize = filesize($thefile['path']);
    if (($maxbytes!=-1) && ($filesize>$maxbytes)) {
        print_error('maxbytes');
    }
    if (!empty($thefile)) {
        $record = new stdClass();
        $record->filepath = $savepath;
        $record->filename = $filename;
        $record->component = 'user';
        $record->filearea = 'draft';
        $record->itemid   = $itemid;
        $record->license  = '';
        $record->author   = '';
        $record->source   = $thefile['url'];
        try {
            $info = repository::move_to_filepool($thefile['path'], $record);
            redirect($home_url, get_string('downloadsucc', 'repository'));
        } catch (moodle_exception $e) {
            // inject target URL
            $e->link = $PAGE->url->out();
            echo $OUTPUT->header(); // hack: we need the embedded header here, standard error printing would not use it
            throw $e;
        }
    } else {
        print_error('cannotdownload', 'repository');
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
    echo '    <td><input type="text" name="filename" value="'.s($filename).'" /></td>';
    echo '    <td><input type="hidden" name="fileurl" value="'.s($fileurl).'" /></td>';
    echo '    <td><input type="hidden" name="action" value="download" /></td>';
    echo '    <td><input type="hidden" name="itemid" value="'.s($itemid).'" /></td>';
    echo '  </tr>';
    echo '</table>';
    echo '<div>';
    // the save path
    echo ' <input name="draftpath" type="hidden" value="'.s($draftpath).'" />';
    echo ' <input name="savepath" type="hidden" value="'.s($savepath).'" />';
    echo ' <input type="submit" value="'.s(get_string('download', 'repository')).'" />';
    echo '</div>';
    echo '</form>';
    echo $OUTPUT->footer();
    break;

default:
case 'plugins':
    $params = array();
    $params['context'] = array($user_context, get_system_context());
    $params['currentcontext'] = $PAGE->context;
    $params['return_types'] = FILE_INTERNAL;

    $repos = repository::get_instances($params);
    echo $OUTPUT->header();
    echo html_writer::link($home_url->out(false), get_string('backtodraftfiles', 'repository'));
    echo '<div>';
    echo '<ul>';
    foreach($repos as $repo) {
        $info = $repo->get_meta();

        $aurl = clone($url);
        $aurl->params(array('savepath'=>$savepath, 'action' => 'list', 'repo_id' => $info->id, 'draftpath'=>$draftpath));

        echo '<li>';
        echo '<img src="'.$info->icon.'" alt="'.$info->name.'" width="16" height="16" /> ';
        echo html_writer::link($aurl, $info->name);
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo $OUTPUT->footer();
    break;
}
