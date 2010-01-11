<?php

function xmldb_auth_nntp_install() {
    global $CFG, $DB;

    // upgrade from 1.9.x, introducing version.php

    // remove cached passwords, we do not need them for this plugin
    $DB->set_field('user', 'password', 'not cached', array('auth'=>'nntp'));

}
