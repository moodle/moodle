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
 * The Web service script that is called from the filepicker front end
 *
 * @since 2.0
 * @package    core
 * @subpackage repository
 * @copyright  2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib/filelib.php');
require_once(dirname(__FILE__).'/lib.php');

$err = new stdClass();

/// Parameters
$action    = optional_param('action', '', PARAM_ALPHA);
$repo_id   = optional_param('repo_id', 0, PARAM_INT);           // Repository ID
$contextid = optional_param('ctx_id', SYSCONTEXTID, PARAM_INT); // Context ID
$env       = optional_param('env', 'filepicker', PARAM_ALPHA);  // Opened in editor or moodleform
$license   = optional_param('license', $CFG->sitedefaultlicense, PARAM_TEXT);
$author    = optional_param('author', '', PARAM_TEXT);          // File author
$source    = optional_param('source', '', PARAM_RAW);           // File to download
$itemid    = optional_param('itemid', 0, PARAM_INT);            // Itemid
$page      = optional_param('page', '', PARAM_RAW);             // Page
$maxbytes  = optional_param('maxbytes', 0, PARAM_INT);          // Maxbytes
$req_path  = optional_param('p', '', PARAM_RAW);                // Path
$accepted_types  = optional_param('accepted_types', '*', PARAM_RAW);
$saveas_filename = optional_param('title', '', PARAM_FILE);     // save as file name
$saveas_path   = optional_param('savepath', '/', PARAM_PATH);   // save as file path
$search_text   = optional_param('s', '', PARAM_CLEANHTML);
$linkexternal  = optional_param('linkexternal', '', PARAM_ALPHA);

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);
$PAGE->set_context($context);

echo $OUTPUT->header(); // send headers
@header('Content-type: text/html; charset=utf-8');

// if uploaded file is larger than post_max_size (php.ini) setting, $_POST content will lost
if (empty($_POST) && !empty($action)) {
    $err->error = get_string('errorpostmaxsize', 'repository');
    die(json_encode($err));
}

if (!confirm_sesskey()) {
    $err->error = get_string('invalidsesskey');
    die(json_encode($err));
}

/// Get repository instance information
$sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i WHERE i.id=? AND i.typeid=r.id';

if (!$repository = $DB->get_record_sql($sql, array($repo_id))) {
    $err->error = get_string('invalidrepositoryid', 'repository');
    die(json_encode($err));
} else {
    $type = $repository->type;
}

/// Check permissions
repository::check_capability($contextid, $repository);

$moodle_maxbytes = get_max_upload_file_size();
// to prevent maxbytes greater than moodle maxbytes setting
if ($maxbytes == 0 || $maxbytes>=$moodle_maxbytes) {
    $maxbytes = $moodle_maxbytes;
}

/// Wait as long as it takes for this script to finish
set_time_limit(0);

// Early actions which need to be done before repository instances initialised
switch ($action) {
    // global search
    case 'gsearch':
        $params = array();
        $params['context'] = array(get_context_instance_by_id($contextid), get_system_context());
        $params['currentcontext'] = get_context_instance_by_id($contextid);
        $repos = repository::get_instances($params);
        $list = array();
        foreach($repos as $repo){
            if ($repo->global_search()) {
                $ret = $repo->search($search_text);
                array_walk($ret['list'], 'repository_attach_id', $repo->id);  // See function below
                $tmp = array_merge($list, $ret['list']);
                $list = $tmp;
            }
        }
        $listing = array('list'=>$list);
        $listing['gsearch'] = true;
        die(json_encode($listing));
        break;

    // remove the cache files & logout
    case 'ccache':
        $cache = new curl_cache;
        $cache->refresh();
        $action = 'list';
        break;
}

if (file_exists($CFG->dirroot.'/repository/'.$type.'/lib.php')) {
    require_once($CFG->dirroot.'/repository/'.$type.'/lib.php');
    $classname = 'repository_' . $type;
    $repo = new $classname($repo_id, $contextid, array('ajax'=>true, 'name'=>$repository->name, 'type'=>$type));
} else {
    $err->error = get_string('invalidplugin', 'repository', $type);
    die(json_encode($err));
}

