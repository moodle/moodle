<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_local_autologin_install() {
    // Nothing to do on installation.
}

function xmldb_local_autologin_upgrade($oldversion) {
    // Nothing to do on upgrade.
    return true;
}
