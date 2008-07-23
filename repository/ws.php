<?php
set_time_limit(0);
require_once('../config.php');
require_once('lib.php');
// set one hour here
$CFG->repository_cache_expire = 60*60;
// repository id
$id     = optional_param('id', 1, PARAM_INT);
// action of client
$action = optional_param('action', '', PARAM_RAW);
// Search text
$search = optional_param('s', '', PARAM_RAW);
// files to be downloaded
$file  = optional_param('file', '', PARAM_RAW);
$title = optional_param('title', '', PARAM_RAW);
$p     = optional_param('p', '', PARAM_RAW);

if(!$repository = $DB->get_record('repository', array('id'=>$id))) {
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
        $repo = new $classname($id, SITEID, array('ajax'=>true));
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
    $ret = $repo->get_file($file, $title);
    // TODO
    // Need to communicate with FILE API
    // Copy the tmp file to final location
    try {
        echo json_encode($ret);
    } catch (repository_exception $e){
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