/// These actions all occur on the currently active repository instance
switch ($action) {
    case 'sign':
    case 'signin':
    case 'list':
        if ($repo->check_login()) {
            $listing = $repo->get_listing($req_path, $page);
            $listing['repo_id'] = $repo_id;
            echo json_encode($listing);
            break;
        } else {
            $action = 'login';
        }
    case 'login':
        $listing = $repo->print_login();
        $listing['repo_id'] = $repo_id;
        echo json_encode($listing);
        break;
    case 'logout':
        $logout = $repo->logout();
        $logout['repo_id'] = $repo_id;
        echo json_encode($logout);
        break;
    case 'searchform':
        $search_form['form'] = $repo->print_search();
        echo json_encode($search_form);
        break;
    case 'search':
        $search_result = $repo->search($search_text, (int)$page);
        $search_result['repo_id'] = $repo_id;
        $search_result['issearchresult'] = true;
        echo json_encode($search_result);
        break;
    case 'download':
        // validate mimetype
        $mimetypes = array();
        if ((is_array($accepted_types) and in_array('*', $accepted_types)) or $accepted_types == '*') {
            $mimetypes = '*';
        } else {
            foreach ($accepted_types as $type) {
                $mimetypes[] = mimeinfo('type', $type);
            }
            if (!in_array(mimeinfo('type', $saveas_filename), $mimetypes)) {
                throw new moodle_exception('invalidfiletype', 'repository', '', get_string(mimeinfo('type', $saveas_filename), 'mimetypes'));
            }
        }

        // We have two special repository type need to deal with
        // local and recent plugins don't added new files to moodle, just add new records to database
        // so we don't check user quota and maxbytes here
        $allowexternallink = (int)get_config(null, 'repositoryallowexternallinks');
        if (!empty($allowexternallink)) {
            $allowexternallink = true;
        } else {
            $allowexternallink = false;
        }
        // allow external links in url element all the time
        $allowexternallink = ($allowexternallink || ($env == 'url'));

        // Use link of the files
        if ($allowexternallink and $linkexternal === 'yes' and ($repo->supported_returntypes() & FILE_EXTERNAL)) {
            // use external link
            $link = $repo->get_link($source);
            $info = array();
            $info['filename'] = $saveas_filename;
            $info['type'] = 'link';
            $info['url'] = $link;
            echo json_encode($info);
            die;
        } else {
            // some repository plugins deal with moodle internal files, so we cannot use get_file
            // method, so we use copy_to_area method
            // (local, user, coursefiles, recent)
            if ($repo->has_moodle_files()) {
                // check filesize against max allowed size
                $filesize = $repo->get_file_size($source);
                if (empty($filesize)) {
                    $err->error = get_string('filesizenull', 'repository');
                    die(json_encode($err));
                }
                if (($maxbytes !== -1) && ($filesize > $maxbytes)) {
                    throw new file_exception('maxbytes');
                }
                $fileinfo = $repo->copy_to_area($source, $itemid, $saveas_path, $saveas_filename);
                echo json_encode($fileinfo);
                die;
            }
            // Download file to moodle
            $file = $repo->get_file($source, $saveas_filename);
            if ($file['path'] === false) {
                $err->error = get_string('cannotdownload', 'repository');
                die(json_encode($err));
            }

            // check if exceed maxbytes
            if (($maxbytes!==-1) && (filesize($file['path']) > $maxbytes)) {
                throw new file_exception('maxbytes');
            }

            $record = new stdClass();
            $record->filepath = $saveas_path;
            $record->filename = $saveas_filename;
            $record->component = 'user';
            $record->filearea = 'draft';
            $record->itemid   = $itemid;

            if (!empty($file['license'])) {
                $record->license  = $file['license'];
            } else {
                $record->license  = $license;
            }
            if (!empty($file['author'])) {
                $record->author   = $file['author'];
            } else {
                $record->author   = $author;
            }
            $record->source = !empty($file['url']) ? $file['url'] : '';

            $info = repository::move_to_filepool($file['path'], $record);
            if (empty($info)) {
                $info['e'] = get_string('error', 'moodle');
            }
            echo json_encode($info);
            die;
        }
        break;
    case 'upload':
        $result = $repo->upload($saveas_filename, $maxbytes);
        echo json_encode($result);
        break;

    case 'overwrite':
        // existing file
        $filepath    = required_param('existingfilepath', PARAM_PATH);
        $filename    = required_param('existingfilename', PARAM_FILE);
        // user added file which needs to replace the existing file
        $newfilepath = required_param('newfilepath', PARAM_PATH);
        $newfilename = required_param('newfilename', PARAM_FILE);

        echo json_encode(repository::overwrite_existing_draftfile($itemid, $filepath, $filename, $newfilepath, $newfilename));
        break;

    case 'deletetmpfile':
        // delete tmp file
        $newfilepath = required_param('newfilepath', PARAM_PATH);
        $newfilename = required_param('newfilename', PARAM_FILE);
        echo json_encode(repository::delete_tempfile_from_draft($itemid, $newfilepath, $newfilename));

        break;
}
