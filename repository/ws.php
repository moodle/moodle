<?php  // $Id$

/// The Web service script that is called from the filepicker front end

    set_time_limit(0);
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    require_once('../config.php');
    require_once('../lib/filelib.php');
    require_once('lib.php');

/// Parameters
    $p     = optional_param('p', '', PARAM_INT);              // page or path
    $env   = optional_param('env', 'form', PARAM_ALPHA);      // opened in editor or moodleform
    $file  = optional_param('file', '', PARAM_RAW);           // file to download
    $title = optional_param('title', '', PARAM_FILE);         // new file name
    $action = optional_param('action', '', PARAM_ALPHA);
    $ctx_id  = optional_param('ctx_id', SITEID, PARAM_INT);   // context ID
    $repo_id = optional_param('repo_id', 1, PARAM_INT);       // repository ID
    $callback = optional_param('callback', '', PARAM_CLEANHTML);


/// Check permissions
    if (! (isloggedin() && repository_check_context($ctx_id)) ) {
        $err = new stdclass;
        $err->e = get_string('nopermissiontoaccess', 'repository');
        die(json_encode($err));
    }

/// Check for actions that do not need repository ID
    switch ($action) {

        case 'gsearch':     //  Global Search
            $repos = repository_get_instances(array(get_context_instance_by_id($ctx_id), get_system_context()));
            $list = array();
            foreach($repos as $repo){
                if ($repo->global_search()) {
                    try {
                        $ret = $repo->get_listing(null, $search);
                        array_walk($ret['list'], 'repository_attach_id', $repo->id);  // See function below
                        $tmp = array_merge($list, $ret['list']);
                        $list = $tmp;
                    } catch (repository_exception $e) {
                        $err = new stdclass;
                        $err->e = $e->getMessage();
                        die(json_encode($err));
                    }
                }
            }
            die(json_encode(array('list'=>$list)));
            break;

        case 'ccache':      // Clean cache
            $cache = new curl_cache;
            $cache->refresh();
            $action = 'list';
            break;
    }

/// Get repository instance information
    $sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i '.
           'WHERE i.id='.$repo_id.' AND i.typeid=r.id';
    if (!$repository = $DB->get_record_sql($sql)) {
        $err = new stdclass;
        $err->e = get_string('invalidrepositoryid', 'repository');
        die(json_encode($err));
    } else {
        $type = $repository->type;
    }

    if (file_exists($CFG->dirroot.'/repository/'.$type.'/repository.class.php')) {
        require_once($CFG->dirroot.'/repository/'.$type.'/repository.class.php');
        $classname = 'repository_' . $type;
        try {
            $repo = new $classname($repo_id, $ctx_id, array('ajax'=>true, 'name'=>$repository->name));
        } catch (repository_exception $e){
            $err = new stdclass;
            $err->e = $e->getMessage();
            die(json_encode($err));
        }
    } else {
        $err = new stdclass;
        $err->e = get_string('invalidplugin', 'repository');
        die(json_encode($err));
    }

    if (!empty($callback)) {
        // call opener window to refresh repository
        // the callback url should be something like this:
        // http://xx.moodle.com/repository/ws.php?callback=yes&repo_id=1&sid=xxx
        // sid is the attached auth token from external source
        $js  =<<<EOD
<html><head><script type="text/javascript">
    window.opener.repository_callback($repo_id);
    window.close();
</script><body></body></html>
EOD;
        echo $js;
        die;
    }


/// These actions all occur on the currently active repository instance
    switch ($action) {
        case 'searchform':
            $repo->print_search();
            break;
        case 'login':
            try {
                echo json_encode($repo->print_login());
            } catch (repository_exception $e){
                $err = new stdclass;
                $err->e = $e->getMessage();
                die(json_encode($err));
            }
            break;
        case 'search':
            try {
                echo json_encode($repo->search());
            } catch (repository_exception $e) {
                $err = new stdclass;
                $err->e = $e->getMessage();
                die(json_encode($err));
            }
            break;
        case 'list':
            try {
                if (!empty($p)) {
                    echo json_encode($repo->get_listing($p));
                } else {
                    echo json_encode($repo->get_listing());
                }
            } catch (repository_exception $e) {
                $err = new stdclass;
                $err->e = $e->getMessage();
                die(json_encode($err));
            }
            break;
        case 'download':
            $path = $repo->get_file($file, $title);
            $itemid = (int)substr(hexdec(uniqid()), 0, 9)+rand(1,100);
            try {
                $info = repository_move_to_filepool($path, $title, $itemid);
                if ($env == 'form'){
                    echo json_encode($info);
                } else if ($env == 'editor') {
                    echo json_encode($info);
                } else {
                }
            } catch (repository_exception $e){
                $err = new stdclass;
                $err->e = $e->getMessage();
                die(json_encode($err));
            } catch (Exception $e) {
                $err = new stdclass;
                $err->e = $e->getMessage();
                die(json_encode($err));
            }
            break;
        case 'upload':
            try {
                echo json_encode($repo->get_listing());
            } catch (repository_exception $e){
                $err = new stdclass;
                $err->e = $e->getMessage();
                die(json_encode($err));
            }
            break;
    }

/**
 * Small function to walk an array to attach repository ID
 */
function repository_attach_id(&$value, $key, $id){
    $value['repo_id'] = $id;
}
