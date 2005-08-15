<?php

/**
 * This file should be a series of functions that provide hooks for customisations
 * to play happily. To have local database customisations, provide the following
 * 
 * local/
 * local/version.php
 * local/db/{$CFG->dbtype}.php
 * 
 * To make it easier for people running custom moodles, it isn't necessary to have {$CFG->dbtype}.sql 
 * as well. Rather than detecting the difference between install and upgrade, 
 * and looking for {$CFG->dbtype}.php or {$CFG->type}.sql accordingly,
 * the upgrade_local_db function will just start from 0 if it hasn't been run before,
 * effectively playing all upgrades in {$CFG->dbtype}.php
*/


function upgrade_local_db($continueto) {

    global $CFG, $db;
    
    // if we don't have code version or a db upgrade file, just return true, we're unneeded
    if (!file_exists($CFG->dirroot.'/local/version.php') || !file_exists($CFG->dirroot.'/local/db/'.$CFG->dbtype.'.php')) {
        return true;
    }

    require_once ($CFG->dirroot .'/local/version.php');  // Get code versions

    if (empty($CFG->local_version)) { // normally we'd install, but just replay all the upgrades.
        $CFG->local_version = 0;
    }

    if ($local_version > $CFG->local_version) { // upgrade! 
        $strdatabaseupgrades = get_string('databaseupgrades');
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);
        
        require_once ($CFG->dirroot .'/local/db/'. $CFG->dbtype .'.php');

        $db->debug=true;
        if (local_upgrade($CFG->local_version)) {
            $db->debug=false;
            if (set_config('local_version', $local_version)) {
                notify(get_string('databasesuccess'), 'notifysuccess');
                notify(get_string('databaseupgradelocal', '', $local_version));
                print_continue($continueto);
                exit;
            } else {
                error('Upgrade of local database customisations failed! (Could not update version in config table)');
            }
        } else {
            $db->debug=false;
            error('Upgrade failed!  See local/version.php');
        }

    } else if ($local_version < $CFG->local_version) {
        notify('WARNING!!!  The local version you are using is OLDER than the version that made these databases!');
    }
}


?>