<?php  //$Id$

// This file keeps track of upgrades to 
// the rss_client block
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
// using the functions defined in lib/ddllib.php

function xmldb_block_rss_client_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

/// And upgrade begins here. For each one, you'll need one 
/// block of code similar to the next one. Please, delete 
/// this comment lines once this file start handling proper
/// upgrade code.

    if ($result && $oldversion < 2007080100) { //New version in version.php
    /// block_rss_timeout config setting must be block_rss_client_timeout
        set_field('config', 'name', 'block_rss_client_timeout', 'name', 'block_rss_timeout');
    }

    return $result;
}

?>
