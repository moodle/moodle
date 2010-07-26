<?php

function xmldb_auth_ldap_install() {
    global $CFG, $DB;

    // upgrade from 1.9.x, introducing version.php

    // remove cached passwords, we do not need them for this plugin, but only if internal
    if (get_config('auth/ldap', 'preventpassindb')) {
        $DB->set_field('user', 'password', 'not cached', array('auth'=>'ldap'));
    }

    // We kept the LDAP version used to connect to the server in
    // $config->version. In 2.0, $config->version is overwritten with
    // the plugin version number, so we need to change the setting
    // name. Let's call it 'ldap_version' and remove the old setting.
    //
    // This works by pure luck, as the plugin version number is stored in
    // config_plugins table before we get called. The good news is the new
    // version number is stored for 'auth_ldap' plugin name, while the old ldap
    // version setting is stored for 'auth/ldap' plugin name. Yay!
    if ($ldap_version = get_config('auth/ldap', 'version')) {
        set_config('ldap_version', $ldap_version, 'auth/ldap');
        unset_config('version', 'auth/ldap');
    }
}
