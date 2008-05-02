<?php

require_once('../../../config.php');
require_once('../config.php');


if (empty($THEME->chameleonenabled)) {
    die('CHAMELEON_ERROR Editing this theme has been disabled');
}

$id = optional_param('id', SITEID, PARAM_INT);
if (!$course = get_record('course', 'id', $id)) {
    error('Incorrect course id');
}

course_setup($course); // we should not require login here

if ($id != SITEID and !empty($CFG->allowcoursethemes) and !empty($course->theme) and !empty($THEME->chameleonteachereditenabled)) {
    if (!has_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $id))) {
        die('CHAMELEON_ERROR Either you are not logged in or you are not allowed to edit this theme');
    }
} else if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
    die('CHAMELEON_ERROR Either you are not logged in or you are not allowed to edit this theme');
}


require_once('ChameleonCSS.class.php');
require_once('ChameleonFileBrowser.class.php');



if (isset($_GET['path'])) {
    $fm = new ChameleonFileBrowser;
    die($fm->readfiles());
}

$chameleon = new ChameleonCSS('../', 'user_styles.css', 'temp_user_styles.css');

if (isset($_POST['css'])) {
    if (!isset($_GET['temp'])) {
        if (!$chameleon->update('perm', $_POST['css'])) {
            die('CHAMELEON_ERROR ' . $chameleon->error);
        }
        if (!$chameleon->update('temp')) {
            die('CHAMELEON_ERROR ' . $chameleon->error);
        }
    } else {
        if (!$chameleon->update('temp', $_POST['css'])) {
            die('CHAMELEON_ERROR ' . $chameleon->error);
        }
    }
    
} else {

    $css = $chameleon->read();
    if ($css === false) {
        echo 'CHAMELEON_ERROR ' . $chameleon->error;
    } else {
        echo $css;
    }
}


?>