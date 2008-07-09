<?php
require_once('../config.php');
require_once('lib.php');
$CFG->repository_cache_expire = 12000;
$id        = optional_param('id', PARAM_INT);
$action    = optional_param('action', '', PARAM_RAW);
$p         = optional_param('p', '', PARAM_RAW);
$search    = optional_param('search', '', PARAM_RAW);

if(!$repository = $DB->get_record('repository', array('id'=>$id))) {
    echo json_encode('wrong');
    die;
}

if(is_file($CFG->dirroot.'/repository/'.$repository->repositorytype.'/repository.class.php')) {
    require_once($CFG->dirroot.'/repository/'.$repository->repositorytype.'/repository.class.php');
    $classname = 'repository_' . $repository->repositorytype;
    $repo = new $classname($id, SITEID, array('ajax'=>true));
} else {
    print_error('invalidplugin', 'repository');
    echo json_encode('invalidplugin');
    die;
}

if($action == 'list') {
    if(!empty($p)) {
        echo json_encode($repo->get_listing($p));
    } else if(!empty($search)) {
        echo json_encode($repo->get_listing('', $search));
    } else {
        echo json_encode($repo->get_listing());
    }
    
} else {
    echo json_encode($repo->print_login());
}

?>
