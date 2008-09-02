<?php
set_time_limit(0);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once('../config.php');
require_once('../lib/filelib.php');
require_once('lib.php');
// set one hour here
$CFG->repository_cache_expire = 60*60;
// page or path
$p     = optional_param('p', '', PARAM_INT);
// opened in editor or moodleform
$env   = optional_param('env', 'form', PARAM_ALPHA);
// file to download
// TODO: which type should be?
$file  = optional_param('file', '', PARAM_RAW);
// rename the file name
$title = optional_param('title', '', PARAM_FILE);
$action = optional_param('action', '', PARAM_ALPHA);
$search = optional_param('s', '', PARAM_CLEANHTML);
// id of repository
$repo_id = optional_param('repo_id', 1, PARAM_INT);
// TODO
// what will happen if user use a fake ctx_id?
// Think about using $SESSION save it
$ctx_id  = optional_param('ctx_id', SITEID, PARAM_INT);
$userid  = $USER->id;

$sql = 'SELECT i.name, i.typeid, r.type FROM {repository} r, {repository_instances} i WHERE i.id='.$repo_id.' AND i.typeid=r.id';
if(!$repository = $DB->get_record_sql($sql)) {
    $err = new stdclass;
    $err->e = get_string('invalidrepositoryid', 'repository');
    die(json_encode($err));
} else {
    $type = $repository->type;
}

if(file_exists($CFG->dirroot.'/repository/'.
    $type.'/repository.class.php'))
{
    require_once($CFG->dirroot.'/repository/'.
        $type.'/repository.class.php');
    $classname = 'repository_' . $type;
    try{
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

if ($action == 'list' || $action == 'search') {
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
    $itemid = (int)substr(hexdec(uniqid()), 0, 9)+rand(1,100);
    try {
        $info = repository_move_to_filepool($path, $title, $itemid);
        if($env == 'form'){
            echo json_encode($info);
        } elseif($env == 'editor') {
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
} elseif ($action == 'login') {
    try {
        echo json_encode($repo->print_login());
    } catch (repository_exception $e){
        $err = new stdclass;
        $err->e = $e->getMessage();
        die(json_encode($err));
    }
} elseif ($action == 'upload') {
    try {
        echo json_encode($repo->get_listing());
    } catch (repository_exception $e){
        $err = new stdclass;
        $err->e = $e->getMessage();
        die(json_encode($err));
    }
}
