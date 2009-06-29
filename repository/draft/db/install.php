<?php

function xmldb_repository_draft_install() {
    global $CFG;
    $result = true;
    require_once($CFG->dirroot.'/repository/lib.php');
    $draft_plugin = new repository_type('draft', array(), true);
    if(!$id = $draft_plugin->create(true)) {
        $result = false;
    }
    return $result;
}
