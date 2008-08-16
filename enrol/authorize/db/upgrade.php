<?php  //$Id$

// This file keeps track of upgrades to
// the authorize enrol plugin
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_enrol_authorize_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

//===== 1.9.0 upgrade line ======//

    if ($result && $oldversion < 2008020500 && is_enabled_enrol('authorize')) {
        require_once($CFG->dirroot.'/enrol/authorize/localfuncs.php');
        if (!check_curl_available()) {
            notify("You are using the authorize.net enrolment plugin for payment handling but cUrl is not available.
                    PHP must be compiled with cURL+SSL support (--with-curl --with-openssl)");
        }
    }

    return $result;
}

?>
