<?php
require_once('../config.php');
require_once('lib.php');
$id        = optional_param('id', PARAM_INT);
$action    = optional_param('action', '', PARAM_RAW);
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
    echo json_encode($repo->get_listing());
} else {
    echo json_encode($repo->print_login());
}

?>
