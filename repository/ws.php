<?php
set_time_limit(0);
require_once('../config.php');
require_once('../lib/filelib.php');
require_once('lib.php');
// set one hour here
$CFG->repository_cache_expire = 60*60;
// page
$p     = optional_param('p', '', PARAM_RAW);
// opened in editor or moodleform
$env   = optional_param('env', 'form', PARAM_RAW);
// file to download
$file  = optional_param('file', '', PARAM_RAW);
// rename the file name
$title = optional_param('title', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$search = optional_param('s', '', PARAM_RAW);
// id of repository
$repo_id = optional_param('repo_id', 1, PARAM_INT);
$itemid  = optional_param('itemid',  0, PARAM_INT);

if(!$repository = $DB->get_record('repository', array('id'=>$repo_id))) {
    $err = new stdclass;
    $err->e = get_string('invalidrepositoryid', 'repository');
    die(json_encode($err));
}

if(file_exists($CFG->dirroot.'/repository/'.
    $repository->repositorytype.'/repository.class.php'))
{
    require_once($CFG->dirroot.'/repository/'.
        $repository->repositorytype.'/repository.class.php');
    $classname = 'repository_' . $repository->repositorytype;
    try{
        $repo = new $classname($repo_id, SITEID, array('ajax'=>true));
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

if($action == 'list') {
    try {
        if(!empty($p)) {
            echo json_encode($repo->get_listing($p));
        } else if(!empty($search)) {
            echo json_encode($repo->get_listing('', $search));
        } else {
            echo json_encode($repo->get_listing());
        }
    } catch (repository_exception $e) {
        $err = new stdclass;
        $err->e = $e->getMessage();
        die(json_encode($err));
    }

} elseif($action == 'download') {
    $path = $repo->get_file($file, $title);
    try {
        $info = move_to_filepool($path, $title, $itemid);
        if($env == 'form'){
            echo json_encode($info['id']);
        } elseif($env == 'editor') {
            echo json_encode($info['url']);
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
} else {
    try {
        echo json_encode($repo->print_login());
    } catch (repository_exception $e){
        $err = new stdclass;
        $err->e = $e->getMessage();
        die(json_encode($err));
    }
}

?>
