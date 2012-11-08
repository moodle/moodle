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
 * @package    repository
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib/filelib.php');
require_once(dirname(__FILE__).'/lib.php');

$err = new stdClass();

// Parameters
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
$accepted_types  = optional_param_array('accepted_types', '*', PARAM_RAW);
$saveas_filename = optional_param('title', '', PARAM_FILE);     // save as file name
$areamaxbytes  = optional_param('areamaxbytes', FILE_AREA_MAX_BYTES_UNLIMITED, PARAM_INT); // Area max bytes.
$saveas_path   = optional_param('savepath', '/', PARAM_PATH);   // save as file path
$search_text   = optional_param('s', '', PARAM_CLEANHTML);
$linkexternal  = optional_param('linkexternal', '', PARAM_ALPHA);
$usefilereference  = optional_param('usefilereference', false, PARAM_BOOL);

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);
$PAGE->set_context($context);

echo $OUTPUT->header(); // send headers
@header('Content-type: text/html; charset=utf-8');

// If uploaded file is larger than post_max_size (php.ini) setting, $_POST content will be empty.
if (empty($_POST) && !empty($action)) {
    $err->error = get_string('errorpostmaxsize', 'repository');
    die(json_encode($err));
}

if (!confirm_sesskey()) {
    $err->error = get_string('invalidsesskey', 'error');
    die(json_encode($err));
}

// Get repository instance information
$repooptions = array(
    'ajax' => true,
    'mimetypes' => $accepted_types
);
$repo = repository::get_repository_by_id($repo_id, $contextid, $repooptions);

// Check permissions
$repo->check_capability();

$coursemaxbytes = 0;
if (!empty($course)) {
    $coursemaxbytes = $course->maxbytes;
}
// Make sure maxbytes passed is within site filesize limits.
$maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes, $coursemaxbytes, $maxbytes);

// Wait as long as it takes for this script to finish
set_time_limit(0);

