<?php

function xmldb_repository_local_install() {
    global $CFG;
    $result = true;
    require_once($CFG->dirroot.'/repository/lib.php');
    $local_plugin = new repository_type('local', array(), true);
    if(!$id = $local_plugin->create(true)) {
        $result = false;
    }
    return $result;
}
