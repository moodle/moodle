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
 * @package moodlecore
 * @subpackage repository
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/lib/filelib.php');
require_once(dirname(__FILE__).'/lib.php');

require_login();

/// Parameters
$action    = optional_param('action', '', PARAM_ALPHA);
$repo_id   = optional_param('repo_id', 0, PARAM_INT);           // repository ID
$callback  = optional_param('callback', '', PARAM_CLEANHTML);
$client_id = optional_param('client_id', '', PARAM_RAW);        // client ID
$contextid = optional_param('ctx_id', SYSCONTEXTID, PARAM_INT);       // context ID
$env       = optional_param('env', 'filepicker', PARAM_ALPHA);  // opened in editor or moodleform
$license   = optional_param('license', $CFG->sitedefaultlicense, PARAM_TEXT);
$author    = optional_param('author', '', PARAM_TEXT);
$source    = optional_param('source', '', PARAM_RAW);           // file to download
$itemid    = optional_param('itemid', 0, PARAM_INT);
$page      = optional_param('page', '', PARAM_RAW);             // page
$maxbytes  = optional_param('maxbytes', -1, PARAM_INT);
$req_path  = optional_param('p', '', PARAM_RAW);                // path
$saveas_filearea = optional_param('filearea', 'user_draft', PARAM_TEXT);
$saveas_filename = optional_param('title', '', PARAM_FILE);           // new file name
$saveas_path   = optional_param('saveaspath', '/', PARAM_PATH);
$search_text   = optional_param('s', '', PARAM_CLEANHTML);
$linkexternal  = optional_param('linkexternal', '', PARAM_ALPHA);

/// Headers to make it not cacheable
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

$err = new stdclass;
$err->client_id = $client_id;

/// Check permissions
if (! (isloggedin() && repository::check_context($contextid)) ) {
    $err->e = get_string('nopermissiontoaccess', 'repository');
    die(json_encode($err));
}

/// Wait as long as it takes for this script to finish
set_time_limit(0);

// Early actions which need to be done before repository instaces initialised
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
                try {
                    $ret = $repo->search($search_text);
                    array_walk($ret['list'], 'repository_attach_id', $repo->id);  // See function below
                    $tmp = array_merge($list, $ret['list']);
                    $list = $tmp;
                } catch (repository_exception $e) {
                    $err->e = $e->getMessage();
                    die(json_encode($err));
                }
            }
        }
        $listing = array('list'=>$list);
        $listing['gsearch'] = true;
        $listing['client_id'] = $client_id;
        die(json_encode($listing));
        break;

    // remove the cache files & logout
    case 'ccache':
        $cache = new curl_cache;
        $cache->refresh();
        $action = 'list';
        break;
}

/// Get repository instance information
$sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i '.
       'WHERE i.id=? AND i.typeid=r.id';

if (!$repository = $DB->get_record_sql($sql, array($repo_id))) {
    $err->e = get_string('invalidrepositoryid', 'repository');
    die(json_encode($err));
} else {
    $type = $repository->type;
}

if (file_exists($CFG->dirroot.'/repository/'.$type.'/repository.class.php')) {
    require_once($CFG->dirroot.'/repository/'.$type.'/repository.class.php');
    $classname = 'repository_' . $type;
    try {
        $repo = new $classname($repo_id, $contextid, array('ajax'=>true, 'name'=>$repository->name, 'type'=>$type, 'client_id'=>$client_id));
    } catch (repository_exception $e){
        $err->e = $e->getMessage();
        die(json_encode($err));
    }
} else {
    $err->e = get_string('invalidplugin', 'repository', $type);
    die(json_encode($err));
}


if (!empty($callback)) {
    // call opener window to refresh repository
    // the callback url should be something like this:
    // http://xx.moodle.com/repository/repository_ajax.php?callback=yes&repo_id=1&sid=xxx
    // sid is the attached auth token from external source
    // If Moodle is working on HTTPS mode, then we are not allowed to access
    // parent window, in this case, we need to alert user to refresh the repository
    // manually.
    $strhttpsbug = get_string('cannotaccessparentwin', 'repository');
    $strrefreshnonjs = get_string('refreshnonjsfilepicker', 'repository');
    $js =<<<EOD
<html>
<head>
    <script type="text/javascript">
    if(window.opener){
        window.opener.M.core_filepicker.active_filepicker.list();
        window.close();
    } else {
        alert("{$strhttpsbug }");
    }
    </script>
</head>
<body>
    <noscript>
    {$strrefreshnonjs}
    </noscript>
</body>
</html>
EOD;
    die($js);
}

