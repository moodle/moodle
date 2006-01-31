<?php

require_once('../../../config.php');
require_once('../config.php');


if (!isset($THEME->chameleonenabled) || !$THEME->chameleonenabled) {
    die('CHAMELEON_ERROR Editing this theme has been disabled');
}


$chameleon_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($chameleon_id != 0) {
    if (!isteacher($chameleon_id)) {
        die('CHAMELEON_ERROR You are not logged in');
    }
} else if (!isadmin()) {
    die('CHAMELEON_ERROR You are not logged in');
}


require_once('ChameleonCSS.class.php');
require_once('ChameleonFileBrowser.class.php');



if (isset($_GET['path'])) {
    $fm = new ChameleonFileBrowser;
    die($fm->readFiles());
}

$chameleon = new ChameleonCSS('../', 'user_styles.css', 'temp_user_styles.css');
if (isset($_POST['css'])) {
    if (!isset($_GET['temp'])) {
        $chameleon->update('perm', $_POST['css']);
        $chameleon->update('temp');
    } else {
        $chameleon->update('temp', $_POST['css']);
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