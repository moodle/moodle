<?php
set_time_limit(0);
require_once('../config.php');
require_once('../lib/filelib.php');
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
        die(json_encode($err.time()));
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
    // Ask Petr how to use FILE_API here
    try {
        $pathname = $ret;
        $entry = new object();
        $entry->contextid = SITEID;
        $entry->filearea  = 'repository';
        $entry->filepath  = '/';
        $entry->filename  = $title;
        $entry->timecreated  = time();
        $entry->timemodified = time();
        $entry->itemid       = $USER->id;
        $entry->mimetype     = mimeinfo('type', $pathname);
        $entry->userid       = $USER->id;
        $fs = get_file_storage();
        if ($file = $fs->create_file_from_pathname($entry, $pathname)) {
            //echo json_encode($file->get_content_file_location());
            echo json_encode($file->get_itemid());
        }
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
