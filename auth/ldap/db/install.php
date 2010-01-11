<?php

function xmldb_auth_ldap_install() {
    global $CFG, $DB;

    // upgrade from 1.9.x, introducing version.php

    // remove cached passwords, we do not need them for this plugin, but only if internal
    if (get_config('auth/ldap', 'preventpassindb')) {
        $DB->set_field('user', 'password', 'not cached', array('auth'=>'ldap'));
    }

}