/// These actions all occur on the currently active repository instance
switch ($action) {
    case 'sign':
    case 'signin':
    case 'list':
        if ($repo->check_login()) {
            try {
                $listing = $repo->get_listing($req_path, $page);
                $listing['client_id'] = $client_id;
                $listing['repo_id'] = $repo_id;
                echo json_encode($listing);
            } catch (repository_exception $e) {
                $err->e = $e->getMessage();
                die(json_encode($err));
            }
            break;
        } else {
            $action = 'login';
        }
    case 'login':
        try {
            $listing = $repo->print_login();
            $listing['client_id'] = $client_id;
            $listing['repo_id'] = $repo_id;
            echo json_encode($listing);
        } catch (repository_exception $e){
            $err->e = $e->getMessage();
            die(json_encode($err));
        }
        break;
    case 'logout':
        $logout = $repo->logout();
        $logout['client_id'] = $client_id;
        $logout['repo_id'] = $repo_id;
        echo json_encode($logout);
        break;
    case 'searchform':
        $search_form['form'] = $repo->print_search($client_id);
        $search_form['client_id'] = $client_id;
        echo json_encode($search_form);
        break;
    case 'search':
        try {
            $search_result = $repo->search($search_text, (int)$page);
            $search_result['client_id'] = $client_id;
            $search_result['repo_id'] = $repo_id;
            $search_result['search_result'] = true;
            echo json_encode($search_result);
        } catch (repository_exception $e) {
            $err->e = $e->getMessage();
            die(json_encode($err));
        }
        break;
    case 'download':
        try {
            // we have two special repoisitory type need to deal with
            if ($repo->options['type'] == 'local' || $repo->options['type'] == 'recent' ) {
                $fileinfo = repository::move_to_draft($source, $saveas_filename, $itemid, $saveas_path);
                $info = array();
                $info['client_id'] = $client_id;
                $info['file'] = $fileinfo['title'];
                $info['id'] = $itemid;
                $info['url'] = $CFG->httpswwwroot.'/draftfile.php/'.$fileinfo['contextid'].'/user_draft/'.$itemid.'/'.$fileinfo['title'];
                $filesize = $fileinfo['filesize'];
                if (($maxbytes!==-1) && ($filesize > $maxbytes)) {
                    $fileinfo->delete();
                    throw new file_exception('maxbytes');
                }
                die(json_encode($info));
            }

            $allowexternallink = (int)get_config(null, 'repositoryallowexternallinks');
            if (!empty($allowexternallink)) {
                $allowexternallink = true;
            } else {
                $allowexternallink = false;
            }
            // allow external links in url element all the time
            $allowexternallink = ($allowexternallink || ($env == 'url'));

            if ($allowexternallink and $linkexternal === 'yes' and ($repo->supported_returntypes() || FILE_EXTERNAL)) {
                try {
                    $link = $repo->get_link($source);
                } catch (repository_exception $e){
                }
                $info = array();
                $info['filename'] = $saveas_filename;
                $info['type'] = 'link';
                $info['url'] = $link;
                die(json_encode($info));
            }

            // get the file location
            $file = $repo->get_file($source, $saveas_filename);
            if ($file['path'] === false) {
                $err->e = get_string('cannotdownload', 'repository');
                die(json_encode($err));
            }
            if (($maxbytes!==-1) && (filesize($file['path']) > $maxbytes)) {
                throw new file_exception('maxbytes');
            }

            $record = new stdclass;
            $record->filepath = $saveas_path;
            $record->filename = $saveas_filename;
            $record->filearea = $saveas_filearea;
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
        } catch (repository_exception $e){
            $err->e = $e->getMessage();
            die(json_encode($err));
        } catch (Exception $e) {
            $err->e = $e->getMessage();
            die(json_encode($err));
        }
        break;
    case 'upload':
        try {
            $result = $repo->upload();
            $result['client_id'] = $client_id;
            echo json_encode($result);
        } catch (Exception $e){
            $err->e = $e->getMessage();
            $err->client_id = $client_id;
            die(json_encode($err));
        }
        break;
}

/**
 * Small function to walk an array to attach repository ID
 * @param array $value
 * @param string $key
 * @param int $id
 */
function repository_attach_id(&$value, $key, $id){
    $value['repo_id'] = $id;
}