// Early actions which need to be done before repository instances initialised
switch ($action) {
    // global search
    case 'gsearch':
        $params = array();
        $params['context'] = array(context::instance_by_id($contextid), get_system_context());
        $params['currentcontext'] = context::instance_by_id($contextid);
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

// These actions all occur on the currently active repository instance
switch ($action) {
    case 'sign':
    case 'signin':
    case 'list':
        if ($repo->check_login()) {
            $listing = repository::prepare_listing($repo->get_listing($req_path, $page));
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
        $search_form['repo_id'] = $repo_id;
        $search_form['form'] = $repo->print_search();
        $search_form['allowcaching'] = true;
        echo json_encode($search_form);
        break;
    case 'search':
        $search_result = repository::prepare_listing($repo->search($search_text, (int)$page));
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
                throw new moodle_exception('invalidfiletype', 'repository', '', get_mimetype_description(array('filename' => $saveas_filename)));
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

        $reference = $repo->get_file_reference($source);

        // Use link of the files
        if ($allowexternallink and $linkexternal === 'yes' and ($repo->supported_returntypes() & FILE_EXTERNAL)) {
            // use external link
            $link = $repo->get_link($reference);
            $info = array();
            $info['file'] = $saveas_filename;
            $info['type'] = 'link';
            $info['url'] = $link;
            echo json_encode($info);
            die;
        } else {
            $fs = get_file_storage();

            // Prepare file record.
            $record = new stdClass();
            $record->filepath = $saveas_path;
            $record->filename = $saveas_filename;
            $record->component = 'user';
            $record->filearea = 'draft';
            $record->itemid = $itemid;
            $record->license = $license;
            $record->author = $author;

            if ($record->filepath !== '/') {
                $record->filepath = trim($record->filepath, '/');
                $record->filepath = '/'.$record->filepath.'/';
            }
            $usercontext = context_user::instance($USER->id);
            $now = time();
            $record->contextid = $usercontext->id;
            $record->timecreated = $now;
            $record->timemodified = $now;
            $record->userid = $USER->id;
            $record->sortorder = 0;

            // Check that user has permission to access this file
            if (!$repo->file_is_accessible($source)) {
                throw new file_exception('storedfilecannotread');
            }

            // {@link repository::build_source_field()}
            $sourcefield = $repo->get_file_source_info($source);
            $record->source = $repo::build_source_field($sourcefield);

            // If file is already a reference, set $source = file source, $repo = file repository
            // note that in this case user may not have permission to access the source file directly
            // so no file_browser/file_info can be used below
            if ($repo->has_moodle_files()) {
                $file = repository::get_moodle_file($source);
                if ($file && $file->is_external_file()) {
                    $sourcefield = $file->get_source(); // remember the original source
                    $record->source = $repo::build_source_field($sourcefield);
                    $record->contenthash = $file->get_contenthash();
                    $record->filesize = $file->get_filesize();
                    $reference = $file->get_reference();
                    $repo_id = $file->get_repository_id();
                    $repo = repository::get_repository_by_id($repo_id, $contextid, $repooptions);
                }
            }

            if ($usefilereference) {
                if ($repo->has_moodle_files()) {
                    $sourcefile = repository::get_moodle_file($reference);
                    $record->contenthash = $sourcefile->get_contenthash();
                    $record->filesize = $sourcefile->get_filesize();
                }
                // Check if file exists.
                if (repository::draftfile_exists($itemid, $saveas_path, $saveas_filename)) {
                    // File name being used, rename it.
                    $unused_filename = repository::get_unused_filename($itemid, $saveas_path, $saveas_filename);
                    $record->filename = $unused_filename;
                    // Create a file copy using unused filename.
                    $storedfile = $fs->create_file_from_reference($record, $repo_id, $reference);

                    $event = array();
                    $event['event'] = 'fileexists';
                    $event['newfile'] = new stdClass;
                    $event['newfile']->filepath = $saveas_path;
                    $event['newfile']->filename = $unused_filename;
                    $event['newfile']->url = moodle_url::make_draftfile_url($itemid, $saveas_path, $unused_filename)->out();

                    $event['existingfile'] = new stdClass;
                    $event['existingfile']->filepath = $saveas_path;
                    $event['existingfile']->filename = $saveas_filename;
                    $event['existingfile']->url      = moodle_url::make_draftfile_url($itemid, $saveas_path, $saveas_filename)->out();;
                } else {

                    $storedfile = $fs->create_file_from_reference($record, $repo_id, $reference);
                    $event = array(
                        'url'=>moodle_url::make_draftfile_url($storedfile->get_itemid(), $storedfile->get_filepath(), $storedfile->get_filename())->out(),
                        'id'=>$storedfile->get_itemid(),
                        'file'=>$storedfile->get_filename(),
                        'icon' => $OUTPUT->pix_url(file_file_icon($storedfile, 32))->out(),
                    );
                }
                // Repository plugin callback
                // You can cache reository file in this callback
                // or complete other tasks.
                $repo->cache_file_by_reference($reference, $storedfile);
                echo json_encode($event);
                die;
            } else if ($repo->has_moodle_files()) {
                // Some repository plugins (local, user, coursefiles, recent) are hosting moodle
                // internal files, we cannot use get_file method, so we use copy_to_area method

                // If the moodle file is an alias we copy this alias, otherwise we copy the file
                // {@link repository::copy_to_area()}.
                $fileinfo = $repo->copy_to_area($reference, $record, $maxbytes, $areamaxbytes);

                echo json_encode($fileinfo);
                die;
            } else {
                // Download file to moodle.
                $downloadedfile = $repo->get_file($reference, $saveas_filename);
                if (empty($downloadedfile['path'])) {
                    $err->error = get_string('cannotdownload', 'repository');
                    die(json_encode($err));
                }

                // Check if we exceed the max bytes of the area.
                if (file_is_draft_area_limit_reached($itemid, $areamaxbytes, filesize($downloadedfile['path']))) {
                    throw new file_exception('maxareabytes');
                }

                // Check if exceed maxbytes.
                if ($maxbytes != -1 && filesize($downloadedfile['path']) > $maxbytes) {
                    throw new file_exception('maxbytes');
                }

                $info = repository::move_to_filepool($downloadedfile['path'], $record);
                if (empty($info)) {
                    $info['e'] = get_string('error', 'moodle');
                }
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

        $info = repository::overwrite_existing_draftfile($itemid, $filepath, $filename, $newfilepath, $newfilename);
        echo json_encode($info);
        break;

    case 'deletetmpfile':
        // delete tmp file
        $newfilepath = required_param('newfilepath', PARAM_PATH);
        $newfilename = required_param('newfilename', PARAM_FILE);
        echo json_encode(repository::delete_tempfile_from_draft($itemid, $newfilepath, $newfilename));

        break;
}
